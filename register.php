<?php

require_once __DIR__.'/config/database.php';
require_once __DIR__.'/config/auth.php';
require_once __DIR__.'/config/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!checkCsrf($_POST['csrf'] ?? null)) {

        $msg = 'Sesi formulir tidak valid. Silakan muat ulang halaman.';
        $msgType = 'error';

    } else {

        $npm           = trim($_POST['npm'] ?? '');
        $nama          = trim($_POST['nama'] ?? '');
        $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
        $tempat_lahir  = trim($_POST['tempat_lahir'] ?? '');
        $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
        $email         = trim($_POST['email'] ?? '');
        $no_hp         = trim($_POST['no_hp'] ?? '');
        $alamat        = trim($_POST['alamat'] ?? '');
        $password      = $_POST['password'] ?? '';
        $password2     = $_POST['password2'] ?? '';

        $fotoPath = null;


        /* =====================================================
           VALIDASI DASAR
        ====================================================== */

        if ($npm === '' || $nama === '' || $password === '') {

            $msg = 'NPM, nama, dan password wajib diisi.';
            $msgType = 'error';

        } elseif (!preg_match('/^[0-9]+$/', $npm)) {

            $msg = 'NPM hanya boleh berisi angka.';
            $msgType = 'error';

        } elseif (strlen($password) < 6) {

            $msg = 'Password minimal 6 karakter.';
            $msgType = 'error';

        } elseif ($password !== $password2) {

            $msg = 'Konfirmasi password tidak sama.';
            $msgType = 'error';

        }


        /* =====================================================
           CEK NPM
        ====================================================== */

        if ($msg === '') {

            $st = $pdo->prepare(
                'SELECT id FROM users WHERE username=? LIMIT 1'
            );

            $st->execute([$npm]);

            if ($st->fetch()) {

                $msg = 'NPM sudah terdaftar.';
                $msgType = 'error';

            }

        }


        /* =====================================================
           UPLOAD FOTO
        ====================================================== */

        if (
            $msg === '' &&
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {

                $msg = 'Foto gagal diunggah.';
                $msgType = 'error';

            } else {

                $maxSize = 3 * 1024 * 1024;

                if ($_FILES['foto']['size'] > $maxSize) {

                    $msg = 'Ukuran foto maksimal 3 MB.';
                    $msgType = 'error';

                } else {

                    $allowedTypes = [
                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/webp' => 'webp'
                    ];

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);

                    $mime = finfo_file(
                        $finfo,
                        $_FILES['foto']['tmp_name']
                    );

                    finfo_close($finfo);

                    if (!isset($allowedTypes[$mime])) {

                        $msg =
                            'Format foto harus JPG, PNG, atau WEBP.';

                        $msgType = 'error';

                    } else {

                        $uploadDir =
                            __DIR__.'/uploads/mahasiswa';

                        if (!is_dir($uploadDir)) {

                            mkdir(
                                $uploadDir,
                                0777,
                                true
                            );

                        }

                        $extension =
                            $allowedTypes[$mime];

                        $filename =
                            'mhs_' .
                            preg_replace(
                                '/[^0-9]/',
                                '',
                                $npm
                            ) .
                            '_' .
                            time() .
                            '.' .
                            $extension;

                        $destination =
                            $uploadDir.'/'.$filename;


                        if (
                            move_uploaded_file(
                                $_FILES['foto']['tmp_name'],
                                $destination
                            )
                        ) {

                            $fotoPath =
                                'uploads/mahasiswa/'.$filename;

                        } else {

                            $msg =
                                'Foto tidak dapat disimpan.';

                            $msgType = 'error';

                        }

                    }

                }

            }

        }


        /* =====================================================
           SIMPAN DATABASE
        ====================================================== */

        if ($msg === '') {

            try {

                $pdo->beginTransaction();


                /* ---------------------------------------------
                   USER
                --------------------------------------------- */

                $passwordHash =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                $st = $pdo->prepare(
                    'INSERT INTO users
                    (username, password, role)
                    VALUES (?, ?, ?)'
                );


                $st->execute([
                    $npm,
                    $passwordHash,
                    'mahasiswa'
                ]);


                $userId =
                    $pdo->lastInsertId();


                /* ---------------------------------------------
                   DATA DEFAULT AKADEMIK
                --------------------------------------------- */

                $kelasId = 1;

                $angkatan = 2024;

                $qrCode =
                    'MHS-'.$npm;

                $kartuNomor =
                    'AK-2026-'.$npm;


                /* ---------------------------------------------
                   MAHASISWA
                --------------------------------------------- */

                $st = $pdo->prepare(
                    'INSERT INTO mahasiswa
                    (
                        user_id,
                        npm,
                        nama,
                        jenis_kelamin,
                        tempat_lahir,
                        tanggal_lahir,
                        fakultas,
                        prodi,
                        angkatan,
                        kelas_id,
                        email,
                        no_hp,
                        alamat,
                        kabupaten_kota,
                        provinsi,
                        foto,
                        qr_code,
                        kartu_nomor,
                        status
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?, ?
                    )'
                );


                $st->execute([

                    $userId,

                    $npm,

                    $nama,

                    $jenis_kelamin ?: null,

                    $tempat_lahir ?: null,

                    $tanggal_lahir ?: null,

                    'Fakultas Teknik',

                    'Teknik Informatika',

                    $angkatan,

                    $kelasId,

                    $email ?: null,

                    $no_hp ?: null,

                    $alamat ?: null,

                    'Pidie',

                    'Aceh',

                    $fotoPath,

                    $qrCode,

                    $kartuNomor,

                    'Aktif'

                ]);


                $mahasiswaId =
                    $pdo->lastInsertId();


                /* ---------------------------------------------
                   MASUKKAN KE KELAS
                --------------------------------------------- */

                $st = $pdo->prepare(
                    'INSERT INTO kelas_mahasiswa
                    (kelas_id, mahasiswa_id)
                    VALUES (?, ?)'
                );


                $st->execute([

                    $kelasId,

                    $mahasiswaId

                ]);


                /* ---------------------------------------------
                   COMMIT
                --------------------------------------------- */

                $pdo->commit();


                $msg =
                    'Akun berhasil dibuat.';

                $msgType =
                    'success';


                /*
                 * Bersihkan data form
                 */

                $_POST = [];


            } catch (Throwable $e) {

                if ($pdo->inTransaction()) {

                    $pdo->rollBack();

                }


                /*
                 * Hapus foto jika database gagal
                 */

                if ($fotoPath) {

                    $uploadedFile =
                        __DIR__.'/'.$fotoPath;

                    if (is_file($uploadedFile)) {

                        unlink($uploadedFile);

                    }

                }


                $msg =
                    'Pendaftaran gagal. Periksa kembali data Anda.';

                $msgType =
                    'error';

            }

        }

    }

}


