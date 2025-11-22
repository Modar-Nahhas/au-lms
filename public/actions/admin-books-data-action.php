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

// DataTables server-side params
$draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 0;
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$length = isset($_GET['length']) ? (int)$_GET['length'] : 10;


// Convert to page/limit for your Book::all()
$limit = $length > 0 ? $length : 10;
$page = (int)floor($start / $limit) + 1;


// Get current filters from query string
$filters = [
    'title' => $_GET['search']['value'] ?? null,
    'author' => $_GET['search']['value'] ?? null,
    'language' => $_GET['search']['value'] ?? null,
    'category' => $_GET['search']['value'] ?? null,
    'isbn' => $_GET['search']['value'] ?? null,
];

// Use your existing pagination (Book::all or BookService wrapper)
$result = $bookService->filterBooks($filters, $limit, $page, true, false, false);
// expecting: ['data' => Book[], 'total' => int, 'page' => int, 'limit' => int]

$books = $result['data'] ?? [];
$total = $result['total'] ?? 0;

$data = [];

foreach ($books as $book) {
    $data[] = [
        'id' => $book->id,
        'isbn' => $book->isbn,
        'title' => $book->title,
        'author' => $book->author,
        'category' => $book->category,
        'language' => $book->language,
        'status' => $book->bookStatus->status,
    ];
}

// DataTables server-side response format
echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $total,
    'recordsFiltered' => $total, // no extra filtering yet
    'data' => $data,
]);
