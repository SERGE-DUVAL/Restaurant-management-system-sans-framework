<?php
namespace src\Config;

use PDO;
use Dotenv\Dotenv;

class Database {
    private static $pdo;

    public static function getConnection() {
        if (!self::$pdo) {
            // Charger .env
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();

            $driver = $_ENV['DB_DRIVER'] ?? 'mysql';
            $host   = $_ENV['DB_HOST'] ?? 'localhost';
            $port   = $_ENV['DB_PORT'] ?? '3306';
            $db     = $_ENV['DB_NAME'] ?? 'test';
            $user   = $_ENV['DB_USER'] ?? 'root';
            $pass   = $_ENV['DB_PASS'] ?? '';

            self::$pdo = new PDO(
                "$driver:host=$host;port=$port;dbname=$db",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }

        return self::$pdo;
    }
}
