<?php
    session_start();
    session_regenerate_id();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
        header("location: /project/login");
        exit;
    }

    // connect to db
    require __DIR__."/../db_connect.php";

    require __DIR__ . "/../get_stats.php";


    require_once 'vendor/autoload.php';

    $loader = new Twig\Loader\FilesystemLoader('views');
    $twig = new Twig\Environment($loader);

    echo $twig->render('stats.html', [
        'totalHomeViews' => getTotalHomeViews($conn),
        'totalVisitors' => getUniqueVisitors($conn)
    ]);

    $conn->close();
?>