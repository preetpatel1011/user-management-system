<?php
declare(strict_types = 1);
session_start();

use Dotenv\Dotenv;
use App\Core\Logger;

require_once __DIR__ . '/../vendor/autoload.php';
// require_once __DIR__ . '/../app/Core/autoload.php';
require_once __DIR__ . '/../config/constant.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

/*
|--------------------------------------------------------------------------
| Global Exception Handler
|--------------------------------------------------------------------------
*/
// set_exception_handler(function ($e) {
//     date_default_timezone_set('Asia/kolkata');
//     $message =
//     "[" . date('Y-m-d H:i:s') . "] " .
//     $e->getMessage() .
//     " | File: " . $e->getFile() .
//         " | Line: " . $e->getLine() .
//         PHP_EOL;
        
//         file_put_contents(
//         BASE_PATH . '/storage/logs/exception.log',
//         $message,
//         FILE_APPEND
//         );
        
//         echo "Something went wrong. Check logs.";
// });

set_exception_handler(function ($e) {
    
    $context = [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    
    Logger::error($e->getMessage(), $context);
    
    echo "Something went wrong. Check logs.";
});

use App\Contracts\Route;
require_once __DIR__ . '/../routes/web.php';

Route::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

/**
 * //TODO: 1. Proper logger class through exception handling 
* to-take-care :: //(class logger, in controller) (expection, 404 page) //fascad calling no static property in static way!
* //TODO: 2. Improve the code base like this : Route::get('/login', 'AuthController@login'); //fascad method
* //TODO: 3. More better one: Route::middleware('isUser')->get('/users', 'UserController@index');
*/