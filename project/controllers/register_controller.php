<?php
    require __DIR__."/../middleware/verify_captcha.php";

    // connect to db
    require __DIR__."/../db_connect.php";
    
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

    // require __DIR__."/../views/register.php";
?>