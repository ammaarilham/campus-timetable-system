<?php 
session_start();
include 'connection.php'; 

// Retrieve timetable_id from session
$timetable_id = $_SESSION['timetable_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data and insert into timetable_entry table
    $day = $_POST['day'];
    $slot = $_POST['slot'];
    $lecturer_id = $_POST['lecturer'];
    $batch_id = $_POST['batch'];
    $module_id = $_POST['module'];
    $classroom_id = $_POST['classroom'];
    $start_time = $_POST['startTime'];
    $end_time = $_POST['endTime'];
    // $start_time = $_POST['startTime']; // Retrieve start time
    // $start_time = date("H:i:s", strtotime($_POST['startTime']));

    
    // Check if the selected slot is already occupied in the timetable for the specified day
    $slot_occupied_query = "SELECT * FROM timetable_entry WHERE Timetable_ID = $timetable_id AND Day = '$day' AND Slot = '$slot'";
    $slot_occupied_result = mysqli_query($conn, $slot_occupied_query);
    
    if (mysqli_num_rows($slot_occupied_result) > 0) {
        // Slot is already occupied, display an error message
        echo "<script>showErrorMessage(' The selected slot is already occupied.');</script>";
    } else {
        // Check for classroom availability during the specified slot
        $classroom_availability_query = "SELECT * FROM timetable_entry WHERE Timetable_ID = $timetable_id AND Day = '$day' AND Slot = '$slot' AND Classroom_ID = $classroom_id";
        $classroom_availability_result = mysqli_query($conn, $classroom_availability_query);
    
        if (mysqli_num_rows($classroom_availability_result) > 0) {
            // Classroom is not available during the specified slot, display an error message
            echo "<script>alert('The selected classroom is not available during the specified slot.');</script>";
        } else {
            // Check for lecturer availability during the specified slot across all timetables
            $lecturer_availability_query = "SELECT * FROM timetable_entry WHERE Day = '$day' AND Slot = '$slot' AND Lecturer_ID = $lecturer_id";
            $lecturer_availability_result = mysqli_query($conn, $lecturer_availability_query);
    
            if (mysqli_num_rows($lecturer_availability_result) > 0) {
                // Lecturer is not available during the specified slot, display an error message
                echo "<script>alert('The selected lecturer is not available during the specified slot.');</script>";
            } else {
                // Check for batch availability during the specified slot
                $batch_availability_query = "SELECT * FROM timetable_entry WHERE Timetable_ID = $timetable_id AND Day = '$day' AND Slot = '$slot' AND Batch_ID = $batch_id";
                $batch_availability_result = mysqli_query($conn, $batch_availability_query);
    
                if (mysqli_num_rows($batch_availability_result) > 0) {

                    echo "<script>alert('The selected batch is not available during the specified slot.');</script>";
                } else {
                    // All checks passed, insert the new timetable entry
                    $insert_query = "INSERT INTO timetable_entry (Timetable_ID, Slot, Day, Start_Time,End_Time, Lecturer_ID, Batch_ID, Module_ID, Classroom_ID) 
                                     VALUES ($timetable_id, '$slot', '$day','$start_time','$end_time', $lecturer_id, $batch_id, $module_id, $classroom_id)";
                    if (mysqli_query($conn, $insert_query)) {
                        // Timetable entry inserted successfully
                        echo "New record inserted successfully.";
                    } else {
                        // Error inserting timetable entry
                        echo "Error: " . mysqli_error($conn);
                    }
                }
            }
        }
    }
}

// Fetch specific timetable data based on timetable_id
$timetable_query = "SELECT * FROM timetable WHERE Timetable_ID = $timetable_id";
$timetable_result = mysqli_query($conn, $timetable_query);

if (!$timetable_result) {
    echo "Error: " . mysqli_error($conn);
} else {
    $timetable = mysqli_fetch_assoc($timetable_result);
}

// Fetch timetable entries for each slot
$timetable_entries_query = "SELECT * FROM timetable_entry WHERE Timetable_ID = $timetable_id";
$timetable_entries_result = mysqli_query($conn, $timetable_entries_query);

