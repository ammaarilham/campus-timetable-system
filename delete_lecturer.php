<?php
include 'connection.php';

// Retrieve lecturer ID from URL parameter
$lecturer_id = $_GET['lecturer_id'];

// Delete lecturer from the table
$query = "DELETE FROM lecturer WHERE Lecturer_ID='$lecturer_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: lecturer_page.php"); // Redirect to admin page after successful deletion
} else {
    echo "Error: " . mysqli_error($conn); // Error handling
}

mysqli_close($conn);
?>