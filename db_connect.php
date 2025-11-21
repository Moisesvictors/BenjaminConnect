<?php
$HOST = ${DB_HOST};
$PASSWORD = ${DB_PASSWORD};
$USER = ${DB_USER};
$DATABASE = ${DB_DATABASE};

$dsn = "mysql:host=$HOST;dbname=DATABASE;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $USER, $PASSWORD, $options);
} catch (\PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}
?>