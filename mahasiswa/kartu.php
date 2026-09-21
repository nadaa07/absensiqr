<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../config/helpers.php';

requireRole('mahasiswa');

$base = '..';
$assetBase = '../assets';
$pageTitle = 'Kartu Absensi QR';

require __DIR__.'/../partials/head.php';


/* =========================================================
   AMBIL DATA MAHASISWA
========================================================= */

$st = $pdo->prepare(
    'SELECT
        m.*,
        k.nama_kelas
     FROM mahasiswa m
     JOIN users u
        ON u.id = m.user_id
     LEFT JOIN kelas k
        ON k.id = m.kelas_id
     WHERE u.id = ?
     LIMIT 1'
);

$st->execute([userId()]);

$m = $st->fetch();


/* =========================================================
   FOTO
========================================================= */

$fotoUrl = '';

if (!empty($m['foto'])) {

    $fotoPath = __DIR__.'/../'.$m['foto'];

    if (is_file($fotoPath)) {
        $fotoUrl = '../'.$m['foto'];
    }
}


/* =========================================================
   DATA TAMBAHAN
========================================================= */

$nama = $m['nama'] ?? '-';
$npm = $m['npm'] ?? '-';
$prodi = $m['prodi'] ?? 'Teknik Informatika';
$kelas = $m['nama_kelas'] ?? '-';
$angkatan = $m['angkatan'] ?? '-';
$fakultas = $m['fakultas'] ?? 'Fakultas Teknik';
$jenisKelamin = $m['jenis_kelamin'] ?? '-';
$email = $m['email'] ?? '-';
$noHp = $m['no_hp'] ?? '-';
$qrCode = $m['qr_code'] ?? 'MHS-'.$npm;
$kartuNomor = $m['kartu_nomor'] ?? 'AK-2026-'.$npm;
$status = $m['status'] ?? 'Aktif';

?>

