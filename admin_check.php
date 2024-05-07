<?php
session_start();
include 'connection.php'; // Include database connection file

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the form
    $username = $_POST['admin_username'];
    $password = $_POST['admin_password'];

    // Query to check admin credentials in the database
    $query = "SELECT * FROM admin WHERE Username='$username' AND Password='$password'";
    $result = mysqli_query($conn, $query);

    // If a matching record is found, log in the admin
    if (mysqli_num_rows($result) == 1) {
        // Set session variables to indicate admin login
        $_SESSION['admin_username'] = $username;
        // Redirect to the admin dashboard or any other page
        header("Location: admin_login.php");
    } else {
        // Invalid credentials, display error 
        header("Location: index.php");
        echo "Invalid username or password";
    }
}
?>