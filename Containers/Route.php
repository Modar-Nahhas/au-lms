<?php

namespace LMS_Website\Containers;

use LMS_Website\Enums\MessageTypeEnum;

abstract class Route
{
    /**
     * @param string $url
     * @param string $message
     * @param MessageTypeEnum $type
     * @return void
     */
    public static function redirect(string $url, string $message = '', MessageTypeEnum $type = MessageTypeEnum::Error): void
    {
        Session::setValue('message', $message);
        Session::setValue('message_type', $type->value);
        header("Location: $url");
        die;
    }

    /**
     * @param string $message
     * @param MessageTypeEnum $type
     * @return void
     */
    public static function redirectBack(string $message, MessageTypeEnum $type = MessageTypeEnum::Error): void
    {
        Session::setValue('message', $message);
        Session::setValue('message_type', $type->value);
        header("Location: $_SERVER[HTTP_REFERER]");
        die;
    }

}