<div class="dashboard-layout">

    <?php require __DIR__.'/../partials/sidebar.php'; ?>


    <main class="main">

        <!-- MOBILE HEADER -->

        <div class="mobile-head">

            <b>
                Sistem Absensi QR
            </b>

            <button class="menu-toggle">
                ☰
            </button>

        </div>


        <!-- TOPBAR -->

        <header class="topbar">

            <div>

                <h1>
                    Kartu Absensi QR
                </h1>

                <p>
                    Kartu identitas digital dengan QR personal.
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


        <!-- =================================================
             CARD SECTION
        ================================================== -->

        <section class="card-page">

            <div class="card-intro">

                <div>

                    <span class="eyebrow">
                        DIGITAL STUDENT CARD
                    </span>

                    <h2>
                        Kartu Absensi Personal
                    </h2>

                    <p>
                        Gunakan QR personal ini saat melakukan
                        proses presensi.
                    </p>

                </div>

                <div class="card-number">

                    <span>
                        CARD ID
                    </span>

                    <strong>
                        <?=e($kartuNomor)?>
                    </strong>

                </div>

            </div>


            <!-- =================================================
                 DIGITAL CARD
            ================================================== -->

            <div
                class="student-id-card"
                id="studentCard"
            >

                <!-- DECORATIVE BACKGROUND -->

                <div class="card-glow card-glow-one"></div>
                <div class="card-glow card-glow-two"></div>

                <div class="card-pattern"></div>


                <!-- CARD HEADER -->

                <div class="student-card-header">

                    <div class="university-brand">

                        <div class="university-symbol">
                            U
                        </div>

                        <div>

                            <strong>
                                UNIVERSITAS JABAL GHAFUR
                            </strong>

                            <span>
                                SISTEM ABSENSI DIGITAL
                            </span>

                        </div>

                    </div>


                    <div class="active-status">

                        <span class="status-dot"></span>

                        <?=e(strtoupper($status))?>

                    </div>

                </div>


                <!-- CARD CONTENT -->

                <div class="student-card-content">


                    <!-- FOTO -->

                    <div class="student-photo-wrap">

                        <div class="student-photo">

                            <?php if ($fotoUrl): ?>

                                <img
                                    src="<?=e($fotoUrl)?>"
                                    alt="Foto <?=e($nama)?>"
                                >

                            <?php else: ?>

                                <div class="photo-placeholder">

                                    <span>
                                        <?=e(strtoupper(substr($nama, 0, 1)))?>
                                    </span>

                                </div>

                            <?php endif; ?>

                        </div>

                        <span class="photo-label">
                            MAHASISWA
                        </span>

                    </div>


                    <!-- BIODATA -->

                    <div class="student-biodata">

                        <div class="biodata-main">

                            <span class="data-label">
                                NAMA MAHASISWA
                            </span>

                            <h2>
                                <?=e($nama)?>
                            </h2>

                        </div>


                        <div class="biodata-grid">

                            <div class="data-item">

                                <span class="data-label">
                                    NPM
                                </span>

                                <strong>
                                    <?=e($npm)?>
                                </strong>

                            </div>


                            <div class="data-item">

                                <span class="data-label">
                                    KELAS
                                </span>

                                <strong>
                                    <?=e($kelas)?>
                                </strong>

                            </div>


                            <div class="data-item">

                                <span class="data-label">
                                    PROGRAM STUDI
                                </span>

                                <strong>
                                    <?=e($prodi)?>
                                </strong>

                            </div>


                            <div class="data-item">

                                <span class="data-label">
                                    ANGKATAN
                                </span>

                                <strong>
                                    <?=e($angkatan)?>
                                </strong>

                            </div>


                            <div class="data-item">

                                <span class="data-label">
                                    FAKULTAS
                                </span>

                                <strong>
                                    <?=e($fakultas)?>
                                </strong>

                            </div>


                            <div class="data-item">

                                <span class="data-label">
                                    JENIS KELAMIN
                                </span>

                                <strong>
                                    <?=e($jenisKelamin)?>
                                </strong>

                            </div>

                        </div>


                        <div class="card-footer-data">

                            <span>
                                <?=e($kartuNomor)?>
                            </span>

                            <span>
                                <?=e($qrCode)?>
                            </span>

                        </div>

                    </div>


                    <!-- QR -->

                    <div class="student-qr-section">

                        <div
                            class="student-qr"
                            id="qrBox"
                        ></div>

                        <span class="qr-caption">
                            QR PERSONAL
                        </span>

                        <small>
                            Scan untuk identifikasi
                        </small>

                    </div>

                </div>


                <!-- CARD BOTTOM -->

                <div class="student-card-bottom">

                    <span>
                        DIGITAL ATTENDANCE SYSTEM
                    </span>

                    <span>
                        <?=e($fakultas)?>
                    </span>

                </div>

            </div>


            <!-- =================================================
                 ACTIONS
            ================================================== -->

            <div class="card-actions">

                <button
                    type="button"
                    class="btn btn-primary"
                    id="downloadQr"
                >
                    Unduh QR
                </button>


                <button
                    type="button"
                    class="btn btn-ghost"
                    id="printCard"
                >
                    Cetak Kartu
                </button>

            </div>


            <!-- INFO -->

            <div class="card-info-grid">

                <div class="info-card">

                    <span class="info-icon">
                        QR
                    </span>

                    <div>

                        <strong>
                            QR Personal
                        </strong>

                        <p>
                            QR ini terhubung dengan identitas
                            mahasiswa yang terdaftar di sistem.
                        </p>

                    </div>

                </div>


                <div class="info-card">

                    <span class="info-icon">
                        ID
                    </span>

                    <div>

                        <strong>
                            Nomor Kartu
                        </strong>

                        <p>
                            <?=e($kartuNomor)?>
                        </p>

                    </div>

                </div>


                <div class="info-card">

                    <span class="info-icon">
                        SC
                    </span>

                    <div>

                        <strong>
                            Cara Penggunaan
                        </strong>

                        <p>
                            Tampilkan QR ketika diminta sistem
                            untuk melakukan presensi.
                        </p>

                    </div>

                </div>

            </div>

        </section>

    </main>

</div>


<!-- =========================================================
     QR CODE LIBRARY
========================================================= -->

<script
    src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"
></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const qrBox = document.getElementById('qrBox');

    const qrValue =
        <?=json_encode($qrCode)?>;


    /* =====================================================
       GENERATE QR
    ===================================================== */

    if (qrBox && typeof QRCode !== 'undefined') {

        new QRCode(qrBox, {

            text: qrValue,

            width: 150,
            height: 150,

            colorDark: '#17101d',
            colorLight: '#ffffff',

            correctLevel:
                QRCode.CorrectLevel.H

        });

    }


    /* =====================================================
       DOWNLOAD QR
    ===================================================== */

    const downloadButton =
        document.getElementById('downloadQr');

    if (downloadButton) {

        downloadButton.addEventListener(
            'click',
            function () {

                const canvas =
                    qrBox.querySelector('canvas');

                const image =
                    qrBox.querySelector('img');


                let url = '';


                if (canvas) {

                    url = canvas.toDataURL(
                        'image/png'
                    );

                } else if (image) {

                    url = image.src;
                }


                if (!url) {

                    alert(
                        'QR belum selesai dibuat.'
                    );

                    return;
                }


                const link =
                    document.createElement('a');

                link.href = url;

                link.download =
                    'QR-' +
                    <?=json_encode($npm)?> +
                    '.png';

                document.body.appendChild(link);

                link.click();

                link.remove();

            }
        );

    }


    /* =====================================================
       PRINT CARD
    ===================================================== */

    const printButton =
        document.getElementById('printCard');

    if (printButton) {

        printButton.addEventListener(
            'click',
            function () {

                window.print();

            }
        );

    }

});

