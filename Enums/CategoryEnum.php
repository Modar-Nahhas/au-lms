<?php

namespace LMS_Website\Enums;

use LMS_Website\Traits\EnumTrait;

enum CategoryEnum: string
{
    use EnumTrait;
    case Fiction     = 'Fiction';
    case NonFiction  = 'Non-Fiction';
    case Reference   = 'Reference';
}
