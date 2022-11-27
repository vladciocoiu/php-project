<?php
    // require_once "config.php";
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "test";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection error: " . $conn->connect_error);
    }

    $name = $password = $email = "";
    $email_err = $password_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["email"]))) {
            $email_err = "Please enter an email address.";
        } else {
            $param_email = $_POST["email"];
            $result = $conn->query("SELECT * FROM users WHERE email = '$param_email'");
            if($result->num_rows > 0) {
                $email_err = "Email already taken.";
            }
        }
        $email = trim($_POST["email"]);

        if(empty((trim($_POST["password"])))) {
            $password_err = "Please enter a password.";
        } elseif(strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        }
        if(empty($password_err) && empty($email_err)) {
            $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
            $name = $_POST["name"];
        
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        
            if ($conn->query($sql) === TRUE) {
                header("location: /project/login");
                exit;
            } else {
                echo "Something went wrong.";
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
    <main class="register-main">
        <h1 class="register-heading">Register</h1>
        <form class="register-form" method="POST">
            <?php 
                if(!empty($email_err)) {
                    echo("<div class='error'><p>". $email_err."</p></div>");
                } elseif(!empty($password_err)) {
                    echo("<div class='error'><p>". $password_err."</p></div>");
                }
            ?>
            <input type="email" placeholder="Email" name="email" required />
            <input type="text" placeholder="Name" name="name" required />
            <input type="password" placeholder="Password" name="password" required />
            <input type="submit" value="Submit" class="submit" />    
        </form>
        <a href="/project/login">Already have an account? Log in.</a>
    </main>
</body>
</html>