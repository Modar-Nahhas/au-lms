<?php
require_once __DIR__ . '/../Containers/bootstrap.php';

use LMS_Website\Services\AuthService;
use LMS_Website\Containers\Session;
use LMS_Website\Containers\Auth;

if (Auth::check()) {
    header('Location: index.php');
}

$message = Session::getValue('message');
$messageType = Session::getValue('message_type');
$userInput = Session::getValue('user_input');
Session::removeValue('message');
Session::removeValue('message_type');
Session::removeValue('user_input');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read POST data
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirmPassword'] ?? '';
    
    $authService = new AuthService();
    $authService->register($firstName, $lastName, $email, $password, $confirm);
}

?>

<!doctype html>
<html lang="en">
<head>
    
    <?php include __DIR__ . '/../Partials/head.html'; ?>
    
    <title>Australian University LMS - Sign Up</title>
    
    <!-- Optional: your own CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

</head>
<body>
    <?php include __DIR__ . '/../Partials/header.php'; ?>
    
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h1 class="h4 mb-0">Create an Account</h1>
                        <small class="text-muted">Sign up to access the Australian University Library Management
                            System.</small>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?>">
                                <?= $message ?>
                            </div>
                        <?php endif; ?>
                        <form id="signup-form" action="signup.php" method="post">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="firstName">First name</label>
                                    <input name="firstName" type="text" class="form-control" id="firstName"
                                           placeholder="John"
                                           value="<?= $userInput['firstName'] ?? '' ?>"
                                    >
                                    <div class="invalid-feedback d-none">First name is required.</div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="lastName">Last name</label>
                                    <input name="lastName" type="text" class="form-control" id="lastName"
                                           placeholder="Doe"
                                           value="<?= $userInput['lastName'] ?? '' ?>"
                                    >
                                    <div class="invalid-feedback d-none">Last name is required.</div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="signupEmail">Email address</label>
                                <input name="email" type="email" class="form-control" id="signupEmail"
                                       placeholder="name@example.com"
                                       value="<?= $userInput['email'] ?? '' ?>"
                                >
                                <div class="invalid-feedback d-none">Please enter a valid email address.</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="signupPassword">Password</label>
                                <div class="input-group">
                                    <input name="password" type="password" class="form-control" id="signupPassword">
                                    <div class="input-group-append" style="cursor: pointer">
                                        <span class="input-group-text toggle-password" data-target="#signupPassword">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback d-none">Password must be at least 6 characters.</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="signupPasswordConfirm">Confirm password</label>
                                <div class="input-group">
                                    <input name="confirmPassword" type="password" class="form-control"
                                           id="signupPasswordConfirm">
                                    <div class="input-group-append" style="cursor: pointer">
                                        <span class="input-group-text toggle-password"
                                              data-target="#signupPasswordConfirm">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback d-none">Passwords do not match.</div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Sign up</button>
                            
                            <p class="mt-3 mb-0 text-center">
                                Already have an account?
                                <a href="login.php">Login here</a>.
                            </p>
                        </form>
                    
                    </div>
                </div>
            
            </div>
        </div>
    </main>
    
    
    <?php include __DIR__ . '/../Partials/footer.php'; ?>
    <?php include __DIR__ . '/../Partials/footer-js.html'; ?>
    
    
    <script src="/assets/js/signup.js"></script>

</body>
</html>
