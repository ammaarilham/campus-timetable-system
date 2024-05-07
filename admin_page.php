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
    <title>Admin Records</title>
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

        // Fetch all admin records
        $query = "SELECT * FROM admin";
        $result = mysqli_query($conn, $query);
    ?>

    <div class="wrapper-admin">

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
                <h2>Admin Records</h2>
                <div>
                    <button class="btn-primary" onclick="document.getElementById('addModal').style.display='block'">Add
                        Admin</button>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Admin ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>".$row['Admin_ID']."</td>";
                    echo "<td>".$row['Name']."</td>";
                    echo "<td>".$row['Username']."</td>";
                    echo "<td>".$row['Email']."</td>";
                    echo "<td>".$row['Phone']."</td>";
                    echo "<td>";
                    echo "<button class='btn-primary' onclick='editAdmin(" . $row['Admin_ID'] . ",\"" . $row['Name'] . "\",\"" . $row['Username'] . "\",\"" . $row['Email'] . "\",\"" . $row['Phone'] . "\")'>Edit</button>";
                    echo "<button class='btn-primary' style='margin-left:10px;' onclick=\"callAlert('Do you want to delete this admin record?', " . $row['Admin_ID'] . ")\">Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            ?>
                </tbody>
            </table>

            <!-- Add Admin Modal -->
            <div id="addModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('addModal').style.display='none'">&times;</span>
                    <h2>Add Admin</h2>
                    <form method="post" action="add_admin.php">
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
                        <button type="submit">Add Admin</button>
                    </form>
                </div>
            </div>

            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close"
                        onclick="document.getElementById('editModal').style.display='none'">&times;</span>
                    <h2>Edit Admin</h2>
                    <form id="editForm" method="post" action="update_admin.php">
                        <input type="hidden" id="admin_id" name="admin_id">
                        <label for="name">Name:</label>
                        <input type="text" id="edit_name" name="name" required><br><br>
                        <label for="username">Username:</label>
                        <input type="text" id="edit_username" name="username" required><br><br>
                        <label for="email">Email:</label>
                        <input type="email" id="edit_email" name="email" required><br><br>
                        <label for="phone">Phone:</label>
                        <input type="text" id="edit_phone" name="phone" required><br><br>
                        <button type="submit">Update Admin</button>
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
    // Get the editAdmin function
    function editAdmin(adminID, name, username, email, phone) {
        document.getElementById("admin_id").value = adminID;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_username").value = username;
        document.getElementById("edit_email").value = email;
        document.getElementById("edit_phone").value = phone;
        document.getElementById('editModal').style.display = 'block';
    }



    function deleteAdmin(adminID) {
        // Call the custom alert function
        callAlert("Are you sure you want to delete this admin?", adminID);
    }

    function callAlert(msg, adminID) {
        alert(msg, null, function(proceed) {
            if (proceed) {
                // If user clicks proceed, redirect to delete_student.php with studentID as parameter
                window.location.href = 'delete_admin.php?admin_id=' + adminID;
            }
        });
    }
    </script>

    <script src="main.js"></script>


</body>

</html>