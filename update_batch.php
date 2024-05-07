<?php
// Include database connection file
include 'connection.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $batch_id = $_POST['batch_id'];
    $batch_name = $_POST['batch_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Update database
    $update_query = "UPDATE batch SET Batch_Name='$batch_name', Start_Date='$start_date', End_Date='$end_date' WHERE Batch_ID=$batch_id";
    if (mysqli_query($conn, $update_query)) {
        // Update successful, redirect to batch records page
        header("Location: batch_page.php");
        exit();
    } else {
        // Error occurred
        echo "Error: " . mysqli_error($conn);
    }
}
?>