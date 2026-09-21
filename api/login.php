<?php

session_start();

header(
    "Content-Type: application/json; charset=UTF-8"
);

require_once "../config/database.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Metode request tidak valid."
    ]);

    exit;
}


$role =
    $_POST["role"] ?? "";

$identity =
    trim($_POST["identity"] ?? "");

$password =
    $_POST["password"] ?? "";


if (
    empty($role) ||
    empty($identity) ||
    empty($password)
) {

    echo json_encode([
        "success" => false,
        "message" => "Semua field wajib diisi."
    ]);

    exit;
}


if (
    !in_array(
        $role,
        [
            "mahasiswa",
            "dosen",
            "admin"
        ],
        true
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Role tidak valid."
    ]);

    exit;
}


try {

    /*
     * =====================================================
     * MAHASISWA
     * =====================================================
     */

    if ($role === "mahasiswa") {

        $sql = "
            SELECT
                u.id AS user_id,
                u.username,
                u.password,
                u.role,
                u.status AS user_status,

                m.id AS mahasiswa_id,
                m.npm,
                m.nama,
                m.kelas_id,
                m.prodi,
                m.foto,
                m.qr_code,
                m.kartu_nomor,
                m.status AS mahasiswa_status

            FROM users u

            INNER JOIN mahasiswa m
                ON m.user_id = u.id

            WHERE
                u.username = ?
                AND u.role = 'mahasiswa'

            LIMIT 1
        ";

        $stmt =
            $pdo->prepare($sql);

        $stmt->execute([
            $identity
        ]);

        $user =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        if (
            !$user ||
            !password_verify(
                $password,
                $user["password"]
            )
        ) {

            echo json_encode([
                "success" => false,
                "message" =>
                    "NPM atau password salah."
            ]);

            exit;
        }


        if (
            $user["user_status"] !== "aktif" ||
            $user["mahasiswa_status"] !== "Aktif"
        ) {

            echo json_encode([
                "success" => false,
                "message" =>
                    "Akun mahasiswa tidak aktif."
            ]);

            exit;
        }


        $_SESSION["user_id"] =
            $user["user_id"];

        $_SESSION["role"] =
            "mahasiswa";

        $_SESSION["mahasiswa_id"] =
            $user["mahasiswa_id"];

        $_SESSION["npm"] =
            $user["npm"];

        $_SESSION["nama"] =
            $user["nama"];


        $redirect =
            "../mahasiswa/index.php";
    }


    /*
     * =====================================================
     * DOSEN
     * =====================================================
     */

    elseif ($role === "dosen") {

        $sql = "
            SELECT
                u.id AS user_id,
                u.username,
                u.password,
                u.role,
                u.status AS user_status,

                d.id AS dosen_id,
                d.nidn,
                d.nama,
                d.gelar_depan,
                d.gelar_belakang,
                d.email,
                d.foto,
                d.status AS dosen_status

            FROM users u

            INNER JOIN dosen d
                ON d.user_id = u.id

            WHERE
                u.username = ?
                AND u.role = 'dosen'

            LIMIT 1
        ";

        $stmt =
            $pdo->prepare($sql);

        $stmt->execute([
            $identity
        ]);

        $user =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        if (
            !$user ||
            !password_verify(
                $password,
                $user["password"]
            )
        ) {

            echo json_encode([
                "success" => false,
                "message" =>
                    "NIDN atau password salah."
            ]);

            exit;
        }


        if (
            $user["user_status"] !== "aktif" ||
            $user["dosen_status"] !== "Aktif"
        ) {

            echo json_encode([
                "success" => false,
                "message" =>
                    "Akun dosen tidak aktif."
            ]);

            exit;
        }


        $_SESSION["user_id"] =
            $user["user_id"];

        $_SESSION["role"] =
            "dosen";

        $_SESSION["dosen_id"] =
            $user["dosen_id"];

        $_SESSION["nidn"] =
            $user["nidn"];

        $_SESSION["nama"] =
            $user["nama"];


        $redirect =
            "../dosen/index.php";
    }


    /*
     * =====================================================
     * ADMIN
     * =====================================================
     */

    else {

        $sql = "
            SELECT
                id,
                username,
                password,
                role,
                status

            FROM users

            WHERE
                username = ?
                AND role = 'admin'

            LIMIT 1
        ";

        $stmt =
            $pdo->prepare($sql);

        $stmt->execute([
            $identity
        ]);

        $user =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        if (
            !$user ||
            !password_verify(
                $password,
                $user["password"]
            )
        ) {

            echo json_encode([
                "success" => false,
                "message" =>
                    "Username atau password salah."
            ]);

            exit;
        }


        if (
            $user["status"] !== "aktif"
        ) {

            echo json_encode([
                "success" => false,
                "message" =>
                    "Akun admin tidak aktif."
            ]);

            exit;
        }


        $_SESSION["user_id"] =
            $user["id"];

        $_SESSION["role"] =
            "admin";

        $_SESSION["username"] =
            $user["username"];

        $redirect =
            "../admin/index.php";
    }


    /*
     * =====================================================
     * UPDATE LAST LOGIN
     * =====================================================
     */

    $update =
        $pdo->prepare("
            UPDATE users
            SET last_login = NOW()
            WHERE id = ?
        ");

    $update->execute([
        $_SESSION["user_id"]
    ]);


    echo json_encode([
        "success" => true,
        "message" =>
            "Login berhasil.",
        "redirect" =>
            $redirect
    ]);

} catch (
    PDOException $e
) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Terjadi kesalahan server/database."
    ]);

}