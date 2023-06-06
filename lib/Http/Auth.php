<?php

namespace Lib\Http;

use App\Models\User;
use Lib\Exception\ExceptionHandler;
use Lib\Model\{PersonalAccessToken, Session as SessionModel};
use Lib\Support\{Hash, Token};
use Lib\Http\{Cookie, Session};

class Auth
{
    public static function attemptWeb(string $email, string $password)
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

    public static function attemptAPI(string $email, string $password)
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

    public static function logoutWeb()
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

    public static function logoutAPI()
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

    public static function checkWeb(): bool
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

    public static function checkAPI(): bool
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

    public static function userWeb()
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

    public static function userAPI()
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
}
