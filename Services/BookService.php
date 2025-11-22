<?php

namespace LMS_Website\Services;

use LMS_Website\Containers\Auth;
use LMS_Website\Containers\Authorize;
use LMS_Website\Containers\File;
use LMS_Website\Containers\Route;
use LMS_Website\Enums\BookStatusEnum;
use LMS_Website\Enums\CategoryEnum;
use LMS_Website\Enums\LanguageEnum;
use LMS_Website\Enums\MessageTypeEnum;
use LMS_Website\Models\Book;

readonly class BookService
{
    protected BookStatusService $bookStatusService;

    public function __construct()
    {
        $this->bookStatusService = new BookStatusService();
    }

    public function featuredBooks(int $count = 3): array
    {
        $paginatedBooks = Book::all([], $count, 1, false);
        return $paginatedBooks['data'];

    }

    public function allBooks(int $count = 1000, int $page = 1, bool $withTrashed = false): array
    {
        if (!Authorize::isAdmin()) {
            $withTrashed = false;
        }
        return Book::all([], $count, $page, $withTrashed);
    }

    public function findById(int|string $id, bool $withTrashed = false): Book|null
    {
        if (!Authorize::isAdmin()) {
            $withTrashed = false;
        }
        return Book::find($id, $withTrashed);
    }

    /**
     * Filter books by title, author, language, and category.
     *
     * @param array $filters [
     *     'title'    => string|null,
     *     'author'   => string|null,
     *     'language' => string|null,
     *     'category' => string|null,
     * ]
     * @param int $limit
     * @param int $page
     *
     * @return array{
     *     data: Book[],
     *     total: int,
     *     page: int,
     *     limit: int
     * }
     */
    public function filterBooks(array $filters, int $limit = 9, int $page = 1, bool $withTrashed = false, bool $andFiler = true, bool $exactMatch = true): array
    {
        if (!Authorize::isAdmin()) {
            $withTrashed = false;
        }
        return Book::all($filters, $limit, $page, $withTrashed, $andFiler, $exactMatch);
    }

    public function borrowBook(string|int $bookId): void
    {
        $userId = Auth::id();
        if (!$userId) {
            Route::redirect('/book-details.php?id=' . $bookId, 'User need to be signed in', MessageTypeEnum::Warning);
        }
        // 1) Load the book
        $book = Book::find($bookId, false);
        if (!$book) {
            throw new \RuntimeException('Book not found.');
        }

        if ($book->bookStatus->status !== BookStatusEnum::Available->value) {
            throw new \RuntimeException('Book is not available.');
        }

        // 2) Create a new book status record
        $this->bookStatusService->create(BookStatusEnum::OnLoan, $bookId, $userId);
        Route::redirect('/book-details.php?id=' . $bookId, 'Book borrowed successfully.', MessageTypeEnum::Success);

    }


    /**
     * Validate data and create a new Book.
     *
     * @param array $data
     *  [
     *      'isbn'       => string,
     *      'title'      => string,
     *      'author'     => string,
     *      'publisher'  => string,
     *      'language'   => string,
     *      'category'   => string,
     *      'coverImage' => string|null,
     *  ]
     *
     * @return array{
     *     success: bool,
     *     book?: Book,
     *     errors?: array<string,string>
     * }
     */
    public function createBook(array $data): array
    {
        list($isbn, $title, $author, $publisher, $language, $category, $coverImage, $errors) = $this->validateInput($data);

        // If any validation errors → return them
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        try {
            $imageName = null;
            if ($coverImage && $coverImage['error'] !== UPLOAD_ERR_NO_FILE) {
                $imageName = File::saveUploadedImage($coverImage);
            }
            $book = Book::create(
                $isbn,
                $title,
                $author,
                $publisher,
                $language,
                $category,
                $data['description'] ?? null,
                $imageName
            );

            return [
                'success' => true,
                'book' => $book,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'errors' => ['coverImage' => $e->getMessage()],
            ];
        }
    }

    public function updateBook(int $id, array $data): bool
    {
        $table = Book::$table;

        $sql = "UPDATE $table
            SET title = :title,
                author = :author,
                publisher = :publisher,
                language = :language,
                category = :category,
                status = :status
            WHERE id = :id";

        $data['id'] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function validateInput(array $data): array
    {
// Trim inputs
        $isbn = trim($data['isbn'] ?? '');
        $title = trim($data['title'] ?? '');
        $author = trim($data['author'] ?? '');
        $publisher = trim($data['publisher'] ?? '');
        $language = trim($data['language'] ?? '');
        $category = trim($data['category'] ?? '');
        $coverImage = $data['coverImage'] ?? null;

        $errors = [];

        /**************
         * ISBN regex validation was disabled for easier testing
         * /*************/
        // --- ISBN ---
//        $isbnClean = str_replace(['-', ' '], '', $isbn);
        if ($isbn === '') {
            $errors['isbn'] = 'ISBN is required.';
        }
// elseif (!preg_match('/^\d{10}(\d{3})?$/', $isbnClean)) {
//            $errors['isbn'] = 'ISBN must be valid ISBN-10 or ISBN-13 format.';
//        }


        // --- Title ---
        if ($title === '') {
            $errors['title'] = 'Title is required.';
        }

        // --- Author ---
        if ($author === '') {
            $errors['author'] = 'Author is required.';
        }

        // --- Publisher ---
        if ($publisher === '') {
            $errors['publisher'] = 'Publisher is required.';
        }

        // --- Language (must match your ENUM) ---
        $allowedLanguages = LanguageEnum::toArray();
        if ($language === '') {
            $errors['language'] = 'Language is required.';
        } elseif (!in_array($language, $allowedLanguages, true)) {
            $errors['language'] = 'Invalid language selected.';
        }

        // --- Category (must match your ENUM) ---
        $allowedCategories = CategoryEnum::toArray();
        if ($category === '') {
            $errors['category'] = 'Category is required.';
        } elseif (!in_array($category, $allowedCategories, true)) {
            $errors['category'] = 'Invalid category selected.';
        }
        return array($isbn, $title, $author, $publisher, $language, $category, $coverImage, $errors);
    }


}