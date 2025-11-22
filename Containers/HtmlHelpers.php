<?php

namespace LMS_Website\Containers;

use LMS_Website\Enums\BookStatusEnum;

abstract class HtmlHelpers
{
    public static function isActive(string $page, ?string $class = 'text-light'): string
    {
        $currentPage = basename($_SERVER['PHP_SELF']);
        return $currentPage == $page ? $class : '';
    }

    public static function buildPageUrl(string $pageName, int $page): string
    {
        $params = $_GET;
        $params['page'] = $page;
        return "$pageName?" . http_build_query($params);
    }

    public static function getStatusClass(string $status): string
    {
        $statusClass = 'badge-secondary';
        switch ($status) {
            case BookStatusEnum::Available->value:
                $statusClass = 'badge-success';
                break;
            case BookStatusEnum::OnLoan->value:
                $statusClass = 'badge-warning';
                break;
            case BookStatusEnum::Deleted->value:
                $statusClass = 'badge-danger';
                break;
        }
        return $statusClass;
    }

    public static function getShadowClass(string $status): string
    {
        $shadowClass = '';
        switch ($status) {
            case BookStatusEnum::Available->value:
                $shadowClass = 'book-available-shadow';
                break;
            case BookStatusEnum::OnLoan->value:
                $shadowClass = 'book-onloan-shadow';
                break;
            case BookStatusEnum::Deleted->value:
                $shadowClass = 'book-deleted-shadow';
                break;
        }
        return $shadowClass;
    }
}