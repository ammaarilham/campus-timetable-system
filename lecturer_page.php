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
    <title>Lecturer Records</title>
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

    // Fetch all lecturer records
    $query = "SELECT * FROM lecturer";
    $result = mysqli_query($conn, $query);
    ?>

    <div class="wrapper-lecturer">

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
                <h2>Lecturer Records</h2>
                <div>
                    <button class="btn-primary" onclick="document.getElementById('addModal').style.display='block'">Add
                        Lecturer</button>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Lecturer ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['Lecturer_ID'] . "</td>";
                        echo "<td>" . $row['Name'] . "</td>";
                        echo "<td>" . $row['Username'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td>" . $row['Phone'] . "</td>";
                        echo "<td>";
                        echo "<button class='btn-primary' onclick='editLecturer(" . $row['Lecturer_ID'] . ",\"" . $row['Name'] . "\",\"" . $row['Username'] . "\",\"" . $row['Email'] . "\",\"" . $row['Phone'] . "\")'>Edit</button>";
                        echo "<button class='btn-primary' style='margin-left:10px;' onclick=\"callAlert('Do you want to delete this lecturer record?', " . $row['Lecturer_ID'] . ")\">Delete</button>";


                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>


            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('addModal').style.display='none'">&times;</span>
                    <h2>Add Lecturer</h2>
                    <form method="post" action="add_lecturer.php">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required><br><br>
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required><br><br>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required><br><br>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required><br><br>
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" required><br><br>
                        <button type="submit">Add Lecturer</button>
                    </form>
                </div>
            </div>

            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('editModal').style.display='none'">&times;</span>
                    <h2>Edit Lecturer</h2>
                    <form id="editForm" method="post" action="update_lecturer.php">
                        <input type="hidden" id="lecturer_id" name="lecturer_id">
                        <label for="name">Name:</label>
                        <input type="text" id="edit_name" name="name" required><br><br>
                        <label for="username">Username:</label>
                        <input type="text" id="edit_username" name="username" required><br><br>
                        <label for="email">Email:</label>
                        <input type="email" id="edit_email" name="email" required><br><br>
                        <label for="phone">Phone:</label>
                        <input type="text" id="edit_phone" name="phone" required><br><br>
                        <button type="submit">Update Lecturer</button>
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
    // Get the editLecturer function
    function editLecturer(lecturerID, name, username, email, phone) {
        document.getElementById("lecturer_id").value = lecturerID;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_username").value = username;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_phone").value = phone;
        document.getElementById('editModal').style.display = 'block';
    }


    function deleteLecturer(lecturerID) {
        // Call the custom alert function
        callAlert("Are you sure you want to delete this lecturer?", lecturerID);
    }

    function callAlert(msg, lecturerID) {
        alert(msg, null, function(proceed) {
            if (proceed) {
                window.location.href = 'delete_lecturer.php?lecturer_id=' + lecturerID;
            }
        });
    }
    </script>

    <script src="main.js"></script>
</body>

</html>