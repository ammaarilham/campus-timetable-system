<?php
include 'connection.php';

function calculateOverallAttendancePercentage($presentCount, $totalCount) {
    return ($totalCount > 0) ? round(($presentCount / $totalCount) * 100, 2) : 0;
}

$departmentAttendanceQuery = "SELECT COUNT(*) AS present_count
                               FROM attendance
                               WHERE department_name = 'Your_Department_Name'";

$batchAttendanceQuery = "SELECT COUNT(*) AS present_count
                         FROM attendance
                         WHERE batch_name = 'Your_Batch_Name'";

$departmentAttendanceResult = mysqli_query($conn, $departmentAttendanceQuery);
$batchAttendanceResult = mysqli_query($conn, $batchAttendanceQuery);


$totalDepartmentLectureDates = 15; 
$totalBatchLectureDates = 15; 


// Query to fetch all departments
$departmentQuery = "SELECT Department_Name FROM department";
$departmentResult = mysqli_query($conn, $departmentQuery);

// Initialize variables to store total present count and lecture date count for all departments
$totalPresentCountAllDepartments = 0;
$totalLectureDateCountAllDepartments = 0;
?>

<?php
session_start();
include 'connection.php';


if (isset($_SESSION['admin_username'])) {
    
    $username = $_SESSION['admin_username'];

    
    $query = "SELECT * FROM admin WHERE Username='$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
 
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
    <title>View Attendance</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/view_attendance.css">
    <link rel="stylesheet" href="css/styles.css">


</head>

<body>

    <section class="wrapper-batch">
        <section class="navbar">
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
        </section>

        <section class="section1">
            <h2 class="attendance-heading">Attendance section</h2>

            <?php

            echo "<h2 class='common-h2'> Overall department Attendance average </h2>";

            // Start a container for department averages
            echo "<div class='department-averages-container'>";

            while ($department = mysqli_fetch_assoc($departmentResult)) {
                $departmentName = $department['Department_Name'];

                // Query to fetch overall department attendance
                $departmentAttendanceQuery = "SELECT COUNT(*) AS present_count
                                            FROM attendance
                                            WHERE department_name = '$departmentName'";
                $departmentAttendanceResult = mysqli_query($conn, $departmentAttendanceQuery);
                $departmentAttendanceRow = mysqli_fetch_assoc($departmentAttendanceResult);
                $presentCount = $departmentAttendanceRow['present_count'];

                // Query to fetch total number of lecture dates for each department
                $totalLectureDatesQuery = "SELECT COUNT(DISTINCT lecture_date) AS lecture_count
                                            FROM attendance
                                            WHERE department_name = '$departmentName'";
                $totalLectureDatesResult = mysqli_query($conn, $totalLectureDatesQuery);
                $totalLectureDatesRow = mysqli_fetch_assoc($totalLectureDatesResult);
                $totalLectureDateCount = $totalLectureDatesRow['lecture_count'];

                // Calculate overall attendance percentage for the current department
                $attendancePercentage = calculateOverallAttendancePercentage($presentCount, $totalLectureDateCount);

                // Display department average in a circle
                echo "<div class='department-average-circle'>";
                echo "<span>{$attendancePercentage}%</span>";
                echo "</div>";
            }

            // End container for department averages
            echo "</div>";



            ?>




            <h2 class='common-h2'>Overall Department Attendance</h2>
            <?php
            // Query to fetch overall department attendance
            $departmentAttendanceQuery = "SELECT department_name, COUNT(*) AS present_count
                                    FROM attendance
                                    GROUP BY department_name";
            $departmentAttendanceResult = mysqli_query($conn, $departmentAttendanceQuery);
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Department Name</th>
                        <th>Present Count</th>
                        <th>Overall Attendance Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($departmentAttendanceResult)) {
                        $departmentName = $row['department_name'];
                        $presentCount = $row['present_count'];
                        $attendancePercentage = calculateOverallAttendancePercentage($presentCount, $totalLectureDateCountAllDepartments);
                        echo "<tr>";
                        echo "<td>{$departmentName}</td>";
                        echo "<td>{$presentCount}</td>";
                        echo "<td>{$attendancePercentage}%</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <h2 class='common-h2'>Overall Batch Attendance</h2>
            <?php
            // Calculate overall batch attendance percentage
            $batchAttendanceRow = mysqli_fetch_assoc($batchAttendanceResult);
            $batchAttendancePercentage = calculateOverallAttendancePercentage($batchAttendanceRow['present_count'], $totalBatchLectureDates);

            // Display overall batch attendance percentage
            echo "<p>Overall Batch Attendance Percentage: {$batchAttendancePercentage}%</p>";
            ?>

            <h2 class='common-h2'>Each Student's Overall Attendance under Each Batch</h2>
            <form method="post">
                <!-- Dropdown menu to select batch -->
                <select name="batch_name">
                    <?php
                    // Fetch batch names from the batch table and populate the dropdown menu
                    $batchQuery = "SELECT Batch_Name FROM batch";
                    $batchResult = mysqli_query($conn, $batchQuery);
                    while ($batch = mysqli_fetch_assoc($batchResult)) {
                        echo "<option value=\"{$batch['Batch_Name']}\">{$batch['Batch_Name']}</option>";
                    }
                    ?>
                </select>
                <button type="submit">View Attendance</button>
            </form>

            <?php
            // If batch is selected, display student-wise overall attendance
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['batch_name'])) {
                $selectedBatch = $_POST['batch_name'];

                // Query to fetch student-wise attendance for the selected batch
                $studentAttendanceQuery = "SELECT student_id, COUNT(*) AS present_count
                                           FROM attendance
                                           WHERE batch_name = '$selectedBatch'
                                           GROUP BY student_id";
                $studentAttendanceResult = mysqli_query($conn, $studentAttendanceQuery);

                // Display student-wise overall attendance
                ?>
            <h2 class='common-h2'>Student-wise Overall Attendance for Batch <?php echo $selectedBatch; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Overall Attendance Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        while ($studentRow = mysqli_fetch_assoc($studentAttendanceResult)) {
                            $studentAttendancePercentage = calculateOverallAttendancePercentage($studentRow['present_count'], $totalBatchLectureDates);
                            echo "<tr>";
                            echo "<td>{$studentRow['student_id']}</td>";
                            echo "<td>{$studentAttendancePercentage}%</td>";
                            echo "</tr>";
                        }
                        ?>
                </tbody>
            </table>
            <?php
            }
            ?>
        </section>
    </section>
    <div class="footer">
        <div class="footer-content">
            <div class="left-section">
                <p>Designed and built by <a href="https://ammaarsportfolio.vercel.app">Ammaar Ilham</a></p>
                <p>Contact: <a href="mailto:ammaarilham2056@gmail.com">ammaarilham2056@gmail.com</a></p>
            </div>
            <div class="right-section">
                <img src="images/bcaslogo.png" alt="Campus Logo" width="70">
            </div>
        </div>
    </div>
</body>

</html>