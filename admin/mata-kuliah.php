<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');

$base = '..';
$assetBase = '../assets';
$pageTitle = 'Mata Kuliah';

require __DIR__ . '/../partials/head.php';

$mataKuliah = [
    [
        'no' => 1,
        'kode' => 'MK-V2-01',
        'nama' => 'Interaksi Manusia & Komputer',
        'sks' => 3
    ],
    [
        'no' => 2,
        'kode' => 'MK-V2-02',
        'nama' => 'Jaringan Komputer II',
        'sks' => 3
    ],
    [
        'no' => 3,
        'kode' => 'MK-V2-03',
        'nama' => 'Komputer Grafik',
        'sks' => 3
    ],
    [
        'no' => 4,
        'kode' => 'MK-V2-04',
        'nama' => 'Konsep Data Warehouse & Data Mining',
        'sks' => 3
    ],
    [
        'no' => 5,
        'kode' => 'MK-V2-05',
        'nama' => 'Pemrograman Berorientasi Objek',
        'sks' => 3
    ],
    [
        'no' => 6,
        'kode' => 'MK-V2-06',
        'nama' => 'Proyek Perangkat Lunak',
        'sks' => 3
    ],
    [
        'no' => 7,
        'kode' => 'MK-V2-07',
        'nama' => 'Rekayasa Perangkat Lunak II',
        'sks' => 3
    ]
];

?>

<div class="dashboard-layout">

    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main">

        <div class="mobile-head">
            <b>Sistem Absensi QR</b>
            <button class="menu-toggle" type="button">☰</button>
        </div>

        <header class="topbar">

            <div>
                <h1>Mata Kuliah</h1>
                <p>Kelas V.2 · Semester V</p>
            </div>

            <div class="top-actions">
                <a class="btn btn-ghost" href="../index.php">
                    ↗ Beranda
                </a>
            </div>

        </header>

        <section class="section">

            <div class="stats-grid">

                <div class="stat">
                    <span class="stat-label">Mata Kuliah</span>
                    <strong class="stat-value">7</strong>
                </div>

                <div class="stat">
                    <span class="stat-label">Total SKS</span>
                    <strong class="stat-value">21</strong>
                </div>

                <div class="stat">
                    <span class="stat-label">Semester</span>
                    <strong class="stat-value">V</strong>
                </div>

                <div class="stat">
                    <span class="stat-label">Kelas</span>
                    <strong class="stat-value">V.2</strong>
                </div>

            </div>

        </section>

        <section class="section" style="margin-top:20px">

            <div class="section-head">

                <div>
                    <h2>Daftar Mata Kuliah</h2>
                </div>

            </div>

            <div class="table-wrap">

                <table class="table">

                    <thead>

                        <tr>
                            <th>NO.</th>
                            <th>KODE MK</th>
                            <th>MATA KULIAH</th>
                            <th>SKS</th>
                            <th>SEMESTER</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($mataKuliah as $item): ?>

                            <tr>

                                <td>
                                    <?= e($item['no']) ?>
                                </td>

                                <td>
                                    <span class="badge">
                                        <?= e($item['kode']) ?>
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        <?= e($item['nama']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= e($item['sks']) ?>
                                </td>

                                <td>
                                    V
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>