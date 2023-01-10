<?php

echo 'ok';


    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: /project");
        exit;
    }

    // connect to db
    require __DIR__."/../db_connect.php";

    $code = $email = "";

    if (!empty($_SERVER["QUERY_STRING"])) {
        parse_str($_SERVER["QUERY_STRING"], $params);

        $email = trim($params["email"]);
        $code = trim($params["activation_code"]);

        // email validation
        $email_regex = "/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/";
        if (empty($email) || preg_match($email_regex, $email) === 0) {
            echo "Invalid email address.";
            exit;
        }
        $sql = "SELECT * FROM users WHERE email = ?";

        // query execution with bound parameters
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            echo "User does not exist.";
            exit;
        } elseif ($result->num_rows > 1) {
            echo "Something went wrong.";
            exit;
        }

        $row = $result->fetch_assoc();
        if ($row['active'] == 1) {
            $conn->close();

            header("location: /project/login");
            exit;
        }

        if($row['activation_code'] !== $code) {
            echo "Invalid activation code.";
            exit;
        }

        $sql = "UPDATE users SET active = 1 WHERE id = " . $row['id'];

        $result = $conn->query($sql);
        if ($result === false) {
            echo "SQL error.";
            exit;
        }


        $conn->close();

        header("location: /project/login");
    }
?>