<?php

namespace Lib\Support;

use Lib\Exception\ExceptionHandler;
use Lib\Exception\StorageExceptions\{
    FileNotFoundException,
    FileUploadException,
    MimeTypeException,
    FileDeleteException,
    FileSizeException
};

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

            throw new FileUploadException();
        } catch (FileUploadException | \Throwable $th) {
            ExceptionHandler::handleException($th);
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

            throw new FileDeleteException();
        } catch (FileDeleteException | \Throwable $th) {
            ExceptionHandler::handleException($th);
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
            ExceptionHandler::handleException($th);
        }
    }

    public static function has($file)
    {
        try {
            if (!isset($file['name'], $file['tmp_name'])) {
                throw new FileUploadException('Invalid file');
            }

            return true;
        } catch (FileUploadException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function exists($url)
    {
        try {
            $targetPath = self::getTargetPath($url);

            if (file_exists($targetPath)) {
                return true;
            }

            throw new FileNotFoundException();
        } catch (FileNotFoundException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function getSize($url)
    {
        try {
            $targetPath = self::getTargetPath($url);

            if (file_exists($targetPath)) {
                if (filesize($targetPath) === 0) {
                    throw new FileSizeException();
                } else {
                    return filesize($targetPath);
                }
            }

            throw new FileNotFoundException();
        } catch (FileNotFoundException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function getMimeType($url)
    {
        try {
            $targetPath = self::getTargetPath($url);

            if (file_exists($targetPath)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $targetPath);

                if (empty($mimeType)) {
                    throw new MimeTypeException();
                }

                finfo_close($finfo);
                return $mimeType;
            }

            throw new FileNotFoundException();
        } catch (FileNotFoundException | MimeTypeException | \Throwable $th) {
            ExceptionHandler::handleException($th);
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
