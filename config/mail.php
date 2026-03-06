<?php

/*
|--------------------------------------------------------------------------
| Mail Configuration
|--------------------------------------------------------------------------
|
| All SMTP settings live here so credentials never bleed into controllers.
| For local development / training, sign up for a free Mailtrap account at
| https://mailtrap.io and paste your sandbox SMTP credentials below.
|
*/

return [

    /*
     * SMTP host provided by your mail service.
     * Mailtrap sandbox: sandbox.smtp.mailtrap.io
     */
    'host' => $_ENV['SMTP_HOST']??'sandbox.smtp.mailtrap.io',

    /*
     * SMTP port.
     * Mailtrap supports: 25, 465, 587, 2525
     * Use 2525 as a safe fallback when firewalls block others.
     */
    'port' => (int) ($_ENV['SMTP_PORT']) ?? 587,

    /*
     * SMTP authentication credentials.
     * Get these from your Mailtrap inbox → Integration tab.
     */
    'username' => $_ENV['SMTP_USERNAME'] ?? '828cf931c853d8',
    'password' => $_ENV['SMTP_PASSWORD'] ?? '631453b2f1a8b7',

    /*
     * Encryption protocol: 'tls' (recommended) or 'ssl'.
     * Leave empty '' if your server requires none (not recommended).
     */
    'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',

    /*
     * The "From" address and name shown in the recipient's inbox.
     */
    'from_email' => $_ENV['EMAIL_USERNAME'] ?? 'no-reply@example.com',
    'from_name' => $_ENV['FROM_NAME'] ?? 'PHP MVC App',
    'admin_email' => $_ENV['ADMIN_EMAIL'] ?? 'admin@example.com',
];
