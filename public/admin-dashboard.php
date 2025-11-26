<?php

require_once __DIR__ . '/../Containers/bootstrap.php';

use LMS_Website\Containers\Authorize;
use LMS_Website\Containers\Session;
use LMS_Website\Services\BookService;

if (Authorize::isAdmin() === false) {
    header('Location: index.php');
}

$message = Session::getValue('message');
$messageType = Session::getValue('message_type');
Session::removeValue('message');
Session::removeValue('message_type');

$bookService = new BookService();

?>

<!doctype html>
<html lang="en">
<head>
    
    <?php include __DIR__ . '/../Partials/head.html'; ?>
    
    <title>Australian University LMS - Admin Dashboard</title>
    <!-- DataTables CSS (Bootstrap 4 style) -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <?php include __DIR__ . '/../Partials/header.php'; ?>
    
    <main class="container my-4">
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <div id="admin-message" class="alert alert-warning d-none">
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-0">Admin Dashboard</h1>
                <small class="text-muted">Manage books in the Australian University LMS</small>
            </div>
            <div class="action-buttons">
                <button class="btn btn-info" id="btn-test">
                    <a href="Test.php" class="text-white" style="text-decoration: none">Run Test</a>
                </button>
                <button class="btn btn-success" id="btn-add-book">
                    + Add New Book
                </button>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <table id="books-table" class="table table-striped table-bordered" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">ISBN</th>
                            <th class="text-center">Title</th>
                            <th class="text-center">Author</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Language</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- rows are injected by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    
    <?php include __DIR__ . '/../Partials/footer.php'; ?>
    
    <?php include __DIR__ . '/../Partials/add-edit-book-modal.php'; ?>
    
    <!-- ========== SCRIPTS ========== -->
    
    <?php include __DIR__ . '/../Partials/footer-js.html'; ?>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    
    <!--    Don't change the order of the following scripts-->
    <script src="/assets/js/api-handlers/BooksApi.js"></script>
    <script src="/assets/js/admin-dashboard.js"></script>
    <script src="/assets/js/admin-add-edit-book-modal.js"></script>

</body>
</html>
