<?php
session_start();
include 'firebase_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Pastikan PHPMailer terinstall

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Cek apakah email sudah terdaftar
        try {
            $user = $auth->getUserByEmail($email);
            echo "Email sudah digunakan. <a href='login.php'>Login di sini</a>";
            exit();
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            // Jika email belum terdaftar, lanjutkan proses registrasi
        }

        // Buat akun pengguna di Firebase Authentication
        $userProperties = [
            'email' => $email,
            'password' => $password,
        ];
        $createdUser = $auth->createUser($userProperties);
        $uid = $createdUser->uid;

        // Simpan data user ke Firebase Realtime Database
        $userData = [
            'email' => $email,
            'created_at' => date('Y-m-d H:i:s'),
            'verified' => false
        ];
        $database->getReference('users/'.$uid)->set($userData);

        // **Buat link verifikasi email**
        $actionCodeSettings = [
            'continueUrl' => 'http://localhost/verify.php', // Ganti dengan domain Anda
            'handleCodeInApp' => false
        ];
        $verificationLink = $auth->getEmailVerificationLink($email, $actionCodeSettings);

        // **Kirim email verifikasi dengan PHPMailer**
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Sesuaikan dengan SMTP Anda
            $mail->SMTPAuth = true;
            $mail->Username = 'emailanda@gmail.com'; // Ganti dengan email Anda
            $mail->Password = 'passwordemail'; // Ganti dengan password email Anda
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('emailanda@gmail.com', 'Admin');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verifikasi Email Anda';
            $mail->Body = "Klik link berikut untuk verifikasi email Anda: <a href='$verificationLink'>Verifikasi Email</a>";

            $mail->send();
            echo "Registrasi berhasil! Silakan cek email Anda untuk verifikasi.";
        } catch (Exception $e) {
            echo "Email gagal dikirim. Error: {$mail->ErrorInfo}";
        }

        exit();
    } catch (Exception $e) {
        echo "Registrasi gagal: " . $e->getMessage();
    }
}
?>
