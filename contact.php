<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If necessary, modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require 'vendor/autoload.php';

// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
$sender = getenv('SENDER_EMAIL');
$senderName = getenv('SENDER_NAME');

// Replace recipient@example.com with a "To" address. If your account
// is still in the sandbox, this address must be verified.
$recipient = getenv('RECIPIENT_EMAIL');

// Replace smtp_username with your Amazon SES SMTP user name.
$usernameSmtp = getenv('SMTP_USERNAME');

// Replace smtp_password with your Amazon SES SMTP password.
$passwordSmtp = getenv('SMTP_PASSWORD');

// Specify a configuration set. If you do not want to use a configuration
// set, comment or remove the next line.
// $configurationSet = 'ConfigSet';

// If you're using Amazon SES in a region other than US West (Oregon),
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
// endpoint in the appropriate region.
$host = getenv('HOST_DOMAIN');
$port = getenv('HOST_PORT');

// The subject line of the email
$emailSubject = 'Message from Contact Form';

$name = htmlspecialchars($_POST['name']);
$company = htmlspecialchars($_POST['company']);
$email = htmlspecialchars($_POST['email']);
$messageSubject = htmlspecialchars($_POST['subject']);
$message = htmlspecialchars($_POST['message']);

// The plain-text body of the email
$bodyText =  "Name:\r\n{$name}\r\n\r\n";
$bodyText .= "Company:\r\n{$company}\r\n\r\n";
$bodyText .= "Email:\r\n{$email}\r\n";
$bodyText .= "Subject:\r\n{$messageSubject}\r\n";
$bodyText .= "Message:\r\n{$message}";


// The HTML-formatted body of the email
$bodyHtml = "<p><b>Name:</b><br/>{$name}</p>";
$bodyHtml .= "<p><b>Company:</b><br/>{$company}</p>";
$bodyHtml .= "<p><b>Email:</b><br/>{$email}</p>";
$bodyHtml .= "<p><b>Subject:</b><br/>{$messageSubject}</p>";
$bodyHtml .= "<p><b>Message:</b><br/>{$message}</p>";

$mail = new PHPMailer(true);

try {
    // Specify the SMTP settings.
    $mail->isSMTP();
    $mail->setFrom($sender, $senderName);
    $mail->Username   = $usernameSmtp;
    $mail->Password   = $passwordSmtp;
    $mail->Host       = $host;
    $mail->Port       = $port;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'tls';
    $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

    // Specify the message recipients.
    $mail->addAddress($recipient);
    // You can also add CC, BCC, and additional To recipients here.

    // Specify the content of the message.
    $mail->isHTML(true);
    $mail->Subject    = $emailSubject;
    $mail->Body       = $bodyHtml;
    $mail->AltBody    = $bodyText;
    $mail->Send();

    $location = getenv('RETURN_LOCATION');
    header("Location: {$location}");
    exit;
} catch (phpmailerException $e) {
    error_log("An error occurred. {$e->errorMessage()}"); //Catch errors from PHPMailer.
    header("Location: {$location}");
    exit;
} catch (Exception $e) {
    error_log("Email not sent. {$mail->ErrorInfo}"); //Catch errors from Amazon SES.
    header("Location: {$location}");
    exit;
}

?>


