<?php
// Include database connection file
include 'connection.php';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $batch_name = $_POST['batch_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $course_title = $_POST['course_title'];

    // Insert into database
    $insert_query = "INSERT INTO batch (Batch_Name, Start_Date, End_Date,Course_Title) VALUES ('$batch_name', '$start_date', '$end_date','$course_title')";
    if (mysqli_query($conn, $insert_query)) {
        // Insert successful, create a table for batch attendance
        $attendance_table_name = $batch_name . "_attendance";
        $create_table_query = "CREATE TABLE $attendance_table_name (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            student_id INT(10) NOT NULL
        )";

        if (mysqli_query($conn, $create_table_query)) {
            // Table creation successful, redirect to batch records page
            header("Location: batch_page.php");
            exit();
        } else {
            // Error occurred while creating the table
            echo "Error creating table: " . mysqli_error($conn);
        }
    } else {
        // Error occurred
        echo "Error: " . mysqli_error($conn);
    }
}
?>