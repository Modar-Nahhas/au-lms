<?php

namespace LMS_Website\Containers;

use PDO;

abstract class DB
{

    public static $CONN;

    /**
     * @param ?string $host
     * @param ?string $db
     * @param ?string $user
     * @param ?string $pass
     * @return PDO
     */
    public static function connectToDatabase(?string $host = null, ?string $db = null, ?string $user = null, ?string $pass = null): PDO
    {

        Env::loadEnv();
        $host = $host ?? $_ENV['DB_HOST'];
        $db = $db ?? $_ENV['DB_NAME'];
        $user = $user ?? $_ENV['DB_USER'];
        $pass = $pass ?? $_ENV['DB_PASS'];
        try {
            if (self::$CONN === null) {
                /** @var PDO $pdo */
                self::$CONN = new PDO(
                    "mysql:host=$host;dbname=$db;charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            }
            return self::$CONN;
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
