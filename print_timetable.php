<?php
session_start();
include 'connection.php'; 

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

// Fetch department name, batch name, and semester from the database based on the selected department
if(isset($department)) {
    // Query to fetch department name, batch name, and semester
    $department_query = "SELECT Department_Name FROM department WHERE Department_ID = '$department'";
    $department_result = mysqli_query($conn, $department_query);
    if($department_result && mysqli_num_rows($department_result) > 0) {
        $department_data = mysqli_fetch_assoc($department_result);
        $department_name = $department_data['Department_Name'];
       
    } else {
        // Set default values if data not found
        $department_name = "N/A";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Timetable</title>
    <link rel="stylesheet" href="css/admin_login.css">
    <!-- You can create this CSS file to style the printed timetable -->
</head>

<body>

    <section class="section1">

        <div class="outer-container">
            <h4 class="title"><?php echo $department_name ?> - Timetable</h4>

            <div class="inner-container">
                <div>
                    <img src="images/bcaslogo.png" alt="BCAS Logo">
                </div>
                <div class="college-info">
                    <h2>BRITISH COLLEGE OF APPLIED STUDIES</h2>
                    <h3><?php echo $department_name ?> </h3>
                    <h3 class="timetable-heading">Timetable</h3>
                </div>
            </div>
        </div>


        <?php if(isset($timetables) && !empty($timetables)) { ?>
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
                            echo "<td>";
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
                                        echo "Lecturer:<b> $lecturer_name </b> <br>";
                                        echo "Batch Name:<b> $batch_name </b><br>";
                                        echo "Module Name:<b> $module_name </b><br>";
                                        echo "Classroom:<b> $classroom_name</b> <br>";
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

        <script>
        // Print the timetable when the print button is clicked
        window.onload = function() {
            window.print();
        };
        </script>

    </section>

</body>

</html>