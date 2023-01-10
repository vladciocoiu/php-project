<?php
    require_once __DIR__ . "/../scrape_rating.php";

    session_start();
    session_regenerate_id();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false){
        header("location: /project/login");
        exit;
    }

    // connect to db
    require __DIR__."/../db_connect.php";

    $validation_err = $validation_succ = '';
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // csrf protection
        if(!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
            header("location: /project");
            exit;
        }

        // prevent regular users from doing this
        if(!isset($_SESSION["roles"]) || in_array("admin", $_SESSION["roles"]) === false){
            header("location: /project/items");
            exit;
        }

        // fake put request
        // admins can modify quantity of items
        if($_POST['_method'] === 'PUT') {
            $id = intval($_POST['item_id']);

            $quantity = intval($_POST['quantity']);

            if($id <= 0 || $quantity <= 0) {
                echo "Something went wrong.";
                exit;
            }

            $sql = 'UPDATE items SET quantity = ' . $quantity . ' WHERE id = ' . $id;
            $result = $conn->query($sql);

            if ($result === true) {
                header("location: /project/items");
                exit;
            } else {
                echo "Something went wrong";
                exit;
            }

        // fake delete request 
        } elseif($_POST['_method'] === 'DELETE') {
            $id = intval($_POST['item_id']);

            if($id <= 0) {
                echo "Something went wrong.";
                exit;
            }

            $sql = 'DELETE FROM items WHERE id=' . $id;
            $result = $conn->query($sql);

            if ($result === true) {
                header("location: /project/items");
                exit;
            } else {
                echo "Something went wrong";
                exit;
            }

        // actual post request
        } else {

            // type validation
            $type = strtolower(trim($_POST['type']));
            $available_types = ['book'];

            if (array_search($type, $available_types) === false) {
                $validation_err = 'Invalid item type.';
            }

            // title validation
            $title = trim($_POST["title"]);
            $title_regex = "/\b[\w,.?!:]+\b/";

            if (preg_match($title_regex, $title) === 0) {
                $validation_err = "Invalid title.";
            } elseif (empty($title)) {
                $validation_err = "Please enter a title.";
            } else {
                // capitalize the title correctly
                $title = ucwords(strtolower($title), "?!:., \t\r\n\f\v");

                $sql = "SELECT * FROM books WHERE title = ?";

                // query execution with bound parameters
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $title);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $validation_err = "There already exists an item with this title.";
                }
            }

            // genre validation
            $genre = strtolower(trim($_POST["genre"]));
            $genre_regex = "/\b\w+\b/";
            if (preg_match($genre_regex, $genre) === 0) {
                $validation_err = "Invalid genre.";
            } elseif (empty($genre)) {
                $validation_err = "Please enter a genre.";
            }

            // author validation
            $author = ucwords(strtolower(trim($_POST["author"])), "?!:., \t\r\n\f\v");
            $author_regex = "/\b\w+\b/";

            if (preg_match($author_regex, $author) === 0) {
                $validation_err = "Invalid author.";
            } elseif (empty($author)) {
                $validation_err = "Please enter an author.";
            }

            // quantity validation
            $quantity = intval($_POST["quantity"]);
            if ($quantity <= 0) {
                $validation_err = "Invalid quantity.";
            }

            if (empty($validation_err)) {
                // insert in items table
                $sql = "INSERT INTO items (type, quantity) VALUES (?, ?)";

                // query execution with bound parameters
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('si', $type, $quantity);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($stmt->errno !== 0) {
                    echo "Something went wrong.";
                    exit;
                }

                // retrieve id 
                $id = $conn->insert_id;

                #insert into books table
                $sql = "INSERT INTO books (item_id, title, author, genre) VALUES (?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param('isss', $id, $title, $author, $genre);
                $stmt->execute();

                $result = $stmt->get_result();

                if ($stmt->errno === 0) {
                    $validation_succ = 'Item added.';
                } else {
                    echo "Something went wrong.";
                }
            }
        }
    }

    $items = [];
    if(!empty($_SERVER["QUERY_STRING"])) {
        parse_str($_SERVER["QUERY_STRING"], $params);

        $table_name = "";
        if($params['type'] == 'book') $table_name = 'books';

        $sql = "SELECT * FROM items JOIN " . $table_name . " ON id = item_id WHERE quantity > 0 ";
        $params_arr = [];
        $param_types = '';

        if(!empty($params['title'])) {
            array_push($params_arr, $params['title']);
            $param_types .= 's';
            $sql = $sql."AND title = ? ";
        }
        if(!empty($params['author'])) {
            array_push($params_arr, $params['author']);
            $param_types .= 's';
            $sql = $sql."AND author = ? ";
        }
        if(!empty($params['genre'])) {
            array_push($params_arr, $params['genre']);
            $param_types .= 's';
            $sql = $sql."AND genre = ? ";
        }
        array_unshift($params_arr, $param_types);

        // executes a query with bound parameters, which should be safe against sql injections
        $stmt = $conn->prepare($sql);
        if (!empty($param_types)) {
            call_user_func_array(array($stmt, 'bind_param'), $params_arr);
        }
        $stmt->execute();

        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $item = array_merge($row, scrapeRating($row['title']));
                array_push($items, $item);
            }
        } else {
            echo 'No items found.';
        }
    }

    $conn->close();

    require_once 'vendor/autoload.php';

    $loader = new Twig\Loader\FilesystemLoader('views');
    $twig = new Twig\Environment($loader);

    echo $twig->render('items.html', [
        'items' => $items,
        'admin' => (isset($_SESSION["roles"]) && in_array("admin", $_SESSION["roles"])) ,
        'validation_err' => $validation_err,
        'validation_succ' => $validation_succ,
        'csrf_token' => $_SESSION['csrf_token'],
        'date_now' => date('Y-m-d'),
        'date_max' => date('Y-m-d', strtotime('+2 months'))
    ]);
?>