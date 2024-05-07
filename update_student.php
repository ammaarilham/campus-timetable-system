<?php
include 'connection.php';

// Retrieve data from form
$student_id = $_POST['student_id'];
$name = $_POST['name'];
$zoho_number = $_POST['zoho_number'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$batch_id = $_POST['batch']; // Assuming batch_id is submitted from the form

// Update data in student table
$query = "UPDATE student SET Name='$name', Zoho_Number='$zoho_number', Email='$email', Phone='$phone', Batch_ID='$batch_id' WHERE Student_ID='$student_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: student_page.php"); // Redirect to admin page after successful update
} else {
    echo "Error: " . mysqli_error($conn); // Error handling
}

mysqli_close($conn);
?>