<?php
session_start();
include 'connection.php'; // Include database connection file

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    // If not logged in, redirect to login page
    header("Location: index.php");
    exit; // Stop further execution
}

// Check if the search query is provided
if (isset($_GET['search_query'])) {
    // Sanitize the search query to prevent SQL injection
    $search_query = mysqli_real_escape_string($conn, $_GET['search_query']);

    // Query to search for a student based on the provided query
    $query = "SELECT * FROM student WHERE Name LIKE '%$search_query%' OR Zoho_Number LIKE '%$search_query%' OR Email LIKE '%$search_query%' OR Phone LIKE '%$search_query%'";
    $result = mysqli_query($conn, $query);

    // Check if there are any matching records
    if (mysqli_num_rows($result) > 0) {
        // Fetch and display search results
        while ($row = mysqli_fetch_assoc($result)) {
            // Output the search results in the desired format (e.g., table rows)
            echo "<tr>";
            echo "<td>" . $row['Student_ID'] . "</td>";
            echo "<td>" . $row['Batch_ID'] . "</td>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>" . $row['Zoho_Number'] . "</td>";
            echo "<td>" . $row['Email'] . "</td>";
            echo "<td>" . $row['Phone'] . "</td>";
            echo "<td>";
            // Include any actions you want to provide for each search result (e.g., edit, delete)
            echo "<button class='btn-primary' onclick='editStudent(" . $row['Student_ID'] . ",\"" . $row['Batch_ID'] . "\",\"" . $row['Name'] . "\",\"" . $row['Zoho_Number'] . "\",\"" . $row['Email'] . "\",\"" . $row['Phone'] . "\")'>Edit</button>";
            echo "<button class='btn-primary' style='margin-left:10px;' onclick=\"callAlert('Do you want to delete this item?', " . $row['Student_ID'] . ")\">Delete</button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        // No matching records found
        echo "<tr><td colspan='7'>No matching records found.</td></tr>";
    }
} else {
    // No search query provided, display an error message or handle accordingly
    echo "<tr><td colspan='7'>No search query provided.</td></tr>";
}
?>