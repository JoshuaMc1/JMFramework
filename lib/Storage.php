<?php

namespace Lib;

class Storage
{
    private static $storagePath =  __DIR__ . "/../public/storage/";

    public static function put($file, $subdirectory = '')
    {
        $filename = uniqid() . '_' . $file['name'];
        $directory = self::$storagePath . '/' . $subdirectory;
        if (!empty($subdirectory) && !file_exists($directory)) {
            mkdir($directory, 0666, true);
        }
        $targetPath = $directory . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $publicPath = '/storage/' . ($subdirectory ? $subdirectory . '/' : '') . $filename;
            return self::url($publicPath);
        }

        return false;
    }

    public static function delete($url)
    {
        $targetPath = self::$storagePath . '/' . basename(parse_url($url, PHP_URL_PATH));

        if (file_exists($targetPath)) {
            unlink($targetPath);
            return true;
        }

        return false;
    }

    public static function url($path)
    {
        if (strpos($path, 'http') === 0) {
            return $path;
        }

        return 'http://' . $_SERVER['HTTP_HOST'] . '/storage/' . $path;
    }
}
