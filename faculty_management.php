<?php
session_start();
include 'connection.php';

// Get the current day of the week
$current_day = date('l');

// Query to fetch timetable entries for the current day
// Query to fetch timetable entries for the current day and order them by start time
$timetable_entries_query = "SELECT * FROM timetable_entry WHERE Day = '$current_day' ORDER BY Start_Time";
$timetable_entries_result = mysqli_query($conn, $timetable_entries_query);


// Check if the admin is logged in
if (isset($_SESSION['admin_username'])) {
    // Retrieve the admin username from the session
    $username = $_SESSION['admin_username'];

    // Query to fetch admin details based on the username
    $query = "SELECT * FROM admin WHERE Username='$username'";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch admin details
        $admin = mysqli_fetch_assoc($result);
        $adminName = $admin['Name'];
        $adminEmail = $admin['Email'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facutly managment page</title>
    <link rel="stylesheet" href="css/styles.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>
    body {
        font-family: "Poppins", sans-serif !important;
        margin: 0;
        padding: 0;
    }

    .div1 {
        width: 90%;
        margin-bottom: 70px;
        margin: 5px auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    </style>
</head>

<body>
    <?php
    // Include database connection file
    include 'connection.php';

    // Fetch all batch records
    $query = "SELECT * FROM batch";
    $result = mysqli_query($conn, $query);
    ?>

    <div class="wrapper-batch">
        <!-- Navbar -->
        <nav class="navbar">
            <div>
                <div class="col">
                    <div class="col-panel">
                        <img src="images/dummyprofile.png" alt="">

                        <div>
                            <h3><?php echo isset($adminName) ? $adminName : ''; ?></h3>
                            <p><?php echo isset($adminEmail) ? $adminEmail : ''; ?></p>

                        </div>
                    </div>
                </div>
                <hr>
                <div class="col">
                    <ul>
                        <li><a href="admin_login.php">Admin Dashboard</a></li>

                        <li><a href="admin_page.php">Admin Settings</a></li>

                        <li><a href="lecturer_page.php">Lecturer details</a></li>

                        <li><a href="student_page.php">Student details</a></li>

                        <li><a href="batch_page.php">Batch details</a></li>

                        <li><a href="view_timetables.php">Timetable Dashboard</a></li>

                        <li><a href="batch_timetable_view.php">Batch timetables</a></li>

                        <li><a href="faculty_management.php">Facutly daily schedule</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="section1">

            <h2>Today's current schedule</h2>
            <?php

// Check if there are timetable entries for the current day
if ($timetable_entries_result && mysqli_num_rows($timetable_entries_result) > 0) {
    // Display the timetable entries
    echo "<table>";
    echo "<tr><th>Batch </th><th>Module </th><th>Module Time</th><th>Classroom</th></tr>";
    while ($row = mysqli_fetch_assoc($timetable_entries_result)) {
        // $department_id = $row['Department_ID'];
        $batch_id = $row['Batch_ID'];
        $module_id = $row['Module_ID'];
        $classroom_id = $row['Classroom_ID'];
        $start_time = $row['Start_Time'];
        
        // Fetch department name
     

        // Fetch batch name
        $batch_query = "SELECT Batch_Name FROM batch WHERE Batch_ID = '$batch_id'";
        $batch_result = mysqli_query($conn, $batch_query);
        $batch_name = mysqli_fetch_assoc($batch_result)['Batch_Name'];

        // Fetch module name
        $module_query = "SELECT Module_Name FROM module WHERE Module_ID = '$module_id'";
        $module_result = mysqli_query($conn, $module_query);
        $module_name = mysqli_fetch_assoc($module_result)['Module_Name'];

        // Fetch classroom name
        $classroom_query = "SELECT Room_Number FROM classroom WHERE Classroom_ID = '$classroom_id'";
        $classroom_result = mysqli_query($conn, $classroom_query);
        $classroom_name = mysqli_fetch_assoc($classroom_result)['Room_Number'];

        echo "<tr><td>$batch_name</td><td>$module_name</td><td>$start_time</td><td>$classroom_name</td></tr>";
    }
    echo "</table>";
} else {
    echo "No timetable entries for today.";
}

            ?>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="left-section">
                <p>Designed and built by <a href="https://ammaarsportfolio.vercel.app">Ammaar Ilham</a></p>
                <p>Contact: <a href="mailto:ammaarilham2056@gmail.com">ammaarilham2056@gmail.com</a></p>
            </div>
            <div class="right-section">
                <img src="images/bcaslogo.png" alt="Campus Logo" width="70">
            </div>
        </div>
    </footer>


</body>

</html>