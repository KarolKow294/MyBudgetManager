<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail {
    public static function send($to, $subject, $text, $html) {
        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = Config::SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = Config::SMTP_USER;
            $mail->Password = Config::SMTP_PASS;
            $mail->SMTPSecure = 'tsl';
            $mail->Port = 587;

            $mail->setFrom(Config::MAIL_SENDER);
            $mail->addAddress($to);
            $mail->addReplyTo($to);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = $text;

            $mail->send();

            $sent = true;

        } catch (Exception $e) {

            $errors[] = $mail->ErrorInfo;

        }
    }
}