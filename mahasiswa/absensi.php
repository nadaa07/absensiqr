<?php

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../config/helpers.php';

requireRole('mahasiswa');

$base = '..';
$assetBase = '../assets';
$pageTitle = 'Scan Absensi';

require __DIR__.'/../partials/head.php';

?>

<div class="dashboard-layout">

    <?php require __DIR__.'/../partials/sidebar.php'; ?>


    <main class="main">

        <!-- MOBILE HEADER -->
        <div class="mobile-head">

            <b>Sistem Absensi QR</b>

            <button
                class="menu-toggle"
                type="button">
                ☰
            </button>

        </div>


        <!-- TOPBAR -->
        <header class="topbar">

            <div>

                <h1>
                    Scan Absensi
                </h1>

                <p>
                    Scan kartu mahasiswa atau QR personal untuk melakukan presensi.
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


        <!-- =====================================================
             SCANNER PAGE
        ====================================================== -->

        <section class="attendance-scanner-page">


            <!-- =================================================
                 SCANNER HEADER
            ================================================== -->

            <div class="scanner-intro">

                <div>

                    <span class="scanner-eyebrow">
                        SMART ATTENDANCE SCANNER
                    </span>

                    <h2>
                        Scan Kartu Absensi
                    </h2>

                    <p>
                        Arahkan kamera ke kartu mahasiswa.
                        Sistem akan mencari QR personal secara otomatis.
                    </p>

                </div>


                <div class="scanner-live-status">

                    <span
                        class="live-dot"
                        id="liveDot">
                    </span>

                    <span id="scanStatus">
                        READY
                    </span>

                </div>

            </div>



            <!-- =================================================
                 MODE SELECTOR
            ================================================== -->

            <div class="scanner-mode">

                <button
                    type="button"
                    class="scanner-mode-btn active"
                    id="cardModeBtn">

                    <span class="mode-icon">
                        ▣
                    </span>

                    <span>
                        <strong>Scan Kartu</strong>
                        <small>Kartu mahasiswa penuh</small>
                    </span>

                </button>


                <button
                    type="button"
                    class="scanner-mode-btn"
                    id="qrModeBtn">

                    <span class="mode-icon">
                        ▦
                    </span>

                    <span>
                        <strong>Scan QR</strong>
                        <small>QR saja</small>
                    </span>

                </button>

            </div>



            <!-- =================================================
                 MAIN SCANNER
            ================================================== -->

            <div class="scanner-layout">


                <!-- =================================================
                     CAMERA PANEL
                ================================================== -->

                <section class="scanner-panel">


                    <div class="scanner-panel-head">

                        <div>

                            <span class="panel-label">
                                CAMERA SCANNER
                            </span>

                            <h3 id="scannerTitle">
                                Arahkan Kartu ke Scanner
                            </h3>

                        </div>


                        <span
                            class="scanner-state"
                            id="scannerState">

                            STANDBY

                        </span>

                    </div>



                    <!-- CAMERA AREA -->

                    <div
                        class="scanner-stage"
                        id="scannerStage">


                        <!-- html5-qrcode -->
                        <div
                            id="qr-reader"
                            class="qr-reader-host">
                        </div>


                        <!-- DARK OVERLAY -->

                        <div
                            class="scanner-overlay"
                            id="scannerOverlay">


                            <!-- CARD FRAME -->

                            <div
                                class="scan-frame scan-frame-card"
                                id="cardFrame">


                                <div class="frame-corner corner-tl"></div>
                                <div class="frame-corner corner-tr"></div>
                                <div class="frame-corner corner-bl"></div>
                                <div class="frame-corner corner-br"></div>


                                <!-- QR POSITION INDICATOR -->

                                <div class="qr-position">

                                    <div class="qr-position-box">

                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>

                                    </div>

                                    <small>
                                        QR
                                    </small>

                                </div>


                                <!-- LASER -->

                                <div
                                    class="scan-laser"
                                    id="scanLaser">
                                </div>


                                <div class="scan-grid"></div>

                            </div>



                            <!-- QR FRAME -->

                            <div
                                class="scan-frame scan-frame-qr hidden"
                                id="qrFrame">

                                <div class="frame-corner corner-tl"></div>
                                <div class="frame-corner corner-tr"></div>
                                <div class="frame-corner corner-bl"></div>
                                <div class="frame-corner corner-br"></div>


                                <div
                                    class="scan-laser">
                                </div>

                            </div>


                        </div>



                        <!-- SCANNER CENTER MESSAGE -->

                        <div
                            class="scanner-center-message"
                            id="scannerCenterMessage">

                            <div class="scanner-radar">

                                <span></span>

                            </div>

                            <strong>
                                Menunggu kamera
                            </strong>

                            <small>
                                Tekan "Buka Kamera"
                            </small>

                        </div>



                        <!-- SUCCESS -->

                        <div
                            class="scanner-success"
                            id="scannerSuccess">

                            <div class="success-circle">
                                ✓
                            </div>

                            <strong>
                                QR TERBACA
                            </strong>

                            <small>
                                Identitas sedang diverifikasi
                            </small>

                        </div>

                    </div>



                    <!-- CAMERA BUTTONS -->

                    <div class="scanner-controls">


                        <button
                            type="button"
                            class="scanner-control primary"
                            id="cameraBtn">

                            <span class="control-icon">
                                ◉
                            </span>

                            <span>
                                Buka Kamera
                            </span>

                        </button>


                        <label
                            class="scanner-control secondary"
                            for="fileQr">

                            <span class="control-icon">
                                ▧
                            </span>

                            <span>
                                Pilih dari Galeri
                            </span>

                            <input
                                type="file"
                                id="fileQr"
                                accept="image/*"
                                hidden>

                        </label>

                    </div>



                    <!-- INFO -->

                    <div
                        class="scanner-help"
                        id="scanMessage">

                        <span class="help-icon">
                            i
                        </span>

                        <span>
                            Mode kartu dapat membaca QR yang berada
                            di dalam foto kartu penuh. Tidak perlu memotong QR.
                        </span>

                    </div>

                </section>



                <!-- =================================================
                     RESULT PANEL
                ================================================== -->

                <section class="result-panel">


                    <div class="result-panel-head">

                        <div>

                            <span class="panel-label">
                                ATTENDANCE VERIFICATION
                            </span>

                            <h3>
                                Konfirmasi Kehadiran
                            </h3>

                        </div>


                        <div class="verification-badge">
                            SECURE
                        </div>

                    </div>



                    <!-- RESULT -->

                    <div
                        class="identity-card"
                        id="result">


                        <div class="identity-status">

                            <span class="identity-status-dot"></span>

                            <span id="identityStatus">
                                MENUNGGU SCAN
                            </span>

                        </div>


                        <div
                            class="identity-avatar"
                            id="identityAvatar">

                            ?

                        </div>


                        <div class="identity-info">

                            <span>
                                IDENTITAS MAHASISWA
                            </span>

                            <strong id="studentName">
                                Belum ada data
                            </strong>

                            <small id="studentMeta">
                                Scan QR pada kartu terlebih dahulu.
                            </small>

                        </div>

                    </div>



                    <!-- LOCATION -->

                    <div class="modern-input">

                        <label for="location">
                            Lokasi Absensi
                        </label>

                        <div class="select-wrap">

                            <select
                                id="location"
                                class="select">

                                <option value="">
                                    Memuat lokasi...
                                </option>

                            </select>

                        </div>

                    </div>



                    <!-- SESSION -->

                    <div class="modern-input">

                        <label for="session">
                            Sesi Aktif
                        </label>

                        <div class="select-wrap">

                            <select
                                id="session"
                                class="select">

                                <option value="">
                                    Memuat sesi...
                                </option>

                            </select>

                        </div>

                    </div>



                    <!-- CONFIRM -->

                    <button
                        type="button"
                        class="attendance-submit"
                        id="submitAttendance"
                        disabled>

                        <span>
                            Konfirmasi Kehadiran
                        </span>

                        <strong>
                            →
                        </strong>

                    </button>



                    <!-- LOCATION STATUS -->

                    <div
                        id="locationStatus"
                        class="verification-message">

                        <span class="message-icon">
                            •
                        </span>

                        <span>
                            Lokasi perangkat belum diperiksa.
                        </span>

                    </div>




                </section>

            </div>

        </section>

    </main>

