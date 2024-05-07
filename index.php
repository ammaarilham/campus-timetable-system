<?php

session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Timetable System</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <div class="container">
        <h1>Welcome to BCAS LSMS dashboard</h1>
        <div class="wrapper1">
            <div class="logo-container">
                <img src="images/bcaslogo.png" alt="Campus Timetable System Logo">

            </div>

            <div class="buttons-container">
                <form method="post" action="login.php">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email"><br><br>

                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password"><br><br>

                    <input type="submit" class="btn-default" value="Login">
                </form>
            </div>
        </div>
    </div>

</body>

</html>