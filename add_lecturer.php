<?php
include 'connection.php';

// Retrieve data from form
$name = $_POST['name'];
$username = $_POST['username'];
$password = $_POST['password']; // Assuming you're hashing passwords properly before storing them
$email = $_POST['email'];
$phone = $_POST['phone'];

// Insert data into lecturer table
$query = "INSERT INTO lecturer (Name, Username, Password, Email, Phone) VALUES ('$name', '$username', '$password', '$email', '$phone')";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: lecturer_page.php"); // Redirect to admin page after successful addition
} else {
    echo "Error: " . mysqli_error($conn); // Error handling
}

mysqli_close($conn);
?>