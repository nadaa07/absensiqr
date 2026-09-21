<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../config/helpers.php';

requireRole('dosen');

$base = '..';
$assetBase = '../assets';
$pageTitle = 'Absensi Kelas';

require __DIR__.'/../partials/head.php';
?>

<div class="dashboard-layout">

    <?php require __DIR__.'/../partials/sidebar.php'; ?>

    <main class="main">

        <div class="mobile-head">
            <b>Sistem Absensi QR</b>
            <button class="menu-toggle">☰</button>
        </div>


        <header class="topbar">

            <div>
                <h1>Absensi Kelas</h1>

                <p>
                    Lihat siapa hadir, terlambat, izin, sakit,
                    atau belum hadir.
                </p>
            </div>

            <div class="top-actions">

                <a
                    class="btn btn-ghost"
                    href="../index.php">
                    ↗ Beranda
                </a>

            </div>

        </header>


<?php

/* =========================================================
   IDENTITAS DOSEN
   ========================================================= */

$did = (int) (
    $pdo->query("
        SELECT d.id
        FROM dosen d
        JOIN users u
            ON u.username = d.nidn
        WHERE u.id = " . userId()
    )->fetchColumn() ?: 0
);


/* =========================================================
   AMBIL ID SESI DARI URL
   ========================================================= */

$sid = (int) ($_GET['sesi'] ?? 0);


/* =========================================================
   AMBIL SESI YANG DIPILIH
   ========================================================= */

