<?php

namespace App\Core;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;

/**
 * LoggerHelper
 * ------------
 * A singleton factory that builds and returns a shared Monolog Logger instance.
 *
 * WHY SINGLETON?
 *   We want ONE logger shared across the entire request lifecycle so that:
 *   - All log entries go to the same files in the same consistent format.
 *   - We avoid opening file descriptors multiple times per request.
 *   - Any class can call LoggerHelper::getInstance() without constructor injection.
 *
 * HOW MONOLOG WORKS (key concepts for trainees):
 *
 *   Logger  ──► Handler(s)  ──► Formatter
 *
 *   • Logger    : Named channel ("app"). You call $log->info(), $log->error() etc.
 *   • Handler   : Decides WHERE to write — file, Slack, email, database, etc.
 *                 Here we use two handlers:
 *                   - RotatingFileHandler : daily rotating log, keeps 30 days.
 *                   - StreamHandler       : dedicated file for ERROR and above.
 *   • Formatter : Decides HOW each line looks.
 *                 Here we use LineFormatter for human-readable lines.
 *
 * LOG LEVELS (lowest → highest severity):
 *   debug    — detailed dev info, variable dumps
 *   info     — normal business events ("Mail sent", "User logged in")
 *   notice   — normal but significant ("Config reloaded")
 *   warning  — unexpected but non-fatal ("Deprecated API used")
 *   error    — failures that need attention ("DB query failed")
 *   critical — system in bad state ("PDO connection lost")
 *   alert    — action needed NOW ("Disk almost full")
 *   emergency— system is unusable
 *
 * USAGE (static facade — no injection needed):
 *   LoggerHelper::info('User logged in', ['user_id' => 42]);
 *   LoggerHelper::error('DB failed', ['exception' => $e->getMessage()]);
 *
 * USAGE (direct instance — useful when you need to pass the logger around):
 *   $log = LoggerHelper::getInstance();
 *   $log->info('User logged in', ['user_id' => 42]);
 */
class Logger
{
    /** The single shared instance of this class. */
    private static ?MonologLogger $instance = null;

    /**
     * Private constructor — prevents direct `new LoggerHelper()` calls.
     * Use LoggerHelper::getInstance() instead.
     */
    private function __construct()
    {
    }

    /**
     * Returns the shared MonologLogger instance, creating it on first call.
     *
     * @return MonologLogger
     */
    public static function getInstance(): MonologLogger
    {
        if (self::$instance === null) {
            self::$instance = self::build();
        }

        return self::$instance;
    }

    /**
     * Builds the configured Monolog Logger.
     * Called exactly once per request lifecycle.
     */
    private static function build(): MonologLogger
    {
        date_default_timezone_set('Asia/Kolkata');
        // ----------------------------------------------------------------
        // 1. Log level — read from .env so you can change verbosity without
        //    touching code. Defaults to 'debug' if the key is missing.
        // ----------------------------------------------------------------
        $levelName = strtolower($_ENV['LOG_LEVEL'] ?? 'debug');
        $level     = Level::fromName($levelName);   // converts string → Monolog enum

        // ----------------------------------------------------------------
        // 2. Log directory — create it if it does not exist yet.
        // ----------------------------------------------------------------
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // ----------------------------------------------------------------
        // 3. Formatter — controls how each line looks in the log file.
        //
        //    [2026-03-03 10:00:00] app.ERROR: PDO failed {"code":500} []
        //     ^^^^^^^^^^^^^^^^^    ^^^ ^^^^^  ^^^^^^^^^^^  ^^^^^^^^^  ^
        //     datetime             ch  level  message      context    extra
        // ----------------------------------------------------------------
        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',   // datetime format
            true,             // allow inline line-breaks inside messages
            true              // ignore empty context [] arrays in output
        );

        // ----------------------------------------------------------------
        // 4. Rotating handler — writes to storage/logs/app.log, rotates
        //    daily, and retains the last 30 files.
        //    Minimum level is whatever LOG_LEVEL is set to.
        // ----------------------------------------------------------------
        $rotatingHandler = new RotatingFileHandler(
            $logDir . '/app.log',
            30,
            $level
        );
        $rotatingHandler->setFormatter($formatter);

        // ----------------------------------------------------------------
        // 5. Error handler — a dedicated file for ERROR and above so that
        //    serious failures are easy to find without wading through debug
        //    noise. Always active regardless of LOG_LEVEL.
        // ----------------------------------------------------------------
        $errorHandler = new StreamHandler(
            $logDir . '/exception.log',
            Level::Error
        );
        $errorHandler->setFormatter($formatter);

        // ----------------------------------------------------------------
        // 6. Logger — the named "channel". Using 'app' matches standard
        //    Laravel/Symfony conventions so trainees feel at home later.
        //    Handlers are evaluated in push order (rotating first).
        // ----------------------------------------------------------------
        $logger = new MonologLogger('app');
        $logger->pushHandler($rotatingHandler);
        $logger->pushHandler($errorHandler);

        return $logger;
    }

    // ====================================================================
    // Static facade methods
    // Thin wrappers so callers never need to hold an instance reference.
    // ====================================================================

    /**
     * Log at an arbitrary level.
     *
     * @param string|Level $level   Monolog level name or enum.
     * @param string       $message Human-readable description.
     * @param array        $context Key/value pairs for structured context.
     */


    public static function error(string $message, array $context = []): void
    {
        self::getInstance()->error($message, $context);
    }
}