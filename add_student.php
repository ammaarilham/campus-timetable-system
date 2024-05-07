<?php
include 'connection.php';

// Retrieve data from form
$name = $_POST['name'];
$zoho_number = $_POST['zoho_number'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$batch_id = $_POST['batch_id']; 
// Insert data into student table
$query = "INSERT INTO student (Name, Zoho_Number, Email, Phone, Batch_ID) VALUES ('$name', '$zoho_number', '$email', '$phone', '$batch_id')";
$result = mysqli_query($conn, $query);

if ($result) {
    // Retrieve batch details
    $batch_query = "SELECT * FROM batch WHERE Batch_ID = $batch_id";
    $batch_result = mysqli_query($conn, $batch_query);
    $batch_row = mysqli_fetch_assoc($batch_result);
    
    if ($batch_row) {
        // Construct attendance table name
        $attendance_table_name = $batch_row['Batch_Name'] . "_attendance"; // Assuming Batch_Name is the column name in the batch table

        // Insert student record into batch attendance table
        $insert_attendance_query = "INSERT INTO $attendance_table_name (student_id) VALUES (LAST_INSERT_ID())"; // Assuming student_id is the primary key in the student table
        $insert_attendance_result = mysqli_query($conn, $insert_attendance_query);

        if ($insert_attendance_result) {
            header("Location: student_page.php"); // Redirect to admin page after successful addition
        } else {
            echo "Error: " . mysqli_error($conn); // Error handling
        }
    } else {
        echo "Batch details not found";
    }
} else {
    echo "Error: " . mysqli_error($conn); // Error handling
}

mysqli_close($conn);
?>