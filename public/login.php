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
            $message = "Email belum diverifikasi. Silakan cek email Anda.";
        } else {
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
                $message = "User tidak ditemukan di database.";
            }
        }
    } catch (Exception $e) {
        $message = "Login gagal: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; }
        .container { max-width: 400px; background: white; padding: 20px; margin: auto; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: green; color: white; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($message)) echo "<p>$message</p>"; ?>
        <form method="POST">
            <input type="email" name="email" required placeholder="Email">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit">Login</button>
        </form>
        <a href="register.php">Belum punya akun? Register</a>
    </div>
</body>
</html>
