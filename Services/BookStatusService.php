<?php

namespace LMS_Website\Services;

use LMS_Website\Enums\BookStatusEnum;
use LMS_Website\Models\BookStatus;

readonly class BookStatusService
{

    public function create(BookStatusEnum $statusEnum, string|int $bookId, string|int $userId): BookStatus
    {
        return BookStatus::create($bookId, $statusEnum->value, $userId);
    }

}