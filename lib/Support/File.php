<?php

namespace Lib\Support;

/**
 * Class File
 * 
 * Provides functionality for working with files.
 */
class File
{
    /**
     * Check if a file exists.
     *
     * @param string $path The path to the file.
     * 
     * @return bool True if the file exists, false otherwise.
     */
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Read the contents of a file.
     *
     * @param string $path The path to the file.
     * 
     * @return string|false The contents of the file, or false on failure.
     */
    public static function read(string $path)
    {
        return file_get_contents($path);
    }

    /**
     * Write data to a file.
     *
     * @param string $path The path to the file.
     * @param string $data The data to write to the file.
     * 
     * @return int|false The number of bytes written, or false on failure.
     */
    public static function write(string $path, string $data)
    {
        return file_put_contents($path, $data);
    }

    /**
     * Delete a file.
     *
     * @param string $path The path to the file.
     * 
     * @return bool True on success, false on failure.
     */
    public static function delete(string $path): bool
    {
        return unlink($path);
    }

    /**
     * Get the size of a file in bytes.
     *
     * @param string $path The path to the file.
     * 
     * @return int|false The size of the file in bytes, or false on failure.
     */
    public static function size(string $path)
    {
        return filesize($path);
    }

    /**
     * Get the last modified time of a file.
     *
     * @param string $path The path to the file.
     * 
     * @return int|false The last modified time of the file, or false on failure.
     */
    public static function lastModified(string $path)
    {
        return filemtime($path);
    }

    /**
     * Check if a path is a directory.
     *
     * @param string $path The path to check.
     * 
     * @return bool True if the path is a directory, false otherwise.
     */
    public static function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Check if a path is a file.
     * 
     * @param string $path The path to check.
     * 
     * @return bool True if the path is a file, false otherwise.
     */
    public static function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Create a directory.
     *
     * @param string $path The path to the directory.
     * @param int $mode The permissions to set for the directory (default: 0777).
     * @param bool $recursive Whether to create parent directories if they do not exist (default: false).
     * 
     * @return bool True on success, false on failure.
     */
    public static function makeDirectory(string $path, int $mode = 0777, bool $recursive = false): bool
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Delete a directory.
     *
     * @param string $path The path to the directory.
     * 
     * @return bool True on success, false on failure.
     */
    public static function deleteDirectory(string $path): bool
    {
        if (!self::isDirectory($path)) {
            return false;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }

        return rmdir($path);
    }

    /**
     * Get a list of files in a directory.
     * 
     * @param string $path The path to the directory.
     * 
     * @return array A list of files in the directory.
     */
    public static function scandir(string $path): array
    {
        return array_diff(scandir($path), ['.', '..']);
    }
}
