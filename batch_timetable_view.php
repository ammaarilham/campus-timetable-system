<?php
session_start();
include 'connection.php'; 

if(isset($_SESSION["timetable_id"])){
    $timetable_id = $_SESSION["timetable_id"] ; 
}
else {
    $timetable_id = 1;
}

// Initialize $batch_id variable
$batch_id = '';

// Check if batch is selected
if(isset($_POST['batch'])) {
    $batch_id = $_POST['batch'];

    // Fetch timetable ID based on batch
    $timetable_query = "SELECT Timetable_ID FROM timetable WHERE Batch_ID = '$batch_id'";
    $timetable_result = mysqli_query($conn, $timetable_query);

    if (!$timetable_result) {
        echo "Error: " . mysqli_error($conn);
        exit;
    } else {
        // Check if timetable data exists
        if(mysqli_num_rows($timetable_result) > 0) {
            $timetable_data = mysqli_fetch_assoc($timetable_result);
            $timetable_id = $timetable_data['Timetable_ID'];
            
            // Set session variable
            $_SESSION['selected_timetable_id'] = $timetable_id;
        } else {
            // Handle case when timetable data does not exist
            echo "No timetable available for the selected batch.";
            exit; // Exit the script if no timetable exists for the selected batch
        }
    }
} else {
    // Redirect to batch selection page if not provided
}

// Fetch timetable data based on timetable_id
$timetable_query = "SELECT * FROM timetable WHERE Timetable_ID = $timetable_id";
$timetable_result = mysqli_query($conn, $timetable_query);

if (!$timetable_result) {
    echo "Error: " . mysqli_error($conn);
    exit;
} else {
    $timetable = mysqli_fetch_assoc($timetable_result);
}

// Check if $batch_id is set before using it in the SQL query
if ($batch_id !== '') {
    // Fetch timetable entries for the selected batch
    $timetable_entries_query = "SELECT * FROM timetable_entry WHERE Timetable_ID = $timetable_id AND Batch_ID = $batch_id";
    $timetable_entries_result = mysqli_query($conn, $timetable_entries_query);

    // Initialize an array to store timetable entries by slot
    $timetable_entries_by_slot = [];
    while ($row = mysqli_fetch_assoc($timetable_entries_result)) {
        $timetable_entries_by_slot[$row['Slot']][] = $row;
    }
}

// Fetch data for select options (batches)
$batch_query = "SELECT Batch_ID, Batch_Name FROM batch";
$batch_result = mysqli_query($conn, $batch_query);

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
    <!-- <link rel="stylesheet" href="css/edit_timetable.css"> -->
    <link rel="stylesheet" href="css/admin_login.css">

</head>

<body>

    <section class="wrapper">
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
            </div>
        </section>

        <section class="section1">
            <h2>Welcome to Batch timetable view</h2>

            <form class="form1" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <select class="batch-select" name="batch" id="batch">
                    <?php
        // Include your database connection file
        include 'connection.php';

        // Query to fetch batches
        $batch_query = "SELECT * FROM batch";
        $batch_result = mysqli_query($conn, $batch_query);

        // Check if there are batches
        if (mysqli_num_rows($batch_result) > 0) {
            // Loop through batches to populate select options
            while ($row = mysqli_fetch_assoc($batch_result)) {
                // Check if timetable exists for the current batch
                $timetable_check_query = "SELECT * FROM timetable WHERE Batch_ID = " . $row['Batch_ID'];
                $timetable_check_result = mysqli_query($conn, $timetable_check_query);
                if (mysqli_num_rows($timetable_check_result) > 0) {
                    // Timetable exists for this batch, show it in the dropdown
                    $selected = ($row['Batch_ID'] == $_POST['batch']) ? "selected" : "";
                    echo "<option class='batch-option' value='" . $row['Batch_ID'] . "' $selected>" . $row['Batch_Name'] . "</option>";
                }
            }
        } else {
            echo "<option value='' disabled>No batches available</option>";
        }
        ?>
                </select>

                <button onclick="window.open('print_timetable.php', 'Print Timetable', 'width=800,height=600')">Print
                    Timetable</button>
                <input class="submit-button" type="submit" value="View Timetable">
            </form>

            <?php if ($batch_id !== '' && count($timetable_entries_by_slot) > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Days of the Week</th>
                        <?php
                        // Generate columns for time duration of modules
                        $slots = $timetable['Slots'];
                        $start_time = strtotime($timetable['Start_Time']);
                        $end_time = strtotime($timetable['End_Time']);
                        $break_time = strtotime($timetable['Break_Time']);
                        $slot_duration = strtotime($timetable['Slot_Duration']) - strtotime('00:00:00');

                        $total_duration = ($end_time - $start_time - $break_time) / 3600; // in hours
                        $total_slot_duration = $slot_duration * $slots / 3600; // in hours

                        $current_time = $start_time;
                        for ($i = 0; $i < $slots; $i++) {
                            $time_slot = date("h:i A", $current_time);
                            echo "<th>$time_slot</th>";
                            // Increment current time by slot duration
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
                        for ($i = 0; $i < $slots; $i++) {
                            $slot_identifier = $day . '_' . $i;
                            echo "<td id='slot_$slot_identifier'>";
                            // Check if there are timetable entries for this slot
                            if (isset($timetable_entries_by_slot[$slot_identifier])) {
                                foreach ($timetable_entries_by_slot[$slot_identifier] as $entry) {
                                    $lecturer_id = $entry['Lecturer_ID'];
                                    $batch_id = $entry['Batch_ID'];
                                    $module_id = $entry['Module_ID'];
                                    $classroom_id = $entry['Classroom_ID'];
                                    $lecturer_query = "SELECT Name FROM lecturer WHERE Lecturer_ID = $lecturer_id";
                                    $lecturer_result = mysqli_query($conn, $lecturer_query);
                                    if ($lecturer_result && mysqli_num_rows($lecturer_result) > 0) {
                                        $lecturer_name = mysqli_fetch_assoc($lecturer_result)['Name'];
                                    } else {
                                        $lecturer_name = "";
                                    }
                                    echo "Lecturer:<b> $lecturer_name </b> <br>";
                                    echo "Batch Name:<b>  $batch_id </b><br>";
                                    $module_query = "SELECT Module_Name FROM module WHERE Module_ID = $module_id";
                                    $module_result = mysqli_query($conn, $module_query);
                                    if ($module_result && mysqli_num_rows($module_result) > 0) {
                                        $module_name = mysqli_fetch_assoc($module_result)['Module_Name'];
                                        echo "Module Name: <b>$module_name</b><br>";
                                    } else {
                                        echo "Module Name: <br>";
                                    }
                                    $classroom_query = "SELECT Room_Number FROM classroom WHERE Classroom_ID = $classroom_id";
                                    $classroom_result = mysqli_query($conn, $classroom_query);
                                    if ($classroom_result && mysqli_num_rows($classroom_result) > 0) {
                                        $classroom_name = mysqli_fetch_assoc($classroom_result)['Room_Number'];
                                        echo "Classroom Name:<b> $classroom_name </b>";
                                    } else {
                                        echo "Classroom Name:";
                                    }
                                }
                            } else {
                                echo "";
                            }
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php } ?>
        </section>
    </section>

</body>

</html>