</div>



<!-- =========================================================
     HTML5 QR CODE
========================================================= -->

<script
    src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js">
</script>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /* =====================================================
           STATE
        ====================================================== */

        let scanned = null;

        let scanner = null;

        let cameraRunning = false;

        let scanLocked = false;

        let currentMode = 'card';

        let selectedLocation = null;
        let currentLatitude = null;
        let currentLongitude = null;



        /* =====================================================
           HELPER
        ====================================================== */

        const $ = selector =>
            document.querySelector(selector);



        /* =====================================================
           ELEMENTS
        ====================================================== */

        const qrReader =
            $('#qr-reader');

        const cameraBtn =
            $('#cameraBtn');

        const fileQr =
            $('#fileQr');

        const scanStatus =
            $('#scanStatus');

        const scannerState =
            $('#scannerState');

        const scanMessage =
            $('#scanMessage');

        const scannerTitle =
            $('#scannerTitle');

        const scannerCenterMessage =
            $('#scannerCenterMessage');

        const scannerSuccess =
            $('#scannerSuccess');

        const submitButton =
            $('#submitAttendance');

        const result =
            $('#result');



        /* =====================================================
           MODE
        ====================================================== */

        $('#cardModeBtn').addEventListener(
            'click',
            function () {

                currentMode = 'card';

                $('#cardModeBtn')
                    .classList
                    .add('active');

                $('#qrModeBtn')
                    .classList
                    .remove('active');


                $('#cardFrame')
                    .classList
                    .remove('hidden');

                $('#qrFrame')
                    .classList
                    .add('hidden');


                scannerTitle.textContent =
                    'Arahkan Kartu ke Scanner';

                scanMessage.querySelector('span:last-child')
                    .textContent =
                    'Mode kartu dapat membaca QR yang berada di dalam foto kartu penuh. Tidak perlu memotong QR.';

            }
        );



        $('#qrModeBtn').addEventListener(
            'click',
            function () {

                currentMode = 'qr';

                $('#qrModeBtn')
                    .classList
                    .add('active');

                $('#cardModeBtn')
                    .classList
                    .remove('active');


                $('#qrFrame')
                    .classList
                    .remove('hidden');

                $('#cardFrame')
                    .classList
                    .add('hidden');


                scannerTitle.textContent =
                    'Arahkan QR ke Scanner';

                scanMessage.querySelector('span:last-child')
                    .textContent =
                    'Mode QR digunakan jika kamera diarahkan langsung ke QR personal.';

            }
        );



        /* =====================================================
           LOAD DATA
        ====================================================== */

        async function load() {

            try {


                /* =========================
                   LOKASI
                ========================= */

                const locationResponse =
                    await fetch(
                        '../api/data.php?action=locations'
                    );


                const locationJson =
                    await locationResponse.json();


                if (
                    locationJson.ok &&
                    Array.isArray(locationJson.data)
                ) {

                    window.absensiLocations = locationJson.data;

                    $('#location').innerHTML =
                        '<option value="">Pilih lokasi</option>' +

                        locationJson.data
                            .map(location => `

                                <option value="${location.id}">

                                    ${escapeHtml(location.nama_lokasi)}

                                    · radius
                                    ${escapeHtml(location.radius)}
                                    m

                                </option>

                            `)
                            .join('');

                } else {

                    $('#location').innerHTML =
                        '<option value="">Lokasi tidak tersedia</option>';

                }



                /* =========================
                   SESI
                ========================= */

                const sessionResponse =
                    await fetch(
                        '../api/data.php?action=active_sessions'
                    );


                const sessionJson =
                    await sessionResponse.json();


                if (
                    sessionJson.ok &&
                    Array.isArray(sessionJson.data)
                ) {


                    const hariUrutan = {

                        'Senin': 1,
                        'Selasa': 2,
                        'Rabu': 3,
                        'Kamis': 4,
                        'Jumat': 5,
                        'Sabtu': 6,
                        'Minggu': 7

                    };


                    const sessions =
                        [...sessionJson.data]
                            .sort(
                                (a, b) => {

                                    const hariA =
                                        hariUrutan[a.hari] ?? 99;

                                    const hariB =
                                        hariUrutan[b.hari] ?? 99;


                                    if (
                                        hariA !== hariB
                                    ) {

                                        return hariA - hariB;

                                    }


                                    return String(
                                        a.jam_mulai
                                    ).localeCompare(
                                        String(
                                            b.jam_mulai
                                        )
                                    );

                                }
                            );


                    $('#session').innerHTML =
                        '<option value="">Pilih sesi</option>' +

                        sessions
                            .map(session => {

                                const mulai =
                                    String(
                                        session.jam_mulai
                                    ).slice(0, 5);


                                const selesai =
                                    String(
                                        session.jam_selesai
                                    ).slice(0, 5);


                                return `

                                    <option value="${session.id}">

                                        ${escapeHtml(session.nama_mk)}
                                        ·
                                        ${escapeHtml(session.nama_kelas)}
                                        ·
                                        ${escapeHtml(session.hari)}
                                        ·
                                        ${mulai}–${selesai}

                                    </option>

                                `;

                            })
                            .join('');

                } else {

                    $('#session').innerHTML =
                        '<option value="">Tidak ada sesi aktif</option>';

                }


            } catch (error) {

                console.error(error);


                $('#location').innerHTML =
                    '<option value="">Gagal memuat lokasi</option>';


                $('#session').innerHTML =
                    '<option value="">Gagal memuat sesi</option>';


                toast(
                    'Data lokasi atau jadwal belum dapat dimuat.',
                    'error'
                );

            }

        }



        /* =====================================================
           HTML ESCAPE
        ====================================================== */

        function escapeHtml(value) {

            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

        }



        /* =====================================================
           GPS PERANGKAT
        ====================================================== */

        window.absensiLocations = [];

        function setLocationStatus(type, message) {

            const box = $('#locationStatus');

            box.className = 'verification-message ' + (type || '');

            box.innerHTML = `
                <span class="message-icon">${type === 'success' ? '✓' : type === 'error' ? '!' : '•'}</span>
                <span>${escapeHtml(message)}</span>
            `;

        }

        function calculateDistance(lat1, lon1, lat2, lon2) {

            const R = 6371000;
            const toRad = value => value * Math.PI / 180;
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        }

        function updateSubmitState() {
            const sessionOk = !!$('#session').value;
            const locationOk = !!$('#location').value;
            const gpsOk = currentLatitude !== null && currentLongitude !== null;
            submitButton.disabled = !(scanned && sessionOk && locationOk && gpsOk);
        }

        function checkSelectedLocationWithGps() {

            const locationId = $('#location').value;

            if (!locationId) {
                selectedLocation = null;
                currentLatitude = null;
                currentLongitude = null;
                setLocationStatus('', 'Lokasi perangkat belum diperiksa.');
                updateSubmitState();
                return;
            }

            selectedLocation = (window.absensiLocations || []).find(
                item => String(item.id) === String(locationId)
            ) || null;

            if (!selectedLocation) {
                setLocationStatus('error', 'Data lokasi tidak ditemukan.');
                updateSubmitState();
                return;
            }

            currentLatitude = null;
            currentLongitude = null;
            updateSubmitState();
            setLocationStatus('loading', 'Mencari GPS presisi tinggi...');

            if (!navigator.geolocation) {
                setLocationStatus('error', 'GPS tidak tersedia pada browser ini.');
                return;
            }

            let watchId = null;
            let finished = false;
            let best = null;
            const startedAt = Date.now();
            // Radius REAL absensi: 1700 meter.
            // Harus sama dengan validasi server agar pengecekan browser tidak menolak lebih dulu.
            const radius = 2000;

            const finish = (position, timedOut = false) => {
                if (finished) return;
                finished = true;

                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                }

                if (!position) {
                    currentLatitude = null;
                    currentLongitude = null;
                    setLocationStatus('error', 'Lokasi perangkat tidak dapat diperoleh. Aktifkan izin lokasi dan pastikan layanan lokasi perangkat aktif.');
                    updateSubmitState();
                    return;
                }

                currentLatitude = position.coords.latitude;
                currentLongitude = position.coords.longitude;

                const accuracy = Number(position.coords.accuracy || 0);
                const distance = calculateDistance(
                    currentLatitude,
                    currentLongitude,
                    Number(selectedLocation.latitude),
                    Number(selectedLocation.longitude)
                );

                if (distance <= radius) {
                    setLocationStatus(
                        'success',
                        `Lokasi ditemukan · jarak ${Math.round(distance)} m · akurasi ±${Math.round(accuracy)} m · maksimal ${Math.round(radius)} m`
                    );
                } else {
                    setLocationStatus(
                        'error',
                        `Di luar radius lokasi · jarak ${Math.round(distance)} m · akurasi ±${Math.round(accuracy)} m · maksimal ${Math.round(radius)} m`
                    );
                }

                updateSubmitState();
            };

            const onPosition = position => {
                const accuracy = Number(position.coords.accuracy || 999999);
                const distance = calculateDistance(
                    position.coords.latitude,
                    position.coords.longitude,
                    Number(selectedLocation.latitude),
                    Number(selectedLocation.longitude)
                );

                /* Simpan pembacaan terbaik: akurasi paling kecil. */
                if (!best || accuracy < best.coords.accuracy) {
                    best = position;
                    currentLatitude = position.coords.latitude;
                    currentLongitude = position.coords.longitude;

                    setLocationStatus(
                        'loading',
                        `GPS diperbarui · jarak ${Math.round(distance)} m · akurasi ±${Math.round(accuracy)} m...`
                    );
                }

                /* Jika sudah cukup akurat dan berada di radius, langsung selesai. */
                if (distance <= radius && accuracy <= Math.max(50, radius)) {
                    finish(position);
                    return;
                }

                /* Beri waktu beberapa detik agar GPS laptop/HP mendapat fix yang lebih baik. */
                if (Date.now() - startedAt >= 8000) {
                    finish(best || position, true);
                }
            };

            const onError = error => {
                /* Jangan langsung gagal pada error sementara. Beri kesempatan getPosition berikutnya. */
                if (Date.now() - startedAt >= 8000) {
                    finish(best, true);
                }
            };

            try {
                watchId = navigator.geolocation.watchPosition(
                    onPosition,
                    onError,
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );

                setTimeout(() => {
                    if (!finished) {
                        finish(best, true);
                    }
                }, 8500);
            } catch (error) {
                console.error(error);
                finish(best, true);
            }
        }

        $('#location').addEventListener('change', checkSelectedLocationWithGps);
        $('#session').addEventListener('change', updateSubmitState);


        /* =====================================================
           SCAN SUCCESS
        ====================================================== */

        async function onScan(decodedText) {

            if (scanLocked) {
                return;
            }


            scanLocked = true;

            scanned =
                String(decodedText)
                    .trim();


            scanStatus.textContent =
                'DETECTED';

            scannerState.textContent =
                'QR TERBACA';


            scannerSuccess
                .classList
                .add('show');


            scannerCenterMessage
                .classList
                .remove('show');


            $('#liveDot')
                .classList
                .add('detected');


            scanMessage.querySelector('span:last-child')
                .textContent =
                'QR berhasil ditemukan. Sistem sedang memeriksa identitas...';



            /* =================================================
               QR MAHASISWA
            ================================================== */

            if (
                scanned.startsWith('MHS-')
            ) {

                try {

                    const response =
                        await fetch(
                            '../api/data.php?action=identify&qr=' +
                            encodeURIComponent(scanned)
                        );


                    const json =
                        await response.json();


                    if (
                        json.ok &&
                        json.data
                    ) {

                        $('#studentName')
                            .textContent =
                            json.data.nama;


                        $('#studentMeta')
                            .textContent =
                            json.data.npm +
                            ' · ' +
                            json.data.kelas;


                        $('#identityStatus')
                            .textContent =
                            'IDENTITAS TERVERIFIKASI';


                        $('#identityAvatar')
                            .textContent =
                            String(
                                json.data.nama
                            )
                            .charAt(0)
                            .toUpperCase();


                        $('#identityAvatar')
                            .classList
                            .add('verified');


                        result
                            .classList
                            .add('show');


                        submitButton.disabled =
                            false;


                        scannerState.textContent =
                            'VERIFIED';


                        scanStatus.textContent =
                            'VERIFIED';


                        scanMessage.querySelector(
                            'span:last-child'
                        ).textContent =
                            'Identitas mahasiswa berhasil ditemukan. Silakan pilih lokasi dan sesi.';



                        await stopCamera();

                    } else {

                        invalidScan(
                            'QR mahasiswa tidak terdaftar.'
                        );

                    }

                } catch (error) {

                    console.error(error);

                    invalidScan(
                        'Gagal memeriksa QR mahasiswa.'
                    );

                }


                return;
            }



            /* =================================================
               QR SESI DOSEN
            ================================================== */

            if (
                scanned.startsWith('SESSION|')
            ) {

                const token =
                    scanned.split('|')[1];


                try {

                    const response =
                        await fetch(
                            '../api/data.php?action=session_by_token&token=' +
                            encodeURIComponent(token)
                        );


                    const json =
                        await response.json();


                    if (
                        json.ok &&
                        json.data
                    ) {

                        $('#session').value =
                            json.data.id;


                        $('#identityStatus')
                            .textContent =
                            'SESI TERVERIFIKASI';


                        $('#studentName')
                            .textContent =
                            'Sesi Absensi';


                        $('#studentMeta')
                            .textContent =
                            json.data.nama_mk;


                        $('#identityAvatar')
                            .textContent =
                            'QR';


                        result
                            .classList
                            .add('show');


                        submitButton.disabled =
                            false;


                        scannerState.textContent =
                            'SESSION OK';


                        scanStatus.textContent =
                            'VERIFIED';


                        scanMessage.querySelector(
                            'span:last-child'
                        ).textContent =
                            'QR sesi dosen berhasil ditemukan.';


                        await stopCamera();

                    } else {

                        invalidScan(
                            'QR sesi tidak valid.'
                        );

                    }

                } catch (error) {

                    console.error(error);

                    invalidScan(
                        'Gagal memeriksa QR sesi.'
                    );

                }


                return;
            }



            invalidScan(
                'QR tidak dikenali oleh sistem.'
            );

        }



        /* =====================================================
           INVALID SCAN
        ====================================================== */

        function invalidScan(message) {

            scanStatus.textContent =
                'INVALID';


            scannerState.textContent =
                'SCAN GAGAL';


            scanMessage.querySelector(
                'span:last-child'
            ).textContent =
                message;


            toast(
                message,
                'error'
            );


            setTimeout(
                function () {

                    scanLocked = false;

                    scannerSuccess
                        .classList
                        .remove('show');


                    scannerCenterMessage
                        .classList
                        .add('show');


                    scanStatus.textContent =
                        'SCANNING';


                    scannerState.textContent =
                        'MENCARI QR';


                },
                1500
            );

        }



        /* =====================================================
           CAMERA
        ====================================================== */

        cameraBtn.addEventListener(
            'click',
            async function () {

                if (cameraRunning) {

                    await stopCamera();

                    return;

                }


                try {

                    if (
                        typeof Html5Qrcode ===
                        'undefined'
                    ) {

                        throw new Error(
                            'Library scanner belum tersedia.'
                        );

                    }


                    scanLocked = false;


                    scannerSuccess
                        .classList
                        .remove('show');


                    scannerCenterMessage
                        .classList
                        .remove('show');


                    scanStatus.textContent =
                        'SCANNING';


                    scannerState.textContent =
                        'MENCARI QR';


                    $('#liveDot')
                        .classList
                        .add('scanning');


                    cameraBtn.innerHTML = `
                        <span class="control-icon">
                            ■
                        </span>
                        <span>
                            Tutup Kamera
                        </span>
                    `;


                    scanner = new Html5Qrcode('qr-reader');

                    /*
                     * Kamera dibuat lebih kompatibel untuk laptop/HP.
                     * Jangan memaksa aspectRatio atau facingMode=environment
                     * karena webcam laptop sering tidak memiliki kamera
                     * yang memenuhi constraint tersebut.
                     */
                    let cameraConfig = null;

                    try {
                        const cameras = await Html5Qrcode.getCameras();
                        if (Array.isArray(cameras) && cameras.length) {
                            const preferred = cameras.find(camera =>
                                /back|rear|environment|belakang/i.test(String(camera.label || ''))
                            );
                            cameraConfig = preferred ? preferred.id : cameras[0].id;
                        }
                    } catch (cameraListError) {
                        console.warn('Daftar kamera tidak dapat dibaca:', cameraListError);
                    }

                    if (!cameraConfig) {
                        cameraConfig = { facingMode: { ideal: 'environment' } };
                    }

                    await scanner.start(
                        cameraConfig,
                        {
                            fps: 10,
                            qrbox: function(viewfinderWidth, viewfinderHeight) {
                                const size = Math.floor(Math.min(viewfinderWidth, viewfinderHeight) * 0.82);
                                return { width: size, height: size };
                            },
                            aspectRatio: 1,
                            disableFlip: false,
                            videoConstraints: {
                                width: { ideal: 1280 },
                                height: { ideal: 720 },
                                facingMode: { ideal: 'environment' }
                            }
                        },
                        function(decodedText) {
                            onScan(decodedText);
                        },
                        function() {}
                    );

                    cameraRunning = true;


                } catch (error) {

                    console.error(error);


                    cameraRunning = false;


                    cameraBtn.innerHTML = `
                        <span class="control-icon">
                            ◉
                        </span>
                        <span>
                            Buka Kamera
                        </span>
                    `;


                    scanStatus.textContent =
                        'READY';


                    scannerState.textContent =
                        'STANDBY';


                    scannerCenterMessage
                        .classList
                        .add('show');


                    $('#liveDot')
                        .classList
                        .remove('scanning');


                    toast(
                        'Kamera tidak dapat dibuka. Pastikan izin kamera diberikan.',
                        'error'
                    );

                }

            }
        );



        /* =====================================================
           STOP CAMERA
        ====================================================== */

        async function stopCamera() {

            try {

                if (
                    scanner &&
                    cameraRunning
                ) {

                    await scanner.stop();

                    scanner.clear();

                }

            } catch (error) {

                console.warn(error);

            }


            scanner = null;

            cameraRunning = false;


            cameraBtn.innerHTML = `
                <span class="control-icon">
                    ◉
                </span>
                <span>
                    Buka Kamera
                </span>
            `;


            $('#liveDot')
                .classList
                .remove('scanning');


            scannerCenterMessage
                .classList
                .add('show');

        }



        /* =====================================================
           GALLERY / FILE
        ====================================================== */

        fileQr.addEventListener(
            'change',
            async function (event) {

                const file =
                    event.target.files[0];


                if (!file) {
                    return;
                }


                scanLocked = false;


                scannerSuccess
                    .classList
                    .remove('show');


                scannerCenterMessage
                    .classList
                    .remove('show');


                scanStatus.textContent =
                    'ANALYZING';


                scannerState.textContent =
                    'MEMBACA GAMBAR';


                scanMessage.querySelector(
                    'span:last-child'
                ).textContent =
                    'Sedang mencari QR di seluruh gambar...';


                try {

                    if (
                        typeof Html5Qrcode ===
                        'undefined'
                    ) {

                        throw new Error(
                            'Library scanner belum tersedia.'
                        );

                    }


                    /*
                     * FILE SCAN
                     *
                     * Tidak peduli gambar berupa:
                     *
                     * 1. QR saja
                     * 2. Foto kartu penuh
                     * 3. Screenshot kartu
                     *
                     * html5-qrcode akan mencari QR
                     * pada gambar tersebut.
                     */

                    const fileScanner =
                        new Html5Qrcode(
                            'qr-reader'
                        );


                    const decodedText =
                        await fileScanner.scanFile(
                            file,
                            true
                        );


                    await fileScanner.clear();


                    onScan(decodedText);


                } catch (error) {

                    console.error(error);


                    scanStatus.textContent =
                        'NOT FOUND';


                    scannerState.textContent =
                        'QR TIDAK DITEMUKAN';


                    scanMessage.querySelector(
                        'span:last-child'
                    ).textContent =
                        'QR tidak ditemukan. Pastikan foto kartu terlihat jelas dan QR tidak buram.';


                    toast(
                        'QR tidak ditemukan pada gambar.',
                        'error'
                    );


                    setTimeout(
                        function () {

                            scanStatus.textContent =
                                'READY';

                            scannerState.textContent =
                                'STANDBY';

                            scannerCenterMessage
                                .classList
                                .add('show');

                        },
                        1800
                    );

                }


                /*
                 * Reset input supaya gambar yang sama
                 * bisa dipilih lagi.
                 */

                event.target.value = '';

            }
        );



        /* =====================================================
           KONFIRMASI ABSENSI
        ====================================================== */

        submitButton.addEventListener(
            'click',
            function () {

                if (!scanned) {
                    return;
                }


                const sessionId =
                    $('#session').value;


                const locationId =
                    $('#location').value;


                if (!sessionId) {

                    toast(
                        'Pilih sesi absensi terlebih dahulu.',
                        'error'
                    );

                    return;

                }


                if (!locationId) {

                    toast(
                        'Pilih lokasi absensi terlebih dahulu.',
                        'error'
                    );

                    return;

                }


                const statusBox =
                    $('#locationStatus');

                if (currentLatitude === null || currentLongitude === null) {
                    checkSelectedLocationWithGps();
                    toast('Lokasi perangkat belum valid.', 'error');
                    return;
                }

                statusBox.className = 'verification-message loading';
                statusBox.innerHTML = `
                    <span class="message-spinner"></span>
                    <span>Mengirim absensi ke server...</span>
                `;

                fetch(
                    '../api/attendance.php',
                    {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            qr: scanned,
                            session_id: sessionId,
                            location_id: locationId,
                            latitude: currentLatitude,
                            longitude: currentLongitude
                        })
                    }
                )
                .then(response => response.json())
                .then(function(json) {
                    if (json.ok) {
                        statusBox.className = 'verification-message success';
                        statusBox.innerHTML = `
                            <span class="message-icon">✓</span>
                            <span>${escapeHtml(json.message)} · ${Math.round(json.data.jarak)} m</span>
                        `;
                        toast('Absensi berhasil disimpan', 'success');
                        submitButton.disabled = true;
                        submitButton.innerHTML = `
                            <span>Absensi Berhasil</span>
                            <strong>✓</strong>
                        `;
                    } else {
                        statusBox.className = 'verification-message error';
                        statusBox.innerHTML = `
                            <span class="message-icon">!</span>
                            <span>${escapeHtml(json.message)}</span>
                        `;
                        toast(json.message, 'error');
                    }
                })
                .catch(function(error) {
                    console.error(error);
                    statusBox.className = 'verification-message error';
                    statusBox.innerHTML = `
                        <span class="message-icon">!</span>
                        <span>Gagal terhubung ke server.</span>
                    `;
                    toast('Gagal terhubung ke server.', 'error');
                });

            }
        );



        /* =====================================================
           LOAD
        ====================================================== */

        load();


    }

);

