<?php

namespace Lib\Http;

use App\Models\User;
use Lib\Model\Session as SessionModel;
use Lib\Support\Hash;
use Lib\Http\Cookie;
use Lib\Http\Session;

class Auth
{
    public static function attempt(string $email, string $password)
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
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function logout()
    {
        try {
            $session = SessionModel::find(Session::get('session_id'));

            if ($session) {
                (new SessionModel())->delete($session['id']);
            }

            Cookie::remove('session_id');
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function check(): bool
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
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }

    public static function user()
    {
        try {
            if (!self::check()) {
                return null;
            }

            $sessionId = Cookie::get('session_id');
            $session = SessionModel::find($sessionId);

            if (!$session) {
                self::logout();
                Session::setFlash('error', 'The session with the ID ' . $sessionId . ' does not exist.');
                return null;
            }

            $user = User::find($session['user_id']);

            if (!$user) {
                self::logout();
                Session::setFlash('error', 'The user with the ID ' . $session['user_id'] . ' does not exist.');
                return null;
            }

            return $user;
        } catch (\Throwable $th) {
            ErrorHandler::renderError(500, 'Internal Server Error', $th->getMessage());
        }
    }
}
