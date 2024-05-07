<?php
include 'connection.php';

// Retrieve data from form
$department_id = $_POST['department_id'];
$batch_id = $_POST['batch_id']; // Add this line
$semester = $_POST['semester'];
$slots = $_POST['slots'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$break_time = $_POST['break_time'];
$break_duration = $_POST['break_duration'];
$slot_duration = $_POST['slot_duration'];

// Insert data into timetable table
$query = "INSERT INTO timetable (Department_ID, Batch_ID, Semester, Slots, Start_Time, End_Time, Break_Time, Break_Duration, Slot_Duration) VALUES ('$department_id', '$batch_id', '$semester', '$slots', '$start_time', '$end_time', '$break_time', '$break_duration', '$slot_duration')";

$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: view_timetables.php"); // Redirect to admin page after successful addition
} else {
    echo "Error: " . mysqli_error($conn); // Error handling
}

mysqli_close($conn);
?>