<?php
$host = !empty($_ENV['MYSQL_HOST']) ? $_ENV['MYSQL_HOST'] : 'db'; // service name from docker compose
$dbname = !empty($_ENV['MYSQL_DB']) ? $_ENV['MYSQL_DB'] : 'mobile';
$username = !empty($_ENV['MYSQL_USER']) ? $_ENV['MYSQL_USER'] : 'root';
$password = !empty($_ENV['MYSQL_PASSWORD']) ? $_ENV['MYSQL_PASSWORD'] : 'secret';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}