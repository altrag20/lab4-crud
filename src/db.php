<?php
// Підключення до БД через PDO. Параметри — зі змінних оточення (.env), у коді паролів немає.
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $host = getenv('DB_HOST') ?: 'db';
    $name = getenv('DB_NAME') ?: 'theater';
    $user = getenv('DB_USER') ?: '';
    $pass = getenv('DB_PASS') ?: '';

    $pdo = new PDO(
        "pgsql:host=$host;port=5432;dbname=$name;options='--client_encoding=UTF8'",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // помилки SQL -> винятки
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,                   // справжні підготовлені запити
        ]
    );
    return $pdo;
}
