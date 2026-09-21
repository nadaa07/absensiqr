<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';

requireRole('admin');

$base = '..';
$assetBase = '../assets';
$pageTitle = 'Jadwal Kuliah';

require __DIR__ . '/../partials/head.php';


/*
|--------------------------------------------------------------------------
| ROSTER RESMI KELAS V.2 SEMESTER V
|--------------------------------------------------------------------------
| Hanya jadwal yang tercantum pada roster.
|--------------------------------------------------------------------------
*/

$jadwal = [

    [
        'no' => 1,
        'mata_kuliah' => 'Interaksi Manusia & Komputer',
        'sks' => 3,
        'semester' => 'V',
        'hari' => 'Selasa',
        'mulai' => '11:00',
        'selesai' => '13:30',
        'ruang' => 'FTI-01',
        'dosen' => 'Junaidi Salat, S.Kom., M.Kom'
    ],

    [
        'no' => 2,
        'mata_kuliah' => 'Jaringan Komputer II',
        'sks' => 3,
        'semester' => 'V',
        'hari' => 'Rabu',
        'mulai' => '11:00',
        'selesai' => '13:30',
        'ruang' => 'LAB-03',
        'dosen' => 'Saved Achmady, ST., M.Kom'
    ],

    [
        'no' => 3,
        'mata_kuliah' => 'Komputer Grafik',
        'sks' => 3,
        'semester' => 'V',
        'hari' => 'Senin',
        'mulai' => '11:00',
        'selesai' => '13:30',
        'ruang' => 'LAB-05',
        'dosen' => 'Wahyuni Harahap, ST., MM'
    ],

    [
        'no' => 4,
        'mata_kuliah' => 'Konsep Data Warehouse & Data Mining',
        'sks' => 3,
        'semester' => 'V',
        'hari' => 'Rabu',
        'mulai' => '08:30',
        'selesai' => '11:00',
        'ruang' => 'LAB-04',
        'dosen' => 'Jessika, S.Kom., M.Kom'
    ],

    [
        'no' => 5,
        'mata_kuliah' => 'Pemrograman Berorientasi Objek',
        'sks' => 3,
        'semester' => 'V',
        'hari' => 'Sabtu',
        'mulai' => '14:30',
        'selesai' => '17:00',
        'ruang' => 'FTI-01',
        'dosen' => 'Ir. Mukhsin Nuzula, ST., MT'
    ],

    [
        'no' => 6,
        'mata_kuliah' => 'Proyek Perangkat Lunak',
        'sks' => 3,
        'semester' => 'V',
        'hari' => 'Sabtu',
        'mulai' => '11:00',
        'selesai' => '13:30',
        'ruang' => 'FTI-05',
        'dosen' => 'Ilal Mahdi, S.T., M.T'
    ],

    [
        'no' => 7,
        'mata_kuliah' => 'Rekayasa Perangkat Lunak II',
        'sks' => 3,
        'semester' => 'V',
        'hari' => 'Selasa',
        'mulai' => '14:30',
        'selesai' => '17:00',
        'ruang' => 'LAB-04',
        'dosen' => 'Zikrul Khalid, ST., MT'
    ]

];


/*
|--------------------------------------------------------------------------
| URUTKAN BERDASARKAN HARI DAN JAM
|--------------------------------------------------------------------------
*/

$urutanHari = [
    'Senin' => 1,
    'Selasa' => 2,
    'Rabu' => 3,
    'Kamis' => 4,
    'Jumat' => 5,
    'Sabtu' => 6
];

usort($jadwal, function ($a, $b) use ($urutanHari) {

    $hariA = $urutanHari[$a['hari']] ?? 99;
    $hariB = $urutanHari[$b['hari']] ?? 99;

    if ($hariA !== $hariB) {
        return $hariA <=> $hariB;
    }

    return strcmp($a['mulai'], $b['mulai']);
});

?>

