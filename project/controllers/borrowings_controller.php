<?php
    session_start();
    session_regenerate_id();

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

        // fake put request
        // admins can modify the due date of borrowings via extension or early return
        if ($_POST['_method'] === 'PUT') {

            // prevent regular users from doing this
            if(!isset($_SESSION["roles"]) || in_array("admin", $_SESSION["roles"]) === false){
                header("location: /project/items");
                exit;
            }

            // check for spoofing
            if (strtotime($_POST['date']) < strtotime("today")) {
                echo "Something went wrong.";
                exit;
            }
            
            $date = date("Y-m-d", strtotime($_POST['date']));

            $id = intval($_POST['borrowing_id']);

            if($id <= 0) {
                echo "Something went wrong.";
                exit;
            }

            $sql = "UPDATE borrowings SET due_date = '$date' WHERE id = $id";

            $result = $conn->query($sql);
            if ($result === true) {
                header("Refresh:0");                
                exit;
            } else {
                echo "Something went wrong";
                exit;
            }

        // actual post request
        } else {

            $date = date("Y-m-d", strtotime($_POST['date']));

            // check for valid date in case of spoofing
            if (strtotime("+2 months") < strtotime($_POST['date'])) {
                echo "Something went wrong.";
                exit;
            }

            $item_id = intval($_POST['item_id']);

            if($id <= 0) {
                echo "Something went wrong.";
                exit;
            }

            $quant_res = $conn->query("SELECT * FROM items WHERE id = $item_id");
            $quant = $quant_res->fetch_assoc()['quantity'];

            // check quantity in case of spoofing
            if ($quant <= 0) {
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
    }


    // my borrowings
    $sql = "SELECT b.id, b.item_id, i.type, b.due_date FROM borrowings b JOIN items i ON i.id = b.item_id WHERE b.user_id = $user_id";
    
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
            $query = $conn->query("SELECT title, author, genre FROM books WHERE item_id = $id");
            if($query->num_rows == 1) {
                $row = $query->fetch_assoc();
                $item = array_merge($borrowing, $row);
                array_push($items, $item);
            }
        }
    }

    // results for search form
    $search_items = [];
    if(!empty($_SERVER["QUERY_STRING"])) {
        parse_str($_SERVER["QUERY_STRING"], $params);

        // csrf protection
        if(!isset($params["csrf_token"]) || $params["csrf_token"] !== $_SESSION['csrf_token']) {
            header("location: /project/borrowings");
            exit;
        }

        // prevent regular users from doing this
        if(!isset($_SESSION["roles"]) || in_array("admin", $_SESSION["roles"]) === false){
            header("location: /project/borrowings");
            exit;
        }

        if(!empty($params['email'])) {
            $email = $params['email'];

            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $email);
            $stmt->execute();
    
            $result = $stmt->get_result();

            if($result->num_rows === 1) {
                $id = $result->fetch_assoc()['id'];

                $sql = "SELECT b.id, b.item_id, i.type, b.due_date FROM borrowings b JOIN items i ON i.id = b.item_id WHERE b.user_id = $id";

    
                $result = $conn->query($sql);
                $search_borrowings = [];
                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        array_push($search_borrowings, $row);
                    }
                } 
            
                foreach($search_borrowings as $borrowing) {
                    if($borrowing['type'] == 'book') {
                        $item_id = $borrowing['item_id'];
                        $query = $conn->query("SELECT title, author, genre FROM books WHERE item_id = $item_id");
                        if($query->num_rows == 1) {
                            $row = $query->fetch_assoc();
                            $item = array_merge($borrowing, $row);

                            array_push($search_items, $item);
                        }
                    }
                }

            }
        }
    }

    $conn->close();

    require_once 'vendor/autoload.php';

    $loader = new Twig\Loader\FilesystemLoader('views');
    $twig = new Twig\Environment($loader);

    echo $twig->render('borrowings.html', [
        'borrowings' => $items,
        'csrf_token' => $_SESSION['csrf_token'],
        'search_borrowings' => $search_items,
        'admin' => (isset($_SESSION["roles"]) && in_array("admin", $_SESSION["roles"])),
        'date_now' => date('Y-m-d'),
    ]);
?>