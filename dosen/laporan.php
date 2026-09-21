<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
requireRole('dosen');

$base = '..';
$assetBase = '../assets';
$pageTitle = 'Laporan Presensi';

$didStmt = $pdo->prepare("SELECT d.id, d.nidn, d.nama FROM dosen d JOIN users u ON u.username = d.nidn WHERE u.id = ? LIMIT 1");
$didStmt->execute([userId()]);
$dosen = $didStmt->fetch(PDO::FETCH_ASSOC);
$did = (int)($dosen['id'] ?? 0);

$st = $pdo->prepare("SELECT
    s.id AS sesi_id,
    s.tanggal,
    s.pertemuan,
    s.jam_mulai,
    s.jam_selesai,
    mk.kode_mk,
    mk.nama_mk,
    k.nama_kelas,
    COUNT(DISTINCT km.mahasiswa_id) AS total,
    COALESCE(SUM(CASE WHEN a.status = 'Hadir' THEN 1 ELSE 0 END), 0) AS hadir,
    COALESCE(SUM(CASE WHEN a.status = 'Terlambat' THEN 1 ELSE 0 END), 0) AS terlambat,
    COALESCE(SUM(CASE WHEN a.status IN ('Izin','Sakit') THEN 1 ELSE 0 END), 0) AS izin_sakit,
    COALESCE(SUM(CASE WHEN a.status = 'Tidak Hadir' OR a.id IS NULL THEN 1 ELSE 0 END), 0) AS tidak_hadir
FROM sesi_absensi s
JOIN jadwal j ON j.id = s.jadwal_id
JOIN mata_kuliah mk ON mk.id = j.mata_kuliah_id
JOIN kelas k ON k.id = j.kelas_id
JOIN kelas_mahasiswa km ON km.kelas_id = k.id
LEFT JOIN absensi a ON a.sesi_id = s.id AND a.mahasiswa_id = km.mahasiswa_id
WHERE j.dosen_id = ?
GROUP BY s.id, s.tanggal, s.pertemuan, s.jam_mulai, s.jam_selesai, mk.kode_mk, mk.nama_mk, k.nama_kelas
ORDER BY s.tanggal DESC, s.id DESC");
$st->execute([$did]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../partials/head.php';
?>
<div class="dashboard-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>
    <main class="main">
        <div class="mobile-head"><b>Sistem Absensi QR</b><button class="menu-toggle">☰</button></div>
        <header class="topbar">
            <div>
                <h1>Laporan Presensi</h1>
                <p>Ringkasan kehadiran berdasarkan sesi.</p>
            </div>
            <div class="top-actions">
                <a class="btn btn-primary" href="laporan_pdf.php" target="_blank" rel="noopener">↓ Unduh PDF</a>
                <a class="btn btn-ghost" href="../index.php">↗ Beranda</a>
            </div>
        </header>

        <section class="section">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pertemuan</th>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Total</th>
                            <th>Hadir</th>
                            <th>Terlambat</th>
                            <th>Izin/Sakit</th>
                            <th>Tidak hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= e($r['tanggal']) ?></td>
                            <td><?= e((string)$r['pertemuan']) ?></td>
                            <td><?= e(($r['kode_mk'] ? $r['kode_mk'].' - ' : '').$r['nama_mk']) ?></td>
                            <td><?= e($r['nama_kelas']) ?></td>
                            <td><?= (int)$r['total'] ?></td>
                            <td><?= (int)$r['hadir'] ?></td>
                            <td><?= (int)$r['terlambat'] ?></td>
                            <td><?= (int)$r['izin_sakit'] ?></td>
                            <td><?= (int)$r['tidak_hadir'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$rows): ?>
                        <tr><td colspan="9" class="empty">Belum ada data presensi.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<?php require __DIR__ . '/../partials/footer.php'; ?>
