<?php
include 'connection.php';

// Retrieve timetable ID from URL parameter
$timetable_id = $_GET['timetable_id'];

// Delete timetable entries first
$delete_entries_query = "DELETE FROM timetable_entry WHERE Timetable_ID='$timetable_id'";
$entries_result = mysqli_query($conn, $delete_entries_query);

if ($entries_result || mysqli_affected_rows($conn) == 0) {
    // Delete timetable from the table
    $delete_timetable_query = "DELETE FROM timetable WHERE Timetable_ID='$timetable_id'";
    $timetable_result = mysqli_query($conn, $delete_timetable_query);

    if ($timetable_result) {
        mysqli_close($conn);
        echo "<script>alert('Timetable and related entries deleted successfully.'); window.location.href='view_timetables.php';</script>";
    } else {
        mysqli_close($conn);
        echo "<script>alert('Error deleting timetable. Please try again later.'); window.location.href='view_timetables.php';</script>";
        exit();
    }
} else {
    mysqli_close($conn);
    echo "<script>alert('Error deleting timetable entries. Please try again later.'); window.location.href='view_timetables.php';</script>";
    exit();
}
?>