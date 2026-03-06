<?php

namespace App\Models;

class EmailVerification
{
    private $id;
    private $user_id;
    private $email;
    private $token;
    private $expires_at;
    private $created_at;
}