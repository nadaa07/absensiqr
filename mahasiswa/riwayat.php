<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('mahasiswa');

$base = '..';
$assetBase = '../assets';
$pageTitle = 'Riwayat Absensi';

require __DIR__ . '/../partials/head.php';

?>

<div class="dashboard-layout">

    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main">

        <div class="mobile-head">
            <b>Sistem Absensi QR</b>
            <button class="menu-toggle">☰</button>
        </div>


        <header class="topbar">

            <div>

                <h1>
                    Riwayat Absensi
                </h1>

                <p>
                    Semua catatan kehadiran milik Anda.
                </p>

            </div>


            <div class="top-actions">

                <a
                    class="btn btn-ghost"
                    href="../index.php"
                >
                    ↗ Beranda
                </a>

            </div>

        </header>


        <?php

        /*
         * Ambil seluruh riwayat absensi
         * mahasiswa yang sedang login.
         */

        $st = $pdo->prepare("
            SELECT

                a.id,
                a.status,
                a.waktu_absen,
                a.latitude,
                a.longitude,
                a.jarak,
                a.keterangan,

                s.tanggal,
                s.jam_mulai,
                s.jam_selesai,
                s.pertemuan,

                mk.nama_mk,

                d.nama AS dosen,

                l.nama_lokasi

            FROM absensi a

            JOIN mahasiswa m
                ON m.id = a.mahasiswa_id

            JOIN users u
                ON u.username = m.npm

            JOIN sesi_absensi s
                ON s.id = a.sesi_id

            JOIN jadwal j
                ON j.id = s.jadwal_id

            JOIN mata_kuliah mk
                ON mk.id = j.mata_kuliah_id

            JOIN dosen d
                ON d.id = j.dosen_id

            LEFT JOIN lokasi_kampus l
                ON l.status = 'Aktif'

            WHERE u.id = ?

            ORDER BY
                s.tanggal DESC,
                s.id DESC

        ");

        $st->execute([
            userId()
        ]);

        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        ?>


        <section class="section">

            <div class="table-wrap">

                <table class="table">

                    <thead>

                        <tr>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                Mata Kuliah
                            </th>

                            <th>
                                Dosen
                            </th>

                            <th>
                                Pertemuan
                            </th>

                            <th>
                                Waktu
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Lokasi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($rows as $r): ?>

                            <tr>

                                <td>
                                    <?= e($r['tanggal']) ?>
                                </td>


                                <td>
                                    <?= e($r['nama_mk']) ?>
                                </td>


                                <td>
                                    <?= e($r['dosen']) ?>
                                </td>


                                <td>
                                    <?= e((string) $r['pertemuan']) ?>
                                </td>


                                <td>

                                    <?= e(
                                        $r['waktu_absen'] ?? '-'
                                    ) ?>

                                </td>


                                <td>

                                    <span
                                        class="badge <?= in_array(
                                            $r['status'],
                                            ['Hadir', 'Terlambat'],
                                            true
                                        )
                                            ? 'green'
                                            : 'red'
                                        ?>"
                                    >

                                        <?= e($r['status']) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= e(
                                        $r['nama_lokasi'] ?? '-'
                                    ) ?>

                                    <?php if ($r['jarak'] !== null): ?>

                                        ·

                                        <?= e(
                                            (string) round(
                                                (float) $r['jarak']
                                            )
                                        ) ?>

                                        m

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                        <?php if (!$rows): ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="empty"
                                >

                                    Belum ada riwayat.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>


    </main>

</div>


<?php require __DIR__ . '/../partials/footer.php'; ?>