// Initialize an array to store timetable entries by slot
$timetable_entries_by_slot = [];
while ($row = mysqli_fetch_assoc($timetable_entries_result)) {
    $timetable_entries_by_slot[$row['Slot']][] = $row;
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
    <title>Edit Timetable</title>
    <link rel="stylesheet" href="css/styles.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/edit_timetable.css">
</head>

<body>
    <div class="wrapper-timetable">

        <div class="popup-message-wrapper">
            <div class="popup-message">
                <span id="closePopup" class="close-popup">&times;</span>
                <div class="popup-message-text"></div>
            </div>
        </div>



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
                        <hr>
                        <li><a href="admin_page.php">Admin Settings</a></li>
                        <hr>
                        <li><a href="lecturer_page.php">Lecturer details</a></li>
                        <hr>
                        <li><a href="student_page.php">Student details</a></li>
                        <hr>
                        <li><a href="batch_page.php">Batch details</a></li>
                        <hr>
                        <li><a href="view_timetables.php">Timetable Dashboard</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="div1">

            <h2 class="h1">Timetable Layout</h2>

            <table>
                <thead>
                    <tr>
                        <th>Days of the Week</th>
                        <?php
                            $slots = $timetable['Slots'];
                            $start_time = strtotime($timetable['Start_Time']);
                            $end_time = strtotime($timetable['End_Time']);
                            $break_time = strtotime($timetable['Break_Time']);
                            $break_duration = strtotime($timetable['Break_Duration']) - strtotime('00:00:00');
                            $slot_duration = strtotime($timetable['Slot_Duration']) - strtotime('00:00:00');

                            // Calculate total duration excluding break time
                            $total_duration = ($end_time - $start_time) / 3600; // in hours
                            $total_duration -= ($break_duration / 3600); // subtract break time from total duration

                            // Calculate total slot duration without considering break time
                            $total_slot_duration = ($total_duration * 3600) / $slots; // in seconds

                            $current_time = $start_time;
                            $current_slot = 0;
                            while ($current_slot < $slots) {
                                // Check if it's time for the break
                                if ($current_time == $break_time) {
                                    // Skip this time slot and continue to the next one
                                    $current_time += $break_duration;
                                    continue;
                                } else {
                                    // Print the time slot
                                    $end_slot_time = $current_time + $slot_duration;
    
                                    // Format starting and ending time for display
                                    $start_slot_time_formatted = date("h:i A", $current_time);
                                    $end_slot_time_formatted = date("h:i A", $end_slot_time);
                                    
                                    // Display starting and ending time in the column heading
                                    echo "<th>$start_slot_time_formatted - $end_slot_time_formatted</th>";
                                    
                                    // Increment current time by slot duration
                                    $current_time += $slot_duration;
                                    $current_slot++;
                                }
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
                        // Display timetable entries for this slot
                        foreach ($timetable_entries_by_slot[$slot_identifier] as $entry) {
                            // Fetch lecturer name based on ID
                            $lecturer_id = $entry['Lecturer_ID'];
                            $lecturer_name_query = "SELECT Name FROM lecturer WHERE Lecturer_ID = $lecturer_id";
                            $lecturer_name_result = mysqli_query($conn, $lecturer_name_query);
                            $lecturer_name_row = mysqli_fetch_assoc($lecturer_name_result);
                            $lecturer_name = $lecturer_name_row['Name'];

                            // Fetch batch number based on ID
                            $batch_id = $entry['Batch_ID'];
                            $batch_number_query = "SELECT Batch_Name FROM batch WHERE Batch_ID = $batch_id";
                            $batch_number_result = mysqli_query($conn, $batch_number_query);
                            $batch_number_row = mysqli_fetch_assoc($batch_number_result);
                            $batch_number = $batch_number_row['Batch_Name'];

                            // Fetch module name based on ID
                            $module_id = $entry['Module_ID'];
                            $module_name_query = "SELECT Module_Name FROM module WHERE Module_ID = $module_id";
                            $module_name_result = mysqli_query($conn, $module_name_query);
                            $module_name_row = mysqli_fetch_assoc($module_name_result);
                            $module_name = $module_name_row['Module_Name'];

                            // Fetch classroom number based on ID
                            $classroom_id = $entry['Classroom_ID'];
                            $classroom_number_query = "SELECT Room_Number FROM classroom WHERE Classroom_ID = $classroom_id";
                            $classroom_number_result = mysqli_query($conn, $classroom_number_query);
                            $classroom_number_row = mysqli_fetch_assoc($classroom_number_result);
                            $classroom_number = $classroom_number_row['Room_Number'];

                            echo "Lecturer: " . $lecturer_name . "<br>";
                            echo "Batch ID: " . $batch_number . "<br>";
                            echo "Module ID: " . $module_name . "<br>";
                            echo "Classroom ID: " . $classroom_number . "<br>";
                        }
                    }

                    
    
                    echo "<button class='edit-btn btn-primary1' onclick='openEditForm(\"$slot_identifier\", \"$day\", $i)'>Edit</button>";
                    echo "<button class='delete-btn btn-primary1' onclick='deleteTimetableEntry(\"$slot_identifier\")'>Delete</button>";
                    echo "</td>";
                }
                echo "</tr>";
            }
            ?>
                </tbody>
            </table>

            <!-- The Modal -->
            <div id='editModal' class='modal'>

                <!-- Modal content -->
                <div class='modal-content'>
                    <span class='close1'>&times;</span>
                    <h3>Edit Timetable Entry</h3>
                    <form id='editForm' method='post' action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'>
                        <input type='hidden' name='timetable_id' value='<?php echo $timetable_id; ?>'>
                        <input type='hidden' name='day' id='editDay'>
                        <input type='hidden' name='slot' id='editSlot'>
                        <input type='hidden' name='startTime' id='startTime'>

                        <label for='lecturer'>Lecturer:</label>
                        <select name='lecturer' id='lecturer'>
                            <?php
                    while ($row = mysqli_fetch_assoc($lecturer_result)) {
                        echo "<option value='" . $row['Lecturer_ID'] . "'>" . $row['Name'] . "</option>";
                    }
                    ?>
                        </select><br><br>

                        <label for='batch'>Batch ID:</label>
                        <select name='batch' id='batch'>
                            <?php
                    while ($row = mysqli_fetch_assoc($batch_result)) {
                        echo "<option value='" . $row['Batch_ID'] . "'>" . $row['Batch_Name'] . "</option>";
                    }
                    ?>
                        </select><br><br>

                        <label for='module'>Module Name:</label>
                        <select name='module' id='module'>
                            <?php
                    while ($row = mysqli_fetch_assoc($module_result)) {
                        echo "<option value='" . $row['Module_ID'] . "'>" . $row['Module_Name'] . "</option>";
                    }
                    ?>
                        </select><br><br>

                        <label for='classroom'>Classroom:</label>
                        <select name='classroom' id='classroom'>
                            <?php
                    while ($row = mysqli_fetch_assoc($classroom_result)) {
                        echo "<option value='" . $row['Classroom_ID'] . "'>" . $row['Room_Number'] . "</option>";
                    }
                    ?>
                        </select><br><br>

                        <label for='startTime'>Start Time:</label>
                        <input type='time' name='startTime' id='startTime'>
                        <br><br>
                        <label for='endTime'>End Time:</label>
                        <input type='time' name='endTime' id='endTime'>
                        <br><br>


                        <input type='submit' value='Update'>
                        <button type='button' class='close'>Cancel</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
        // Get the modal
        var modal = document.getElementById('editModal');

        // When the user clicks the button, open the modal 
        function openEditForm(slot, day, slotIndex, startTime) {
            document.getElementById('editDay').value = day;
            document.getElementById('editSlot').value = slot;
            // document.getElementById('startTime').value = startTime; // Set the start time
            modal.style.display = 'block';
        }


        // Function to delete timetable entry
        function deleteTimetableEntry(slotIdentifier) {
            // AJAX request to delete the entry
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Reload the page after deletion
                    location.reload();
                }
            };
            xmlhttp.open('GET', 'delete_entry.php?slot=' + slotIdentifier, true);
            xmlhttp.send();
        }

        // When the user clicks on <span> (x), close the modal
        function closeModal() {
            modal.style.display = 'none';
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
        </script>

    </div>

</body>

</html>