<?php
session_start();
$host = "localhost";
$user = "root"; // Sesuaikan dengan user MySQL Anda
$pass = ""; // Jika ada password MySQL, isi di sini
$dbname = "dummy_db";

// Koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Cek jika sudah login
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

// Cek jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query yang rentan terhadap SQL Injection
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Login Gagal. Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Dummy (Vulnerable to SQL Injection)</title>
</head>
<body>
    <h2>Login Form (Rentan SQL Injection)</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username"><br><br>
        <label>Password:</label>
        <input type="password" name="password"><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
