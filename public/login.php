<?php
session_start();
include 'firebase_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Login ke Firebase Authentication
        $signInResult = $auth->signInWithEmailAndPassword($email, $password);
        $user = $signInResult->data();

        // Cek apakah email sudah diverifikasi
        $firebaseUser = $auth->getUserByEmail($email);
        if (!$firebaseUser->emailVerified) {
            echo "Email belum diverifikasi. Silakan cek email Anda.";
            exit();
        }

        // Ambil UID pengguna
        $uid = $signInResult->firebaseUserId();

        // Ambil data user dari Realtime Database
        $userData = $database->getReference('users/'.$uid)->getValue();

        if ($userData) {
            $_SESSION['user'] = $userData;
            $_SESSION['user']['uid'] = $uid;

            header('Location: index.php');
            exit();
        } else {
            echo "User tidak ditemukan di database.";
        }
    } catch (Exception $e) {
        echo "Login gagal: " . $e->getMessage();
    }
}
?>
