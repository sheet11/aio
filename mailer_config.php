<?php
// ============================================
// KONFIGURASI EMAIL (Gmail SMTP)
// ============================================
// Wajib: composer require phpmailer/phpmailer
// Jalankan di folder project: composer require phpmailer/phpmailer

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'isi_email_pengirim@gmail.com');
define('SMTP_PASS', 'isi_app_password_16_digit'); // App Password, bukan password biasa Gmail
define('SMTP_PORT', 587);
define('SMTP_FROM_NAME', 'OIA Admissions Desk - Poltekkes Kemenkes Bengkulu');

/**
 * Kirim satu email. Return ['ok' => bool, 'error' => string|null]
 */
function kirimEmailTidakLulus($toEmail, $toName, $subject, $bodyText)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($toEmail, $toName);

        $mail->Subject = $subject;
        $mail->isHTML(false);
        $mail->Body    = $bodyText;

        $mail->send();
        return ['ok' => true, 'error' => null];
    } catch (Exception $e) {
        return ['ok' => false, 'error' => $mail->ErrorInfo];
    }
}
