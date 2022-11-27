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

    $user_id = $_SESSION['id'];

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $date = date("Y-m-d", strtotime($_POST['date']));
        $item_id = $_POST['item_id'];

        $quant_res = $conn->query("SELECT * FROM items WHERE id = $item_id");
        $quant = $quant_res->fetch_assoc()['quantity'];

        if($quant <= 0) {
            echo "Something went wrong.";
        } else {
            $update_quantity_sql = "UPDATE items SET quantity = quantity - 1 WHERE id = $item_id";
            if ($conn->query($update_quantity_sql) === false) {
                echo "Something went wrong.";
            }
        }

        $insert_sql = "INSERT INTO borrowings (item_id, user_id, due_date) VALUES ($item_id, $user_id, '$date')";
        if ($conn->query($insert_sql) === false) {
            echo "Something went wrong.";
        }
    }

    $sql = "SELECT * FROM borrowings b JOIN items i ON i.id = b.item_id WHERE b.user_id = $user_id";
    
    $result = $conn->query($sql);
    $borrowings = [];
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($borrowings, $row);
        }
    } 

    $items = [];
    foreach($borrowings as $borrowing) {
        if($borrowing['type'] == 'book') {
            $id = $borrowing['item_id'];
            $query = $conn->query("SELECT * FROM books WHERE item_id = $id");
            if($query->num_rows == 1) {
                $row = $query->fetch_assoc();
                $item = array_merge($borrowing, $row);
                array_push($items, $item);
            }
        }
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
    <title>Library App</title>
</head>
<body>
    <main class="borrowings-main">
        <h1 class="borrowings-heading">My Borrowings</h1>
        <div class="borrowings-list">
            <?php
                if(count($items) > 0) {
                    foreach($items as $item) {
                        echo "<div class='borrowing'>";
                        echo "<p class='borrowing-type'>Type: " . $item['type'] . "</p>";
                        echo "<p class='borrowing-title'>Title: " . $item['title'] . "</p>";
                        echo "<p class='borrowing-author'>Author: " . $item['author'] . "</p>";
                        echo "<p class='borrowing-genre'>Genre: " . $item['genre'] . "</p>";
                        echo "<p class='borrowing-quantity'>Due Date: " . $item['due_date'] . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='no-borrowings'>No borrowings found.</p>";
                }
            ?>
        </div>
    </main>
</body>
</html>