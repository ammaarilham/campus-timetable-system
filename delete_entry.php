<?php
// Include the database connection file
include 'connection.php';

// Check if the slot identifier is provided via GET request
if (isset($_GET['slot'])) {
    // Get the slot identifier
    $slot_identifier = $_GET['slot'];

    // Construct the SQL query to delete the timetable entry
    $delete_query = "DELETE FROM timetable_entry WHERE Slot = '$slot_identifier'";

    // Execute the query
    if (mysqli_query($conn, $delete_query)) {
        // Return success message
        echo "Timetable entry deleted successfully";
    } else {
        // Return error message
        echo "Error deleting timetable entry: " . mysqli_error($conn);
    }
} else {
    // Return error message if slot identifier is not provided
    echo "Slot identifier not provided";
}
?>