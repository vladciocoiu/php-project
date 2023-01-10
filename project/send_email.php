<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;

  require_once 'vendor/autoload.php';

  require_once __DIR__ . "/load_env.php";

  function sendEmail($email, $name, $subject, $message) {
    $mail = new PHPMailer(true);

    $mail->IsSMTP();

    try {
    // $mail->SMTPDebug = 3;
    $mail->SMTPAuth = true;

    $mail->SMTPSecure = "ssl";
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->Username = $_ENV['GMAIL_USERNAME']; // Gmail username
    $mail->Password = $_ENV['GMAIL_PASSWORD']; // Gmail password

    $mail->AddReplyTo($email, $name);

    // set the receiver address and name
    $mail->AddAddress('vlad.ciocoiu0@gmail.com', 'Vlad Ciocoiu');

    // set who the message is from
    $mail->SetFrom('vci.phpmailer@gmail.com', $name);

    $mail->Subject = $subject;
    $mail->AltBody = 'Just a message bro';

    $mail->MsgHTML($message);

    $mail->Send();

    // echo "Message Sent OK\n";

    } catch (phpmailerException $e) {
      echo $e->errorMessage(); // error from PHPMailer

    } catch (Exception $e) {
      echo $e->getMessage(); // error from anything else!
    }
  }
?>
