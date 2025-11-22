<?php

use LMS_Website\Containers\Auth;
use LMS_Website\Containers\HtmlHelpers;

$currentPage = basename($_SERVER['PHP_SELF']);

$user = Auth::check();


?>
<footer class="bg-dark text-light py-4 mt-2">
    <div class="container text-center">
        <p class="mb-1">&copy; 2025 My Library Management System</p>
        
        <div class="d-flex justify-content-center gap-3">
            
            <a class="nav-item nav-link <?= HtmlHelpers::isActive('index.php') ?>"
               href="/index.php">Home</a>
            
            <a class="nav-item nav-link <?= HtmlHelpers::isActive('browse.php') ?>"
               href="/browse.php">Browse</a>
            
            <?php if ($user && $user->memberType === \LMS_Website\Enums\UserTypeEnum::Admin->value): ?>
                <a class="nav-item nav-link <?= HtmlHelpers::isActive('admin-dashboard.php') ?>"
                   href="/admin-dashboard.php">Admin</a>
            <?php endif; ?>
            
            <?php if (!$user): ?>
                <a class="nav-item nav-link <?= HtmlHelpers::isActive('signup.php') ?>"
                   href="/signup.php">Sign-up</a>
                <a class="nav-item nav-link <?= HtmlHelpers::isActive('login.php') ?>"
                   href="/login.php">Login</a>
            <?php else: ?>
                <a class="nav-item nav-link" href="/actions/logout-action.php">Logout</a>
            <?php endif; ?>
        
        </div>
    </div>
</footer>
