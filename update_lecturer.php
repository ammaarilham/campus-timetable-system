<?php
include 'connection.php';

// Retrieve data from form
$lecturer_id = $_POST['lecturer_id'];
$name = $_POST['name'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];

// Update data in lecturer table
$query = "UPDATE lecturer SET Name='$name', Username='$username', Email='$email', Phone='$phone' WHERE Lecturer_ID='$lecturer_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: lecturer_page.php"); // Redirect to admin page after successful update
} else {
    echo "Error: " . mysqli_error($conn); // Error handling
}

mysqli_close($conn);
?>