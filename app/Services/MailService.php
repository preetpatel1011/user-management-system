<?php

namespace App\Services;

use App\Core\Mailer;

class MailService
{
    private $mail;
    private $config;
    public function __construct(Mailer $mailer)
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
        $this->mail = $mailer; 
    }

    /**
     * Summary of sendVerificationEmail
     * @param mixed $email
     * @param mixed $name
     * @param mixed $token
     * @return bool
     */
    public function sendVerificationEmail($email, $name, $token): bool
    {
        $link = "http://localhost:4000/verify?token=" . urlencode($token);

        $subject = "Email Verification - {$this->config['from_name']}";
        
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .button { 
                        display: inline-block; 
                        padding: 10px 20px; 
                        background-color: #007bff; 
                        color: #ffffff; 
                        text-decoration: none; 
                        border-radius: 5px; 
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Welcome, {$name}!</h2>
                    <p>Thank you for registering.</p>
                    <a href = {$link} class = 'btn btn-primary'>Verify here </a>
                    <p>{$link}</p>
                    <p>This link will expire in 24 hours.</p>
                </div>
            </body>
            </html>
        ";
        return $this->mail->send($email, $subject, $message);
    }

    /**
     * Summary of sendNewUserJoined
     * @param mixed $to
     * @param mixed $name
     * @param mixed $email
     * @return bool
     */
    public function sendNewUserJoined($to, $name, $email): bool
    {
        $subject = "Email Verification - {$this->config['from_name']}";
        
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .button { 
                        display: inline-block; 
                        padding: 10px 20px; 
                        background-color: #007bff; 
                        color: #ffffff; 
                        text-decoration: none; 
                        border-radius: 5px; 
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Alert, Admin!</h2>
                    <p>New user has joined</p>
                    <p>{$name}</p>
                    <p>{$email}</p>
                </div>
            </body>
            </html>
        ";

        return $this->mail->send($to, $subject, $message);
    }

    /**
     * Summary of sendApprovalMail
     * @param mixed $email
     * @param mixed $name
     * @return bool
     */
    public function sendApprovalMail($email, $name): bool
    {
        $subject = "Approved";

        $messgae = "
        <html>
        <head>
        <head>
        <body>
            <div class='container'>
                <h2>Congratulations {$name}, account approved<h2>
                <a href = 'http://localhost:4000/login' class = 'btn btn-primary'>Login now</a>
            </div>
        </body>    
        </html>
        ";

        return $this->mail->send($email, $subject, $messgae);
    }

    /**
     * Summary of sendRejectMail
     * @param mixed $email
     * @param mixed $name
     * @return bool
     */
    public function sendRejectMail($email, $name): bool
    {
        $subject = "Rejected";

        $messgae = "
        <html>
        <head>
        <head>
        <body>
            <div class='container'>
                <h2>Request rejected {$name}, account rejected<h2>
            </div>
        </body>    
        </html>
        ";

        return $this->mail->send($email, $subject, $messgae);
    }

    /**
     * Summary of sendResetPasswordEmail
     * @param mixed $email
     * @param mixed $name
     * @param mixed $resetLink
     * @return bool
     */
    public function sendResetPasswordEmail($email, $name, $resetLink): bool
    {
        $subject = "Reset Your Password - {$this->config['from_name']}";

        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .button { 
                        display: inline-block; 
                        padding: 10px 20px; 
                        background-color: #007bff; 
                        color: #ffffff; 
                        text-decoration: none; 
                        border-radius: 5px; 
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Password Reset Request</h2>
                    <p>Hello, {$name},</p>
                    <a href='{$resetLink}' class='button'>Reset Password</a>
                    <p>{$resetLink}</p>
                    <p>This link will expire in 4 hours.</p>
                </div>
            </body>
            </html>
        ";

        return $this->mail->send($email, $subject, $message);
    }
}