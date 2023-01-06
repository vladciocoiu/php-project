<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
        header("location: /project/login");
        exit;
    }

    // connect to db
    require __DIR__."/../db_connect.php";

    $user_id = $_SESSION['id'];

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // csrf protection
        if(!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
            header("location: /project");
            exit;
        }

        $date = date("Y-m-d", strtotime($_POST['date']));

        // check for valid date in case of spoofing
        if(strtotime("+2 months") < strtotime($_POST['date'])) {
            echo "Something went wrong.";
            exit;
        }

        $item_id = $_POST['item_id'];

        $quant_res = $conn->query("SELECT * FROM items WHERE id = $item_id");
        $quant = $quant_res->fetch_assoc()['quantity'];

        // check quantity in case of spoofing
        if($quant <= 0) {
            echo "Something went wrong.";
            exit;
        }
        $update_quantity_sql = "UPDATE items SET quantity = quantity - 1 WHERE id = $item_id";

        if ($conn->query($update_quantity_sql) === false) {
            echo "Something went wrong.";
            exit;
        }

        $insert_sql = "INSERT INTO borrowings (item_id, user_id, due_date) VALUES ($item_id, $user_id, '$date')";

        if ($conn->query($insert_sql) === false) {
            echo "Something went wrong.";
            exit;
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