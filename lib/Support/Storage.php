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
use Lib\Exception\CustomException;

/**
 * Class Storage
 *
 * Provides file storage and management functions.
 * 
 * @CodeError 02
 */
class Storage
{
    /**
     * Upload a file and store it in the specified subdirectory.
     *
     * @param array $file The uploaded file data.
     * @param string $subdirectory The subdirectory within the storage path where the file should be stored.
     * 
     * @return string|false The filename if the upload is successful, false otherwise.
     */
    public static function put(array $file, $subdirectory = ''): string|false
    {
        try {
            if (!self::has($file)) {
                return false;
            }

            self::validateFile($file);

            $filename = Str::random(32) . '_' . self::sanitizeFilename($file['name']);
            $directory = rtrim(config('storage.path') . '/' . $subdirectory, '/');
            self::createDirectory($directory);

            $targetPath = $directory . '/' . $filename;

            if (file_exists($targetPath)) {
                throw new FileUploadException(lang('exception.file_already_exists'), lang('exception.file_already_exists_message'));
            }

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $subdirectory ? $subdirectory . '/' . $filename : $filename;
            }

            throw new FileUploadException();
        } catch (FileUploadException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Delete a file based on its URL.
     *
     * @param string $url The URL of the file to delete.
     * 
     * @return bool True if the file is deleted successfully, false otherwise.
     */
    public static function delete(string $url): bool
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

    /**
     * Get the public URL of a stored file.
     *
     * @param string $path The path to the file within the storage.
     * 
     * @return string The public URL of the file.
     */
    public static function url(string $path): string
    {
        try {
            if (strpos($path, 'http') === 0) {
                return $path;
            }

            $http = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
            $host = $_SERVER['HTTP_HOST'];

            return $http . '://' . $host . '/storage/' . ltrim($path, '/');
        } catch (\Throwable $th) {
            ExceptionHandler::handleException(new CustomException(0206, lang('exception.get_public_url_error'), lang('exception.get_public_url_error_message', ['message' => $th->getMessage()])));
        }
    }

    /**
     * Check if a file exists.
     *
     * @param array $file The uploaded file data.
     * 
     * @return bool|null True if the file exists, false otherwise.
     */
    public static function has(array $file): ?bool
    {
        try {
            if (!isset($file['name'], $file['tmp_name'])) {
                throw new FileUploadException(lang('exception.invalid_file_message'));
            }

            return true;
        } catch (FileUploadException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Check if a file exists based on its URL.
     *
     * @param string $url The URL of the file to check.
     * 
     * @return bool True if the file exists, false otherwise.
     */
    public static function exists(string $url): bool
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

    /**
     * Get the size of a file.
     *
     * @param string $url The URL of the file.
     * 
     * @return int|null The size of the file in bytes.
     */
    public static function getSize(string $url): ?int
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

    /**
     * Get the MIME type of a file.
     *
     * @param string $url The URL of the file.
     * 
     * @return string|null The MIME type of the file.
     */
    public static function getMimeType(string $url): ?string
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

    /**
     * Create a directory if it doesn't exist.
     *
     * @param string $directory The directory path.
     * 
     * @return void
     */
    private static function createDirectory($directory): void
    {
        if (!empty($directory) && !file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Get the target path for a file based on its URL.
     *
     * @param string $url The URL of the file.
     * 
     * @return string The target path on the server.
     */
    private static function getTargetPath($url): string
    {
        return config('storage.path') . '/' . ltrim(parse_url($url, PHP_URL_PATH), '/');
    }

    /**
     * Validate an uploaded file.
     *
     * @param array $file The uploaded file data.
     * 
     * @return bool True if the file is valid, false otherwise.
     */
    private static function validateFile($file): bool
    {
        try {
            if (!in_array($file['type'], config('storage.allowed_types'))) {
                throw new MimeTypeException(lang('exception.invalid_file_type_message'));
            }

            $maxSize = config('file.file_size');
            $fileSize = filesize($file['tmp_name']);

            if ($fileSize > $maxSize) {
                throw new FileSizeException(lang('exception.invalid_file_size_message'));
            }

            return true;
        } catch (MimeTypeException | FileSizeException | \Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Sanitize a filename to remove invalid characters.
     *
     * @param string $filename The original filename.
     * @return string The sanitized filename.
     */
    private static function sanitizeFilename($filename)
    {
        $sanitized = preg_replace('/[^a-zA-Z0-9_.\-]/', '_', $filename);
        return $sanitized;
    }
}