</script>



<!-- =========================================================
     MODERN SCANNER STYLE
========================================================= -->

<style>

/* =========================================================
   PAGE
========================================================= */

.attendance-scanner-page {

    max-width: 1180px;

    margin: 0 auto;

}


/* =========================================================
   INTRO
========================================================= */

.scanner-intro {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 25px;

    margin-bottom: 24px;

}

.scanner-eyebrow {

    display: block;

    margin-bottom: 8px;

    color: #9b78b7;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 2px;

}

.scanner-intro h2 {

    margin: 0 0 7px;

    color: #28202c;

    font-size: 27px;

}

.scanner-intro p {

    margin: 0;

    color: #8a8190;

    font-size: 13px;

}

.scanner-live-status {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 9px 13px;

    border: 1px solid #eee8f1;

    border-radius: 999px;

    color: #77707d;

    background: #ffffff;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: 1.5px;

    box-shadow: 0 5px 20px rgba(38,25,45,.04);

}

.live-dot {

    width: 7px;

    height: 7px;

    border-radius: 50%;

    background: #bdb6c2;

}

.live-dot.scanning {

    background: #9e78bd;

    box-shadow: 0 0 0 4px rgba(158,120,189,.10);

    animation: livePulse 1.2s infinite;

}

.live-dot.detected {

    background: #6fb184;

    box-shadow: 0 0 0 4px rgba(111,177,132,.10);

}

