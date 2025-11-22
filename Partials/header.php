<?php

use LMS_Website\Containers\Auth;
use LMS_Website\Containers\HtmlHelpers;

$user = Auth::check();


?>

<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm fixed-top">
        
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="../assets/img/logo.png" alt="Au Library" width="35" class="rounded-circle mr-2">
            AU LMS
        </a>
        <!-- Hamburger button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-nav"
                aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Collapsible menu -->
        <div class="collapse navbar-collapse" id="main-nav">
            <div class="navbar-nav ml-auto align-items-center">
                
                <a class="nav-item nav-link <?= HtmlHelpers::isActive('index.php') ?>" href="/index.php">Home</a>
                <a class="nav-item nav-link <?= HtmlHelpers::isActive('browse.php') ?>" href="/browse.php">Browse</a>
                
                <!-- Admin link: only show if logged in AND role = admin -->
                <?php if ($user && $user->memberType === \LMS_Website\Enums\UserTypeEnum::Admin->value): ?>
                    <a class="nav-item nav-link <?= HtmlHelpers::isActive('admin-dashboard.php') ?>" href="/admin-dashboard.php">Admin</a>
                <?php endif; ?>
                
                <!-- If user NOT logged in -->
                <?php if (!$user): ?>
                    <a class="nav-item nav-link <?= HtmlHelpers::isActive('signup.php') ?>" href="/signup.php">Sign-up</a>
                    <a class="nav-item nav-link <?= HtmlHelpers::isActive('login.php') ?>" href="/login.php">Login</a>
                    
                    <!-- If user IS logged in -->
                <?php else: ?>
                    <a class="nav-item nav-link" href="/actions/logout-action.php">Logout</a>
                    <span class="nav-item nav-link text-primary">
                        Hello, <?= htmlspecialchars($user->fullName()); ?>
                    </span>
                <?php endif; ?>
            
            </div>
        </div>
    </nav>
</header>