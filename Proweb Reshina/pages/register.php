<?php
// Include koneksi ke database
include '../include/koneksi.php';

// Jika form registrasi di submit
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Cek apakah password dan konfirmasi sama
    if ($password !== $confirmPassword) {
        echo "Password dan konfirmasi tidak cocok.";
        exit;
    }

    // Enkripsi password menggunakan md5
    $password = md5($password);

    // Buat query untuk menambahkan data user ke database
    $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

    // Jalankan query, jika berhasil redirect ke halaman login
    if (mysqli_query($conn, $query)) {
        header("Location: login.php");
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal registrasi: " . mysqli_error($conn);
    }
}

// Tutup koneksi ke database
mysqli_close($conn);
?>