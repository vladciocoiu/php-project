<?php
    session_start();

    unset($_SESSION["loggedin"]);
    unset($_SESSION["id"]);
    unset($_SESSION["email"]);    
    unset($_SESSION["name"]); 
    unset($_SESSION["roles"]);
    unset($_SESSION['csrf_token']);

    session_destroy();
    header("location: /project");
?>