<?php

namespace LMS_Website\Enums;

use LMS_Website\Traits\EnumTrait;

enum MessageTypeEnum: string
{
    use EnumTrait;
    case Success = 'success';
    case Error   = 'danger';
    case Warning = 'warning';
    case Info    = 'info';

}
