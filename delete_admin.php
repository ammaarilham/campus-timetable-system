<?php
include 'connection.php';

   
    $admin_id = $_GET['admin_id'];

    $query = "DELETE FROM admin WHERE Admin_ID = $admin_id";
    $result = mysqli_query($conn,$query);

    if($result){
        header("Location: admin_page.php");
    }
    else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);


?>