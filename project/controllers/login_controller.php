<?php
    require __DIR__."/../middleware/verify_captcha.php";

    session_start();
    
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: /project");
        exit;
    }

    // connect to db
    require __DIR__."/../db_connect.php";

    $name = $password = $email = "";
    $email_err = $password_err = $login_err = $captcha_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // verify captcha
        if (!verifyCaptchaResponse($_POST['g-recaptcha-response'])) {
            $captcha_err = 'You failed the captcha.';
        }

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

        if(empty($captcha_err) && empty($password_err) && empty($email_err)) {
            $sql = "SELECT * FROM users WHERE email = ?";

            // query execution with bound parameters
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $email);
            $stmt->execute();

            $result = $stmt->get_result();

            // email exists, check password
            if($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                
                // check if account is active
                if($row["active"] == 0) {
                    $login_err = 'Plase check your email and activate your account.';

                // password is also good => log the user in
                } elseif(password_verify($password, $row["password"])) {

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

    require_once 'vendor/autoload.php';
    require_once __DIR__ . "/../load_env.php";

    $loader = new Twig\Loader\FilesystemLoader('views');
    $twig = new Twig\Environment($loader);

    echo $twig->render('login.html', [
        'email_err' => $email_err,
        'password_err' => $password_err,
        'login_err' => $login_err,
        'captcha_err' => $captcha_err,
        'siteKey' => $_ENV['SITE_KEY']
    ]);
?>