<?php
// Include functions file
require_once '../includes/functions.php';

// Logout user
logoutUser();

// Redirect to homepage
header("Location: ../index.php");
exit();
?>
