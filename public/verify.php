<?php
session_start();
include 'firebase_config.php';

if (isset($_GET['oobCode'])) {
    $oobCode = $_GET['oobCode'];

    try {
        // Verifikasi email dengan kode yang diberikan
        $auth->verifyEmail($oobCode);
        echo "Email berhasil diverifikasi! <a href='login.php'>Login di sini</a>";
    } catch (Exception $e) {
        echo "Verifikasi gagal: " . $e->getMessage();
    }
} else {
    echo "Kode verifikasi tidak ditemukan.";
}
?>
