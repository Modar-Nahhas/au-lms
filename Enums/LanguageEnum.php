<?php

namespace LMS_Website\Enums;

use LMS_Website\Traits\EnumTrait;

enum LanguageEnum: string
{
    use EnumTrait;
    case English  = 'English';
    case French   = 'French';
    case German   = 'German';
    case Mandarin = 'Mandarin';
    case Japanese = 'Japanese';
    case Russian  = 'Russian';
    case Other    = 'Other';
}