</script>


<!-- =========================================================
     CARD STYLE
========================================================= -->

<style>

/* =========================================================
   PAGE
========================================================= */

.card-page {
    max-width: 1200px;
    margin: 0 auto;
}

.card-intro {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 30px;
    margin-bottom: 25px;
}

.card-intro h2 {
    margin: 8px 0 5px;
}

.card-intro p {
    margin: 0;
    color: #8d8493;
    font-size: 13px;
}

.card-number {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
}

.card-number span {
    color: #756b7d;
    font-size: 9px;
    letter-spacing: 1.5px;
}

.card-number strong {
    color: #d7c7df;
    font-size: 12px;
}


/* =========================================================
   STUDENT CARD
========================================================= */

.student-id-card {
    position: relative;

    width: 100%;
    min-height: 385px;

    overflow: hidden;

    padding: 27px 30px 20px;

    border: 1px solid rgba(255,255,255,.11);

    border-radius: 25px;

    background:
        radial-gradient(
            circle at 15% 20%,
            rgba(192,147,255,.13),
            transparent 32%
        ),
        radial-gradient(
            circle at 88% 80%,
            rgba(104,78,135,.12),
            transparent 32%
        ),
        linear-gradient(
            125deg,
            #19131d 0%,
            #211824 45%,
            #17121b 100%
        );

    box-shadow:
        0 25px 70px rgba(0,0,0,.35),
        inset 0 1px 0 rgba(255,255,255,.06);

    color: #fff;

    isolation: isolate;
}


/* =========================================================
   DECORATIVE ELEMENTS
========================================================= */

.card-glow {
    position: absolute;

    width: 250px;
    height: 250px;

    border-radius: 50%;

    filter: blur(70px);

    opacity: .13;

    z-index: -1;
}

.card-glow-one {
    top: -130px;
    left: 25%;
    background: #c59bff;
}

.card-glow-two {
    right: -100px;
    bottom: -120px;
    background: #8c65b1;
}

.card-pattern {
    position: absolute;
    inset: 0;

    opacity: .035;

    background-image:
        linear-gradient(
            135deg,
            transparent 0 48%,
            #fff 49%,
            transparent 50%
        );

    background-size: 35px 35px;

    z-index: -1;
}


/* =========================================================
   HEADER
========================================================= */

.student-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding-bottom: 18px;

    border-bottom: 1px solid rgba(255,255,255,.08);
}

.university-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.university-symbol {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid rgba(216,167,255,.35);
    border-radius: 11px;

    color: #dfbfff;

    font-size: 15px;
    font-weight: 700;

    background: rgba(216,167,255,.07);
}

.university-brand strong {
    display: block;

    color: #f0e9f4;

    font-size: 12px;
    letter-spacing: 1px;
}

.university-brand span {
    display: block;

    margin-top: 4px;

    color: #817687;

    font-size: 8px;
    letter-spacing: 1.7px;
}

.active-status {
    display: flex;
    align-items: center;
    gap: 7px;

    padding: 7px 11px;

    border: 1px solid rgba(115,190,137,.2);
    border-radius: 999px;

    color: #9bd0a9;

    background: rgba(93,163,111,.07);

    font-size: 8px;
    font-weight: 700;
    letter-spacing: 1px;
}

.status-dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #86c796;

    box-shadow: 0 0 10px rgba(134,199,150,.7);
}


/* =========================================================
   CONTENT
========================================================= */

.student-card-content {
    display: grid;

    grid-template-columns: 145px 1fr 180px;

    align-items: center;

    gap: 28px;

    min-height: 255px;
}


/* =========================================================
   PHOTO
========================================================= */

.student-photo-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;

    gap: 9px;
}

.student-photo {
    width: 125px;
    height: 155px;

    overflow: hidden;

    border: 3px solid rgba(255,255,255,.13);

    border-radius: 16px;

    background: #251d2a;

    box-shadow:
        0 12px 30px rgba(0,0,0,.28);
}

.student-photo img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    display: block;
}

.photo-placeholder {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    background:
        linear-gradient(
            145deg,
            #33273b,
            #201922
        );
}

.photo-placeholder span {
    width: 58px;
    height: 58px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    color: #d8a7ff;

    background: rgba(216,167,255,.08);

    font-size: 24px;
    font-weight: 700;
}

.photo-label {
    color: #716778;

    font-size: 7px;
    letter-spacing: 2px;
}


/* =========================================================
   BIODATA
========================================================= */

.student-biodata {
    min-width: 0;
}

.biodata-main {
    margin-bottom: 20px;
}

.data-label {
    display: block;

    margin-bottom: 5px;

    color: #746a7b;

    font-size: 7px;
    font-weight: 700;
    letter-spacing: 1.6px;
}

