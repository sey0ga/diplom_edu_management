<?php
require __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;

function dbConnect(): PDO {
    $dotenv = Dotenv::createImmutable(__DIR__ . "/..");
    $dotenv->load();
    $dotenv->required([
        'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'
    ]);

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $_ENV['DB_HOST'],
        $_ENV['DB_PORT'] ?? '3306',
        $_ENV['DB_NAME'],
        $_ENV['DB_CHARSET'] ?? 'utf8mb4'
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $options);
    } catch (PDOException $e) {
        throw new PDOException("Database connection failed: " . $e->getMessage());
    }
}
?>