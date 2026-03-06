<?php

declare(strict_types=1);

namespace App\Middleware;

class isAdmin
{
    public function handle(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: /dashboard');
            exit;
        }
    }
}