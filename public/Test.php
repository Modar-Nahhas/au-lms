<?php

require_once __DIR__ . '/../Containers/bootstrap.php';

use LMS_Website\Models\User;
use LMS_Website\Models\Book;
use LMS_Website\Models\BookStatus;
use LMS_Website\Containers\Authorize;

if (Authorize::isAdmin() === false) {
    header('Location: index.php');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <?php include __DIR__ . '/../Partials/head.html'; ?>
    <title>LMS Model Test</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 20px 0;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            margin-top: 60px;
        }

        h1 {
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .test-card {
            background: #fff;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 14px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
            border-left: 4px solid #4f46e5;
        }

        .test-title {
            margin: 0 0 0.35rem;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-pass {
            background: #d1fae5;
            color: #047857;
        }

        .badge-fail {
            background: #fee2e2;
            color: #b91c1c;
        }

        .test-body {
            font-size: 0.9rem;
            color: #111827;
        }

        .test-body p {
            margin: 0.2rem 0;
        }

        code {
            background: #eef2ff;
            padding: 1px 4px;
            border-radius: 4px;
            font-family: "Fira Code", Consolas, monospace;
            font-size: 0.85rem;
        }

        pre.log {
            background: #020617;
            color: #e5e7eb;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            overflow-x: auto;
            margin-top: 0.5rem;
        }

        .error-block {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 10px 12px;
            border-radius: 8px;
            margin-top: 16px;
            color: #7f1d1d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../Partials/header.php'; ?>
    <div class="container">
        <h1>LMS Model Test</h1>
        <div class="subtitle">Testing User, Book and BookStatus model functionality.</div>
        
        <?php
        try {
            // Initialise static::$pdo and static::$table
            new User();
            new Book();
            new BookStatus();
            
            /* ===========================
               TEST 1: CREATE USER
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 1: Create User <span class="badge badge-pass">RUN</span></h2>';
            echo '<div class="test-body">';
            
            $user = User::create(
                    'Test',
                    'User',
                    'test.user+' . time() . '@example.com',
                    'Password@123'
            );
            
            echo "<p>Created User ID: <code>{$user->id}</code></p>";
            echo "<p>User name: <code>{$user->firstName} {$user->lastName}</code></p>";
            echo "<p>User type: <code>{$user->memberType}</code></p>";
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 2: FIND USER BY ID
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 2: Find User by ID</h2>';
            echo '<div class="test-body">';
            
            $foundUser = User::find($user->id);
            if ($foundUser) {
                echo '<span class="badge badge-pass">PASS</span>';
                echo "<p>Found user by ID <code>{$user->id}</code>: <code>{$foundUser->email}</code></p>";
            } else {
                echo '<span class="badge badge-fail">FAIL</span>';
                echo "<p>User not found by ID <code>{$user->id}</code></p>";
            }
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 3: FIND USER BY EMAIL
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 3: Find User by Email</h2>';
            echo '<div class="test-body">';
            
            $foundByEmail = User::findByEmail($user->email);
            if ($foundByEmail) {
                echo '<span class="badge badge-pass">PASS</span>';
                echo "<p>Found user by email <code>{$user->email}</code> → ID <code>{$foundByEmail->id}</code></p>";
            } else {
                echo '<span class="badge badge-fail">FAIL</span>';
                echo "<p>User not found by email <code>{$user->email}</code></p>";
            }
            
            echo '</div></section>';
            
            
            /* ===========================
           TEST 4: LIST USERS
   =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 4: List All Users (first 5)</h2>';
            echo '<div class="test-body">';
            
            // Request first page with limit 5
            $result = User::all(5, 1);
            
            $users = $result['data'] ?? [];
            $total = $result['total'] ?? 0;
            $page = $result['page'] ?? 1;
            $limit = $result['limit'] ?? 5;
            
            if (!empty($users)) {
                echo '<span class="badge badge-pass">PASS</span>';
                echo '<pre class="log">';
                echo "Total users: {$total}\n";
                echo "Showing page {$page} (limit {$limit})\n\n";
                
                foreach ($users as $u) {
                    echo "User #{$u->id}: {$u->firstName} {$u->lastName} ({$u->email})\n";
                }
                echo '</pre>';
            } else {
                echo '<span class="badge badge-fail">FAIL</span>';
                echo "<p>No users returned.</p>";
            }
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 5: CREATE BOOK
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 5: Create Book</h2>';
            echo '<div class="test-body">';
            
            $book = Book::create(
                    '978-TEST-' . time(),  // isbn
                    'Great Expectations',  // title
                    'Charles Dickens',     // author
                    'Macmillan Collectors Library', // publisher
                    'English',             // language (must match ENUM)
                    'Fiction',             // category (must match ENUM)
                    null                   // cover_image
            );
            
            echo '<span class="badge badge-pass">PASS</span>';
            echo "<p>Created Book ID: <code>{$book->id}</code></p>";
            echo "<p>Book title: <code>{$book->title}</code></p>";
            echo "<p>Book author: <code>{$book->author}</code></p>";
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 6: FIND BOOK BY ID
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 6: Find Book by ID</h2>';
            echo '<div class="test-body">';
            
            $foundBook = Book::find($book->id);
            if ($foundBook) {
                echo '<span class="badge badge-pass">PASS</span>';
                echo "<p>Found book by ID <code>{$book->id}</code>: <code>{$foundBook->title}</code></p>";
            } else {
                echo '<span class="badge badge-fail">FAIL</span>';
                echo "<p>Book not found by ID <code>{$book->id}</code></p>";
            }
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 7: UPDATE BOOK
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 7: Update Book and Save</h2>';
            echo '<div class="test-body">';
            
            $foundBook->title = 'Great Expectations (Updated)';
            $foundBook->category = 'Non-Fiction'; // just to see update in action
            $saved = $foundBook->save();
            
            if ($saved) {
                echo '<span class="badge badge-pass">PASS</span>';
                echo "<p>Book updated successfully.</p>";
            } else {
                echo '<span class="badge badge-fail">FAIL</span>';
                echo "<p>Book update failed.</p>";
            }
            
            $updatedBook = Book::find($book->id);
            echo "<p>Updated title: <code>{$updatedBook->title}</code></p>";
            echo "<p>Updated category: <code>{$updatedBook->category}</code></p>";
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 8: CREATE STATUS (AVAILABLE)
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 8: Create Book Status (Available)</h2>';
            echo '<div class="test-body">';
            
            $status1 = BookStatus::create($book->id, 'Available', $user->id);
            echo '<span class="badge badge-pass">PASS</span>';
            echo "<p>Created status ID: <code>{$status1->id}</code></p>";
            echo "<p>Status: <code>{$status1->status}</code> for book <code>#{$status1->bookId}</code>, member <code>#{$status1->memberId}</code></p>";
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 9: CREATE STATUS (ONLOAN)
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 9: Create Book Status (Onloan)</h2>';
            echo '<div class="test-body">';
            
            $status2 = BookStatus::create($book->id, 'Onloan', $user->id);
            echo '<span class="badge badge-pass">PASS</span>';
            echo "<p>Created status ID: <code>{$status2->id}</code></p>";
            echo "<p>Status: <code>{$status2->status}</code></p>";
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 10: ALL STATUS RECORDS
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 10: Get All Status Records for Book</h2>';
            echo '<div class="test-body">';
            
            $allStatuses = BookStatus::all($book->id);
            if ($allStatuses) {
                echo '<span class="badge badge-pass">PASS</span>';
                echo '<pre class="log">';
                foreach ($allStatuses as $s) {
                    echo "Status #{$s->id} => {$s->status} (member_id=" .
                            ($s->memberId ?? 'NULL') .
                            ", applied_date={$s->appliedDate})\n";
                }
                echo '</pre>';
            } else {
                echo '<span class="badge badge-fail">FAIL</span>';
                echo "<p>No status records found for book <code>#{$book->id}</code></p>";
            }
            
            echo '</div></section>';
            
            
            /* ===========================
           TEST 11: LIST BOOKS
            =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 11: List All Books (first 5)</h2>';
            echo '<div class="test-body">';
            
            // Request first page with limit 5
            $result = Book::all(5, 1);
            
            $books = $result['data'] ?? [];
            $total = $result['total'] ?? 0;
            $page = $result['page'] ?? 1;
            $limit = $result['limit'] ?? 5;
            
            if (!empty($books)) {
                echo '<span class="badge badge-pass">PASS</span>';
                echo '<pre class="log">';
                echo "Total books: {$total}\n";
                echo "Showing page {$page} (limit {$limit})\n\n";
                
                foreach ($books as $b) {
                    echo "Book #{$b->id}: {$b->title} by {$b->author}\n";
                }
                
                echo '</pre>';
            } else {
                echo '<span class="badge badge-fail">FAIL</span>';
                echo "<p>No books found.</p>";
            }
            
            echo '</div></section>';
            
            
            /* ===========================
               TEST 12: DELETE BOOK
               =========================== */
            echo '<section class="test-card">';
            echo '<h2 class="test-title">Test 12: Delete Book (and its statuses)</h2>';
            echo '<div class="test-body">';
            
            $deleted = $book->delete();
            if ($deleted) {
                echo '<span class="badge badge-pass">PASS</span>';
                echo "<p>Book deleted successfully.</p>";
            } else {
                echo '<span class="badge badge-fail">FAIL</span>';
                echo "<p>Book deletion failed.</p>";
            }
            
            $checkBook = Book::find($book->id);
            if ($checkBook) {
                echo "<p>Book still exists (<code>Soft Delete</code> or delete failed).</p>";
            } else {
                echo "<p>Book no longer found (<code>expected</code> for hard delete).</p>";
            }
            
            echo '</div></section>';
            
        } catch (\Throwable $e) {
            echo '<div class="error-block">';
            echo '<strong>ERROR:</strong><br>';
            echo htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            echo '</div>';
        }
        
        /* ===========================
          TEST 13: LIST BOOKS With Trashed
           =========================== */
        echo '<section class="test-card">';
        echo '<h2 class="test-title">Test 13: List All Books With Trashed (first 50)</h2>';
        echo '<div class="test-body">';
        
        // Request first page with limit 50
        $result = Book::all(50, 1, true);
        
        $books = $result['data'] ?? [];
        $total = $result['total'] ?? 0;
        $page = $result['page'] ?? 1;
        $limit = $result['limit'] ?? 5;
        
        if (!empty($books)) {
            echo '<span class="badge badge-pass">PASS</span>';
            echo '<pre class="log">';
            echo "Total books: {$total}\n";
            echo "Showing page {$page} (limit {$limit})\n\n";
            
            foreach ($books as $b) {
                echo "Book #{$b->id}" . ($b->deletedAt != null ? '(Trashed)' : '') . ": {$b->title} by {$b->author}\n";
            }
            
            echo '</pre>';
        } else {
            echo '<span class="badge badge-fail">FAIL</span>';
            echo "<p>No books found.</p>";
        }
        
        echo '</div></section>';
        
        ?>
    
    </div>
    <?php include __DIR__ . '/../Partials/footer.php'; ?>
</body>
</html>
