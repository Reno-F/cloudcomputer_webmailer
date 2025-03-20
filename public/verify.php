<?php
session_start();
include 'firebase_config.php';

if (isset($_GET['oobCode'])) {
    $oobCode = $_GET['oobCode'];

    try {
        // Verifikasi email dengan kode yang diberikan
        $auth->verifyEmail($oobCode);
        $message = "Email berhasil diverifikasi! <a href='login.php'>Login di sini</a>";
    } catch (Exception $e) {
        $message = "Verifikasi gagal: " . $e->getMessage();
    }
} else {
    $message = "Kode verifikasi tidak ditemukan.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
</head>
<body>
    <h2>Verifikasi Email</h2>
    <p><?php echo $message; ?></p>
</body>
</html>
