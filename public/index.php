<?php

require_once __DIR__ . '/../Containers/bootstrap.php';

use LMS_Website\Services\BookService;
use LMS_Website\Containers\File;
use LMS_Website\Containers\Auth;
use LMS_Website\Containers\HtmlHelpers;

$bookService = new BookService();
$featuredBooks = $bookService->featuredBooks();
?>

<!doctype html>
<html lang="en">
<head>
    <?php include __DIR__ . '/../Partials/head.html'; ?>
    
    <title>Australian University LMS - Home</title>
    <link rel="stylesheet" href="/assets/css/book-gallery.css">

</head>
<body>
    <?php include __DIR__ . '/../Partials/header.php'; ?>
    
    <div class="jumbotron jumbotron-fluid text-white mb-0"
         style="background: url('/assets/img/library1.jpg')  center/cover no-repeat;">
        <div class="container py-5" style="background: rgba(0,0,0,0.4);">
            <h1 class="display-4">Welcome to AU Library</h1>
            <p class="lead mb-4">Discover, borrow, and manage books online.</p>
            <a class="btn btn-primary btn-lg mr-2" href="/browse.php">Browse Books</a>
            <?php if (!Auth::check()): ?>
                <a class="btn btn-outline-light btn-lg" href="/login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container my-5">
        <h2 class="mb-4">Featured Books</h2>
        <div class="card-deck">
            <?php foreach ($featuredBooks as $book): ?>
                <div class="card book-card mb-3 <?= HtmlHelpers::getShadowClass($book->bookStatus->status); ?>" data-id="<?= $book->id ?>"
                     onclick="window.location.href='/book-details.php?id=<?= $book->id ?>';"
                >
                    <img src="<?= htmlspecialchars(File::getFilePath($book->coverImage)) ?>"
                         class="card-img-top"
                         alt="<?= htmlspecialchars($book->title) ?>">
                    
                    <div class="card-body">
                        <h5 class="card-title"><?= strtolower($book->title) ?></h5>
                        <p class="card-text mb-1"><strong>Author:</strong> <?= strtolower($book->author) ?></p>
                        <p class="card-text mb-1"><strong>Publisher:</strong> <?= strtolower($book->publisher) ?></p>
                        <p class="card-text mb-1"><strong>Language:</strong> <?= htmlspecialchars($book->language) ?>
                        </p>
                        <p class="card-text"><strong>Category:</strong> <?= strtolower($book->category) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        
        
        </div>
    </div>
    <?php include __DIR__ . '/../Partials/footer.php'; ?>
    <?php include __DIR__ . '/../Partials/footer-js.html'; ?>
</body>
</html>
