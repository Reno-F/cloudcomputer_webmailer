<?php
session_start();
include 'firebase_config.php';

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
            // Lanjutkan jika email belum terdaftar
        }

        // Buat akun pengguna di Firebase Authentication
        $userProperties = [
            'email' => $email,
            'password' => $password,
            'emailVerified' => false // Pastikan email belum diverifikasi
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

        // Kirim email verifikasi
        $auth->sendEmailVerification($createdUser);

        echo "Registrasi berhasil! Silakan cek email Anda untuk verifikasi.";
        exit();
    } catch (Exception $e) {
        echo "Registrasi gagal: " . $e->getMessage();
    }
}
?>