@keyframes livePulse {

    0%,100% {
        opacity: 1;
    }

    50% {
        opacity: .35;
    }

}


/* =========================================================
   MODE
========================================================= */

.scanner-mode {

    display: flex;

    gap: 10px;

    margin-bottom: 18px;

}

.scanner-mode-btn {

    display: flex;

    align-items: center;

    gap: 11px;

    min-width: 190px;

    padding: 12px 15px;

    border: 1px solid #eee8f1;

    border-radius: 14px;

    color: #77707d;

    background: #fff;

    cursor: pointer;

    text-align: left;

    transition:
        .2s ease;

}

.scanner-mode-btn:hover {

    border-color: #d9cce0;

    transform: translateY(-1px);

}

.scanner-mode-btn.active {

    border-color: #c9b0d8;

    color: #5f4270;

    background:
        linear-gradient(
            135deg,
            #fff,
            #faf6fc
        );

    box-shadow:
        0 8px 25px rgba(82,52,98,.07);

}

.mode-icon {

    width: 35px;

    height: 35px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 10px;

    color: #966db0;

    background: #f5edf8;

    font-size: 16px;

}

.scanner-mode-btn strong {

    display: block;

    margin-bottom: 3px;

    color: #423847;

    font-size: 11px;

}

.scanner-mode-btn small {

    color: #99909e;

    font-size: 8px;

}


