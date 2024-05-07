<?php
session_start();
include 'connection.php'; 

if(isset($_SESSION["timetable_id"])){
    $timetable_id = $_SESSION["timetable_id"] ; 
}
else {
    $timetable_id = 1;
}

// Check if department and semester are selected
if(isset($_POST['department']) && isset($_POST['semester'])) {
    $department = $_POST['department'];
    $semester = $_POST['semester'];
    
    // Fetch timetable ID based on department and semester
    $timetable_query = "SELECT Timetable_ID FROM timetable WHERE Department_ID = '$department' AND Semester = '$semester'";
    $timetable_result = mysqli_query($conn, $timetable_query);

    if (!$timetable_result) {
        echo "Error: " . mysqli_error($conn);
        exit;
    } else {
        // Check if timetable data exists
        if(mysqli_num_rows($timetable_result) > 0) {
            $timetable_data = mysqli_fetch_assoc($timetable_result);
            $timetable_id = $timetable_data['Timetable_ID'];
            
            // Set session variables
            $_SESSION['selected_department'] = $department;
            $_SESSION['selected_semester'] = $semester;
            // $_SESSION['selected_batch'] = $batch_name;
        } else {
            // Handle case when timetable data does not exist
            echo "No timetable available for the selected department and semester.";
            exit; // Exit the script if no timetable exists
        }
    }
} else {
    // Redirect to department and semester selection page if not provided
}

// Retrieve timetable data based on timetable_id
$timetable_query = "SELECT * FROM timetable WHERE Timetable_ID = $timetable_id";
$timetable_result = mysqli_query($conn, $timetable_query);

if (!$timetable_result) {
    echo "Error: " . mysqli_error($conn);
    exit;
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
    <title>Admin login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
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
        </section>

        <section class="section1">
            <h2>Welcome to admin page</h2>

            <!-- Display select option menu bars -->
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

                <select class="semester-select" name="semester" id="semester">
                    <option class="semester-option" value="Semester 1"
                        <?php if (isset($_POST['semester']) && $_POST['semester'] == "Semester 1") echo "selected"; ?>>
                        Semester 1</option>
                    <option class="semester-option" value="Semester 2"
                        <?php if (isset($_POST['semester']) && $_POST['semester'] == "Semester 2") echo "selected"; ?>>
                        Semester 2</option>
                    <option class="semester-option" value="Semester 3"
                        <?php if (isset($_POST['semester']) && $_POST['semester'] == "Semester 3") echo "selected"; ?>>
                        Semester 3</option>
                    <option class="semester-option" value="Semester 4"
                        <?php if (isset($_POST['semester']) && $_POST['semester'] == "Semester 4") echo "selected"; ?>>
                        Semester 4</option>
                </select>


                <button onclick="window.open('print_timetable.php', 'Print Timetable', 'width=800,height=600')">Print
                    Timetable</button>
                <input class="submit-button" type="submit" value="View Timetable">
            </form>

            <!-- Display timetable if available -->
            <?php if(isset($_POST['department']) && isset($_POST['semester'])) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Days of the Week</th>
                        <?php
                      
                        $slots = $timetable['Slots'];
                        $start_time = strtotime($timetable['Start_Time']);
                        $end_time = strtotime($timetable['End_Time']);
                        $break_time = strtotime($timetable['Break_Time']);
                        $slot_duration = strtotime($timetable['Slot_Duration']) - strtotime('00:00:00');

                        $total_duration = ($end_time - $start_time - $break_time) / 3600;
                        $total_slot_duration = $slot_duration * $slots / 3600; 

                        $current_time = $start_time;
                        for ($i = 0; $i < $slots; $i++) {
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
                        for ($i = 0; $i < $slots; $i++) {
                            $slot_identifier = $day . '_' . $i;
                            echo "<td id='slot_$slot_identifier'>";
                           
                            if (isset($timetable_entries_by_slot[$slot_identifier])) {
                                // Display timetable entries for this slot
                                foreach ($timetable_entries_by_slot[$slot_identifier] as $entry) {
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
                            // Add Edit button to show form for updating timetable entry
                            // echo "<button class='edit-btn' onclick='openEditForm(\"$slot_identifier\", \"$day\", $i)'>Edit</button>";
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php } else {
                
                echo "hi";
            }?>
            <script>
            // Get the modal
            var modal = document.getElementById('editModal');

            // When the user clicks the button, open the modal 
            function openEditForm(slot, day, slotIndex) {
                document.getElementById("editDay").value = day;
                document.getElementById("editSlot").value = slot;
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            function closeModal() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            }
            </script>
        </section>
    </section>
</body>

</html>