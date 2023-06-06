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
use Illuminate\Support\Str;

class Storage
{
    private static $storagePath = __DIR__ . '/../../storage/public';
    private static $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'video/mp4',
        'video/mpeg',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ];

    public static function put($file, $subdirectory = '')
    {
        try {
            if (!self::has($file)) {
                return false;
            }

            self::validateFile($file);

            $filename = Str::random(32) . '_' . self::sanitizeFilename($file['name']);
            $directory = rtrim(self::$storagePath . '/' . $subdirectory, '/');
            self::createDirectory($directory);

            $targetPath = $directory . '/' . $filename;

            if (file_exists($targetPath)) {
                throw new FileUploadException('File already exists');
            }

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

            return false;
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

            $http = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
            $host = $_SERVER['HTTP_HOST'];

            return $http . '://' . $host . '/storage/' . ltrim($path, '/');
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

            return false;
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
            mkdir($directory, 0755, true);
        }
    }

    private static function getTargetPath($url)
    {
        return self::$storagePath . '/' . ltrim(parse_url($url, PHP_URL_PATH), '/');
    }

    private static function validateFile($file)
    {
        try {
            if (!in_array($file['type'], self::$allowedTypes)) {
                throw new MimeTypeException('Invalid file type');
            }

            $maxSize = FILE_SIZE;
            $fileSize = filesize($file['tmp_name']);

            if ($fileSize > $maxSize) {
                throw new FileSizeException('File size exceeds the limit');
            }

            return true;
        } catch (MimeTypeException | FileSizeException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    private static function sanitizeFilename($filename)
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9_.\-]/', '_', $filename);
        return $sanitized;
    }
}
