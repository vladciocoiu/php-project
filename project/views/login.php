<?php
    require __DIR__."/../controllers/login_controller.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" />
    <title>Log In</title>
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
                } elseif(!empty($captcha_err)) {
                    echo("<div class='error'><p>". $captcha_err."</p></div>");
                }
            ?>
            <input type="email" placeholder="Email" name="email" required />
            <input type="password" placeholder="Password" name="password" required />
            <br />
            <div class="g-recaptcha" data-sitekey="6LcvrNYjAAAAAKqcfRLqVq1_Zkf-SVG9fSj7ji8g"></div>
            <input type="submit" value="Login" class="submit" />    
        </form>
        <a href="/project/register">Don't have an account? Register.</a>
    </main>
</body>
</html>