<?php
namespace App\Core;

use App\Core\Logger;
class Controller
{
    protected function view($path, $data = [])
    {
        extract($data);
        require __DIR__ . "/../Views/$path.php";
    }

    protected function log(string $message, array $context = []): void
    {
        Logger::error($message, $context);
    }
}