/* =========================================================
   GRID
========================================================= */

.scanner-layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1.45fr)
        minmax(340px, .8fr);

    gap: 18px;

}


/* =========================================================
   PANEL
========================================================= */

.scanner-panel,
.result-panel {

    min-width: 0;

    padding: 20px;

    border: 1px solid #f1e9f4;

    border-radius: 20px;

    background: #2f0b3b;

    box-shadow:
        0 12px 40px rgba(42,27,49,.045);

}

.scanner-panel-head,
.result-panel-head {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 15px;

    margin-bottom: 16px;

}

.panel-label {

    display: block;

    margin-bottom: 5px;

    color: #a096a5;

    font-size: 7px;

    font-weight: 800;

    letter-spacing: 1.7px;

}

.scanner-panel h3,
.result-panel h3 {

    margin: 0;

    color: #342b38;

    font-size: 16px;

}

.scanner-state,
.verification-badge {

    padding: 6px 9px;

    border: 1px solid #eee7f0;

    border-radius: 999px;

    color: #9a919e;

    background: #fbfafc;

    font-size: 7px;

    font-weight: 800;

    letter-spacing: 1px;

}

.verification-badge {

    color: #8d6ca0;

    border-color: #e9dff0;

    background: #faf6fc;

}


/* =========================================================
   SCANNER STAGE
========================================================= */

