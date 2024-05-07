<?php
session_start();
include 'connection.php';

// Check if lecturer is logged in, if not, redirect to login page
if (!isset($_SESSION['lecturer_username'])) {
    header("Location: index.php");
    exit;
}

$module_start_time = $_POST['module_start_time'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['batch_name']) && isset($_POST['attendance'])) {
    $batch_name = $_POST['batch_name'];
    $selected_students = $_POST['attendance'];
    
    // Get the current date and time
    $lecture_date = date('Y-m-d H:i:s');

    // Dynamically construct the name of the attendance table
    $attendance_table_name = $batch_name . "_attendance";

    // Insert attendance records into the database table
    foreach ($selected_students as $student_id) {
        $student_id = intval($student_id); // Convert to integer to prevent SQL injection
        
        // Prepare and execute the SQL query to insert attendance record
        $insert_query = "INSERT INTO $attendance_table_name (student_id, status, lecture_date) VALUES ($student_id, 1, '$lecture_date')";
        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Attendance records saved successfully!";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }

    // Redirect back to the dashboard or wherever appropriate
    header("Location: lecturer_login.php");
    exit;
} else {
    // If the form was not submitted properly, redirect back to the dashboard
    header("Location: lecturer_login.php");
    exit;
}
?>