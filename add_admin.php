<?php
// Include database connection file
include 'connection.php';

// Retrieve form data
$name = $_POST['name'];
$username = $_POST['username'];
$password = $_POST['password']; // Password should be hashed for security, consider using password_hash() function
$email = $_POST['email'];
$phone = $_POST['phone'];

// Insert data into database
$query = "INSERT INTO admin (Name, Username, Password, Email, Phone) VALUES ('$name', '$username', '$password', '$email', '$phone')";
if(mysqli_query($conn, $query)) {
    // Redirect to admin page with success message
    header("Location: admin_page.php?status=success");
    exit();
} else {
    // Redirect to admin page with error message
    header("Location: admin_page.php?status=error");
    exit();
}
?>