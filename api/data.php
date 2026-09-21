<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/helpers.php';

$action = $_GET['action'] ?? '';



/* =========================================================
   LOKASI KAMPUS
   ========================================================= */

if ($action === 'locations') {

    $st = $pdo->query("
        SELECT
            id,
            nama_lokasi,
            latitude,
            longitude,
            radius
        FROM lokasi_kampus
        WHERE status = 'Aktif'
        ORDER BY nama_lokasi
    ");

    jsonResponse([
        'ok' => true,
        'data' => $st->fetchAll()
    ]);
}



/* =========================================================
   SESI ABSENSI AKTIF
   ========================================================= */

if ($action === 'active_sessions') {

    $st = $pdo->query("
        SELECT
            s.id,
            s.token,
            s.tanggal,
            s.jam_mulai,
            s.jam_selesai,

            j.hari,

            mk.nama_mk,

            k.nama_kelas

        FROM sesi_absensi s

        JOIN jadwal j
            ON j.id = s.jadwal_id

        JOIN mata_kuliah mk
            ON mk.id = j.mata_kuliah_id

        JOIN kelas k
            ON k.id = j.kelas_id

        WHERE s.status = 'aktif'

        AND s.tanggal = CURDATE()

        ORDER BY
            j.hari,
            s.jam_mulai
    ");

    jsonResponse([
        'ok' => true,
        'data' => $st->fetchAll()
    ]);
}



/* =========================================================
   IDENTIFIKASI QR MAHASISWA
   ========================================================= */

if ($action === 'identify') {

    $qr = trim($_GET['qr'] ?? '');

    $st = $pdo->prepare("
        SELECT
            m.npm,
            m.nama,
            m.qr_code,
            k.nama_kelas AS kelas

        FROM mahasiswa m

        LEFT JOIN kelas k
            ON k.id = m.kelas_id

        WHERE m.qr_code = ?

        LIMIT 1
    ");

    $st->execute([$qr]);

    $m = $st->fetch();

    if ($m) {

        jsonResponse([
            'ok' => true,
            'data' => $m
        ]);

    }

    jsonResponse([
        'ok' => false,
        'message' => 'QR mahasiswa tidak dikenal.'
    ], 404);
}



/* =========================================================
   SESI BERDASARKAN TOKEN QR
   ========================================================= */

if ($action === 'session_by_token') {

    $token = trim($_GET['token'] ?? '');

    $st = $pdo->prepare("
        SELECT
            s.id,
            s.token,
            s.tanggal,
            s.jam_mulai,
            s.jam_selesai,

            j.hari,

            mk.nama_mk,

            k.nama_kelas

        FROM sesi_absensi s

        JOIN jadwal j
            ON j.id = s.jadwal_id

        JOIN mata_kuliah mk
            ON mk.id = j.mata_kuliah_id

        JOIN kelas k
            ON k.id = j.kelas_id

        WHERE s.token = ?

        AND s.status = 'aktif'

        LIMIT 1
    ");

    $st->execute([$token]);

    $x = $st->fetch();

    if ($x) {

        jsonResponse([
            'ok' => true,
            'data' => $x
        ]);

    }

    jsonResponse([
        'ok' => false,
        'message' => 'QR sesi tidak valid atau sudah ditutup.'
    ], 404);
}



/* =========================================================
   ENDPOINT TIDAK DITEMUKAN
   ========================================================= */

jsonResponse([
    'ok' => false,
    'message' => 'Endpoint tidak ditemukan.'
], 404);