<?php
require_once __DIR__ . '/../../Containers/bootstrap.php';

use LMS_Website\Containers\Route;
use LMS_Website\Containers\Authorize;
use LMS_Website\Services\BookService;

header('Content-Type: application/json');

if (!Authorize::isAdmin()) {
    Route::redirect('index.php');
}

$bookService = new BookService();
$book = $bookService->findById($_GET['id'], true);
$bookArray = $book->toArray();
$bookArray['status'] = $book->bookStatus->status;
echo json_encode($bookArray);
