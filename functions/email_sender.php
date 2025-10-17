<?php
// Ensure PHPMailer is loaded (adjust path based on where you placed the library)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';

/**
 * Sends a modular email using SMTP settings.
 *
 * @param string $recipientEmail The user's email address.
 * @param string $subject The email subject line.
 * @param string $bodyHtml The HTML content of the email.
 * @return bool True on success, False on failure.
 */
function sendOrderConfirmationEmail($recipientEmail, $subject, $bodyHtml)
{
    // Note: Do NOT use PHPMailer(true) if you don't want exceptions thrown for testing
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ashir9112001@gmail.com';
        $mail->Password   = 'vqkha shzls kqkrh yojzi';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('no-reply@kharido.pk', 'Kharido.pk Order Service');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHtml;
        $mail->AltBody = strip_tags($bodyHtml);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
