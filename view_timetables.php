<?php
session_start();
include 'connection.php'; // Include database connection file

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
    <title>Timetable Records</title>
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

    // Fetch departments
    $department_query = "SELECT Department_ID, Department_Name FROM department";
    $department_result = mysqli_query($conn, $department_query);

    // Fetch batch names
    $batch_query = "SELECT Batch_ID, Batch_Name FROM batch";
    $batch_result = mysqli_query($conn, $batch_query);

    

    // Fetch all timetable records
    // Fetch all timetable records with batch names
        $query = "SELECT t.*, d.Department_Name, b.Batch_Name 
        FROM timetable t 
        JOIN department d ON t.Department_ID = d.Department_ID 
        JOIN batch b ON t.Batch_ID = b.Batch_ID";
        $result = mysqli_query($conn, $query);

    ?>

    <div class="wrapper-timetable">

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
            <div class="div1">
                <h2>Timetable Records</h2>
                <div>
                    <button class="btn-primary" onclick="document.getElementById('addModal').style.display='block'">Add
                        Timetable</button>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Timetable ID</th>
                        <th>Department ID</th>
                        <th>Batch Name</th>
                        <th>Semester</th>
                        <th>Slots</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Break Time</th>
                        <th>Break Duration</th>
                        <th>Slot Duration</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['Timetable_ID'] . "</td>";
                        echo "<td>" . $row['Department_ID'] . "</td>";
                        echo "<td>" . $row['Batch_Name'] . "</td>";
                        echo "<td>" . $row['Semester'] . "</td>";
                        echo "<td>" . $row['Slots'] . "</td>";
                        echo "<td>" . $row['Start_Time'] . "</td>";
                        echo "<td>" . $row['End_Time'] . "</td>";
                        echo "<td>" . $row['Break_Time'] . "</td>";
                        echo "<td>" . $row['Break_Duration'] . "</td>";
                        echo "<td>" . $row['Slot_Duration'] . "</td>";
                        echo "<td style='display:flex; flex-direction:column; gap:5px;'>";
                        echo "<button  class='btn-primary' onclick='editTimetable(" . $row['Timetable_ID'] . ",\"" . $row['Department_ID'] . "\",\"" . $row['Semester'] . "\",\"" . $row['Slots'] . "\",\"" . $row['Start_Time'] . "\",\"" . $row['End_Time'] . "\",\"" . $row['Break_Time'] . "\",\"" . $row['Break_Duration'] . "\",\"" . $row['Slot_Duration'] . "\")'>Edit</button>";
                        echo "<button class='edit-btn btn-primary' onclick='createLayout(" . $row['Timetable_ID'] . ")'>Layout</button>";
                        echo "<button class='btn-primary' onclick='deleteTimetable(" . $row['Timetable_ID'] . ")'>Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>


            <!-- Add Timetable Modal -->
            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('addModal').style.display='none'">&times;</span>
                    <h2>Add Timetable</h2>
                    <form method="post" action="add_timetable.php">
                        <label for="department">Department:</label>
                        <select name="department_id" id="department">
                            <?php
            while ($row = mysqli_fetch_assoc($department_result)) {
                echo "<option value='" . $row['Department_ID'] . "'>" . $row['Department_Name'] . "</option>";
            }
            ?>
                        </select><br><br>

                        <label for="semester">Semester:</label>
                        <select name="semester" id="semester">
                            <option value="Semester 1">Semester 1</option>
                            <option value="Semester 2">Semester 2</option>
                            <option value="Semester 3">Semester 3</option>
                            <option value="Semester 4">Semester 4</option>
                        </select><br><br>
                        <label for="batch">Batch:</label>
                        <select name="batch_id" id="batch">
                            <?php
                            while ($row = mysqli_fetch_assoc($batch_result)) {
                                echo "<option value='" . $row['Batch_ID'] . "'>" . $row['Batch_Name'] . "</option>";
                            }
                        ?>
                        </select><br><br>

                        <label for="slots">Slots:</label>
                        <input type="text" id="slots" name="slots" required><br><br>
                        <label for="start_time">Start Time:</label>
                        <input type="time" id="start_time" name="start_time" required><br><br>
                        <label for="end_time">End Time:</label>
                        <input type="time" id="end_time" name="end_time" required><br><br>
                        <label for="break_time">Break Time:</label>
                        <input type="time" id="break_time" name="break_time" required><br><br>
                        <label for="break_duration">Break Duration (hours):</label>
                        <input type="number" id="break_duration" name="break_duration" min="0" required><br><br>
                        <label for="slot_duration">Slot Duration:</label>
                        <input type="time" id="slot_duration" name="slot_duration" required><br><br>
                        <button type="submit">Add Timetable</button>
                    </form>
                </div>
            </div>

            <!-- Edit Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('editModal').style.display='none'">&times;</span>
                    <h2>Edit Timetable</h2>
                    <form id="editForm" method="post" action="update_timetable.php">
                        <input type="hidden" id="timetable_id" name="timetable_id">
                        <label for="department_id">Department ID:</label>
                        <input type="text" id="edit_department_id" name="department_id" required><br><br>
                        <label for="semester">Semester:</label>
                        <input type="text" id="edit_semester" name="semester" required><br><br>
                        <label for="slots">Slots:</label>
                        <input type="text" id="edit_slots" name="slots" required><br><br>
                        <label for="start_time">Start Time:</label>
                        <input type="time" id="edit_start_time" name="start_time" required><br><br>
                        <label for="end_time">End Time:</label>
                        <input type="time" id="edit_end_time" name="end_time" required><br><br>
                        <label for="break_time">Break Time:</label>
                        <input type="time" id="edit_break_time" name="break_time" required><br><br>
                        <label for="break_duration">Break Duration (Hours):</label>
                        <input type="number" id="edit_break_duration" name="break_duration" required><br><br>
                        <label for="slot_duration">Slot Duration:</label>
                        <input type="time" id="edit_slot_duration" name="slot_duration" required><br><br>
                        <button type="submit">Update Timetable</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
    function editTimetable(timetableID, departmentID, semester, slots, startTime, endTime, breakTime, breakDuration,
        slotDuration) {
        document.getElementById("timetable_id").value = timetableID;
        document.getElementById("edit_department_id").value = departmentID;
        document.getElementById("edit_semester").value = semester;
        document.getElementById("edit_slots").value = slots;
        document.getElementById("edit_start_time").value = startTime;
        document.getElementById("edit_end_time").value = endTime;
        document.getElementById("edit_break_time").value = breakTime;
        document.getElementById("edit_break_duration").value = breakDuration; // Add this line
        document.getElementById("edit_slot_duration").value = slotDuration;
        document.getElementById('editModal').style.display = 'block';
    }


    // Get the deleteTimetable function
    function deleteTimetable(timetableID) {
        if (confirm("Are you sure you want to delete this timetable?")) {
            // Redirect to delete_timetable.php with timetableID as parameter
            window.location.href = 'delete_timetable.php?timetable_id=' + timetableID;
        }
    }

    function createLayout(timetableID) {
        // Redirect to delete_timetable.php with timetableID as parameter
        window.location.href = 'set_session.php?timetable_id=' + timetableID;

    }
    </script>
</body>

</html>