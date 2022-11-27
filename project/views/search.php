<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
        header("location: /project/login");
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

    $items = [];
    if(!empty($_SERVER["QUERY_STRING"])) {
        parse_str($_SERVER["QUERY_STRING"], $params);;

        $table_name = "";
        if($params['type'] == 'book') $table_name = 'books';

        $sql = "SELECT * FROM items JOIN " . $table_name . " ON id = item_id WHERE quantity > 0 ";
        if(!empty($params['title'])) {
            $title = $params['title'];
            $sql = $sql."AND title = '$title' ";
        }
        if(!empty($params['author'])) {
            $author = $params['author'];
            $sql = $sql."AND author = '$author' ";
        }
        if(!empty($params['genre'])) {
            $genre = $params['genre'];
            $sql = $sql."AND genre = '$genre' ";
        }

        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($items, $row);
            }
        } else {
            echo 'No items found.';
        }
    }

    // echo count($items);

    $conn->close();
?>

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
    <main class="search-main">
        <h1 class="search-heading">Search</h1>
        <form class="search-form" method="get">

            <select class="type-select" name="type">
                <option value="book">Book</option>
            </select>
            <input type="text" name="title" placeholder="Title" />
            <input type="text" name="author" placeholder="Author" />
            <input type="text" name="genre" placeholder="Genre" />
            <input type="submit" value="Submit" class="submit" />    
        </form>
        <div class="items-list">
            <?php 
                foreach($items as $item) {
                    echo "<div class='item'>";
                    echo "<p class='item-title'>Title: " . $item['title'] . "</p>";
                    echo "<p class='item-author'>Author: " . $item['author'] . "</p>";
                    echo "<p class='item-genre'>Genre: " . $item['genre'] . "</p>";
                    echo "<p class='item-quantity'>Quantity: " . $item['quantity'] . "</p>";
                    echo "<form class='borrow-from' action='/project/borrowings' method='POST'>";
                    echo "<input type='date' name='date' min= '" .date('Y-m-d') . "' max='" . date('Y-m-d', strtotime('+2 months')) . "' class='date-input' required />";
                    echo "<input type='number' name='item_id' value='" . $item['item_id'] . "' class='input-hidden' />";
                    echo "<input type='submit' value='Borrow' class='submit' />";
                    echo "</form>";
                    echo "</div>";
                }
            ?>
        </div>
    </main>
</body>
</html>