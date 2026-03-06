<?php
namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

class Mailer
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }

    public function send($to, $subject, $message)
    {
        $config = require __DIR__ . '/../../config/mail.php';
        try {
            
            $this->mailer->isSMTP();
            $this->mailer->Host = $config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $config['username'];
            $this->mailer->Password = $config['password'];
            $this->mailer->SMTPSecure = $config['encryption'];
            $this->mailer->Port = $config['port'];

            $this->mailer->setFrom($config['from_email'], $config['from_name']);
            $this->mailer->addAddress($to);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;

            $result = $this->mailer->send();
            
            return $result;
        } catch (MailerException $e) {
            throw new \RuntimeException(
                'Mailer Error: ' . $this->mailer->ErrorInfo
            );
        }
    }
}