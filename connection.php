<?php
$servername = "localhost"; // Change this if your MySQL server is on a different host
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$database = "campus_timetable1"; // Replace with the name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Redirect to dashboard.php upon successful connection
    // Make sure to exit after the redirect to prevent further execution
} // For testing purposes, remove this line in production
?>