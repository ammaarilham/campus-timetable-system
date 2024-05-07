<?php
include 'connection.php';

// Retrieve data from form
$timetable_id = $_POST['timetable_id'];
$department_id = $_POST['department_id'];
$semester = $_POST['semester'];
$slots = $_POST['slots'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$break_duration = $_POST['break_duration'];
$break_time = $_POST['break_time'];
$slot_duration = $_POST['slot_duration'];

// Update data in timetable table
$query = "UPDATE timetable SET Department_ID='$department_id', Semester='$semester', Slots='$slots', Start_Time='$start_time', End_Time='$end_time', Break_Time='$break_time', Break_Duration = '$break_duration', Slot_Duration='$slot_duration' WHERE Timetable_ID='$timetable_id'";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: views_timetables.php"); // Redirect to admin page after successful update
} else {
    echo "Error: " . mysqli_error($conn); // Error handling
}

mysqli_close($conn);
?>