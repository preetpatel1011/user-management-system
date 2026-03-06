<?php

namespace App\Models;

class ForgotPassword
{
    private $id;
    private $user_id;
    private $email;
    private $token;
    private $expires_at;
    private $created_at;
}