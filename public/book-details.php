<?php
// bootstrap / autoload as needed
require_once __DIR__ . '/../Containers/bootstrap.php';

use LMS_Website\Containers\HtmlHelpers;
use LMS_Website\Services\BookService;
use LMS_Website\Containers\File;
use LMS_Website\Enums\BookStatusEnum;
use LMS_Website\Containers\Route;
use LMS_Website\Containers\Session;
use LMS_Website\Containers\Auth;
use LMS_Website\Containers\Authorize;

$message = Session::getValue('message');
$messageType = Session::getValue('message_type');
Session::removeValue('message');
Session::removeValue('message_type');

$bookService = new BookService();

// 1) Get book id from query string
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2) Load the book from the database
$book = $bookService->findById($bookId, Authorize::isAdmin());

if (!$book) {
    Route::redirect('/browse.php');
}

// 3) Map status to badge
$statusLabel = $book->bookStatus->status ?? BookStatusEnum::Available->value; // ENUM: Available, Onloan, Deleted

$statusClass = HtmlHelpers::getStatusClass($statusLabel);

$relatedBooks = $bookService->featuredBooks();

?>
<!doctype html>
<html lang="en">
<head>
    
    
    <?php include __DIR__ . '/../Partials/head.html'; ?>
    
    <title>Australian University LMS - <?= htmlspecialchars($book->title) ?></title>
    
    <!-- Reuse your existing styles if needed -->
    <link rel="stylesheet" href="/assets/css/book-gallery.css">

</head>
<body>
    <?php include __DIR__ . '/../Partials/header.php'; ?>
    
    <main class="container my-4" style="margin-top: 80px;">
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <!-- Book title / meta -->
        <div class="mb-4">
            <h1 class="h3 mb-1"><?= htmlspecialchars($book->title) ?></h1>
            <p class="text-muted mb-0">
                by <strong><?= htmlspecialchars($book->author) ?></strong>
            </p>
            <small class="text-muted">
                Category:
                <span class="badge badge-info">
                    <?= htmlspecialchars($book->category) ?>
                </span>
                &nbsp;|&nbsp;
                Language:
                <span class="badge badge-secondary">
                    <?= htmlspecialchars($book->language) ?>
                </span>
            </small>
        </div>
        
        <div class="row">
            <!-- Cover -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="<?= htmlspecialchars(File::getFilePath($book->coverImage)) ?>"
                         class="card-img-top"
                         alt="<?= htmlspecialchars($book->title) ?>">
                </div>
            </div>
            
            <!-- Details -->
            <div class="col-md-8 mb-4">
                <!-- Availability + action -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="badge <?= $statusClass ?> mr-3 p-2">
                        <?= htmlspecialchars($statusLabel) ?>
                    </span>
                    <?php if (Auth::check() && $statusLabel === BookStatusEnum::Available->value): ?>
                        <form action="/actions/borrow-book-action.php" method="post">
                            <input type="hidden" name="book_id" value="<?= $book->id ?>">
                            <button class="btn btn-primary" style="font-size: 0.9rem;"
                                    <?= $statusLabel !== BookStatusEnum::Available->value ? 'disabled' : '' ?>>
                                Borrow this book
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <!-- Key metadata -->
                <div class="card mb-4">
                    <div class="card-header">
                        Book Information
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Title</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($book->title) ?></dd>
                            
                            <dt class="col-sm-4">Author</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($book->author) ?></dd>
                            
                            <dt class="col-sm-4">Publisher</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($book->publisher) ?></dd>
                            
                            <dt class="col-sm-4">ISBN</dt>
                            <dd class="col-sm-8">
                                <?= htmlspecialchars($book->isbn ?? 'N/A') ?>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="card">
                    <div class="card-header">
                        Description
                    </div>
                    <div class="card-body">
                        <?php if (!empty($book->description)): ?>
                            <p class="mb-0">
                                <?= nl2br(htmlspecialchars($book->description)) ?>
                            </p>
                        <?php else: ?>
                            <p class="mb-0 text-muted">
                                No description is available for this book.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Optional: related books -->
        <?php if (!empty($relatedBooks)): ?>
            <section class="mt-5">
                <h2 class="h5 mb-3">You might also like</h2>
                <div class="card-deck">
                    <?php foreach ($relatedBooks as $related): ?>
                        <div class="card book-card mb-3 <?= HtmlHelpers::getShadowClass($related->bookStatus->status); ?>"
                             data-id="<?= $related->id ?>"
                             onclick="window.location.href='/book-details.php?id=<?= $related->id ?>';">
                            <img src="<?= htmlspecialchars(File::getFilePath($related->coverImage)) ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($related->title) ?>">
                            <div class="card-body">
                                <h5 class="card-title mb-1">
                                    <?= htmlspecialchars($related->title) ?>
                                </h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <?= htmlspecialchars($related->author) ?>
                                        &middot;
                                        <?= htmlspecialchars($related->category) ?>
                                    </small>
                                </p>
                                <a href="/book-details.php?id=<?= $related->id ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    View details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    
    </main>
    
    
    <?php include __DIR__ . '/../Partials/footer.php'; ?>
    <?php include __DIR__ . '/../Partials/footer-js.html'; ?>

</body>
</html>
