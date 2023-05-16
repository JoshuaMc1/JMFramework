<?php

namespace Lib\Http;

use Lib\Model\Session as SessionModel;

class Session
{
    private static $instance;

    private function __construct()
    {
        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                $sessionPath = __DIR__ . '/../../storage/sessions/';

                if (!is_dir($sessionPath)) {
                    mkdir($sessionPath, 0777, true);
                }

                session_save_path($sessionPath);
                ini_set('session.gc_probability', 1);
                ini_set('session.gc_divisor', 100);
                ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
                session_start();
            }
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal server error', $th->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get($key)
    {
        self::getInstance();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function set($key, $value)
    {
        self::getInstance();
        $_SESSION[$key] = $value;
    }

    public static function remove($key)
    {
        self::getInstance();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function has($key): bool
    {
        self::getInstance();
        return isset($_SESSION[$key]);
    }

    public static function all()
    {
        self::getInstance();
        return $_SESSION;
    }

    public static function flush()
    {
        self::getInstance();
        session_unset();
    }

    public static function regenerate()
    {
        self::getInstance();
        session_regenerate_id(true);
    }

    public static function destroy()
    {
        self::getInstance();
        session_destroy();
    }

    public static function setFlash($key, $value)
    {
        self::getInstance();
        $_SESSION['flash'][$key] = $value;
    }

    public static function getFlash($key)
    {
        self::getInstance();
        $value = null;
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
        }
        return $value;
    }

    public static function hasFlash($key)
    {
        self::getInstance();
        return isset($_SESSION['flash'][$key]);
    }

    public static function pull($key)
    {
        self::getInstance();
        $value = self::get($key);
        self::forget($key);
        return $value;
    }

    public static function forget($key)
    {
        self::getInstance();
        unset($_SESSION[$key]);
    }

    public static function updateLastActivity($sessionId)
    {
        self::getInstance();
        $session = SessionModel::find($sessionId);
        $session['last_activity'] = time();
        (new SessionModel())->save($session);
    }
}
