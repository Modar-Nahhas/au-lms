<?php

namespace LMS_Website\Containers;

use LMS_Website\Models\User;

abstract class Auth
{
    /**
     * Check if the user is logged in, return the user object, if not return false
     *
     * @return User|bool
     */
    public static function check(): User|bool
    {
        list($isLoggedIn, $user) = Session::initSession();
        return $isLoggedIn ? $user : false;
    }

    /**
     * @param User $user
     * @return void
     */
    public static function setUser(User $user): void
    {
        Session::setValue('user', $user);
    }

    /**
     * @return void
     */
    public static function signOut(): void
    {
        Session::removeValue('user');
    }

    public static function id(): ?int
    {
        $user = Auth::check();
        return $user?->id ?? null;
    }
}