.biodata-main h2 {
    margin: 0;

    overflow: hidden;

    color: #f2ebf5;

    font-size: clamp(20px, 2.3vw, 30px);

    white-space: nowrap;
    text-overflow: ellipsis;
}

.biodata-grid {
    display: grid;

    grid-template-columns: repeat(2, minmax(0,1fr));

    gap: 14px 25px;
}

.data-item strong {
    display: block;

    overflow: hidden;

    color: #d9cfde;

    font-size: 11px;

    white-space: nowrap;
    text-overflow: ellipsis;
}

.card-footer-data {
    display: flex;
    gap: 20px;

    margin-top: 19px;
    padding-top: 13px;

    border-top: 1px solid rgba(255,255,255,.07);

    color: #625969;

    font-family: monospace;

    font-size: 8px;
}


/* =========================================================
   QR
========================================================= */

.student-qr-section {
    display: flex;
    flex-direction: column;
    align-items: center;

    padding-left: 22px;

    border-left: 1px solid rgba(255,255,255,.08);
}

.student-qr {
    width: 160px;
    height: 160px;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 5px;

    border-radius: 14px;

    background: #fff;

    box-shadow:
        0 12px 30px rgba(0,0,0,.28);
}

.student-qr img,
.student-qr canvas {
    display: block;

    max-width: 150px;
    max-height: 150px;
}

.qr-caption {
    margin-top: 11px;

    color: #d8cbe0;

    font-size: 8px;
    font-weight: 700;
    letter-spacing: 2px;
}

.student-qr-section small {
    margin-top: 4px;

    color: #716778;

    font-size: 8px;
}


/* =========================================================
   CARD BOTTOM
========================================================= */

.student-card-bottom {
    display: flex;
    justify-content: space-between;

    padding-top: 14px;

    border-top: 1px solid rgba(255,255,255,.08);

    color: #625969;

    font-size: 7px;
    letter-spacing: 1.5px;
}


/* =========================================================
   ACTIONS
========================================================= */

.card-actions {
    display: flex;
    justify-content: center;
    gap: 10px;

    margin-top: 22px;
}


/* =========================================================
   INFO
========================================================= */

.card-info-grid {
    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 12px;

    margin-top: 20px;
}

.info-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;

    padding: 17px;

    border: 1px solid rgba(255,255,255,.07);

    border-radius: 15px;

    background: rgba(255,255,255,.025);
}

.info-icon {
    width: 32px;
    height: 32px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    color: #d4a8f1;

    background: rgba(212,168,241,.08);

    font-size: 8px;
    font-weight: 700;
    letter-spacing: 1px;
}

.info-card strong {
    color: #dcd3e1;

    font-size: 11px;
}

.info-card p {
    margin: 5px 0 0;

    color: #766d7d;

    font-size: 9px;
    line-height: 1.6;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .student-card-content {
        grid-template-columns: 120px 1fr 150px;
        gap: 18px;
    }

    .student-photo {
        width: 105px;
        height: 135px;
    }

    .student-qr {
        width: 135px;
        height: 135px;
    }

    .student-qr img,
    .student-qr canvas {
        max-width: 125px;
        max-height: 125px;
    }

    .student-qr-section {
        padding-left: 15px;
    }

    .card-info-grid {
        grid-template-columns: 1fr;
    }
}


@media (max-width: 700px) {

    .card-intro {
        align-items: flex-start;
        flex-direction: column;
    }

    .card-number {
        align-items: flex-start;
    }

    .student-id-card {
        padding: 20px;

        min-height: auto;
    }

    .student-card-content {
        grid-template-columns: 95px 1fr;

        gap: 15px;

        padding: 20px 0;
    }

    .student-photo {
        width: 90px;
        height: 115px;
    }

    .student-qr-section {
        grid-column: 1 / -1;

        padding: 20px 0 0;

        border-left: 0;
        border-top: 1px solid rgba(255,255,255,.08);
    }

    .student-qr {
        width: 150px;
        height: 150px;
    }

    .student-qr img,
    .student-qr canvas {
        max-width: 140px;
        max-height: 140px;
    }

    .biodata-grid {
        gap: 11px;
    }

    .biodata-main h2 {
        font-size: 19px;
    }

}


/* =========================================================
   PRINT
========================================================= */

@media print {

    body {
        background: #fff !important;
    }

    body * {
        visibility: hidden;
    }

    .student-id-card,
    .student-id-card * {
        visibility: visible;
    }

    .student-id-card {

        position: absolute;

        left: 0;
        top: 0;

        width: 100%;

        margin: 0;

        border-radius: 0;

        box-shadow: none;

        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    @page {
        size: landscape;
        margin: 10mm;
    }

}

</style>


<?php
require __DIR__.'/../partials/footer.php';
?>