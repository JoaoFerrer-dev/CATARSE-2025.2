<?php
// php/users_list.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/database.php';

$db = new Database();
$pdo = $db->getConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit();
}

try {
    $stmt = $pdo->query('SELECT id, nome, login, celular, cpf, cep, endereco, bairro, cidade, uf, criado_em FROM usuarios ORDER BY id DESC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
