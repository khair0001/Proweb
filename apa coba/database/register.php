<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "reshina_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo "Password dan konfirmasi tidak cocok.";
        exit;
    }

    $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/login.php");
    } else {
        echo "Gagal registrasi: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>