<?php
/**
 * API ABSENSI MAHASISWA
 *
 * Fungsi:
 * - Menerima QR mahasiswa / QR sesi dosen
 * - Memastikan request berasal dari akun mahasiswa yang sedang login
 * - Memeriksa sesi, kelas, lokasi, GPS dan waktu
 * - Menyimpan absensi ke tabel absensi
 */

declare(strict_types=1);

/* Waktu aplikasi mengikuti WIB (Aceh/Indonesia). */
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

/* ---------------------------------------------------------
   SESSION
--------------------------------------------------------- */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ---------------------------------------------------------
   RESPONSE HELPER
--------------------------------------------------------- */
function attendanceResponse(bool $ok, string $message, int $http = 200, array $data = []): never
{
    http_response_code($http);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok'      => $ok,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------------------------------------------------------
   INPUT
--------------------------------------------------------- */
$raw = file_get_contents('php://input');
$input = [];

if (is_string($raw) && trim($raw) !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $input = $decoded;
    }
}

if (!$input && !empty($_POST)) {
    $input = $_POST;
}

$qr        = trim((string)($input['qr'] ?? ''));
$sessionId = (int)($input['session_id'] ?? 0);
$locationId = (int)($input['location_id'] ?? 0);
$latitude  = isset($input['latitude']) && $input['latitude'] !== '' ? (float)$input['latitude'] : null;
$longitude = isset($input['longitude']) && $input['longitude'] !== '' ? (float)$input['longitude'] : null;

if ($qr === '') {
    attendanceResponse(false, 'QR belum terbaca.', 422);
}

if ($sessionId <= 0) {
    attendanceResponse(false, 'Sesi absensi belum dipilih.', 422);
}

if ($locationId <= 0) {
    attendanceResponse(false, 'Lokasi absensi belum dipilih.', 422);
}

if ($latitude === null || $longitude === null) {
    attendanceResponse(false, 'Koordinat GPS belum tersedia.', 422);
}

if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
    attendanceResponse(false, 'Koordinat GPS tidak valid.', 422);
}

/* ---------------------------------------------------------
   IDENTITAS USER LOGIN

   Jangan bergantung hanya pada $_SESSION['role'].
   Beberapa halaman login menyimpan role berbeda/terlambat,
   sedangkan user_id sudah benar. Identitas mahasiswa ditentukan
   dari users.id -> users.username -> mahasiswa.npm.
--------------------------------------------------------- */
$userId = 0;

if (isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
} elseif (isset($_SESSION['user']['id'])) {
    $userId = (int)$_SESSION['user']['id'];
} elseif (isset($_SESSION['user']['user_id'])) {
    $userId = (int)$_SESSION['user']['user_id'];
}

if ($userId <= 0) {
    attendanceResponse(false, 'Sesi login tidak ditemukan. Silakan login ulang sebagai mahasiswa.', 401);
}

