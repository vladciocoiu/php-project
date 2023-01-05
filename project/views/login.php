<?php
    session_start();
    
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: /project");
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

    $name = $password = $email = "";
    $email_err = $password_err = $login_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["email"]))) {
            $email_err = "Please enter an email address.";
        } else {
            $email = trim($_POST["email"]);
        }

        if(empty((trim($_POST["password"])))) {
            $password_err = "Please enter a password.";
        } else {
            $password = trim($_POST["password"]);
        }

        if(empty($password_err) && empty($email_err)) {
            $sql = "SELECT * FROM users WHERE email = ?";

            // query execution with bound parameters
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $email);
            $stmt->execute();

            $result = $stmt->get_result();

            // email exists, check password
            if($result->num_rows == 1) {
                $row = $result->fetch_assoc();

                // password is also good => log the user in
                if(password_verify($password, $row["password"])) {

                    session_start();
                            
                    // store data in session 
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row["id"];
                    $_SESSION["email"] = $row["email"];    
                    $_SESSION["name"] = $row["name"];
                    $_SESSION["roles"] = explode(",", $row["roles"]);

                    // create a random token for csrf protection
                    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
                    
                    // Redirect user to home page
                    header("location: /project");
                } else {
                    $login_err = "Invalid email or password.";
                }
            } else {
                $login_err = "Invalid email or password.";
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
    <main class="login-main">
        <h1 class="login-heading">Login</h1>
        <form class="login-form" method="POST">
            <?php 
                if(!empty($email_err)) {
                    echo("<div class='error'><p>". $email_err."</p></div>");
                } elseif(!empty($password_err)) {
                    echo("<div class='error'><p>". $password_err."</p></div>");
                } elseif(!empty($login_err)) {
                    echo("<div class='error'><p>". $login_err."</p></div>");
                }
            ?>
            <input type="email" placeholder="Email" name="email" required />
            <input type="password" placeholder="Password" name="password" required />
            <input type="submit" value="Login" class="submit" />    
        </form>
        <a href="/project/register">Don't have an account? Register.</a>
    </main>
</body>
</html>