<?php
include 'include/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Tambahkan variabel default untuk foto profil
$profile_pic = "../assets/image/user.png"; // Default profile image

// Jika perlu, ambil foto profil dari database
$query = "SELECT profile_pic FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) === 1) {
    $userData = mysqli_fetch_assoc($result);
    if (isset($userData['profile_pic']) && !empty($userData['profile_pic'])) {
        $profile_pic = $userData['profile_pic'];
    }
}
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reshina - Beranda</title>
  <!-- <link rel="stylesheet" href="../assets/css/styles.css"> -->
  <link rel="stylesheet" href="../assets/css2/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="logo">
        <h1>RESHINA</h1>
      </div>
      <div class="search-bar">
        <form action="search.php" method="GET">
          <input type="text" name="keyword" placeholder="Cari barang bekas berkualitas...">
          <button type="submit"><i class="fas fa-search"></i></button>
        </form>
      </div>
      <div class="user-menu">
        <a href="unggah-barang.php" class="upload-btn"><i class="fas fa-plus"></i> <span>Pasang Barang</span></a>
        <a href="notifikasi.php" class="notification-btn"><i class="fas fa-bell"></i></a>
        <div class="user-dropdown">
          <img src="<?php echo($profile_pic); ?>" alt="User Avatar" class="user-avatar">
          <div class="dropdown-content">
            <a href="profil.php"><i class="fas fa-user"></i><?php echo($username); ?></a>
            <a href="barang-saya.php"><i class="fas fa-box"></i> Barang Saya</a>
            <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat Transaksi</a>
            <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </header>
