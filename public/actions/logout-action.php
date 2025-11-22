<?php
require_once __DIR__ . '/../../Containers/bootstrap.php';

use LMS_Website\Services\AuthService;

$authService = new AuthService();
$authService->logout();