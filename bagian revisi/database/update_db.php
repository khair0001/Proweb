<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "reshina_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL queries to update the database structure
$queries = [
    // Add additional fields to users table for profile information
    "ALTER TABLE users 
    ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS profile_pic VARCHAR(255) DEFAULT '../assets/image/adit.jpg'",
    
    // Create the profile directory if it doesn't exist
    "CREATE TABLE IF NOT EXISTS user_profile_pics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

// Execute each query
$success = true;
$errors = [];

foreach ($queries as $query) {
    if (!mysqli_query($conn, $query)) {
        $success = false;
        $errors[] = mysqli_error($conn);
    }
}

// Check if profile_image column exists and rename it to profile_pic
$checkColumnQuery = "SHOW COLUMNS FROM users LIKE 'profile_image'";
$columnResult = mysqli_query($conn, $checkColumnQuery);

if (mysqli_num_rows($columnResult) > 0) {
    // profile_image column exists, rename it to profile_pic if profile_pic doesn't exist
    $checkNewColumnQuery = "SHOW COLUMNS FROM users LIKE 'profile_pic'";
    $newColumnResult = mysqli_query($conn, $checkNewColumnQuery);
    
    if (mysqli_num_rows($newColumnResult) == 0) {
        // profile_pic doesn't exist, so rename profile_image to profile_pic
        $renameQuery = "ALTER TABLE users CHANGE COLUMN profile_image profile_pic VARCHAR(255) DEFAULT '../assets/image/adit.jpg'";
        if (!mysqli_query($conn, $renameQuery)) {
            $success = false;
            $errors[] = mysqli_error($conn);
        }
    }
}

// Output results
if ($success) {
    echo "Database updated successfully!";
} else {
    echo "Error updating database:<br>";
    foreach ($errors as $error) {
        echo "- " . $error . "<br>";
    }
}

mysqli_close($conn);
?>
