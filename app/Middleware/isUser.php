<?php

declare(strict_types=1);

namespace App\Middleware;

class isUser
{
    public function handle(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}