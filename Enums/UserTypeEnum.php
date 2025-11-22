<?php

namespace LMS_Website\Enums;

use LMS_Website\Traits\EnumTrait;

enum UserTypeEnum: string
{
    use EnumTrait;
    case Admin = 'Admin';
    case Member = 'Member';
}
