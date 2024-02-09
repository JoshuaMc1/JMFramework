<?php

namespace Lib\Support;

use Exception;
use Lib\Exception\CustomException;
use Lib\Exception\ExceptionHandler;
use Throwable;

/**
 * Class Log
 *
 * Provides functionality for writing log entries to a log file.
 */
class Log
{
    /**
     * The filename of the log file.
     * 
     * @var string $filename
     */
    private static $filename = "/jmframework.log";

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
     * 
     * @return void
     */
    private function ensureLogFileExists(): void
    {
        if (!file_exists(log_path())) {
            mkdir(log_path(), 0755, true);
        }

        if (!file_exists(self::getFullPath())) {
            touch(self::getFullPath());
        }
    }

    /**
     * Write a log entry to the log file.
     *
     * @param Exception|Throwable|CustomException $exception The exception object.
     * @param string $type The type of log entry (e.g., 'info', 'error', 'warning').
     * @param string $message The log message.
     * @param array $context An optional context array to include in the log entry.
     * 
     * @return void
     */
    private static function writeLog(Exception | Throwable | CustomException $exception, string $type, string $message, array $context = []): void
    {
        $fullPath = self::getFullPath();
        $timestamp = Date::today()->format('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$type}] {$message}" . PHP_EOL;

        $trace = $exception->getTraceAsString();
        $logMessage .= "[stacktrace]" . PHP_EOL . $trace . PHP_EOL;

        if (!empty($context)) {
            $logMessage .= "[context]" . PHP_EOL . json_encode($context, JSON_PRETTY_PRINT) . PHP_EOL;
        }

        $logMessage .= "[exception]" . PHP_EOL;
        $logMessage .= "    Class: " . get_class($exception) . PHP_EOL;
        $logMessage .= "    File: " . $exception->getFile() . PHP_EOL;
        $logMessage .= "    Line: " . $exception->getLine() . PHP_EOL;

        if ($exception->getPrevious()) {
            $logMessage .= "    Previous: " . get_class($exception->getPrevious()) . PHP_EOL;
        }

        $logMessage .= "    Message: " . $exception->getMessage() . PHP_EOL;
        $logMessage .= "    Code: " . $exception->getCode() . PHP_EOL;

        if ($exception instanceof CustomException) {
            $logMessage .= "    Error Code: " . $exception->getErrorCode() . PHP_EOL;
            $logMessage .= "    Error Title: " . $exception->getErrorTitle() . PHP_EOL;
            $logMessage .= "    Error Message: " . $exception->getErrorMessage() . PHP_EOL;
        }

        $logMessage .= PHP_EOL;

        file_put_contents($fullPath, $logMessage, FILE_APPEND);
    }

    /**
     * Write a log entry to the log file.
     * 
     * @param string $type The type of log entry (e.g., 'info', 'error', 'warning').
     * @param string $title The log title.
     * @param mixed $message The log message.
     * @param array $context An optional context array to include in the log entry.
     * 
     * @return void
     */
    private static function write(string $type, string $title, mixed $message, array $context = []): void
    {
        $fullPath = self::getFullPath();
        $timestamp = Date::today()->format('Y-m-d H:i:s');

        $logMessage = "[{$timestamp}] [{$type}] {$title}" . PHP_EOL;

        switch (true) {
            case is_array($message):
                ob_start();
                var_dump($message);
                $logMessage .= ob_get_clean() . PHP_EOL . PHP_EOL;
                break;

            case is_string($message):
                $logMessage .= $message . PHP_EOL . PHP_EOL;
                break;

            case is_object($message):
                $logMessage .= print_r($message, true) . PHP_EOL . PHP_EOL;
                break;

            default:
                ob_start();
                var_dump($message);
                $logMessage .= ob_get_clean() . PHP_EOL;
        }

        if (!empty($context)) {
            $logMessage .= "[context]" . json_encode($context) . PHP_EOL . PHP_EOL;
        }

        file_put_contents($fullPath, $logMessage, FILE_APPEND);
    }

    /**
     * Get the full path to the log file.
     * 
     * @return string The full path to the log file.
     * */
    public static function getFullPath(): string
    {
        return log_path() . self::$filename;
    }

    /**
     * Write an info log entry.
     * 
     * @param string $title The log title.
     * @param mixed $message The log message.
     * @param array $context An optional context array to include in the log entry.
     * 
     * @return void
     */
    public static function info(string $title, mixed $message, $context = []): void
    {
        self::write('info', $title, $message, $context);
    }

    /**
     * Write an error log entry.
     * 
     * @param string $title The log title.
     * @param mixed $message The log message.
     * @param array $context An optional context array to include in the log entry.
     * 
     * @return void
     */
    public static function error(string $title, mixed $message, $context = []): void
    {
        self::write('error', $title, $message,  $context);
    }

    /**
     * Write a warning log entry.
     * 
     * @param string $title The log title.
     * @param mixed $message The log message.
     * @param array $context An optional context array to include in the log entry.
     * 
     * @return void
     */
    public static function warning(string $title, mixed $message, $context = []): void
    {
        self::write('warning', $title, $message,  $context);
    }

    /**
     * Write a debug log entry.
     * 
     * @param Exception | Throwable | CustomException $exception
     * @param string $message
     * @param array $context An optional contexto array to include in the log entry.
     * 
     * @return void
     */
    public static function debug(Exception | Throwable | CustomException $exception, string $message, $context = []): void
    {
        self::writeLog($exception, 'local.exception', $message, $context);
    }
}
