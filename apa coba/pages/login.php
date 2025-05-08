<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "reshina_db";

$conn = mysqli_connect($host, $user, $pass, $db);

// Initialize error message variable
$error_message = "";

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic input validation
    if (empty($email) || empty($password)) {
        $error_message = "Email dan password harus diisi.";
    } else {
        // Prevent SQL injection
        $email = mysqli_real_escape_string($conn, $email);
        
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $userData = mysqli_fetch_assoc($result);

            // Password comparison (not hashed in this example)
            if ($password === $userData['password']) {
                // Set session variables
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['name'];
                $_SESSION['email'] = $userData['email'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                
                // Redirect to home page
                header("Location: home.php");
                exit();
            } else {
                $error_message = "Password salah.";
            }
        } else {
            $error_message = "Email tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>
  <link rel="stylesheet" href="../assets/css/styles.css" />
</head>

<body>
  <div class="container">
    <div class="left">
      <h1>Login</h1>
      <p>Enter your email and password to log in</p>

      <form id="loginForm" method="POST" action="">
        <?php if (!empty($error_message)): ?>
          <div style="color: red; margin-bottom: 15px; padding: 10px; background-color: #ffeeee; border-radius: 5px;">
            <?php echo $error_message; ?>
          </div>
        <?php endif; ?>
        
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />

        <div class="password-wrapper">
          <label for="password">
            Password <a href="#" class="forgot">forgot password</a>
          </label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required />
          <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png"
               alt="Show Password"
               class="toggle-password"
               onclick="togglePasswordVisibility('password', this)" />
        </div>

        <div class="privasi">
          <input type="checkbox" id="privasi" required />
          <label for="privasi">I agree to the Terms and Conditions and Privacy Policy.</label>
        </div>

        <button type="submit" name="submit" class="login-btn">Login</button>

        <div class="divider">Or</div>

        <div class="social-buttons">
          <button type="button" class="google-btn">
            <img src="https://img.icons8.com/color/48/google-logo.png" alt="Google" /> Sign in with Google
          </button>
          <button type="button" class="apple-btn">
            <img src="https://img.icons8.com/ios-filled/50/000000/mac-os.png" alt="Apple" /> Sign in with Apple
          </button>
        </div>

        <p class="signup">Don't have an account? <a href="register.html">Sign Up</a></p>
      </form>
    </div>

    <div class="right">
      <img src="../assets/image/sepatu-removebg-preview.png" alt="Nike Shoe" class="shoe-img" />
    </div>
  </div>

  <script src="../assets/js/script.js"></script>
</body>

<?php
// Close the database connection
mysqli_close($conn);
?>