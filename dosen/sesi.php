<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../config/helpers.php';

requireRole('dosen');

$base='..';
$assetBase='../assets';
$pageTitle='Buat Sesi & QR';

require __DIR__.'/../partials/head.php';

$did=(int)($pdo->query(
    "SELECT d.id
     FROM dosen d
     JOIN users u ON u.username=d.nidn
     WHERE u.id=".userId()
)->fetchColumn() ?: 0);

$notice='';
$error='';
$created=null;

$st=$pdo->prepare(
    'SELECT 
        j.id,
        j.hari,
        j.jam_mulai,
        j.jam_selesai,
        mk.nama_mk,
        mk.kode_mk,
        k.nama_kelas
     FROM jadwal j
     JOIN mata_kuliah mk ON mk.id=j.mata_kuliah_id
     JOIN kelas k ON k.id=j.kelas_id
     WHERE j.dosen_id=?
     ORDER BY 
        FIELD(j.hari,"Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu"),
        j.jam_mulai'
);

$st->execute([$did]);
$schedules=$st->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST' && checkCsrf($_POST['csrf']??null)){

    $jadwalId=(int)($_POST['jadwal_id']??0);
    $pertemuan=max(1,(int)($_POST['pertemuan']??1));
    $tanggal=$_POST['tanggal']??'';
    $jamMulai=$_POST['jam_mulai']??'';
    $jamSelesai=$_POST['jam_selesai']??'';

    $st=$pdo->prepare(
        'SELECT 
            j.id,
            j.hari,
            j.jam_mulai,
            j.jam_selesai,
            mk.nama_mk,
            k.nama_kelas
         FROM jadwal j
         JOIN mata_kuliah mk ON mk.id=j.mata_kuliah_id
         JOIN kelas k ON k.id=j.kelas_id
         WHERE j.id=? AND j.dosen_id=?'
    );

    $st->execute([$jadwalId,$did]);
    $schedule=$st->fetch();

    if(!$schedule){

        $error='Jadwal tidak valid.';

    }elseif(!$tanggal || !$jamMulai || !$jamSelesai){

        $error='Tanggal dan jam wajib diisi.';

    }elseif($jamSelesai <= $jamMulai){

        $error='Jam selesai harus lebih besar dari jam mulai.';

    }else{

        $st=$pdo->prepare(
            'SELECT id,token
             FROM sesi_absensi
             WHERE jadwal_id=? AND tanggal=?
             LIMIT 1'
        );

        $st->execute([$jadwalId,$tanggal]);
        $existing=$st->fetch();

        if($existing){

            $created=$existing['id'];
            $notice='Sesi untuk jadwal dan tanggal tersebut sudah ada. QR yang sama digunakan kembali.';

        }else{

            $token='SESSION|'.bin2hex(random_bytes(18));

            $st=$pdo->prepare(
                'INSERT INTO sesi_absensi
                (jadwal_id,pertemuan,tanggal,jam_mulai,jam_selesai,token,status)
                VALUES(?,?,?,?,?,?,"aktif")'
            );

            $st->execute([
                $jadwalId,
                $pertemuan,
                $tanggal,
                $jamMulai,
                $jamSelesai,
                $token
            ]);

            $created=$pdo->lastInsertId();
            $notice='Sesi berhasil dibuat.';
        }
    }
}
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
        <h1>Buat Sesi & QR</h1>
        <p>Pilih jadwal, tentukan pertemuan, lalu bagikan QR sesi.</p>
    </div>

    <div class="top-actions">
        <a class="btn btn-ghost" href="../index.php">↗ Beranda</a>
    </div>

</header>

<?php if($error): ?>

<div class="notice" style="margin-bottom:18px">
    <?=e($error)?>
</div>

<?php endif; ?>

<?php if($notice): ?>

<div class="notice success" style="margin-bottom:18px">
    <?=e($notice)?>
</div>

<?php endif; ?>

<section class="section">

<form method="post">

<input type="hidden"
       name="csrf"
       value="<?=e(csrfToken())?>">

<div class="form-grid three">

<div class="input-group">

<label>Jadwal</label>

<select class="select"
        name="jadwal_id"
        required>

<?php if(!$schedules): ?>

<option value="">Tidak ada jadwal</option>

<?php else: ?>

<?php foreach($schedules as $s): ?>

<option value="<?=$s['id']?>">

<?=e(
    $s['nama_mk']
    .' · '
    .$s['nama_kelas']
    .' · '
    .$s['hari']
    .' '
    .substr($s['jam_mulai'],0,5)
    .'–'
    .substr($s['jam_selesai'],0,5)
)?>

</option>

<?php endforeach; ?>

<?php endif; ?>

</select>

</div>

<div class="input-group">

<label>Pertemuan</label>

<input
    class="input"
    type="number"
    name="pertemuan"
    value="1"
    min="1"
    max="30"
    required>

</div>

<div class="input-group">

<label>Tanggal</label>

<input
    class="input"
    type="date"
    name="tanggal"
    value="<?=date('Y-m-d')?>"
    required>

</div>

<div class="input-group">

<label>Jam mulai</label>

<input
    class="input"
    type="time"
    name="jam_mulai"
    value="<?=date('H:i')?>"
    required>

</div>

<div class="input-group">

<label>Jam selesai</label>

<input
    class="input"
    type="time"
    name="jam_selesai"
    value="<?=date('H:i',time()+3600)?>"
    required>

</div>

</div>

<button
    class="btn btn-primary"
    type="submit"
    <?=!$schedules?'disabled':''?>>

Buat QR Sesi

</button>

</form>

</section>

<?php

if($created):

    $st=$pdo->prepare(
        'SELECT 
            s.*,
            mk.nama_mk,
            mk.kode_mk,
            k.nama_kelas
         FROM sesi_absensi s
         JOIN jadwal j ON j.id=s.jadwal_id
         JOIN mata_kuliah mk ON mk.id=j.mata_kuliah_id
         JOIN kelas k ON k.id=j.kelas_id
         WHERE s.id=?'
    );

    $st->execute([$created]);

    $session=$st->fetch();

    if($session):

        $token=$session['token'];

?>

<section class="section" style="margin-top:20px">

<div class="qr-card">

<div class="id-label">
    SESSION QR · SHARE TO CLASS
</div>

<h2 style="font:30px 'Playfair Display'">
    <?=e($session['nama_mk'])?>
</h2>

<div class="muted">
    <?=e($session['nama_kelas'])?>
    ·
    Pertemuan <?=e($session['pertemuan'])?>
</div>

<div class="qr-box" id="sessionQr"></div>

<div class="muted"
     style="font-size:10px;word-break:break-all">

<?=e($token)?>

</div>

</div>

<div class="actions" style="justify-content:center">

<button
    class="btn btn-primary"
    type="button"
    onclick="downloadQR()">

Unduh QR

</button>

<button
    class="btn btn-ghost"
    type="button"
    onclick="window.print()">

Cetak / Tampilkan

</button>

</div>

</section>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>

new QRCode(
    document.getElementById('sessionQr'),
    {
        text: <?=json_encode($token)?>,
        width: 215,
        height: 215
    }
);

function downloadQR(){

    const img=document.querySelector('#sessionQr img');

    if(!img){
        return;
    }

    const a=document.createElement('a');

    a.href=img.src;
    a.download='QR-Sesi-<?=date('Y-m-d')?>.png';

    document.body.appendChild(a);
    a.click();
    a.remove();
}

</script>

<?php

    endif;

endif;

?>

</main>

</div>

<?php require __DIR__.'/../partials/footer.php'; ?>