<?php

declare(strict_types=1);


spl_autoload_register(function ($class) {
    $prefix = 'LMS_Website';
    $base_dir = __DIR__;

    // Remove prefix from class
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Replace namespace with directory separator
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

?>