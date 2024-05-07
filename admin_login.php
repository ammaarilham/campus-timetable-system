<?php
session_start();
include 'connection.php'; 

if(isset($_SESSION["timetable_id"])){
    $timetable_id = $_SESSION["timetable_id"] ; 
}
else {
    $timetable_id = 1;
}

// Check if department is selected
if(isset($_POST['department'])) {
    $department = $_POST['department'];
    
    // Fetch timetable IDs based on department
    $timetable_query = "SELECT Timetable_ID FROM timetable WHERE Department_ID = '$department'";
    $timetable_result = mysqli_query($conn, $timetable_query);

    if (!$timetable_result) {
        echo "Error: " . mysqli_error($conn);
        exit;
    } else {
        // Check if timetable data exists
        if(mysqli_num_rows($timetable_result) > 0) {
            // Initialize an array to store all timetable IDs
            $timetable_ids = [];
            while($row = mysqli_fetch_assoc($timetable_result)) {
                $timetable_ids[] = $row['Timetable_ID'];
            }

            // Set session variable
            $_SESSION['selected_department'] = $department;
        } else {
            // Handle case when timetable data does not exist
            $no_timetable_message = "No timetable available for the selected department.";
        }
    }
} else {
    // Redirect to department selection page if not provided
}

// Fetch timetable data based on timetable_ids
if(isset($timetable_ids)) {
    // Initialize an array to store timetable data
    $timetables = [];
    foreach($timetable_ids as $id) {
        $timetable_query = "SELECT * FROM timetable WHERE Timetable_ID = $id";
        $timetable_result = mysqli_query($conn, $timetable_query);

        if (!$timetable_result) {
            echo "Error: " . mysqli_error($conn);
            exit;
        } else {
            $timetables[] = mysqli_fetch_assoc($timetable_result);
        }
    }
}

// Fetch timetable entries for each timetable
if(isset($timetable_ids)) {
    // Initialize an array to store timetable entries by slot for each timetable
    $timetable_entries_by_slot = [];
    foreach($timetable_ids as $id) {
        $timetable_entries_query = "SELECT * FROM timetable_entry WHERE Timetable_ID = $id";
        $timetable_entries_result = mysqli_query($conn, $timetable_entries_query);

        // Store timetable entries by slot for each timetable
        while ($row = mysqli_fetch_assoc($timetable_entries_result)) {
            $timetable_entries_by_slot[$id][$row['Slot']][] = $row;
        }
    }
}

// Fetch data for select options (lecturers, batches, modules, classrooms)
$lecturer_query = "SELECT Lecturer_ID, Name FROM lecturer";
$lecturer_result = mysqli_query($conn, $lecturer_query);

$batch_query = "SELECT Batch_ID, Batch_Name FROM batch";
$batch_result = mysqli_query($conn, $batch_query);

$module_query = "SELECT Module_ID, Module_Name FROM module";
$module_result = mysqli_query($conn, $module_query);

$classroom_query = "SELECT Classroom_ID, Room_Number FROM classroom";
$classroom_result = mysqli_query($conn, $classroom_query);

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
    <title>Admin login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/admin_login.css">
    <!-- <script src="js/admin_login.js" defer></script> -->

</head>

