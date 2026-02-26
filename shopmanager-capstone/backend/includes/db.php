<?php
/**
 * includes/db.php
 * Returns a singleton PDO instance.
 * Uses environment variables loaded by env.php.
 *
 * Usage anywhere in the app:
 *      $pdo = getDB();
 */

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo !== null) return $pdo;

    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: 'ics2203_capstone';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,   // use native prepared statements
        ]);
    } catch (PDOException $e) {
    // In production hide the real message; in dev show it
    if (getenv('APP_DEBUG') === 'true') {
        die('<h2 style="color:red;">Database Error</h2><pre>' .
            htmlspecialchars($e->getMessage()) . '</pre>');
    }
    die('<h2 style="color:red;">Service Unavailable</h2>
         <p>Please try again later.</p>');
    }

    return $pdo;
}
