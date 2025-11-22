<?php

namespace LMS_Website\Containers;
abstract class Env
{
    private static bool $loaded = false;

    /**
     * @param string $key
     * @return string|null
     */
    public static function getValue(string $key): ?string
    {
        self::loadEnv();
        return $_ENV[$key] ?? null;
    }

    /**
     * @param string $path
     * @return void
     */
    public static function loadEnv(string $path = __DIR__ . '/../.env'): void
    {
        if (!file_exists($path)) {
            return;
        }

        if (self::$loaded) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // skip comments
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Store in environment
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
        self::$loaded = true;
    }
}