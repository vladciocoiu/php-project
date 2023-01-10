<?php
    session_start();
    session_regenerate_id();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // csrf protection
        if(!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION['csrf_token']) {
            header("location: /project");
            exit;
        }

        $email = $_POST['email'];
        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];


        require_once __DIR__ . "/../send_email.php";
        sendEmail($email, $name, $subject, $message, 'vlad.ciocoiu0@gmail.com', 'Vlad Ciocoiu');
    }

    require_once 'vendor/autoload.php';

    $loader = new Twig\Loader\FilesystemLoader('views');
    $twig = new Twig\Environment($loader);

    echo $twig->render('contact.html', [
        'csrf_token' => $_SESSION['csrf_token'],
    ]);

?>