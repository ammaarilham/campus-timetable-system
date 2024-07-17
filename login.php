<?php
session_start();
include 'connection.php';

$email = $_POST['email'];
$password = $_POST['password'];

$lecturer_query = "SELECT * FROM lecturer WHERE Email='$email' AND Password='$password'";
$lecturer_result = mysqli_query($conn, $lecturer_query);

$admin_query = "SELECT * FROM admin WHERE Email='$email' AND Password='$password'";
$admin_result = mysqli_query($conn, $admin_query);

if (mysqli_num_rows($lecturer_result) > 0) {
    // Fetch the lecturer username
    $lecturer_data = mysqli_fetch_assoc($lecturer_result);
    $lecturer_username = $lecturer_data['Username'];
    
    // Set the session variable for lecturer username
    $_SESSION['lecturer_username'] = $lecturer_username;
    
    
    header("Location: lecturer_login.php"); 
    exit();
} elseif (mysqli_num_rows($admin_result) > 0) {
    // Fetch the admin username
    $admin_data = mysqli_fetch_assoc($admin_result);
    $admin_username = $admin_data['Username'];
    
    // Set the session variable for admin username
    $_SESSION['admin_username'] = $admin_username;
    
    header("Location: admin_login.php"); 
    exit();
} else {
    echo "Invalid email or password. Please try again.";
}
?>