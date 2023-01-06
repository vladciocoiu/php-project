<?php
    require __DIR__."/../verify_captcha.php";

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
    $email_err = $password_err = $name_err = $captcha_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // verify captcha
        if(!verifyCaptchaResponse($_POST['g-recaptcha-response'])) {
            $captcha_err = 'You failed the captcha.';

        } else {
            $email = trim($_POST["email"]);

            // email validation
            $email_regex = "/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/";
            if (preg_match($email_regex, $email) === 0) {
                $email_err = "Invalid email address.";
            } elseif (empty($email)) {
                $email_err = "Please enter an email address.";
            } else {
                $sql = "SELECT * FROM users WHERE email = ?";

                // query execution with bound parameters
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $email);
                $stmt->execute();

                $result = $stmt->get_result();
                // $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
                if ($result->num_rows > 0) {
                    $email_err = "Email already taken.";
                }
            }

            $name = trim($_POST["name"]);

            // name validation (to prevent xss)
            $name_regex = "/^[A-Za-z-]+( [A-Za-z-]+)*$/";
            if (preg_match($name_regex, $name) === 0) {
                $name_err = "Invalid name.";
            }

            // password validation
            if (empty((trim($_POST["password"])))) {
                $password_err = "Please enter a password.";
            } elseif (strlen(trim($_POST["password"])) < 6) {
                $password_err = "Password must have at least 6 characters.";
            }
        }

        if(empty($captcha_err) && empty($password_err) && empty($email_err) && empty($name_err)) {
            $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
        
            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";

            // query execution with bound parameters
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $name, $email, $password);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($stmt->errno === 0) {
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
    <title>Register</title>
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
                } elseif(!empty($name_err)) {
                    echo("<div class='error'><p>". $name_err."</p></div>");
                } elseif(!empty($captcha_err)) {
                    echo("<div class='error'><p>". $captcha_err."</p></div>");
                }
            ?>
            <input type="email" placeholder="Email" name="email" required />
            <input type="text" placeholder="Name" name="name" required />
            <input type="password" placeholder="Password" name="password" required />
            <br />
            <div class="g-recaptcha" data-sitekey="6LcvrNYjAAAAAKqcfRLqVq1_Zkf-SVG9fSj7ji8g"></div>
            <input type="submit" value="Submit" class="submit" />    
        </form>
        <a href="/project/login">Already have an account? Log in.</a>
    </main>
     <script>
   function onSubmit(token) {
     document.getElementById("demo-form").submit();
   }
 </script>

</body>
</html>