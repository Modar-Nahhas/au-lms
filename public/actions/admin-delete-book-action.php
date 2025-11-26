<?php

require_once __DIR__ . '/../../Containers/bootstrap.php';

use LMS_Website\Containers\Authorize;
use LMS_Website\Containers\Route;
use LMS_Website\Services\BookService;
use LMS_Website\Enums\MessageTypeEnum;

if (!Authorize::isAdmin()) {
    Route::redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    Route::redirectBack('Invalid request method');
}


$bookService = new BookService();

$bookId = $_GET['id'] ?? null;
if (!$bookId) {
    Route::redirectBack('Book id is required', MessageTypeEnum::Warning);
}

$res = $bookService->deleteBook($bookId);
echo json_encode($res);