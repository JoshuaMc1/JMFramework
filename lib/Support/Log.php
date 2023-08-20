<?php

namespace Lib\Support;

use Lib\Exception\ExceptionHandler;

class Log
{
    private static $logDir = __DIR__ . "/../../storage/logs/";
    private static $logFile = "jmframework.log";

    public function __construct()
    {
        try {
            $this->ensureLogFileExists();
        } catch (\Exception $e) {
            ExceptionHandler::handleException($e);
        }
    }

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

    public static function writeLog($type, $message, $context = [])
    {
        $fullPath = self::$logDir . self::$logFile;
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$type}] {$message} ";

        if (!empty($context)) {
            $logMessage .=  json_encode($context) . PHP_EOL;
        }

        $exception = new \Exception();
        $trace = $exception->getTraceAsString();
        $logMessage .= "[stacktrace]" . PHP_EOL . $trace . PHP_EOL . PHP_EOL;

        file_put_contents($fullPath, $logMessage, FILE_APPEND);
    }
}
