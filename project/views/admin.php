<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
        header("location: /project/login");
        exit;
    }

    // prevent regular users from entering the admin panel
    if(!isset($_SESSION["roles"]) || in_array("admin", $_SESSION["roles"]) === false){
        header("location: /project");
        exit;
    }

    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "test";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection error: " . $conn->connect_error);
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>Admin Panel</title>
</head>
<body>
    <main class="admin-main">
        <h1 class="admin-heading">Admin Panel</h1>

        </div>
    </main>
</body>
</html>