$base = '.';

$assetBase = 'assets';

$pageTitle = 'Daftar Mahasiswa';


require __DIR__.'/partials/head.php';

?>


<div class="login-page">

    <div class="login-box">


        <!-- BRAND -->

        <a
            class="brand"
            href="index.php"
        >

            <span class="brand-mark">
                ✦
            </span>

            <span>

                <b>
                    Sistem Absensi QR
                </b>

                <small>
                    Student registration
                </small>

            </span>

        </a>


        <!-- CARD -->

        <div
            class="auth-card"
            style="margin-top:25px"
        >

            <span class="eyebrow">
                New Student
            </span>


            <h2>
                Daftar akun
            </h2>


            <p>
                Lengkapi data mahasiswa untuk membuat
                akun dan kartu QR personal.
            </p>


            <!-- MESSAGE -->

            <?php if ($msg): ?>

                <div
                    class="notice <?= $msgType === 'error' ? 'error' : 'success' ?>"
                >

                    <?php if ($msgType === 'success'): ?>

                        <span>
                            Akun berhasil dibuat.
                        </span>

                        <a
                            href="index.php"
                            class="login-link"
                        >
                            Silakan masuk ke sistem →
                        </a>

                    <?php else: ?>

                        <?=e($msg)?>

                    <?php endif; ?>

                </div>

            <?php endif; ?>


            <?php if ($msgType !== 'success'): ?>


            <form
                method="post"
                enctype="multipart/form-data"
                autocomplete="off"
            >

                <input
                    type="hidden"
                    name="csrf"
                    value="<?=e(csrfToken())?>"
                >


                <!-- NPM -->

                <div class="input-group">

                    <label>
                        NPM *
                    </label>

                    <input
                        class="input"
                        type="text"
                        name="npm"
                        value="<?=e($_POST['npm'] ?? '')?>"
                        required
                    >

                </div>


                <!-- NAMA -->

                <div class="input-group">

                    <label>
                        Nama Lengkap *
                    </label>

                    <input
                        class="input"
                        type="text"
                        name="nama"
                        value="<?=e($_POST['nama'] ?? '')?>"
                        placeholder="Masukkan nama lengkap"
                        required
                    >

                </div>


                <!-- JENIS KELAMIN -->

                <div class="input-group">

                    <label>
                        Jenis Kelamin
                    </label>

                    <select
                        class="input"
                        name="jenis_kelamin"
                    >

                        <option value="">
                            Pilih jenis kelamin
                        </option>

                        <option
                            value="Perempuan"
                            <?=($_POST['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : ''?>
                        >
                            Perempuan
                        </option>

                        <option
                            value="Laki-laki"
                            <?=($_POST['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : ''?>
                        >
                            Laki-laki
                        </option>

                    </select>

                </div>


                <!-- TEMPAT LAHIR -->

                <div class="input-group">

                    <label>
                        Tempat Lahir
                    </label>

                    <input
                        class="input"
                        type="text"
                        name="tempat_lahir"
                        value="<?=e($_POST['tempat_lahir'] ?? '')?>"
                    >

                </div>


                <!-- TANGGAL LAHIR -->

                <div class="input-group">

                    <label>
                        Tanggal Lahir
                    </label>

                    <input
                        class="input"
                        type="date"
                        name="tanggal_lahir"
                        value="<?=e($_POST['tanggal_lahir'] ?? '')?>"
                    >

                </div>


                <!-- EMAIL -->

                <div class="input-group">

                    <label>
                        Email
                    </label>

                    <input
                        class="input"
                        type="email"
                        name="email"
                        value="<?=e($_POST['email'] ?? '')?>"
                        placeholder="nama@email.com"
                    >

                </div>


                <!-- NO HP -->

                <div class="input-group">

                    <label>
                        No. HP
                    </label>

                    <input
                        class="input"
                        type="text"
                        name="no_hp"
                        value="<?=e($_POST['no_hp'] ?? '')?>"
                        placeholder="08xxxxxxxxxx"
                    >

                </div>


                <!-- FOTO -->

                <div class="input-group">

                    <label>
                        Foto Mahasiswa
                    </label>


                    <div class="photo-upload">

                        <div
                            class="photo-preview"
                            id="photoPreview"
                        >

                            <span id="photoIcon">
                                +
                            </span>

                            <img
                                id="previewImage"
                                src=""
                                alt="Preview foto"
                                style="display:none"
                            >

                        </div>


                        <div class="photo-upload-info">

                            <label
                                for="foto"
                                class="btn btn-ghost"
                            >
                                Pilih Foto
                            </label>


                            <input
                                id="foto"
                                type="file"
                                name="foto"
                                accept="image/jpeg,image/png,image/webp"
                                hidden
                            >


                            <small>

                                JPG, PNG, atau WEBP

                                <br>

                                Maksimal 3 MB

                            </small>

                        </div>

                    </div>

                </div>


                <!-- ALAMAT -->

                <div class="input-group">

                    <label>
                        Alamat
                    </label>

                    <textarea
                        class="input"
                        name="alamat"
                        rows="3"
                        placeholder="Alamat lengkap"
                    ><?=e($_POST['alamat'] ?? '')?></textarea>

                </div>


                <!-- PASSWORD -->

                <div class="input-group">

                    <label>
                        Password *
                    </label>

                    <input
                        class="input"
                        type="password"
                        name="password"
                        minlength="6"
                        placeholder="Minimal 6 karakter"
                        required
                    >

                </div>


                <!-- CONFIRM PASSWORD -->

                <div class="input-group">

                    <label>
                        Konfirmasi Password *
                    </label>

                    <input
                        class="input"
                        type="password"
                        name="password2"
                        minlength="6"
                        placeholder="Ulangi password"
                        required
                    >

                </div>


                <!-- SUBMIT -->

                <button
                    class="btn btn-primary btn-block"
                    type="submit"
                    style="margin-top:20px"
                >
                    Buat Akun →
                </button>


            </form>


            <!-- LOGIN -->

            <div
                class="helper"
                style="margin-top:18px"
            >

                Sudah punya akun?

                <a href="index.php">
                    Masuk ke sistem
                </a>

            </div>


            <?php endif; ?>


        </div>

    </div>

</div>



<!-- =====================================================
     FOTO PREVIEW
====================================================== -->

<style>

.photo-upload {

    display: flex;

    align-items: center;

    gap: 15px;

    padding: 14px;

    border: 1px solid rgba(255,255,255,.08);

    border-radius: 14px;

    background: rgba(255,255,255,.025);

}


.photo-preview {

    width: 72px;

    height: 72px;

    flex-shrink: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

    border-radius: 14px;

    border: 1px dashed rgba(216,167,255,.35);

    background: rgba(216,167,255,.05);

    color: #d8a7ff;

    font-size: 25px;

}


.photo-preview img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}


.photo-upload-info {

    display: flex;

    flex-direction: column;

    gap: 7px;

}


.photo-upload-info .btn {

    width: fit-content;

    padding: 9px 14px;

    cursor: pointer;

}


.photo-upload-info small {

    color: #817787;

    font-size: 10px;

    line-height: 1.5;

}


/* LINK SETELAH BERHASIL DAFTAR */

.notice.success .login-link {

    display: inline-block;

    margin-left: 5px;

    color: #d8a7ff;

    font-weight: 700;

    text-decoration: none;

}


.notice.success .login-link:hover {

    color: #ffffff;

    text-decoration: underline;

}

</style>



<!-- =====================================================
     FOTO PREVIEW SCRIPT
====================================================== -->

<script>

const fotoInput =
    document.getElementById('foto');

const previewImage =
    document.getElementById('previewImage');

const photoIcon =
    document.getElementById('photoIcon');


if (fotoInput) {

    fotoInput.addEventListener(
        'change',
        function () {

            const file =
                this.files[0];


            if (!file) {

                return;

            }


            if (
                file.size >
                3 * 1024 * 1024
            ) {

                alert(
                    'Ukuran foto maksimal 3 MB.'
                );


                this.value = '';


                previewImage.style.display =
                    'none';

                photoIcon.style.display =
                    'block';


                return;

            }


            const reader =
                new FileReader();


            reader.onload =
                function (event) {

                    previewImage.src =
                        event.target.result;

                    previewImage.style.display =
                        'block';

                    photoIcon.style.display =
                        'none';

                };


            reader.readAsDataURL(file);

        }
    );

}

</script>



<?php

require __DIR__.'/partials/footer.php';

?>