.scanner-stage {

    position: relative;

    width: 100%;

    aspect-ratio: 1.45 / 1;

    overflow: hidden;

    border-radius: 17px;

    background:
        radial-gradient(
            circle at 50% 45%,
            #302837 0%,
            #17131b 55%,
            #0e0b11 100%
        );

    box-shadow:
        inset 0 0 0 1px rgba(255,255,255,.08);

}


/* =========================================================
   HTML5 QR
========================================================= */

.qr-reader-host {

    position: absolute;

    inset: 0;

    z-index: 1;

}

#qr-reader {

    width: 100%;

    height: 100%;

    border: 0 !important;

}

#qr-reader video {

    width: 100% !important;

    height: 100% !important;

    object-fit: cover !important;

}

#qr-reader__scan_region {

    width: 100% !important;

    min-height: 100% !important;

}

#qr-reader__dashboard {

    display: none !important;

}


/* =========================================================
   OVERLAY
========================================================= */

.scanner-overlay {

    position: absolute;

    inset: 0;

    z-index: 5;

    pointer-events: none;

    background:
        linear-gradient(
            rgba(8,6,10,.20),
            rgba(8,6,10,.08)
        );

}


/* =========================================================
   CARD FRAME
========================================================= */

.scan-frame {

    position: absolute;

    left: 50%;

    top: 50%;

    transform: translate(-50%, -50%);

    transition:
        .25s ease;

}

.scan-frame-card {

    width: 82%;

    aspect-ratio: 1.586 / 1;

    border: 1px solid rgba(255,255,255,.22);

    border-radius: 13px;

    box-shadow:
        0 0 0 999px rgba(0,0,0,.23),
        0 0 45px rgba(198,162,218,.10);

}

