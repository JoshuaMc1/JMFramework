<?php

namespace Lib\Support;

use Lib\Exception\ExceptionHandler;

/**
 * Class Log
 *
 * Provides functionality for writing log entries to a log file.
 */
class Log
{
    private static $logDir = __DIR__ . "/../../storage/logs/";
    private static $logFile = "jmframework.log";

    /**
     * Constructor to ensure the log file exists.
     */
    public function __construct()
    {
        try {
            $this->ensureLogFileExists();
        } catch (\Exception $e) {
            ExceptionHandler::handleException($e);
        }
    }

    /**
     * Ensure that the log directory and file exist. If not, create them.
     */
    private function ensureLogFileExists()
    {
        if (!file_exists(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }

        $fullPath = self::$logDir . self::$logFile;

        if (!file_exists($fullPath)) {
            touch($fullPath);
        }
    }

    /**
     * Write a log entry to the log file.
     *
     * @param string $type The type of log entry (e.g., 'info', 'error', 'warning').
     * @param string $message The log message.
     * @param array $context An optional context array to include in the log entry.
     */
    public static function writeLog($type, $message, $context = [])
    {
        $fullPath = self::$logDir . self::$logFile;
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$type}] {$message} ";

        if (!empty($context)) {
            $logMessage .=  json_encode($context) . PHP_EOL;
        }

        // Get a stack trace for the current location in case of errors or debugging.
        $exception = new \Exception();
        $trace = $exception->getTraceAsString();
        $logMessage .= "[stacktrace]" . PHP_EOL . $trace . PHP_EOL . PHP_EOL;

        // Append the log message to the log file.
        file_put_contents($fullPath, $logMessage, FILE_APPEND);
    }
}
