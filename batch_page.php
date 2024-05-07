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
    <title>Batch Records</title>
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
            <div class="div1">
                <h2>Batch Records</h2>
                <div>
                    <button class="btn-primary" onclick="document.getElementById('addModal').style.display='block'">Add
                        Batch</button>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Batch Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Course Title</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>".$row['Batch_ID']."</td>";
                        echo "<td>".$row['Batch_Name']."</td>";
                        echo "<td>".$row['Start_Date']."</td>";
                        echo "<td>".$row['End_Date']."</td>";
                        echo "<td>".$row['Course_Title']."</td>";

                        echo "<td>";
                        echo "<button class='btn-primary' onclick='editBatch(" . $row['Batch_ID'] . ",\"" . $row['Batch_Name'] . "\",\"" . $row['Start_Date'] . "\",\"" . $row['End_Date'] . "\",\"" . $row['Course_Title'] . "\")'>Edit</button>";
                        echo "<button class='btn-primary' style='margin-left:10px;' onclick=\"callAlert('Do you want to delete this batch record?', " . $row['Batch_ID'] . ")\">Delete</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Add Batch Modal -->
            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('addModal').style.display='none'">&times;</span>
                    <h2>Add Batch</h2>
                    <form method="post" action="add_batch.php">
                        <label for="batch_name">Batch Name:</label>
                        <input type="text" id="batch_name" name="batch_name" required><br><br>
                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" required><br><br>
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" name="end_date" required><br><br>
                        <label for="course_title">Course Title:</label>
                        <input type="text" id="course_title" name="course_title" required><br><br>
                        <button type="submit">Add Batch</button>
                    </form>
                </div>
            </div>

            <!-- Edit Batch Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('editModal').style.display='none'">&times;</span>
                    <h2>Edit Batch</h2>
                    <form id="editForm" method="post" action="update_batch.php">
                        <input type="hidden" id="batch_id" name="batch_id">
                        <label for="edit_batch_name">Batch Name:</label>
                        <input type="text" id="edit_batch_name" name="batch_name" required><br><br>
                        <label for="edit_start_date">Start Date:</label>
                        <input type="date" id="edit_start_date" name="start_date" required><br><br>
                        <label for="edit_end_date">End Date:</label>
                        <input type="date" id="edit_end_date" name="end_date" required><br><br>
                        <label for="edit_course_title">End Date:</label>
                        <input type="text" id="edit_course_title" name="course_title" required><br><br>
                        <button type="submit">Update Batch</button>
                    </form>
                </div>
            </div>
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

    <script>
    // Function to populate the edit batch modal with batch data
    function editBatch(batchID, batchName, startDate, endDate, courseTitle) {
        document.getElementById("batch_id").value = batchID;
        document.getElementById("edit_batch_name").value = batchName;
        document.getElementById("edit_start_date").value = startDate;
        document.getElementById("edit_end_date").value = endDate;
        document.getElementById("edit_course_title").value = courseTitle;
        document.getElementById('editModal').style.display = 'block';
    }

    // Get the deleteStudent function
    function deleteBatch(batchID) {
        // Call the custom alert function
        callAlert("Are you sure you want to delete this batch record?", batchID);
    }

    function callAlert(msg, batchID) {
        if (confirm(msg)) {
            window.location.href = 'delete_batch.php?batch_id=' + batchID;
        }
    }
    </script>
</body>

</html>