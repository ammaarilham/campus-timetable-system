<?php
// Include database connection file
include 'connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if admin ID is provided
    if(isset($_POST['admin_id'])) {
        // Retrieve admin ID from the form
        $admin_id = $_POST['admin_id'];
        
        // Retrieve other form data
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        // Update admin record in the database
        $query = "UPDATE admin SET Name='$name', Username='$username', Email='$email', Phone='$phone' WHERE Admin_ID = $admin_id";

        if(mysqli_query($conn, $query)) {
            // Redirect to admin page with success message
            header("Location: admin_page.php?status=success");
            exit();
        } else {
            // Redirect to admin page with error message
            header("Location: admin_page.php?status=error");
            exit();
        }
    } else {
        // Redirect to admin page if admin ID is not provided
        header("Location: admin_page.php?status=error");
        exit();
    }
} else {
    // Redirect to admin page if form is not submitted
    header("Location: admin_page.php");
    exit();
}
?>