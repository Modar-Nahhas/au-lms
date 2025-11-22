<?php
require_once __DIR__ . '/../../Containers/bootstrap.php';

use LMS_Website\Containers\Authorize;
use LMS_Website\Containers\Route;
use LMS_Website\Services\BookService;

header('Content-Type: application/json');

// Only admins allowed
if ($_SERVER['REQUEST_METHOD'] == 'GET' || !Authorize::isAdmin()) {
    Route::redirect('index.php');
}

// Read POST data
$bookId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Create service
$bookService = new BookService();
$data = $_POST;
$data['coverImage'] = $_FILES['coverImage'] ?? null;

try {
    if ($bookId > 0) {
        // UPDATE
        $response = $bookService->updateBook($bookId, $data);
    } else {
        // CREATE
        $response = $bookService->createBook($data);
    }
    echo json_encode($response);
    exit;

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
    exit;
}
