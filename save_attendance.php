<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['lecturer_username'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['batch_name']) && isset($_POST['attendance'])) {
    $batch_name = $_POST['batch_name'];
    $selected_students = $_POST['attendance'];
    $lecture_date = date('Y-m-d');
    $module_start_time = $_POST['module_start_time'];

    // Fetch department name and module name based on the provided batch name from the timetable table
$timetable_query = "SELECT d.Department_Name, m.Module_Name 
FROM timetable_entry te
INNER JOIN timetable t ON te.Timetable_ID = t.Timetable_ID
INNER JOIN department d ON t.Department_ID = d.Department_ID
INNER JOIN batch b ON t.Batch_ID = b.Batch_ID
INNER JOIN module m ON te.Module_ID = m.Module_ID
WHERE b.Batch_Name = '$batch_name' AND te.Start_Time = '$module_start_time'";
$timetable_result = mysqli_query($conn, $timetable_query);
if (!$timetable_result || mysqli_num_rows($timetable_result) == 0) {
header("Location: lecturer_login.php?error=Failed to fetch timetable details");
exit;
}
$timetable_row = mysqli_fetch_assoc($timetable_result);
$department_name = $timetable_row['Department_Name'];
$module_name = $timetable_row['Module_Name'];

    // Fetch lecturer ID based on the logged-in lecturer's username
    $lecturer_username = $_SESSION['lecturer_username'];
    $lecturer_query = "SELECT Lecturer_ID FROM lecturer WHERE Username='$lecturer_username'";
    $lecturer_result = mysqli_query($conn, $lecturer_query);
    if (!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
        header("Location: lecturer_login.php?error=Failed to fetch lecturer details");
        exit;
    }
    $lecturer_row = mysqli_fetch_assoc($lecturer_result);
    $lecturer_id = $lecturer_row['Lecturer_ID'];

    // Fetch department name based on the provided batch name from the timetable table
    $department_query = "SELECT d.Department_Name 
                        FROM timetable_entry te
                        INNER JOIN timetable t ON te.Timetable_ID = t.Timetable_ID
                        INNER JOIN department d ON t.Department_ID = d.Department_ID
                        INNER JOIN batch b ON t.Batch_ID = b.Batch_ID
                        WHERE b.Batch_Name = '$batch_name'";
    $department_result = mysqli_query($conn, $department_query);
    if (!$department_result || mysqli_num_rows($department_result) == 0) {
        header("Location: lecturer_login.php?error=Failed to fetch department details");
        exit;
    }
    $department_row = mysqli_fetch_assoc($department_result);
    $department_name = $department_row['Department_Name'];

    $attendance_table_name = "attendance";

    // Check if the attendance table exists
    $check_table_query = "SHOW TABLES LIKE '$attendance_table_name'";
    $result = mysqli_query($conn, $check_table_query);
    if (mysqli_num_rows($result) == 0) {
        // Create the attendance table if it doesn't exist
        // ...
    }

    // Loop through selected students to update attendance
    foreach ($selected_students as $student_id) {
        $attendance_value = 1; // Assume all selected students are present
        // Insert or update attendance record
        $update_query = "INSERT INTO $attendance_table_name (student_id, department_name, batch_name, lecture_date, module_start_time, attendance_status, lecturer_id, module_name)
                         VALUES ($student_id, '$department_name', '$batch_name', '$lecture_date', '$module_start_time', $attendance_value, $lecturer_id, '$module_name')
                         ON DUPLICATE KEY UPDATE attendance_status = $attendance_value";
        if (!mysqli_query($conn, $update_query)) {
            header("Location: lecturer_login.php?error=Failed to update attendance records");
            exit;
        }
    }

    // Redirect to success page
    header("Location: lecturer_login.php?success=Attendance submitted successfully!");
    exit;
} else {
    header("Location: lecturer_login.php");
    exit;
}
?>