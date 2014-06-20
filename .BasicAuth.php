<?php

class BasicAuth {
    private static $secret = 'krU32YU1*';
    private static $users = array();
    private static function generate_auth_cookie() {
        $salt = mt_rand(1000000, 9999999);
        return $salt.':'.md5($salt . self::$secret);
    }
    private static function check_auth_cookie() {
        if (!isset($_COOKIE['admin_auth'])) return false;
        @list($salt,$hash) = explode(':', $_COOKIE['admin_auth']);
        return ($hash==md5($salt . self::$secret));
    }
    private static function check_basic_auth() {
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }
        $user = $_SERVER['PHP_AUTH_USER'];
        $pass = $_SERVER['PHP_AUTH_PW'];
        return (isset(self::$users[$user]) && $pass == self::$users[$user]);
    }
    private static function set_auth_cookie() {
        setcookie('admin_auth', self::generate_auth_cookie(), time()+600);
    }
    private static function prolongate_auth_cookie() {
        setcookie('admin_auth', $_COOKIE['admin_auth'], time()+600);
    }
    private static function require_basic_auth_and_exit() {
        header('WWW-Authenticate: Basic realm="Auth required"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Auth required';
        exit;
    }
    static function SimpleCheck($users) {
        self::$users = $users;
        if (self::check_auth_cookie()) {
            self::prolongate_auth_cookie();
        }
        else {
            if (self::check_basic_auth()) {
                self::set_auth_cookie();
            }
            else {
                self::require_basic_auth_and_exit();
            }
        }
    }
}
