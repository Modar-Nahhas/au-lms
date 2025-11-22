<?php

namespace LMS_Website\Enums;

use LMS_Website\Traits\EnumTrait;

enum BookStatusEnum: string
{
    use EnumTrait;
    case Available = 'Available';
    case OnLoan    = 'Onloan';
    case Deleted   = 'Deleted';
}
