<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>Library App</title>
</head>
<body>
    <main class="index-main">
        <?php
            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                echo("<h1>Hi ".$_SESSION["name"]."!</h1>");
            }
        ?>
        <div class="link-wrapper">
            <a href="/project/register">Register</a>
            <a href="/project/login">Login</a>
            <a href="/project/logout">Logout</a>
            <a href="/project/search">Search Items</a>
            <a href="/project/borrowings">My Borrowings</a>
        </div>
    </main>


</body>
</html>