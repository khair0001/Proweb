<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
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
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="header-left">
        <div class="logo">
          <a href="/proweb reshina/pages/home.php"><h1>RESHINA</h1></a>
        </div>
      </div>
      
      <div class="search-bar">
        <form action="search.php" method="GET">
          <input type="text" name="query" placeholder="Cari barang..." class="search-input" required>
          <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>
      </div>
      
      <div class="user-menu">
        <a href="unggah-barang.php" class="upload-btn"><i class="fas fa-plus"></i> <span>Pasang Barang</span></a>
        <div class="notification-wrapper">
          <a href="notifikasi.php" class="notification-btn"><i class="fas fa-bell"></i></a>
          <span class="notification-badge">2</span>
        </div>
        <div class="user-dropdown">
          <div class="user-info">
            <img src="<?php echo($profile_pic); ?>" alt="User Avatar" class="user-avatar">
            <span class="user-name"><?php echo($username); ?></span>
          </div>
          <div class="dropdown-content">
            <a href="/proweb reshina/pages/profil.php"><i class="fas fa-user"></i><?php echo($username); ?></a>
            <a href="/proweb reshina/index.php"><i class="fas fa-history"></i> dashboard</a>
            <a href="/proweb reshina/Include/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </header>