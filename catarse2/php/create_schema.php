<?php
// php/create_schema.php
// Run this once (open in browser) to create the database and usuarios table.

header('Content-Type: text/plain; charset=utf-8');

$host = 'localhost';
$dbName = 'CATARSE';
$user = 'root';
$pass = ''; // match php/database.php for local XAMPP

try {
    // connect without dbname to create database if needed
    $pdo = new PDO("mysql:host=$host", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database '$dbName' ensured.\n";

    // connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  login VARCHAR(6) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  celular VARCHAR(32),
  cpf VARCHAR(11) NOT NULL UNIQUE,
  cep VARCHAR(20),
  endereco TEXT,
  bairro VARCHAR(100),
  cidade VARCHAR(100),
  uf VARCHAR(10),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    $pdo->exec($sql);
    echo "Table 'usuarios' ensured.\n";

    echo "Done. You can now submit the cadastro form or check the table in phpMyAdmin.\n";

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}

?>
