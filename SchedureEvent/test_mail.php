<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // SMTP config
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rurangirwakhassim84@gmail.com';         // Your Gmail address
    $mail->Password   = 'cbsu vzxr jvgq oiol';            // App password
    $mail->SMTPSecure = 'tls';                          // Encryption
    $mail->Port       = 587;

    // Sender and recipient
    $mail->setFrom('no-reply@nirdakms.com', 'NIRDAKMS');
    $mail->addAddress('rurakassim2020@gmail.com', 'Recipient');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email from PHPMailer via Gmail SMTP.';

    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
