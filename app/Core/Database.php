<?php
namespace App\Core;

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
        } catch (mysqli_sql_exception) {
            
        }
    }
}
