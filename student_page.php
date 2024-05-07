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
    <title>Student Records</title>
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

    .div1-sub {
        display: flex;
        justify-content: space-between;
        flex-direction: column;

    }

    .search-container {
        display: flex;
        align-items: center;
    }

    #searchInput {
        width: 250px;
        padding: 10px;
        margin-right: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    </style>

</head>

<body>
    <?php
  
    $query = "SELECT * FROM student";
    $result = mysqli_query($conn, $query);
    ?>

    <div class="wrapper-batch">


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
                <div class="div1">
                    <h2>Student Records</h2>
                    <div class="div1-sub">
                        <div class="search-container">
                            <input type="text" id="searchInput" onkeyup="searchStudents()"
                                placeholder="Search for students...">
                            <button class="btn-primary"
                                onclick="document.getElementById('addModal').style.display='block'">Add
                                Student</button>

                        </div>

                    </div>
                </div>

            </div>


            <table id="studentTable">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Batch ID</th>
                        <th>Name</th>
                        <th>Zoho Number</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['Student_ID'] . "</td>";
                        echo "<td>" . $row['Batch_ID'] . "</td>";
                        echo "<td>" . $row['Name'] . "</td>";
                        echo "<td>" . $row['Zoho_Number'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td>" . $row['Phone'] . "</td>";
                        echo "<td>";
                        echo "<button class='btn-primary' onclick='editStudent(" . $row['Student_ID'] . ",\"" . $row['Batch_ID'] . "\",\"" . $row['Name'] . "\",\"" . $row['Zoho_Number'] . "\",\"" . $row['Email'] . "\",\"" . $row['Phone'] . "\")'>Edit</button>";
                        echo "<button class='btn-primary' style='margin-left:10px;' onclick=\"callAlert('Do you want to delete this item?', " . $row['Student_ID'] . ")\">Delete</button>";

                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Add Student Modal -->
            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('addModal').style.display='none'">&times;</span>
                    <h2>Add Student</h2>
                    <form method="post" action="add_student.php">
                        <label for="batch_id">Batch ID:</label> <select id="batch_id" name="batch_id" required>
                            <?php
                $batch_query = "SELECT * FROM batch";
                $batch_result = mysqli_query($conn, $batch_query);
                if ($batch_result && mysqli_num_rows($batch_result) > 0) {
                    while ($batch_row = mysqli_fetch_assoc($batch_result)) {
                        echo "<option value='" . $batch_row['Batch_ID'] . "'>" . $batch_row['Batch_Name'] . "</option>";
                    }
                }
                ?>
                        </select><br><br>
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required><br><br>
                        <label for="zoho_number">Zoho Number:</label>
                        <input type="text" id="zoho_number" name="zoho_number" required><br><br>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required><br><br>
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" required><br><br>
                        <button type="submit">Add Student</button>
                    </form>
                </div>
            </div>

            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('editModal').style.display='none'">&times;</span>
                    <h2>Edit Student</h2>
                    <form id="editForm" method="post" action="update_student.php">
                        <input type="hidden" id="student_id" name="student_id">
                        <label for="batch_id">Batch ID:</label>
                        <input type="text" id="edit_batch_id" name="batch_id" required><br><br>
                        <label for="name">Name:</label>
                        <input type="text" id="edit_name" name="name" required><br><br>
                        <label for="zoho_number">Zoho Number:</label>
                        <input type="text" id="edit_zoho_number" name="zoho_number" required><br><br>
                        <label for="email">Email:</label>
                        <input type="email" id="edit_email" name="email" required><br><br>
                        <label for="phone">Phone:</label>
                        <input type="text" id="edit_phone" name="phone" required><br><br>
                        <button type="submit">Update Student</button>
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
    // Get the editStudent function
    function editStudent(studentID, batchID, name, zohoNumber, email, phone) {
        document.getElementById("student_id").value = studentID;
        document.getElementById("edit_batch_id").value = batchID;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_zoho_number").value = zohoNumber;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_phone").value = phone;
        document.getElementById('editModal').style.display = 'block';
    }

    // Get the deleteStudent function
    function deleteStudent(studentID) {
        // Call the custom alert function
        callAlert("Are you sure you want to delete this student?", studentID);
    }

    function callAlert(msg, studentID) {
        alert(msg, null, function(proceed) {
            if (proceed) {
                // If user clicks proceed, redirect to delete_student.php with studentID as parameter
                window.location.href = 'delete_student.php?student_id=' + studentID;
            }
        });
    }


    function searchStudents() {
        // Get the search query entered by the user
        var searchQuery = document.getElementById("searchInput").value;

        // Send an AJAX request to the server to fetch filtered results
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Update the table body with the filtered results
                document.getElementById("studentTable").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "search_student.php?search_query=" + searchQuery, true);
        xhttp.send();
    }
    </script>

    <script src="main.js"></script>
</body>

</html>