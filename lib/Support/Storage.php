<?php

namespace Lib\Support;

use Lib\Http\ErrorHandler;

class Storage
{
    private static $storagePath =  __DIR__ . "/../../public/storage/";

    public static function put($file, $subdirectory = '')
    {
        try {
            if (!self::has($file)) {
                return false;
            }

            $filename = uniqid() . '_' . $file['name'];
            $directory = self::$storagePath . '/' . $subdirectory;
            self::createDirectory($directory);

            $targetPath = $directory . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $subdirectory ? $subdirectory . '/' . $filename : $filename;
            }

            return false;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function delete($url)
    {
        try {
            $targetPath = self::getTargetPath($url);

            if (file_exists($targetPath)) {
                unlink($targetPath);
                return true;
            }

            return false;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function url($path)
    {
        try {
            if (strpos($path, 'http') === 0) {
                return $path;
            }

            $path = parse_url($path, PHP_URL_PATH);
            $http = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
            $host = $_SERVER['HTTP_HOST'];

            return $http . '://' . $host . '/storage/' . $path;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function has($file)
    {
        try {
            return isset($file['name'], $file['tmp_name']);
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function exists($url)
    {
        try {
            $targetPath = self::getTargetPath($url);
            return file_exists($targetPath);
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function getSize($url)
    {
        try {
            $targetPath = self::getTargetPath($url);
            return filesize($targetPath);
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function getMimeType($url)
    {
        try {
            $targetPath = self::getTargetPath($url);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $targetPath);
            finfo_close($finfo);
            return $mimeType;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    private static function createDirectory($directory)
    {
        if (!empty($directory) && !file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    private static function getTargetPath($url)
    {
        return self::$storagePath . '/' . basename(parse_url($url, PHP_URL_PATH));
    }
}
