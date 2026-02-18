<?php

class DB
{
    /** @var \PDO|null */
    private static $pdo = null;

    public static function getConnection(): \PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $driver = env('DB_CONNECTION', 'mysql');
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $database = env('DB_DATABASE', '');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $charset = env('DB_CHARSET', 'utf8mb4');

        $dsn = sprintf('%s:host=%s;port=%s;dbname=%s;charset=%s', $driver, $host, $port, $database, $charset);

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }

        return self::$pdo;
    }

    private function __construct()
    {

    }

    private function __clone()
    {
        
    }
}
