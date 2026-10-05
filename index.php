<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/helpers.php';

$base = '.';
$assetBase = 'assets';
$pageTitle = 'Presensi Digital';
$today = date('d F Y');

require __DIR__ . '/partials/head.php';
?>

<div class="public-shell">
    <header class="public-top">
        <a class="brand" href="index.php">
            <span class="brand-mark" aria-hidden="true">✦</span>
            <span>
                <b>Sistem Absensi QR</b>
                <small>Presensi Digital · Kampus Lebih Modern</small>
            </span>
        </a>

        <div class="top-date">
            <b><?= e($today) ?></b>
            Selamat datang di sistem absensi
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Digital Attendance System</span>
                <h1>Presensi lebih <em>sederhana.</em></h1>
                <p>
                    Satu pintu untuk scan QR, validasi lokasi, waktu perkuliahan,
                    dan identitas mahasiswa. Dibuat supaya absensi tidak berubah
                    menjadi ritual berburu tombol yang hilang.
                </p>

                <div class="feature-row">
                    <div class="feature">
                        <span class="ico" aria-hidden="true">⌁</span>
                        <span>
                            <b>QR Code</b>
                            <small>Cepat &amp; Akurat</small>
                        </span>
                    </div>
                    <div class="feature">
                        <span class="ico" aria-hidden="true">⌖</span>
                        <span>
                            <b>Validasi Lokasi</b>
                            <small>Sesuai Area Kampus</small>
                        </span>
                    </div>
                    <div class="feature">
                        <span class="ico" aria-hidden="true">◷</span>
                        <span>
                            <b>Validasi Waktu</b>
                            <small>Real-time</small>
                        </span>
                    </div>
                </div>

                <div class="actions">
                    <a class="btn btn-primary" href="login.php">
                        Masuk ke Sistem ↗
                    </a>
                    <a class="btn btn-ghost" href="#cara">Cara kerja</a>
                </div>
            </div>

            <section class="auth-card" aria-labelledby="scanner-title">
                <span class="eyebrow">Student Access</span>
                <h2 id="scanner-title">Scan QR</h2>
                <p>
                    Scan QR pada kartu absensi mahasiswa untuk mengenali identitas.
                </p>

                <div class="scanner-stage" aria-hidden="true">
                    <div class="scan-corners">
                        <div class="scan-line"></div>
                    </div>
                </div>

                <div class="scanner-actions">
                    <a class="btn btn-primary" href="mahasiswa/absensi.php">
                        Buka Scanner
                    </a>
                    <a class="btn btn-ghost" href="login.php">Masuk pribadi</a>
                </div>
                <div class="helper">
                    Kamera dan galeri didukung pada perangkat yang kompatibel.
                </div>
            </section>
        </section>

        <section id="cara" class="glass" style="margin-top: 20px">
            <div class="section-head">
                <h2>Alur sistem</h2>
                <span>01 — 04</span>
            </div>

            <div class="stats">
                <div class="stat">
                    <small>01</small>
                    <strong>Scan</strong>
                    <span>QR kartu mahasiswa atau QR sesi.</span>
                </div>
                <div class="stat">
                    <small>02</small>
                    <strong>Kenali</strong>
                    <span>Sistem menemukan identitas dan sesi aktif.</span>
                </div>
                <div class="stat">
                    <small>03</small>
                    <strong>Validasi</strong>
                    <span>Lokasi dan waktu diperiksa server.</span>
                </div>
                <div class="stat">
                    <small>04</small>
                    <strong>Simpan</strong>
                    <span>Status hadir langsung masuk laporan.</span>
                </div>
            </div>
        </section>
    </main>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