<body>

    <section class="wrapper">
        <section class="navbar" id="navbar">
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
        </section>

        <section class="section1" id="mainContent">
            <h2>Welcome to admin page</h2>

            <form class="form1" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <select class="department-select" name="department" id="department">
                    <?php
                    // Include your database connection file
                    include 'connection.php';

                    // Query to fetch departments
                    $department_query = "SELECT * FROM department";
                    $department_result = mysqli_query($conn, $department_query);

                    // Check if there are departments
                    if (mysqli_num_rows($department_result) > 0) {
                        // Loop through departments to populate select options
                        while ($row = mysqli_fetch_assoc($department_result)) {
                            $selected = ($row['Department_ID'] == $_POST['department']) ? "selected" : "";
                            echo "<option class='department-option' value='" . $row['Department_ID'] . "' $selected>" . $row['Department_Name'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No departments available</option>";
                    }
                    ?>
                </select>

                <button class="print-button" type="submit" formaction="print_timetable.php">Print Timetable</button>
                <input class="submit-button" type="submit" value="View Timetable">
            </form>

            <?php if(isset($no_timetable_message)) { ?>
            <p><?php echo $no_timetable_message; ?></p>
            <?php } else if(isset($timetables) && !empty($timetables)) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Days of the Week</th>

                        <?php
                // Generate columns for time duration of modules
                $max_slots = max(array_column($timetables, 'Slots'));

                // Generate slot headings based on the maximum number of slots
                $timetable = $timetables[0]; // Assuming the first timetable for simplicity
                $slots = $timetable['Slots'];
                $start_time = strtotime($timetable['Start_Time']);
                $slot_duration = strtotime($timetable['Slot_Duration']) - strtotime('00:00:00');
                $current_time = $start_time;
                for ($i = 0; $i < $max_slots; $i++) {
                    $time_slot = date("h:i A", $current_time);
                    echo "<th>$time_slot</th>";
                    $current_time += $slot_duration;
                }
                ?>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                    foreach ($days as $day) {
                        echo "<tr>";
                        echo "<td>$day</td>";
                        for ($i = 0; $i < $max_slots; $i++) {
                            echo "<td class='timetable-cell'>";
                            foreach($timetables as $timetable) {
                                if(isset($timetable_entries_by_slot[$timetable['Timetable_ID']][$day . '_' . $i])) {
                                    foreach ($timetable_entries_by_slot[$timetable['Timetable_ID']][$day . '_' . $i] as $entry) {
                                        // Retrieve names of lecturer, batch, module, and classroom based on their IDs
                                        $lecturer_id = $entry['Lecturer_ID'];
                                        $batch_id = $entry['Batch_ID'];
                                        $module_id = $entry['Module_ID'];
                                        $classroom_id = $entry['Classroom_ID'];

                                        // Query to fetch lecturer name
                                        $lecturer_query = "SELECT Name FROM lecturer WHERE Lecturer_ID = $lecturer_id";
                                        $lecturer_result = mysqli_query($conn, $lecturer_query);
                                        if ($lecturer_result && mysqli_num_rows($lecturer_result) > 0) {
                                            $lecturer_name = mysqli_fetch_assoc($lecturer_result)['Name'];
                                        } else {
                                            $lecturer_name = "N/A";
                                        }

                                        // Query to fetch batch name
                                        $batch_query = "SELECT Batch_Name FROM batch WHERE Batch_ID = $batch_id";
                                        $batch_result = mysqli_query($conn, $batch_query);
                                        if ($batch_result && mysqli_num_rows($batch_result) > 0) {
                                            $batch_name = mysqli_fetch_assoc($batch_result)['Batch_Name'];
                                        } else {
                                            $batch_name = "N/A";
                                        }

                                        // Query to fetch module name
                                        $module_query = "SELECT Module_Name FROM module WHERE Module_ID = $module_id";
                                        $module_result = mysqli_query($conn, $module_query);
                                        if ($module_result && mysqli_num_rows($module_result) > 0) {
                                            $module_name = mysqli_fetch_assoc($module_result)['Module_Name'];
                                        } else {
                                            $module_name = "N/A";
                                        }

                                        // Query to fetch classroom name
                                        $classroom_query = "SELECT Room_Number FROM classroom WHERE Classroom_ID = $classroom_id";
                                        $classroom_result = mysqli_query($conn, $classroom_query);
                                        if ($classroom_result && mysqli_num_rows($classroom_result) > 0) {
                                            $classroom_name = mysqli_fetch_assoc($classroom_result)['Room_Number'];
                                        } else {
                                            $classroom_name = "N/A";
                                        }

                                        // Display timetable entry with names
                                        echo "<div class='entry'>";
                                        echo "Lecturer:<b> $lecturer_name </b> <br>";
                                        echo "Batch Name:<b> $batch_name </b><br>";
                                        echo "Module Name:<b> $module_name </b><br>";
                                        echo "Classroom:<b> $classroom_name</b> <br>";
                                        echo "</div>";
                                        echo "<hr/>";
                                    }
                                }
                            }
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php } ?>

            <br>

            <div class="banner">
                <h3>View Student's Attendance Analysis</h3>
                <a href="view_attendance.php" class="btn-primary">View Attendance</a>
            </div>
            <br>



            <div class="section2">
                <h2>Module suggestion box</h2>
                <?php
// Assuming you have already established a database connection

// Fetch data from the "suggestions" table
$query = "SELECT * FROM suggestions";
$result = mysqli_query($conn, $query);

// Check if there are any suggestions
if (mysqli_num_rows($result) > 0) {
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Suggestion ID</th>';
    echo '<th>Lecturer ID</th>';
    echo '<th>Class ID</th>';
    echo '<th>Suggestion Text</th>';
    echo '<th>Created At</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // Loop through the suggestions and display them in a table row
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $row['suggestion_id'] . '</td>';
        echo '<td>' . $row['lecturer_id'] . '</td>';
        echo '<td>' . $row['class_id'] . '</td>';
        echo '<td>' . $row['suggestion_text'] . '</td>';
        echo '<td>' . $row['created_at'] . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
} else {
    // If there are no suggestions, display a message
    echo 'No suggestions found.';
}
?>

            </div>

        </section>
    </section>
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