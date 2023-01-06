<?php
    require __DIR__."/../controllers/register_controller.php";
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
 </script>

</body>
</html>