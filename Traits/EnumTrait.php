<?php

namespace LMS_Website\Traits;

trait EnumTrait
{
    public static function toArray()
    {
        return array_column(self::cases(), 'value');
    }

}