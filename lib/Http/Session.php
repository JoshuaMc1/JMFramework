<?php

namespace Lib\Http;

use Lib\Exception\ExceptionHandler;
use Lib\Model\Session as SessionModel;

/**
 * Class Session
 * 
 * Provides functionality for managing user sessions in the application.
 */
class Session
{
    private static $instance;

    /**
     * Constructor: Initializes the session handling and settings.
     *
     * @throws \Throwable If an error occurs during session initialization.
     */
    private function __construct()
    {
        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                $sessionPath = __DIR__ . '/../../storage/framework/sessions/';

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
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * Get the Singleton instance of the Session class.
     *
     * @return Session The Singleton instance of the Session class.
     */
    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the value of a session key.
     *
     * @param string $key The session key.
     *
     * @return mixed|null The value of the session key, or null if not found.
     */
    public static function get(string $key): mixed
    {
        self::getInstance();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Set the value of a session key.
     *
     * @param string $key   The session key.
     * @param mixed  $value The value to set.
     */
    public static function set(string $key, mixed $value): void
    {
        self::getInstance();
        $_SESSION[$key] = $value;
    }

    /**
     * Remove a session key.
     *
     * @param string $key The session key.
     */
    public static function remove(string $key): void
    {
        self::getInstance();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Check if a session key exists.
     *
     * @param string $key The session key.
     *
     * @return bool True if the session key exists, otherwise false.
     */
    public static function has(string $key): bool
    {
        self::getInstance();
        return isset($_SESSION[$key]);
    }

    /**
     * Get all session values.
     *
     * @return array An associative array of all session values.
     */
    public static function all(): array
    {
        self::getInstance();
        return $_SESSION;
    }

    /**
     * Clear all session values.
     */
    public static function flush(): void
    {
        self::getInstance();
        session_unset();
    }

    /**
     * The function regenerates the session ID in PHP.
     */
    public static function regenerate(): void
    {
        self::getInstance();
        session_regenerate_id(true);
    }

    /**
     * The function destroys the current session.
     */
    public static function destroy(): void
    {
        self::getInstance();
        session_destroy();
    }

    /**
     * The function sets a flash message in the session with a given key and value.
     * 
     * @param string key The key is a string that represents the name or identifier of the flash
     * message. It is used to retrieve the flash message later on.
     * @param string value The value parameter is a string that represents the value to be stored in
     * the flash session.
     */
    public static function setFlash(string $key, string $value): void
    {
        self::getInstance();
        $_SESSION['flash'][$key] = $value;
    }

    /**
     * The function retrieves a flash message from the session and removes it from the session.
     * 
     * @param string key The key parameter is a string that represents the key of the flash message
     * that you want to retrieve from the session.
     * 
     * @return string The value of the flash message associated with the given key is being returned.
     */
    public static function getFlash(string $key): string
    {
        self::getInstance();
        $value = null;

        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
        }

        return $value;
    }

    /**
     * The function checks if a flash message with a specific key exists in the session.
     * 
     * @param string key The key parameter is a string that represents the key of the flash message in
     * the session.
     * 
     * @return bool a boolean value. It returns true if the specified key exists in the 'flash' array
     * of the  variable, and false otherwise.
     */
    public static function hasFlash(string $key): bool
    {
        self::getInstance();
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * The function pulls a value from a storage, removes it, and returns it.
     * 
     * @param string key The key parameter is a string that represents the key of the value you want to
     * retrieve from the data store.
     * 
     * @return mixed the value associated with the given key.
     */
    public static function pull(string $key): mixed
    {
        self::getInstance();
        $value = self::get($key);
        self::forget($key);
        return $value;
    }

    /**
     * The forget function in PHP is used to remove a specific key from the session.
     * 
     * @param string key The key parameter is a string that represents the key of the session variable
     * that you want to remove from the  array.
     */
    public static function forget(string $key): void
    {
        self::getInstance();
        unset($_SESSION[$key]);
    }

    /**
     * The function updates the last activity timestamp of a session in the database.
     * 
     * @param int sessionId The sessionId parameter is the unique identifier for a session. It can be
     * either an integer or a string.
     */
    public static function updateLastActivity(int|string $sessionId): void
    {
        self::getInstance();
        $session = SessionModel::find($sessionId);
        $session['last_activity'] = time();
        (new SessionModel())->save($session);
    }
}
