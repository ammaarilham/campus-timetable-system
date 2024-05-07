<?php
session_start();

// Check if the timetable_id parameter is set in the URL
if(isset($_GET['timetable_id'])) {
    // Set the session variable with the timetable ID
    $_SESSION['timetable_id'] = $_GET['timetable_id'];

    // Redirect to the edit_timetable.php page
    header("Location: edit_timetable.php");
    exit(); // Ensure that no other code is executed after redirection
} else {
    // If timetable_id is not set, handle the error accordingly
    echo "Timetable ID is not set in the URL.";
}
?>