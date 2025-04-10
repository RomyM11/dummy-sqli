<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dummy_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hash_db = $row['password'];

        // CASE 1: Password di database sudah di-hash
        if (password_verify($password_input, $hash_db)) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        }

        // CASE 2: Password belum di-hash (masih mentah)
        else if ($password_input === $hash_db) {
            // Migrasi otomatis: hash password dan update ke database
            $new_hashed = password_hash($password_input, PASSWORD_DEFAULT);

            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $update_stmt->bind_param("ss", $new_hashed, $username);
            $update_stmt->execute();

            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        }

        // CASE 3: Password tidak cocok
        else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login dengan password_verify</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>