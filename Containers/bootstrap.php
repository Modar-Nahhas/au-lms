<?php

namespace LMS_Website\Containers;

require_once __DIR__ . '/../autoload.php';

Env::loadEnv();
Session::initSession();
DB::connectToDatabase();