.scan-frame-qr {

    width: 48%;

    aspect-ratio: 1;

    border: 1px solid rgba(255,255,255,.25);

    border-radius: 18px;

    box-shadow:
        0 0 0 999px rgba(0,0,0,.23),
        0 0 45px rgba(198,162,218,.12);

}

.hidden {

    display: none !important;

}


/* =========================================================
   CORNERS
========================================================= */

.frame-corner {

    position: absolute;

    width: 25px;

    height: 25px;

    border-color: #e2bdf2;

    filter:
        drop-shadow(
            0 0 7px
            rgba(215,174,236,.65)
        );

}

.corner-tl {

    top: -1px;
    left: -1px;

    border-top: 3px solid;
    border-left: 3px solid;

    border-radius: 8px 0 0 0;

}

.corner-tr {

    top: -1px;
    right: -1px;

    border-top: 3px solid;
    border-right: 3px solid;

    border-radius: 0 8px 0 0;

}

.corner-bl {

    bottom: -1px;
    left: -1px;

    border-bottom: 3px solid;
    border-left: 3px solid;

    border-radius: 0 0 0 8px;

}

.corner-br {

    bottom: -1px;
    right: -1px;

    border-bottom: 3px solid;
    border-right: 3px solid;

    border-radius: 0 0 8px 0;

}


/* =========================================================
   LASER
========================================================= */

.scan-laser {

    position: absolute;

    left: 5%;

    right: 5%;

    top: 10%;

    height: 2px;

    border-radius: 999px;

    background:
        linear-gradient(
            90deg,
            transparent,
            #e5c3f4 30%,
            #fff 50%,
            #e5c3f4 70%,
            transparent
        );

    box-shadow:
        0 0 10px rgba(220,181,238,.9),
        0 0 22px rgba(220,181,238,.45);

    animation:
        scannerLaser 2.3s
        ease-in-out
        infinite;

}

@keyframes scannerLaser {

    0% {
        top: 8%;
        opacity: .25;
    }

    50% {
        top: 91%;
        opacity: 1;
    }

    100% {
        top: 8%;
        opacity: .25;
    }

}


/* =========================================================
   GRID
========================================================= */

.scan-grid {

    position: absolute;

    inset: 0;

    opacity: .07;

    background-image:
        linear-gradient(
            rgba(255,255,255,.7) 1px,
            transparent 1px
        ),
        linear-gradient(
            90deg,
            rgba(255,255,255,.7) 1px,
            transparent 1px
        );

    background-size:
        28px 28px;

    mask-image:
        linear-gradient(
            to bottom,
            transparent,
            #000,
            transparent
        );

}


/* =========================================================
   QR POSITION
========================================================= */

.qr-position {

    position: absolute;

    right: 5%;

    bottom: 8%;

    display: flex;

    align-items: center;

    gap: 5px;

}

.qr-position-box {

    position: relative;

    width: 32px;

    height: 32px;

    border: 1px dashed rgba(255,255,255,.55);

    border-radius: 5px;

}

.qr-position-box span {

    position: absolute;

    width: 7px;

    height: 7px;

    border-color: #fff;

}

.qr-position-box span:nth-child(1) {

    top: -1px;
    left: -1px;

    border-top: 2px solid;
    border-left: 2px solid;

}

.qr-position-box span:nth-child(2) {

    top: -1px;
    right: -1px;

    border-top: 2px solid;
    border-right: 2px solid;

}

.qr-position-box span:nth-child(3) {

    bottom: -1px;
    left: -1px;

    border-bottom: 2px solid;
    border-left: 2px solid;

}

.qr-position-box span:nth-child(4) {

    bottom: -1px;
    right: -1px;

    border-bottom: 2px solid;
    border-right: 2px solid;

}

.qr-position small {

    color: rgba(255,255,255,.75);

    font-size: 7px;

    font-weight: 800;

    letter-spacing: 1px;

}


/* =========================================================
   CENTER MESSAGE
========================================================= */

.scanner-center-message {

    position: absolute;

    left: 50%;

    top: 50%;

    z-index: 7;

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 6px;

    transform:
        translate(-50%, -50%);

    color: #fff;

    text-align: center;

    pointer-events: none;

}

.scanner-center-message strong {

    font-size: 11px;

}

.scanner-center-message small {

    color: rgba(255,255,255,.55);

    font-size: 8px;

}

.scanner-radar {

    width: 42px;

    height: 42px;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 1px solid rgba(255,255,255,.2);

    border-radius: 50%;

    margin-bottom: 3px;

}

.scanner-radar span {

    width: 7px;

    height: 7px;

    border-radius: 50%;

    background: #d9b6eb;

    box-shadow:
        0 0 0 6px rgba(217,182,235,.08),
        0 0 20px rgba(217,182,235,.65);

    animation:
        radarPulse 1.5s infinite;

}

@keyframes radarPulse {

    0%,100% {
        transform: scale(.8);
    }

    50% {
        transform: scale(1.3);
    }

}


/* =========================================================
   SUCCESS
========================================================= */

.scanner-success {

    position: absolute;

    inset: 0;

    z-index: 10;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 7px;

    opacity: 0;

    visibility: hidden;

    background:
        rgba(17,27,20,.72);

    backdrop-filter:
        blur(5px);

    transition:
        .25s ease;

}

.scanner-success.show {

    opacity: 1;

    visibility: visible;

}

.success-circle {

    width: 54px;

    height: 54px;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 2px solid #a7d8b3;

    border-radius: 50%;

    color: #bce5c5;

    font-size: 23px;

    box-shadow:
        0 0 30px rgba(125,191,142,.2);

}

.scanner-success strong {

    color: #e6f5e9;

    font-size: 11px;

    letter-spacing: 1.5px;

}

.scanner-success small {

    color: rgba(255,255,255,.6);

    font-size: 8px;

}


/* =========================================================
   CONTROLS
========================================================= */

.scanner-controls {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 9px;

    margin-top: 13px;

}

.scanner-control {

    min-height: 44px;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    border-radius: 11px;

    font-size: 10px;

    font-weight: 700;

    cursor: pointer;

    transition:
        .2s ease;

}

.scanner-control.primary {

    border: 0;

    color: #fff;

    background:
        linear-gradient(
            135deg,
            #68477a,
            #8f68a3
        );

    box-shadow:
        0 7px 20px rgba(105,72,122,.18);

}

