<?php
    require __DIR__."/../middleware/verify_captcha.php";

    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: /project");
        exit;
    }

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
            $activation_code = bin2hex(random_bytes(16));
        
            $sql = "INSERT INTO users (name, email, password, activation_code) VALUES (?, ?, ?, ?)";

            // query execution with bound parameters
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $name, $email, $password, $activation_code);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($stmt->errno === 0) {
                // send activation email
                require_once __DIR__ . "/../send_email.php";
                require_once __DIR__ . "/../load_env.php";

                $activation_link = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "/activate?email=$email&activation_code=$activation_code";
                $subject = 'Vlad National Library Account Verification';
                $message = "Hi $name! Please activate your Vlad National Library account by accessing  the following link: " . $activation_link;
                sendEmail($_ENV['GMAIL_USERNAME'], 'VCI Verificator', $subject, $message, $email, $name);

                header("location: /project/login");
                exit;
            } else {
                echo "Something went wrong.";
            }
        }
    }

    $conn->close();

    require_once 'vendor/autoload.php';

    $loader = new Twig\Loader\FilesystemLoader('views');
    $twig = new Twig\Environment($loader);

    echo $twig->render('register.html', [
        'email_err' => $email_err,
        'password_err' => $password_err,
        'name_err' => $name_err,
        'captcha_err' => $captcha_err,
    ]);
?>