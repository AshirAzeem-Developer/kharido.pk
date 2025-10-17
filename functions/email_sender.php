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
        // Server Settings (!!! REPLACE THESE WITH YOUR ACTUAL SMTP CREDENTIALS !!!)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';   // E.g., 'smtp.gmail.com', 'smtp-mail.outlook.com'
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ashir9112001@gmail.com'; // Your full email address
        $mail->Password   = 'vqkh shzl kqkr yojz';     // Your SMTP password/App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Standard encryption for port 587
        $mail->Port       = 587;                          // Standard port for STARTTLS

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
        // Log the error for debugging purposes
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
