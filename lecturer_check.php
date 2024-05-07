<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username and password from the form
    $lecturer_username = $_POST['lecturer_username'];
    $lecturer_password = $_POST['lecturer_password'];

    // Query to check if the provided credentials match a lecturer record in the database
    $query = "SELECT * FROM lecturer WHERE Username = '$lecturer_username' AND Password = '$lecturer_password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // Lecturer credentials are valid, set session and redirect to lecturer dashboard
        session_start();
        $_SESSION['lecturer_username'] = $lecturer_username;
        header("Location: lecturer_login.php");
        exit; // Always exit after redirection
    } else {
        // Invalid credentials, display error message
        echo "Invalid username or password. Please try again.";
    }
}
?>