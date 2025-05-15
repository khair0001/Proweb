<?php
session_start();
session_destroy();

header("Location: /proweb reshina/pages/login.php");
exit;
?>