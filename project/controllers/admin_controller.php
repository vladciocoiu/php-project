<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
        header("location: /project/login");
        exit;
    }

    // prevent regular users from doing this
    if(!isset($_SESSION["roles"]) || in_array("admin", $_SESSION["roles"]) === false){
        header("location: /project");
        exit;
    }

    // connect to db
    require __DIR__."/../db_connect.php";

    $conn->close();

    require_once 'vendor/autoload.php';

    $loader = new Twig\Loader\FilesystemLoader('views');
    $twig = new Twig\Environment($loader);

    echo $twig->render('admin.html', [

    ]);

?>