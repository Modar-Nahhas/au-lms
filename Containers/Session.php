<?php

namespace LMS_Website\Containers;

use LMS_Website\Models\User;

abstract class Session
{
    /**
     * @return void
     */
    public static function startSessionSafely(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @return void
     */
    public static function destroySession(): void
    {
        session_destroy();
    }

    /**
     * @return array [
     *  bool $isLoggedIn,
     *  ?User $user
     * ]
     */
    public static function initSession(): array
    {
        self::startSessionSafely();

        $isLoggedIn = isset($_SESSION['user']);
        $user = $_SESSION['user'] ?? null;
        return [$isLoggedIn, $user];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function setValue(string $key, mixed $value): void
    {
        self::startSessionSafely();
        $_SESSION[$key] = $value;
    }

    public static function getValue(string $key): mixed
    {
        self::startSessionSafely();
        return $_SESSION[$key] ?? null;
    }

    /**
     * @param string $key
     * @return void
     */
    public static function removeValue(string $key): void
    {
        self::startSessionSafely();
        unset($_SESSION[$key]);
    }
}

?>