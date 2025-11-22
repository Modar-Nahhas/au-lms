<?php

namespace LMS_Website\Containers;

use LMS_Website\Enums\UserTypeEnum;

abstract class Authorize
{
    public static function isAdmin(): bool
    {
        $user = Auth::check();

        if ($user === false) {
            return false;
        }

        return $user->memberType === UserTypeEnum::Admin->value;
    }

    public static function checkRole(UserTypeEnum $userType): bool
    {
        $user = Auth::check();

        if ($user === false) {
            return false;
        }

        return $user->memberType === $userType->value;
    }

}