<div class="dashboard-layout">

    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <main class="main">

        <!-- MOBILE HEADER -->
        <div class="mobile-head">
            <b>Sistem Absensi QR</b>

            <button
                class="menu-toggle"
                type="button"
            >
                ☰
            </button>
        </div>


        <!-- TOPBAR -->
        <header class="topbar">

            <div>

                <h1>Jadwal Kuliah</h1>

                <p>
                    Roster resmi Kelas V.2 Semester V
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


        <!-- =====================================================
             INFORMASI ROSTER
        ====================================================== -->

        <section class="section">

            <div class="section-head">

                <div>

                    <h2>Kelas V.2</h2>

                    <p>
                        Semester V · Tahun Akademik 2026/2027
                    </p>

                </div>

                <div class="badge">
                    7 Mata Kuliah
                </div>

            </div>


            <div class="stats-grid">

                <div class="stat">

                    <span class="stat-label">
                        Total Mata Kuliah
                    </span>

                    <strong class="stat-value">
                        7
                    </strong>

                </div>


                <div class="stat">

                    <span class="stat-label">
                        Total SKS
                    </span>

                    <strong class="stat-value">
                        21
                    </strong>

                </div>


                <div class="stat">

                    <span class="stat-label">
                        Hari Kuliah
                    </span>

                    <strong class="stat-value">
                        4
                    </strong>

                </div>


                <div class="stat">

                    <span class="stat-label">
                        Semester
                    </span>

                    <strong class="stat-value">
                        V
                    </strong>

                </div>

            </div>

        </section>


        <!-- =====================================================
             TABEL ROSTER
        ====================================================== -->

        <section
            class="section"
            style="margin-top:20px"
        >

            <div class="section-head">

                <div>

                    <h2>Roster Perkuliahan</h2>

                    <p>
                        Jadwal sesuai data roster Kelas V.2
                    </p>

                </div>

            </div>


            <div class="table-wrap">

                <table class="table">

                    <thead>

                        <tr>

                            <th>NO.</th>

                            <th>MATA KULIAH</th>

                            <th>SKS</th>

                            <th>SEM</th>

                            <th>HARI</th>

                            <th>JAM</th>

                            <th>RUANG</th>

                            <th>DOSEN PENGAMPU</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($jadwal as $item): ?>

                        <tr>

                            <td>
                                <?= e($item['no']) ?>
                            </td>


                            <td>

                                <strong>
                                    <?= e($item['mata_kuliah']) ?>
                                </strong>

                            </td>


                            <td>
                                <?= e($item['sks']) ?>
                            </td>


                            <td>
                                <?= e($item['semester']) ?>
                            </td>


                            <td>
                                <?= e($item['hari']) ?>
                            </td>


                            <td>

                                <?= e($item['mulai']) ?>

                                —

                                <?= e($item['selesai']) ?>

                            </td>


                            <td>

                                <span class="badge">
                                    <?= e($item['ruang']) ?>
                                </span>

                            </td>


                            <td>
                                <?= e($item['dosen']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- =====================================================
             INFORMASI HARI
        ====================================================== -->

        <section
            class="section"
            style="margin-top:20px"
        >

            <div class="section-head">

                <div>

                    <h2>Distribusi Jadwal</h2>

                    <p>
                        Ringkasan jadwal berdasarkan hari
                    </p>

                </div>

            </div>


            <div class="stats-grid">

                <!-- SENIN -->

                <div class="stat">

                    <span class="stat-label">
                        Senin
                    </span>

                    <strong class="stat-value">
                        1
                    </strong>

                    <small>
                        Komputer Grafik
                    </small>

                </div>


                <!-- SELASA -->

                <div class="stat">

                    <span class="stat-label">
                        Selasa
                    </span>

                    <strong class="stat-value">
                        2
                    </strong>

                    <small>
                        IMK · RPL II
                    </small>

                </div>


                <!-- RABU -->

                <div class="stat">

                    <span class="stat-label">
                        Rabu
                    </span>

                    <strong class="stat-value">
                        2
                    </strong>

                    <small>
                        Data Warehouse · Jaringan II
                    </small>

                </div>


                <!-- SABTU -->

                <div class="stat">

                    <span class="stat-label">
                        Sabtu
                    </span>

                    <strong class="stat-value">
                        2
                    </strong>

                    <small>
                        PPL · PBO
                    </small>

                </div>

            </div>

        </section>


        <!-- =====================================================
             CATATAN
        ====================================================== -->

        <section
            class="section"
            style="margin-top:20px"
        >

            <div class="card">

                <h3>
                    Informasi Jadwal
                </h3>

                <p>
                    Jadwal yang ditampilkan pada halaman ini
                    mengikuti roster Kelas V.2 Semester V.
                </p>

                <p>
                    Hari Kamis dan Jumat tidak memiliki jadwal
                    perkuliahan pada roster tersebut.
                </p>

            </div>

        </section>

    </main>

</div>


<?php require __DIR__ . '/../partials/footer.php'; ?>