.scanner-control.primary:hover {

    transform:
        translateY(-1px);

}

.scanner-control.secondary {

    border: 1px solid #e8e0eb;

    color: #665b6a;

    background: #fff;

}

.scanner-control.secondary:hover {

    border-color: #cdb8d7;

    background: #fcf9fd;

}

.control-icon {

    font-size: 14px;

}


/* =========================================================
   HELP
========================================================= */

.scanner-help {

    display: flex;

    align-items: flex-start;

    gap: 9px;

    margin-top: 12px;

    padding: 11px 12px;

    border: 1px solid #f0e9f2;

    border-radius: 11px;

    color: #8c8391;

    background: #fcfbfd;

    font-size: 9px;

    line-height: 1.5;

}

.help-icon {

    width: 17px;

    height: 17px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 50%;

    color: #89639c;

    background: #f1e7f5;

    font-size: 9px;

    font-weight: 800;

}


/* =========================================================
   RESULT
========================================================= */

.identity-card {

    position: relative;

    display: flex;

    align-items: center;

    gap: 12px;

    margin-bottom: 19px;

    padding: 15px;

    border: 1px solid #eee8f0;

    border-radius: 15px;

    background:
        linear-gradient(
            135deg,
            #fff,
            #fbf9fc
        );

}

.identity-status {

    position: absolute;

    top: 9px;

    right: 10px;

    display: flex;

    align-items: center;

    gap: 4px;

    color: #aaa2ae;

    font-size: 6px;

    font-weight: 800;

    letter-spacing: 1px;

}

.identity-status-dot {

    width: 5px;

    height: 5px;

    border-radius: 50%;

    background: #c2bbc5;

}

.identity-avatar {

    width: 52px;

    height: 52px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border: 1px solid #e9e0ed;

    border-radius: 15px;

    color: #9a73ae;

    background: #f7f0fa;

    font-size: 18px;

    font-weight: 800;

}

.identity-avatar.verified {

    border-color: #d6e9da;

    color: #62926f;

    background: #f0f8f2;

}

.identity-info {

    min-width: 0;

    padding-top: 5px;

}

.identity-info > span {

    display: block;

    margin-bottom: 4px;

    color: #aaa1ad;

    font-size: 6px;

    font-weight: 800;

    letter-spacing: 1.3px;

}

.identity-info strong {

    display: block;

    overflow: hidden;

    color: #403644;

    font-size: 13px;

    white-space: nowrap;

    text-overflow: ellipsis;

}

.identity-info small {

    display: block;

    margin-top: 4px;

    overflow: hidden;

    color: #928a97;

    font-size: 8px;

    white-space: nowrap;

    text-overflow: ellipsis;

}


/* =========================================================
   INPUT
========================================================= */

.modern-input {

    margin-bottom: 13px;

}

.modern-input label {

    display: block;

    margin-bottom: 6px;

    color: #756c7a;

    font-size: 8px;

    font-weight: 800;

    letter-spacing: .8px;

}

.select-wrap {

    position: relative;

}

.select-wrap::after {

    content: '⌄';

    position: absolute;

    right: 13px;

    top: 50%;

    transform:
        translateY(-55%);

    color: #9b91a0;

    pointer-events: none;

}

.select {

    width: 100%;

    height: 42px;

    padding: 0 35px 0 12px;

    border: 1px solid #e9e3eb;

    border-radius: 11px;

    outline: none;

    color: #514957;

    background: #fcfbfd;

    font-size: 10px;

    appearance: none;

}

.select:focus {

    border-color: #c9afd5;

    box-shadow:
        0 0 0 3px
        rgba(155,120,176,.08);

}


/* =========================================================
   SUBMIT
========================================================= */

.attendance-submit {

    width: 100%;

    min-height: 45px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    margin-top: 7px;

    padding: 0 14px;

    border: 0;

    border-radius: 12px;

    color: #fff;

    background:
        linear-gradient(
            135deg,
            #694879,
            #916ba5
        );

    font-size: 10px;

    font-weight: 800;

    cursor: pointer;

    box-shadow:
        0 8px 24px rgba(101,69,119,.18);

    transition:
        .2s ease;

}

.attendance-submit:hover:not(:disabled) {

    transform:
        translateY(-1px);

}

.attendance-submit:disabled {

    color: #aaa3ad;

    background: #eeeaf0;

    box-shadow: none;

    cursor: not-allowed;

}


/* =========================================================
   STATUS
========================================================= */

.verification-message {

    display: flex;

    align-items: flex-start;

    gap: 8px;

    margin-top: 11px;

    padding: 10px 11px;

    border: 1px solid #eee8f0;

    border-radius: 10px;

    color: #8b838f;

    background: #fcfbfd;

    font-size: 8px;

    line-height: 1.5;

}

.verification-message.success {

    border-color: #dcecdf;

    color: #60816a;

    background: #f5faf6;

}

.verification-message.error {

    border-color: #f0dddd;

    color: #9b6666;

    background: #fff8f8;

}

.verification-message.loading {

    color: #826f8b;

}

.message-icon {

    width: 16px;

    height: 16px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 50%;

    background: #eee8f1;

    font-size: 9px;

    font-weight: 800;

}

.message-spinner {

    width: 12px;

    height: 12px;

    flex-shrink: 0;

    border: 2px solid #e5dce9;

    border-top-color: #8d659e;

    border-radius: 50%;

    animation:
        spin .7s linear infinite;

}

@keyframes spin {

    to {
        transform: rotate(360deg);
    }

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 950px) {

    .scanner-layout {

        grid-template-columns: 1fr;

    }

}

@media (max-width: 600px) {

    .scanner-intro {

        align-items: flex-start;

        flex-direction: column;

    }

    .scanner-mode {

        width: 100%;

    }

    .scanner-mode-btn {

        min-width: 0;

        flex: 1;

    }

    .scanner-mode-btn small {

        display: none;

    }

    .scanner-panel,
    .result-panel {

        padding: 15px;

        border-radius: 16px;

    }

    .scanner-stage {

        aspect-ratio: .95 / 1;

    }

    .scan-frame-card {

        width: 90%;

    }

    .scanner-controls {

        grid-template-columns: 1fr;

    }

}

</style>


<?php

require __DIR__.'/../partials/footer.php';

?>