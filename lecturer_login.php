<?php
session_start();
include 'connection.php';

date_default_timezone_set('Asia/Colombo');

$current_datetime = date('Y-m-d H:i:s');

if (!isset($_SESSION['lecturer_username'])) {
    header("Location: index.php");
    exit;
}

$lecturer_username = $_SESSION['lecturer_username'];

$lecturer_query = "SELECT * FROM lecturer WHERE Username = '$lecturer_username'";
$lecturer_result = mysqli_query($conn, $lecturer_query);

if (!$lecturer_result || mysqli_num_rows($lecturer_result) != 1) {
    echo "Error retrieving lecturer details.";
    exit;
}

$lecturer_row = mysqli_fetch_assoc($lecturer_result);
$lecturer_id = $lecturer_row['Lecturer_ID'];

$current_datetime = date('Y-m-d H:i:s');

$current_class_query = "SELECT te.*, b.Batch_Name, m.Module_Name, c.Room_Number
                        FROM timetable_entry te
                        JOIN batch b ON te.Batch_ID = b.Batch_ID
                        JOIN module m ON te.Module_ID = m.Module_ID
                        JOIN classroom c ON te.Classroom_ID = c.Classroom_ID
                        WHERE te.Lecturer_ID = $lecturer_id
                        AND te.Day = DATE_FORMAT(NOW(), '%W')
                        AND te.Start_Time <= '$current_datetime'
                        AND te.End_Time > '$current_datetime'";

$current_class_result = mysqli_query($conn, $current_class_query);

$current_class = mysqli_fetch_assoc($current_class_result);

$upcoming_classes_query = "SELECT te.*, b.Batch_Name, m.Module_Name, c.Room_Number
                           FROM timetable_entry te
                           JOIN batch b ON te.Batch_ID = b.Batch_ID
                           JOIN module m ON te.Module_ID = m.Module_ID
                           JOIN classroom c ON te.Classroom_ID = c.Classroom_ID
                           WHERE te.Lecturer_ID = $lecturer_id
                           AND te.Day >= DATE_FORMAT(NOW(), '%W')
                           AND te.Start_Time > '$current_datetime'
                           ORDER BY te.Day, te.Start_Time
                           LIMIT 5";

$upcoming_classes_result = mysqli_query($conn, $upcoming_classes_query);

$current_batch_name = "";

if ($current_class) {
    $current_batch_name = $current_class['Batch_Name'];
}

if ($current_batch_name != "") {
    $batch_id_query = "SELECT Batch_ID FROM batch WHERE Batch_Name = '$current_batch_name'";
    $batch_id_result = mysqli_query($conn, $batch_id_query);

    if ($batch_id_result && mysqli_num_rows($batch_id_result) > 0) {
        $batch_row = mysqli_fetch_assoc($batch_id_result);
        $current_batch_id = $batch_row['Batch_ID'];

        $students_query = "SELECT * FROM student WHERE Batch_ID = $current_batch_id";
        $students_result = mysqli_query($conn, $students_query);
    }
}


// Check if attendance has been submitted
$attendance_submitted = !empty($_POST['attendance']);

if ($attendance_submitted) {
    // Attendance already submitted, disable form elements
    $disable_form_elements = 'disabled';
} else {
    $disable_form_elements = ''; // No need to disable form elements
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_description'])) {
    $username = $_SESSION['lecturer_username'];
    $task_description = $_POST['task_description'];

    $insert_query = "INSERT INTO todo_list_tasks (username, task_description) VALUES ('$username', '$task_description')";
    mysqli_query($conn, $insert_query);
}

if (isset($_POST['delete_task_id'])) {
    $delete_task_id = $_POST['delete_task_id'];
    $delete_query = "DELETE FROM todo_list_tasks WHERE task_id = $delete_task_id";
    mysqli_query($conn, $delete_query);
}

$username = $_SESSION['lecturer_username'];
$get_tasks_query = "SELECT * FROM todo_list_tasks WHERE username = '$username'";
$tasks_result = mysqli_query($conn, $get_tasks_query);




// Function to display custom alert messages
function displayAlert($message, $type) {
    echo "<script>";
    echo "alert('$message');";
    echo "</script>";
}

// Check if there is a success or error message in the URL query parameters
if (isset($_GET['success'])) {
    $successMessage = $_GET['success'];
    displayAlert($successMessage, 'success');
} elseif (isset($_GET['error'])) {
    $errorMessage = $_GET['error'];
    displayAlert($errorMessage, 'error');
}   
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/lecturer_login.css">
</head>

<body>
    <div class="wrapper">
        <div class="col">
            <div class="class-wrapper">
                <h1>Welcome, <?php echo $lecturer_row['Name']; ?>!</h1>
                <h2>Current Class:</h2>
                <?php
                if ($current_class) {
                    echo "<div  class='current_class_div'>";
                    echo "<p>Module: " . $current_class['Module_Name'] . "</p>";
                    echo "<p>Batch: " . $current_class['Batch_Name'] . "</p>";
                    echo "<p>Room: " . $current_class['Room_Number'] . "</p>";
                    echo "</div>";
                } else {
                    echo "<p class='no_current_class_div'>No current class.</p>";
                }
                ?>
                <h2>Upcoming Classes:</h2>
                <ul>
                    <?php
                    while ($row = mysqli_fetch_assoc($upcoming_classes_result)) {
                        echo "<li class='upcoming_class_div'>";
                        echo "<p>Module: " . $row['Module_Name'] . "</p>";
                        echo "<p>Batch: " . $row['Batch_Name'] . "</p>";
                        echo "<p>Room: " . $row['Room_Number'] . "</p>";
                        echo "</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="col1">
            <div class="batch-wrapper">
                <?php if (isset($students_result) && mysqli_num_rows($students_result) > 0) : ?>
                <h2>Students List for Batch <?php echo $current_batch_name; ?>:</h2>
                <form method="post" action="save_attendance.php">
                    <input type="hidden" name="batch_name" value="<?php echo $current_batch_name; ?>">
                    <input type="hidden" name="module_start_time" value="<?php echo $current_class['Start_Time']; ?>">
                    <input type="hidden" name="department_id" value="<?php echo $selected_department_id; ?>">
                    <ul>
                        <?php
                            while ($student = mysqli_fetch_assoc($students_result)) {
                                echo "<div class='student_result_div'>";
                                echo "<span>" . $student['Name'] . "</span>";
                                echo "<label><input type='checkbox' name='attendance[]' value='" . $student['Student_ID'] . "' $disable_form_elements></label>";
                                echo "</div>";
                            }
                            ?>
                    </ul>
                    <button class="attendance_btn" type="submit" <?php echo $disable_form_elements; ?>>Submit
                        Attendance</button>
                </form>
                <?php else : ?>
                <p class="no_current_batch_div">No students found for the current batch.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col">
            <div class="todo-list">
                <h1>Todo List</h1>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="task_description" placeholder="Enter task description" required>
                    <button type="submit">Add Task</button>
                </form>
                <ul>
                    <?php while ($row = mysqli_fetch_assoc($tasks_result)) : ?>
                    <li>
                        <span><?php echo $row['task_description']; ?></span>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="delete_task_id" value="<?php echo $row['task_id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="suggestion-box-container">
                <div class="suggestion-box">
                    <h1 class="suggestion-box-title">Suggestion Box</h1>
                    <form class="suggestion-form" method="post" action="submit_suggestion.php">
                        <textarea class="suggestion-textarea" name="suggestion_text" rows="4" cols="50"
                            placeholder="Enter your suggestion here..." required></textarea>
                        <br>
                        <button class="submit-button" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</body>

</html>