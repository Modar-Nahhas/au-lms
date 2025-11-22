<?php
require_once __DIR__ . '/../Containers/bootstrap.php';

use LMS_Website\Services\BookService;
use LMS_Website\Containers\File;
use LMS_Website\Containers\HtmlHelpers;

$bookService = new BookService();

// Get current page from query string (default: 1)
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;

// Get current filters from query string
$filters = [
        'title' => $_GET['filter-title'] ?? null,
        'author' => $_GET['filter-author'] ?? null,
        'language' => $_GET['filter-language'] ?? null,
        'category' => $_GET['filter-category'] ?? null,
];

// How many books per page
$limit = 6; // or 6, 12 etc.

// Get paginated result from service
$result = $bookService->filterBooks($filters, $limit, $page);

// Unpack response
$books = $result['data'] ?? [];
$total = $result['total'] ?? 0;
$page = $result['page'] ?? $page;
$limit = $result['limit'] ?? $limit;

// Calculate total pages
$totalPages = $limit > 0 ? (int)ceil($total / $limit) : 1;


?>

<!doctype html>
<html lang="en">
<head>
    
    <?php include __DIR__ . '/../Partials/head.html'; ?>
    
    <title>Australian University LMS - Browse</title>
    <link rel="stylesheet" href="/assets/css/book-gallery.css">
    <link rel="stylesheet" href="/assets/css/browse.css">

</head>
<body>
    <?php include __DIR__ . '/../Partials/header.php'; ?>
    
    <main>
        <div class="container my-4">
            <h1>Browse Books</h1>
            
            <?php include __DIR__ . '/../Partials/books-filter.php'; ?>
        </div>
        
        <div class="container my-5">
            <div class="card-deck">
                <?php if (!empty($books)): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="card book-card mb-3 <?= HtmlHelpers::getShadowClass($book->bookStatus->status); ?>"
                             data-id="<?= $book->id ?>"
                             onclick="window.location.href = 'book-details.php?id=<?= $book->id ?>'">
                            <img src="<?= htmlspecialchars(File::getFilePath($book->coverImage)) ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($book->title) ?>">
                            
                            <div class="card-body">
                                <h5 class="card-title"><?= strtolower($book->title) ?>
                                    <span class="badge <?= HtmlHelpers::getStatusClass($book->bookStatus->status) ?> mr-3 p-2"
                                          style="font-size: 0.7rem;">
                                        <?= htmlspecialchars($book->bookStatus->status) ?>
                                    </span>
                                </h5>
                                
                                <p class="card-text mb-1"><strong>Author:</strong> <?= strtolower($book->author) ?></p>
                                <p class="card-text mb-1">
                                    <strong>Publisher:</strong> <?= strtolower($book->publisher) ?></p>
                                <p class="card-text mb-1">
                                    <strong>Language:</strong> <?= htmlspecialchars($book->language) ?></p>
                                <p class="card-text"><strong>Category:</strong> <?= strtolower($book->category) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning w-100 text-center">
                        No books found matching your search.
                    </div>
                <?php endif; ?>
            
            </div>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="container mb-5">
                <nav aria-label="Books pagination">
                    <ul class="pagination justify-content-center">
                        
                        <!-- Previous -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= $page > 1 ? HtmlHelpers::buildPageUrl('browse.php', $page - 1) : '#' ?>">Previous</a>
                        </li>
                        
                        <!-- Page numbers -->
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link"
                                   href="<?= HtmlHelpers::buildPageUrl('browse.php', $p) ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <!-- Next -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= $page < $totalPages ? HtmlHelpers::buildPageUrl('browse.php', $page + 1) : '#' ?>">Next</a>
                        </li>
                    
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </main>
    
    
    <?php include __DIR__ . '/../Partials/footer.php'; ?>
    <?php include __DIR__ . '/../Partials/footer-js.html'; ?>

</body>
</html>
