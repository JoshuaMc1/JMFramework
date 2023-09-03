<?php

use Lib\Http\Cookie;

if (!Cookie::has('csrf_token') || !is_valid_csrf_token(Cookie::get('csrf_token'))) {
    Cookie::set('csrf_token', csrf_token(), time() + 3600, '/', APP_URL);
}
