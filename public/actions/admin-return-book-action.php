<?php

require_once __DIR__ . '/../../Containers/bootstrap.php';

use LMS_Website\Containers\Authorize;
use LMS_Website\Containers\Route;
use LMS_Website\Services\BookService;
use LMS_Website\Enums\MessageTypeEnum;

if (!Authorize::isAdmin()) {
    Route::redirect('index.php');
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Route::redirect('admin-dashboard.php', 'Invalid request method');
}


$bookId = $_POST['id'];

if (!$bookId) {
    Route::redirectBack('Book id is required', MessageTypeEnum::Warning);
}
$bookService = new BookService();

$res = $bookService->returnBook($bookId);
echo json_encode($res);