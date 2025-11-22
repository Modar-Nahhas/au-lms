<?php
require_once __DIR__ . '/../Containers/bootstrap.php';

use LMS_Website\Services\AuthService;
use LMS_Website\Containers\Session;
use LMS_Website\Containers\Auth;

if(Auth::check()) {
    header('Location: index.php');
}

$message = Session::getValue('message');
$messageType = Session::getValue('message_type');
Session::removeValue('message');
Session::removeValue('message_type');

$oldEmail = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $authService = new AuthService();
    $authService->login($email, $password);
}
?>
<!doctype html>
<html lang="en">
<head>
    
    <?php include __DIR__ . '/../Partials/head.html'; ?>
    
    <title>Australian University LMS - Login</title>

</head>
<body>
    <?php include __DIR__ . '/../Partials/header.php'; ?>
    
    <main class="container my-5" style="margin-top: 80px;">
        <div class="row justify-content-center">
            <div class="col-md-5">
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h1 class="h4 mb-0">Login</h1>
                        <small class="text-muted">Sign in with your email and password.</small>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?>">
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form id="login-form" method="post" action="/login.php">
                            <div class="form-group">
                                <label for="loginEmail">Email address</label>
                                <input
                                        type="email"
                                        class="form-control"
                                        id="loginEmail"
                                        name="email"
                                        placeholder="name@example.com"
                                        value="<?= htmlspecialchars($oldEmail) ?>"
                                >
                                <div class="invalid-feedback d-none">Please enter a valid email.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="loginPassword">Password</label>
                                <div class="input-group">
                                    <input
                                            type="password"
                                            class="form-control"
                                            id="loginPassword"
                                            name="password"
                                    >
                                    <div class="input-group-append" style="cursor: pointer">
                                        <span class="input-group-text toggle-password" data-target="#loginPassword">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback d-none">Password is required.</div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                            
                            <p class="mt-3 mb-0 text-center">
                                Don’t have an account?
                                <a href="signup.php">Sign up here</a>.
                            </p>
                        </form>
                    
                    </div>
                </div>
            
            </div>
        </div>
    </main>
    
    
    <?php include __DIR__ . '/../Partials/footer.php'; ?>
    <?php include __DIR__ . '/../Partials/footer-js.html'; ?>
    
    
    <script src="/assets/js/login.js"></script>
</body>
</html>