$st = $pdo->prepare("
    SELECT
        s.*,
        mk.nama_mk,
        k.nama_kelas,
        j.hari,
        j.jam_mulai AS jadwal_mulai,
        j.jam_selesai AS jadwal_selesai

    FROM sesi_absensi s

    JOIN jadwal j
        ON j.id = s.jadwal_id

    JOIN mata_kuliah mk
        ON mk.id = j.mata_kuliah_id

    JOIN kelas k
        ON k.id = j.kelas_id

    WHERE s.id = ?
      AND j.dosen_id = ?

    LIMIT 1
");

$st->execute([
    $sid,
    $did
]);

$session = $st->fetch();


/* =========================================================
   JIKA SESI TIDAK DITEMUKAN,
   AMBIL SESI TERBARU DOSEN
   ========================================================= */

if (!$session) {

    $st = $pdo->prepare("
        SELECT
            s.id

        FROM sesi_absensi s

        JOIN jadwal j
            ON j.id = s.jadwal_id

        WHERE j.dosen_id = ?

        ORDER BY
            s.tanggal DESC,
            s.id DESC

        LIMIT 1
    ");

    $st->execute([$did]);

    $sid = (int) $st->fetchColumn();


    if ($sid) {

        $st = $pdo->prepare("
            SELECT
                s.*,
                mk.nama_mk,
                k.nama_kelas,
                j.hari,
                j.jam_mulai AS jadwal_mulai,
                j.jam_selesai AS jadwal_selesai

            FROM sesi_absensi s

            JOIN jadwal j
                ON j.id = s.jadwal_id

            JOIN mata_kuliah mk
                ON mk.id = j.mata_kuliah_id

            JOIN kelas k
                ON k.id = j.kelas_id

            WHERE s.id = ?

            LIMIT 1
        ");

        $st->execute([$sid]);

        $session = $st->fetch();
    }
}


/* =========================================================
   DATA MAHASISWA
   ========================================================= */

$rows = [];

if ($session) {

    $st = $pdo->prepare("
        SELECT

            m.npm,
            m.nama,

            /*
             * STATUS UTAMA
             *
             * 1. Pengajuan disetujui Sakit
             * 2. Pengajuan disetujui Izin
             * 3. Absensi QR
             * 4. Tidak ada data = Tidak Hadir
             */

            CASE

                WHEN p.status = 'Disetujui'
                     AND LOWER(TRIM(p.jenis)) = 'sakit'
                    THEN 'Sakit'

                WHEN p.status = 'Disetujui'
                     AND LOWER(TRIM(p.jenis)) = 'izin'
                    THEN 'Izin'

                WHEN a.status IS NOT NULL
                    THEN a.status

                ELSE 'Tidak Hadir'

            END AS status,


            a.waktu_absen,
            a.jarak,


            /*
             * DATA PENGAJUAN
             */

            p.jenis AS pengajuan_jenis,
            p.alasan AS pengajuan_alasan,
            p.status AS pengajuan_status,
            p.catatan_dosen,
            p.diverifikasi_at


        FROM mahasiswa m


        JOIN kelas_mahasiswa km
            ON km.mahasiswa_id = m.id


        JOIN jadwal j
            ON j.kelas_id = km.kelas_id


        JOIN sesi_absensi s
            ON s.jadwal_id = j.id


        /*
         * ABSENSI QR
         */

        LEFT JOIN absensi a
            ON a.sesi_id = s.id
            AND a.mahasiswa_id = m.id


        /*
         * PENGAJUAN YANG SUDAH DISETUJUI
         */

        LEFT JOIN pengajuan_absensi p
            ON p.sesi_id = s.id
            AND p.mahasiswa_id = m.id
            AND p.status = 'Disetujui'


        WHERE s.id = ?

        ORDER BY
            m.nama ASC
    ");

    $st->execute([$sid]);

    $rows = $st->fetchAll();
}


/* =========================================================
   HITUNG STATISTIK
   ========================================================= */

$count = [
    'Hadir' => 0,
    'Terlambat' => 0,
    'Izin' => 0,
    'Sakit' => 0,
    'Tidak Hadir' => 0
];


foreach ($rows as $r) {

    $status = $r['status'];

    if (isset($count[$status])) {

        $count[$status]++;

    } else {

        $count['Tidak Hadir']++;

    }

}

?>


        <!-- STATISTIK -->

        <div class="stats">

            <div class="stat">

                <small>Hadir</small>

                <strong>
                    <?= $count['Hadir'] ?>
                </strong>

            </div>


            <div class="stat">

                <small>Terlambat</small>

                <strong>
                    <?= $count['Terlambat'] ?>
                </strong>

            </div>


            <div class="stat">

                <small>Izin / Sakit</small>

                <strong>
                    <?= $count['Izin'] + $count['Sakit'] ?>
                </strong>

            </div>


            <div class="stat">

                <small>Belum hadir</small>

                <strong>
                    <?= $count['Tidak Hadir'] ?>
                </strong>

            </div>

        </div>



        <!-- DAFTAR ABSENSI -->

        <section
            class="section"
            style="margin-top:20px">


            <div class="section-head">

                <div>

                    <h2>
                        <?= e(
                            $session['nama_mk']
                            ?? 'Belum ada sesi'
                        ) ?>
                    </h2>


                    <span>

                        <?= e(
                            $session['tanggal']
                            ?? '-'
                        ) ?>

                        ·

                        <?= e(
                            $session['nama_kelas']
                            ?? '-'
                        ) ?>

                        <?php if (!empty($session['hari'])): ?>

                            ·

                            <?= e(
                                $session['hari']
                            ) ?>

                        <?php endif; ?>

                    </span>

                </div>


                <a
                    class="btn btn-primary"
                    href="sesi.php">

                    Buat QR

                </a>

            </div>



            <div class="table-wrap">

                <table class="table">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>NPM</th>

                            <th>Mahasiswa</th>

                            <th>Status</th>

                            <th>Waktu</th>

                            <th>Jarak</th>

                        </tr>

                    </thead>


                    <tbody>

<?php if ($rows): ?>

<?php foreach ($rows as $i => $r): ?>

<?php

$status = $r['status'];

if (
    in_array(
        $status,
        ['Hadir', 'Terlambat']
    )
) {

    $badgeClass = 'green';

} elseif (
    in_array(
        $status,
        ['Izin', 'Sakit']
    )
) {

    $badgeClass = 'gold';

} else {

    $badgeClass = 'red';

}

?>

                        <tr>

                            <td>
                                <?= $i + 1 ?>
                            </td>


                            <td>
                                <?= e($r['npm']) ?>
                            </td>


                            <td>

                                <?= e($r['nama']) ?>

                                <?php if (
                                    in_array(
                                        $status,
                                        ['Izin', 'Sakit']
                                    )
                                    && !empty(
                                        $r['pengajuan_alasan']
                                    )
                                ): ?>

                                    <small
                                        style="
                                            display:block;
                                            color:#887d90;
                                            margin-top:4px;
                                        ">

                                        <?= e(
                                            $r['pengajuan_alasan']
                                        ) ?>

                                    </small>

                                <?php endif; ?>

                            </td>


                            <td>

                                <span
                                    class="badge <?= $badgeClass ?>">

                                    <?= e($status) ?>

                                </span>

                            </td>


                            <td>

                                <?php

                                if (
                                    in_array(
                                        $status,
                                        ['Izin', 'Sakit']
                                    )
                                ) {

                                    echo e(
                                        $r['diverifikasi_at']
                                        ?? '-'
                                    );

                                } else {

                                    echo e(
                                        $r['waktu_absen']
                                        ?? '-'
                                    );

                                }

                                ?>

                            </td>


                            <td>

                                <?php if (
                                    $status === 'Hadir'
                                    || $status === 'Terlambat'
                                ): ?>

                                    <?= e(
                                        $r['jarak']
                                            ? round(
                                                (float)
                                                $r['jarak']
                                            ) . ' m'
                                            : '-'
                                    ) ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </td>

                        </tr>

<?php endforeach; ?>

<?php else: ?>

                        <tr>

                            <td
                                colspan="6"
                                style="
                                    text-align:center;
                                    padding:30px;
                                ">

                                Belum ada data mahasiswa.

                            </td>

                        </tr>

<?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>


    </main>

</div>


<?php require __DIR__.'/../partials/footer.php'; ?>