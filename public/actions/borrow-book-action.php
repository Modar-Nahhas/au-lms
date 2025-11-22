<?php

require_once __DIR__ . '/../../Containers/bootstrap.php';

use LMS_Website\Containers\Route;
use LMS_Website\Services\BookService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['book_id'])) {
    Route::redirect('index.php');
}

$bookService = new BookService();
$bookService->borrowBook($_POST['book_id']);