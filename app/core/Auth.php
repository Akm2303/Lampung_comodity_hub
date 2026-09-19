<?php
class Auth {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public static function login($username, $password) {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE username = ? AND is_active = 1", [$username]);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'full_name' => $user['full_name']
            ];
            return true;
        }
        return false;
    }

    public static function user() { return $_SESSION['user'] ?? null; }
    public static function check() { return isset($_SESSION['user']); }
    public static function hasRole($roles) {
        if (!self::check()) return false;
        return in_array($_SESSION['user']['role'], (array)$roles);
    }
    public static function logout() { session_destroy(); }
}