<?php
    require_once __DIR__ . "/load_env.php";

    $servername = $_ENV['DB_SERVER_NAME'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection error: " . $conn->connect_error);
    }
?>