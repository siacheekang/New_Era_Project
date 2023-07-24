<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../project/phpmailer/src/Exception.php';
require '../project/phpmailer/src/PHPMailer.php';
require '../project/phpmailer/src/SMTP.php';

if(isset($_POST["send"])){
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'developweb178@gmail.com';
    $mail->Password = 'fyuabnhgnmdlcmgz';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('developweb178@gmail.com');

    $mail->addAddress('developweb178@gmail.com');

    $mail->isHTML(true);

    $mail->Subject = $_POST["name"];
    $mail->Body = $_POST["message"];

    $mail->send();

    echo 
    "
    <script>
    alert('Sent Successfully');
    document.location.href = 'contact.php';
    </script>
    ";
}

?>