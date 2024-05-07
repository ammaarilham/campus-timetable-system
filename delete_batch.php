<?php
// Include database connection file
include 'connection.php';

// Check if batch ID is provided
if (isset($_GET['batch_id'])) {
    $batch_id = $_GET['batch_id'];

    // Delete batch record from database
    $delete_query = "DELETE FROM batch WHERE Batch_ID=$batch_id";
    
    try {
        if (mysqli_query($conn, $delete_query)) {
            // Deletion successful, redirect to batch records page
            mysqli_close($conn);
            echo "<script>alert('Batch deleted successfully.'); window.location.href='batch_page.php';</script>";
            exit();
        } else {
            // Error occurred
            mysqli_close($conn);
            echo "<script>alert('Error deleting batch. Please try again later.'); window.location.href='batch_page.php';</script>";
            exit();
        }
    } catch (mysqli_sql_exception $exception) {
        // Handle foreign key constraint error
        mysqli_close($conn);
        echo "<script>alert('Cannot delete batch. It may be associated with other records.'); window.location.href='batch_page.php';</script>";
        exit();
    }
} else {
    // Redirect to batch records page if batch ID is not provided
    header("Location: batch_page.php");
    exit();
}
?>