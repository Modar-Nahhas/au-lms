<?php

namespace LMS_Website\Services;

use LMS_Website\Enums\BookStatusEnum;
use LMS_Website\Models\BookStatus;

readonly class BookStatusService
{

    public function create(BookStatusEnum $statusEnum, string|int $bookId, string|int|null $userId): BookStatus
    {
        return BookStatus::create($bookId, $statusEnum->value, $userId);
    }

    public function updateStatus(string|int $id, BookStatusEnum $status, string|int|null $memberId = null): array
    {
        if ($id <= 0) {
            return [
                'success' => false,
                'errors' => ['id' => 'Invalid book status ID.'],
            ];
        }

        $bookStatus = BookStatus::find($id);
        if (!$bookStatus) {
            return [
                'success' => false,
                'errors' => ['general' => 'Book status not found.'],
            ];
        }
        // If member id is null, then the status is updated by admin, so the status is set to available
        // Else, meaning the book is being borrowed by the member, the status is set to on loan
        $bookStatus->status = !$memberId ? $status->value : BookStatusEnum::OnLoan->value;
        $bookStatus->memberId = $memberId;;

        // 5) Persist to DB using the model's save() method
        if (!$bookStatus->save()) {
            return [
                'success' => false,
                'errors' => ['general' => 'Failed to update book status in the database.'],
            ];
        }

        return [
            'success' => true,
            'book' => $bookStatus,
        ];
    }

}