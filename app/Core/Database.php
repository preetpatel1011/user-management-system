<?php
namespace App\Core;

use Exception;
use mysqli;
use mysqli_sql_exception;

class Database
{
    public static function connect()
    {
        $config = require __DIR__ . '/../../config/database.php';
        try {
            return new mysqli($config['host'], 
                              $config['user'], 
                              $config['pass'], 
                              $config['dbname']);
        } catch (mysqli_sql_exception $e) {
            error_log("DB Connection Failed: " . $e->getMessage());
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());

        } catch (Exception $e) {
            error_log("Unexpected DB Error: " . $e->getMessage());
            throw new \RuntimeException("Unexpected database error.");
        }
    }
}
