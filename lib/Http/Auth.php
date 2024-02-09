<?php

namespace Lib\Http;

use App\Models\User;
use Lib\Exception\ExceptionHandler;
use Lib\Model\{PersonalAccessToken, Session as SessionModel};
use Lib\Support\{Hash, Token};
use Lib\Http\{Cookie, Session};

/**
 * Class Auth
 * 
 * this class is responsible for handling all authentication logic 
 */
class Auth
{
    /**
     * The function attempts to authenticate a user by checking their email and password, creating a
     * new session, and setting a session ID cookie and session ID in the session.
     * 
     * @param string email The email parameter is a string that represents the email address of the
     * user attempting to log in.
     * @param string password The password parameter is a string that represents the user's password.
     * 
     * @return bool a boolean value. It returns true if the login attempt is successful and false if it
     * is not.
     */
    private static function attemptWeb(string $email, string $password): bool
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::verify($password, $user['password'])) {
                return false;
            }

            $session = new SessionModel();

            $newSession = [
                'user_id' => $user['id'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'last_activity' => time(),
            ];

            $session = $session->create($newSession);

            Cookie::set('session_id', $session['id']);
            Session::set('session_id', $session['id']);

            return true;
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * The function attempts to authenticate a user by checking their email and password, and if
     * successful, generates and stores an API token.
     * 
     * @param string email The email parameter is a string that represents the user's email address.
     * @param string password The password parameter is a string that represents the user's password.
     * 
     * @return bool a boolean value. If the user is found and the password is verified, it will return
     * the access token. Otherwise, it will return false.
     */
    private static function attemptAPI(string $email, string $password): bool
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::verify($password, $user['password'])) {
                return false;
            }

            $personalAccessToken = new PersonalAccessToken();

            $accessToken = [
                'name' => 'API Token',
                'token' => Hash::encrypt(Token::createToken(['user_id' => $user['id']])),
                'last_used_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $personalAccessToken = $personalAccessToken->create($accessToken);

            Cookie::set('api_token', $accessToken['token']);

            return $accessToken;
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function attempt(string $email, string $password, string $guard = 'web'): bool
    {
        try {
            return ($guard == 'web') ?
                self::attemptWeb($email, $password) :
                self::attemptAPI($email, $password);
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * The function `logoutWeb` logs out a user from a web session by deleting the session record and
     * removing the session ID cookie.
     */
    private static function logoutWeb(): void
    {
        try {
            $session = SessionModel::find(Session::get('session_id'));

            if ($session) {
                (new SessionModel())->delete($session['id']);
            }

            Cookie::remove('session_id');
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * The function `logoutAPI` checks for an API token in a cookie, deletes the token from the
     * database, and removes the cookie.
     * 
     * @return bool a boolean value. If the condition `` is false (empty or not set), then it
     * will return `false`. Otherwise, it will return `true` after removing the 'api_token' cookie.
     */
    private static function logoutAPI(): bool
    {
        try {
            $apiToken = Cookie::get('api_token');

            if (!$apiToken) {
                return false;
            }

            $personalAccessToken = new PersonalAccessToken();

            $personalAccessToken->where('token', $apiToken)->first();

            Cookie::remove('api_token');

            return true;
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function logout(string $guard = 'web'): bool
    {
        try {
            return ($guard == 'web') ?
                self::logoutWeb() :
                self::logoutAPI();
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * This PHP function checks if a web session is valid and active.
     * 
     * @return bool a boolean value. It returns true if the web session is valid and active, and false
     * if the session is invalid or expired.
     */
    private static function checkWeb(): bool
    {
        try {
            $sessionId = Cookie::get('session_id');

            if (!$sessionId) {
                return false;
            }

            $session = SessionModel::find($sessionId);

            if (!$session) {
                Cookie::remove('session_id');
                return false;
            }

            $maxLifetime = ini_get('session.gc_maxlifetime');

            if (time() - $session['last_activity'] > $maxLifetime) {
                $session->delete();
                Cookie::remove('session_id');
                return false;
            }

            $session['last_activity'] = time();

            (new SessionModel())->save($session);

            Session::set('session_id', $sessionId);

            return true;
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * The function checks if the API token stored in a cookie is valid and has not expired.
     * 
     * @return bool a boolean value.
     */
    private static function checkAPI(): bool
    {
        try {
            $apiToken = Cookie::get('api_token');

            if (!$apiToken) {
                return false;
            }

            $accessToken = PersonalAccessToken::find($apiToken);

            if (!$accessToken) {
                Cookie::remove('api_token');
                return false;
            }

            $maxLifetime = $accessToken['expires_at'];

            if (time() > strtotime($maxLifetime)) {
                $accessToken->delete();
                Cookie::remove('api_token');
                return false;
            }

            return true;
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function check(string $guard = 'web'): bool
    {
        try {
            return ($guard == 'web') ?
                self::checkWeb() :
                self::checkAPI();
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * The function `userWeb()` checks if a user is logged in on a web session and returns the
     * corresponding user object if they are.
     * 
     * @return ?User a User object or null.
     */
    private static function userWeb(): ?User
    {
        try {
            if (!self::checkWeb()) {
                return null;
            }

            $sessionId = Cookie::get('session_id');
            $session = SessionModel::find($sessionId);

            if (!$session) {
                self::logoutWeb();
                Session::setFlash('error', 'The session with the ID ' . $sessionId . ' does not exist.');
                return null;
            }

            $user = User::find($session['user_id']);

            if (!$user) {
                self::logoutWeb();
                Session::setFlash('error', 'The user with the ID ' . $session['user_id'] . ' does not exist.');
                return null;
            }

            return $user;
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    /**
     * The userAPI function checks if the API token exists and retrieves the corresponding user if it
     * does.
     * 
     * @return ?User a User object or null.
     */
    private static function userAPI(): ?User
    {
        try {
            if (!self::checkAPI()) {
                return null;
            }

            $apiToken = Cookie::get('api_token');
            $accessToken = PersonalAccessToken::find($apiToken);

            if (!$accessToken) {
                self::logoutAPI();
                Session::setFlash('error', 'The API token with ID ' . $apiToken . ' does not exist.');
                return null;
            }

            $user = User::find($accessToken['user_id']);

            if (!$user) {
                self::logoutAPI();
                Session::setFlash('error', 'The user with the ID ' . $accessToken['user_id'] . ' does not exist.');
                return null;
            }

            return $user;
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }

    public static function user(string $guard = 'web'): ?User
    {
        try {
            return ($guard == 'web') ?
                self::userWeb() :
                self::userAPI();
        } catch (\Throwable $th) {
            ExceptionHandler::handleException($th);
        }
    }
}