/* Cari mahasiswa berdasarkan akun yang sedang login. */
$st = $pdo->prepare('
    SELECT
        m.id,
        m.npm,
        m.nama,
        m.qr_code,
        u.id AS user_id,
        u.username
    FROM users u
    INNER JOIN mahasiswa m ON m.npm = u.username
    WHERE u.id = ?
    LIMIT 1
');
$st->execute([$userId]);
$mahasiswa = $st->fetch(PDO::FETCH_ASSOC);

if (!$mahasiswa) {
    attendanceResponse(
        false,
        'Akun yang sedang login belum terhubung ke data mahasiswa. Pastikan login menggunakan akun mahasiswa.',
        403
    );
}

/*
 * Role tidak dijadikan satu-satunya sumber kebenaran.
 * Kalau akun berhasil ditemukan di tabel mahasiswa melalui users.id,
 * maka akun tersebut memang mempunyai identitas mahasiswa.
 * Jika session role tersedia dan jelas bukan mahasiswa, tolak.
 */
$sessionRole = strtolower(trim((string)($_SESSION['role'] ?? '')));
if ($sessionRole !== '' && !in_array($sessionRole, ['mahasiswa', 'student', 'siswa'], true)) {
    attendanceResponse(false, 'Sesi login bukan akun mahasiswa. Silakan login ulang sebagai mahasiswa.', 403);
}

/* ---------------------------------------------------------
   VALIDASI QR
--------------------------------------------------------- */
$isSessionQr = str_starts_with($qr, 'SESSION|');
$isStudentQr = str_starts_with($qr, 'MHS-') || !$isSessionQr;

if ($isStudentQr && !$isSessionQr) {
    if ((string)$mahasiswa['qr_code'] === '' || $qr !== (string)$mahasiswa['qr_code']) {
        attendanceResponse(false, 'QR mahasiswa bukan milik akun yang sedang login.', 403);
    }
}

/* ---------------------------------------------------------
   AMBIL SESI + JADWAL
--------------------------------------------------------- */
$st = $pdo->prepare('
    SELECT
        s.id,
        s.jadwal_id,
        s.pertemuan,
        s.tanggal,
        s.jam_mulai,
        s.jam_selesai,
        s.token,
        s.qr_data,
        s.lokasi_id AS sesi_lokasi_id,
        s.radius AS sesi_radius,
        s.status AS sesi_status,
        j.mata_kuliah_id,
        j.dosen_id,
        j.kelas_id,
        j.lokasi_id AS jadwal_lokasi_id,
        j.ruangan,
        mk.kode_mk,
        mk.nama_mk,
        d.nama AS nama_dosen
    FROM sesi_absensi s
    INNER JOIN jadwal j ON j.id = s.jadwal_id
    INNER JOIN mata_kuliah mk ON mk.id = j.mata_kuliah_id
    INNER JOIN dosen d ON d.id = j.dosen_id
    WHERE s.id = ?
    LIMIT 1
');
$st->execute([$sessionId]);
$sesi = $st->fetch(PDO::FETCH_ASSOC);

if (!$sesi) {
    attendanceResponse(false, 'Sesi absensi tidak ditemukan.', 404);
}

/* Status sesi boleh berupa aktif/Aktif tergantung data lama. */
if (strtolower((string)$sesi['sesi_status']) !== 'aktif') {
    attendanceResponse(false, 'Sesi absensi sedang tidak aktif.', 409);
}

/* QR sesi harus benar-benar berasal dari sesi yang dipilih. */
if ($isSessionQr) {
    $parts = explode('|', $qr, 2);
    $token = trim($parts[1] ?? '');

    if ($token === '' || !hash_equals((string)$sesi['token'], $token)) {
        attendanceResponse(false, 'QR sesi tidak sesuai dengan sesi yang dipilih.', 403);
    }
}

/* ---------------------------------------------------------
   TANGGAL + KELAS MAHASISWA
--------------------------------------------------------- */
$today = date('Y-m-d');
if ((string)$sesi['tanggal'] !== $today) {
    attendanceResponse(false, 'Sesi absensi bukan untuk hari ini.', 409, [
        'tanggal_sesi' => $sesi['tanggal'],
        'tanggal_server' => $today
    ]);
}

$st = $pdo->prepare('
    SELECT 1
    FROM kelas_mahasiswa
    WHERE kelas_id = ?
      AND mahasiswa_id = ?
    LIMIT 1
');
$st->execute([(int)$sesi['kelas_id'], (int)$mahasiswa['id']]);

if (!$st->fetchColumn()) {
    attendanceResponse(false, 'Mahasiswa tidak terdaftar pada kelas sesi ini.', 403);
}

/* ---------------------------------------------------------
   LOKASI

   absensi TIDAK mempunyai lokasi_id.
   Relasinya:
   absensi.sesi_id -> sesi_absensi.lokasi_id -> lokasi_kampus.id
   atau fallback ke jadwal.lokasi_id.
--------------------------------------------------------- */
$sessionLocationId = (int)($sesi['sesi_lokasi_id'] ?? 0);
if ($sessionLocationId <= 0) {
    $sessionLocationId = (int)($sesi['jadwal_lokasi_id'] ?? 0);
}

/*
 * Jika sesi lama belum memiliki lokasi, gunakan lokasi yang dipilih
 * pada halaman absensi lalu simpan ke sesi. Lokasi tetap diverifikasi
 * ke tabel lokasi_kampus dan radius GPS sebelum absensi diterima.
 */
if ($sessionLocationId <= 0) {
    $sessionLocationId = $locationId;

    try {
        $st = $pdo->prepare('UPDATE sesi_absensi SET lokasi_id = ? WHERE id = ? AND (lokasi_id IS NULL OR lokasi_id = 0)');
        $st->execute([$sessionLocationId, $sessionId]);
    } catch (Throwable $e) {
        error_log('attendance.php update sesi lokasi error: ' . $e->getMessage());
        attendanceResponse(false, 'Lokasi sudah dipilih tetapi gagal disimpan ke sesi absensi.', 500);
    }
}

if ($locationId !== $sessionLocationId) {
    attendanceResponse(false, 'Lokasi yang dipilih tidak sesuai dengan lokasi sesi.', 403);
}

$st = $pdo->prepare('
    SELECT
        id,
        nama_lokasi,
        latitude,
        longitude,
        radius,
        status
    FROM lokasi_kampus
    WHERE id = ?
    LIMIT 1
');
$st->execute([$sessionLocationId]);
$lokasi = $st->fetch(PDO::FETCH_ASSOC);

if (!$lokasi) {
    attendanceResponse(false, 'Lokasi absensi tidak ditemukan.', 404);
}

if (strtolower((string)$lokasi['status']) !== 'aktif') {
    attendanceResponse(false, 'Lokasi absensi sedang tidak aktif.', 409);
}

/* ---------------------------------------------------------
   HITUNG JARAK
--------------------------------------------------------- */
$distance = null;

if (function_exists('distanceMeters')) {
    $distance = (float)distanceMeters(
        $latitude,
        $longitude,
        (float)$lokasi['latitude'],
        (float)$lokasi['longitude']
    );
} else {
    /* Haversine fallback jika helper tidak menyediakan fungsi tersebut. */
    $earthRadius = 6371000.0;
    $lat1 = deg2rad($latitude);
    $lat2 = deg2rad((float)$lokasi['latitude']);
    $dLat = deg2rad((float)$lokasi['latitude'] - $latitude);
    $dLon = deg2rad((float)$lokasi['longitude'] - $longitude);

    $a = sin($dLat / 2) ** 2
       + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;
}

// Radius REAL absensi: 1700 meter.
// Nilai ini sengaja dipakai di server agar tidak kembali ke radius 100 m dari database.
$radius = 2000.0;
if ($distance > $radius) {
    attendanceResponse(false, 'Di luar radius absensi. Jarak Anda ' . round($distance) . ' m, batas ' . round($radius) . ' m.', 403, [
        'jarak' => round($distance, 2),
        'radius' => $radius,
        'lokasi' => $lokasi['nama_lokasi']
    ]);
}

/* ---------------------------------------------------------
   WAKTU SESI
--------------------------------------------------------- */
try {
    $now = new DateTime('now');
    $start = new DateTime($sesi['tanggal'] . ' ' . $sesi['jam_mulai']);
    $end = new DateTime($sesi['tanggal'] . ' ' . $sesi['jam_selesai']);
} catch (Throwable $e) {
    attendanceResponse(false, 'Format waktu sesi tidak valid.', 500);
}

if ($now < $start || $now > $end) {
    attendanceResponse(false, 'Di luar waktu sesi absensi.', 403, [
        'mulai' => $start->format('Y-m-d H:i:s'),
        'selesai' => $end->format('Y-m-d H:i:s'),
        'server' => $now->format('Y-m-d H:i:s')
    ]);
}

/* 15 menit pertama = Hadir, sisanya = Terlambat. */
$limitHadir = clone $start;
$limitHadir->modify('+15 minutes');
$statusAbsen = ($now <= $limitHadir) ? 'Hadir' : 'Terlambat';
$waktuAbsen = $now->format('Y-m-d H:i:s');

/* ---------------------------------------------------------
   DUPLIKAT
--------------------------------------------------------- */
$st = $pdo->prepare('
    SELECT id, status, waktu_absen
    FROM absensi
    WHERE sesi_id = ?
      AND mahasiswa_id = ?
    LIMIT 1
');
$st->execute([$sessionId, (int)$mahasiswa['id']]);
$existing = $st->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    attendanceResponse(false, 'Anda sudah melakukan absensi pada sesi ini.', 409, [
        'absensi_id' => (int)$existing['id'],
        'status' => $existing['status'],
        'waktu' => $existing['waktu_absen']
    ]);
}

/* ---------------------------------------------------------
   SIMPAN
--------------------------------------------------------- */
$perangkat = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'), 0, 500);
$ipAddress = substr((string)($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
$keterangan = null;

try {
    $pdo->beginTransaction();

    /*
     * PENTING:
     * tabel absensi tidak mempunyai lokasi_id.
     * Lokasi diambil dari sesi_absensi/jadwal.
     */
    $st = $pdo->prepare('
        INSERT INTO absensi (
            sesi_id,
            mahasiswa_id,
            status,
            waktu_absen,
            latitude,
            longitude,
            jarak,
            lokasi_valid,
            qr_valid,
            face_valid,
            perangkat,
            ip_address,
            keterangan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1, 0, ?, ?, ?)
    ');

    $st->execute([
        $sessionId,
        (int)$mahasiswa['id'],
        $statusAbsen,
        $waktuAbsen,
        $latitude,
        $longitude,
        round($distance, 2),
        $perangkat,
        $ipAddress,
        $keterangan
    ]);

    $attendanceId = (int)$pdo->lastInsertId();
    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    /* UNIQUE(sesi_id, mahasiswa_id) menangani request ganda/race condition. */
    if ((int)($e->errorInfo[1] ?? 0) === 1062) {
        attendanceResponse(false, 'Anda sudah melakukan absensi pada sesi ini.', 409);
    }

    error_log('attendance.php INSERT error: ' . $e->getMessage());
    attendanceResponse(false, 'Gagal menyimpan data absensi ke database.', 500);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('attendance.php error: ' . $e->getMessage());
    attendanceResponse(false, 'Terjadi kesalahan saat menyimpan absensi.', 500);
}

/* ---------------------------------------------------------
   BERHASIL
--------------------------------------------------------- */
attendanceResponse(true, 'Absensi berhasil dicatat.', 200, [
        'absensi_id' => $attendanceId,
        'nama' => $mahasiswa['nama'],
        'npm' => $mahasiswa['npm'],
        'status' => $statusAbsen,
        'jarak' => round($distance, 2),
        'radius' => $radius,
        'lokasi' => $lokasi['nama_lokasi'],
        'sesi_id' => $sessionId,
        'mata_kuliah' => $sesi['nama_mk'],
        'dosen' => $sesi['nama_dosen'],
        'waktu' => $now->format('H:i:s'),
    ]);
