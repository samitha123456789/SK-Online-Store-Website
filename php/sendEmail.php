<?php


require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fullName = htmlspecialchars($_POST['fullName']); 
    $email = htmlspecialchars($_POST['email']);       
    $message = htmlspecialchars($_POST['message']);   

    // Set up the email
    $to = "samithamadhushanka123@gmail.com"; 
    $subject = "New Message from Contact Form - SK Online Store";
    $body = "
    You have received a new message from the contact form:

    Name: $fullName
    Email: $email
    Message: 
    $message
    ";

    
    $mail = new PHPMailer(true); 

    try {
        // Server settings
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.mailtrap.io'; 
        $mail->SMTPAuth = true; 
        $mail->Username = '422d33dc07cb59'; 
        $mail->Password = '0892b346769ec5'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port = 2525; 

        // Recipients
        $mail->setFrom($email, $fullName); // Sender's email and name
        $mail->addAddress($to); // Recipient email address

        // Content
        $mail->isHTML(false); // Set email format to plain text
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Send the email
        if ($mail->send()) {
            echo "Email sent successfully.";
        } else {
            echo "Failed to send email.";
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
