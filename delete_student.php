<?php
include 'connection.php';

// Retrieve student ID from URL parameter
$student_id = $_GET['student_id'];

// Delete student from the table
$query = "DELETE FROM student WHERE Student_ID='$student_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: student_page.php"); // Redirect to admin page after successful deletion
} else {
    echo "Error: " . mysqli_error($conn); // Error handling
}

mysqli_close($conn);
?>