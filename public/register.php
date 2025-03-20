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

        $message = "Registrasi berhasil! Silakan cek email Anda untuk verifikasi sebelum login.";
    } catch (Exception $e) {
        $message = "Registrasi gagal: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; }
        .container { max-width: 400px; background: white; padding: 20px; margin: auto; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: blue; color: white; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (isset($message)) echo "<p>$message</p>"; ?>
        <form action="register.php" method="POST">
            <input type="email" name="email" required placeholder="Email">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit">Register</button>
        </form>
        <a href="login.php">Sudah punya akun? Login</a>
    </div>
</body>
</html>
