<?php
session_start();
include 'connection.php';

// Check if lecturer is logged in, if not, redirect to login page
if (!isset($_SESSION['lecturer_username'])) {
    header("Location: index.php");
    exit;
}

// Retrieve the lecturer's details
$lecturer_username = $_SESSION['lecturer_username'];

// Query to get the lecturer's details
$lecturer_query = "SELECT * FROM lecturer WHERE Username = '$lecturer_username'";
$lecturer_result = mysqli_query($conn, $lecturer_query);

if (!$lecturer_result || mysqli_num_rows($lecturer_result) != 1) {
    // Handle error if lecturer details are not found
    echo "Error retrieving lecturer details.";
    exit;
}

$lecturer_row = mysqli_fetch_assoc($lecturer_result);
$lecturer_id = $lecturer_row['Lecturer_ID'];

// Retrieve the current class details
$current_class_query = "SELECT * FROM timetable_entry WHERE Lecturer_ID = $lecturer_id 
                        AND Day = DATE_FORMAT(NOW(), '%W') 
                        AND Start_Time <= NOW() 
                        AND End_Time > NOW()";
$current_class_result = mysqli_query($conn, $current_class_query);

if (!$current_class_result || mysqli_num_rows($current_class_result) != 1) {
    // Handle error if current class details are not found
    echo "Error retrieving current class details.";
    exit;
}

$current_class_row = mysqli_fetch_assoc($current_class_result);
$class_id = $current_class_row['Classroom_ID'];

// Process suggestion submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['suggestion_text'])) {
    $suggestion_text = $_POST['suggestion_text'];

    // Insert the suggestion into the database
    $insert_query = "INSERT INTO suggestions (Lecturer_ID, Class_ID, Suggestion_Text) VALUES ('$lecturer_id',$class_id, '$suggestion_text')";
    $insert_result = mysqli_query($conn, $insert_query);

    if ($insert_result) {
        echo "Suggestion submitted successfully.";
        header("Location:lecturer_login.php");
    } else {
        echo "Error submitting suggestion.";
        header("Location:lecturer_login.php");

        
    }
}
?>