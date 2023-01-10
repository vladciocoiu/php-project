<?php
    require_once 'vendor/autoload.php';

    require __DIR__."/../db_connect.php";

    require __DIR__ . "/../get_stats.php";
    
    incrementHomeViews($conn);
    $conn->close();

    $loader = new Twig\Loader\FilesystemLoader('views');
    $twig = new Twig\Environment($loader);

    session_start(); 

    echo $twig->render('home.html', [
        'logged_in' => (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true),
        'name' => $_SESSION["name"],
        'admin' => (isset($_SESSION["roles"]) and in_array("admin", $_SESSION["roles"]) === true)
    ]);
?>