<?php
include 'connection.php';

if(isset($_GET['slot'])) {
    $slot = $_GET['slot'];

    // Assuming $slot is a string, so it needs to be properly escaped in the query
    $escaped_slot = mysqli_real_escape_string($conn, $slot);

    $query = "SELECT * FROM timetable_entry WHERE Slot = '$escaped_slot'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        $entryDetails = mysqli_fetch_assoc($result);
        echo json_encode($entryDetails);
    } else {
        echo json_encode(array()); // Return empty array if no entry found
    }
} else {
    echo json_encode(array()); // Return empty array if slot parameter not provided
}
?>