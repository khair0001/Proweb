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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>
  <div class="container">
    <div class="left">
      <h1>Sign Up</h1>
      <p>Register to access your account.</p>

      <form id="registerForm" action="register.php" method="POST">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" placeholder="Khair" required>

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="khair@gmail.com" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required>

        <button type="submit" name="submit"class="login-btn">Register</button>
      </form>

      <div class="divider">or sign up with</div>
      <div class="social-buttons">
        <button type="button" class="google-btn">
          <img src="https://img.icons8.com/color/48/google-logo.png" alt="Google" /> Sign in with Google
        </button>
        <button type="button" class="apple-btn">
          <img src="https://img.icons8.com/ios-filled/50/000000/mac-os.png" alt="Apple" /> Sign in with Apple
        </button>
      </div>

      <div class="signup">
        Already have an account? <a href="login.php">Login</a>
      </div>
    </div>

    <div class="right">
      <img src="../assets/image/sepatu-removebg-preview.png" alt="Sneaker" class="shoe-img">
    </div>
  </div>

  <script src="../assets/js/script.js"></script>
</body>
</html>

// Tutup koneksi ke database
<?php
mysqli_close($conn);
?>