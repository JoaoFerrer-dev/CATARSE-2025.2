<?php
// php/user_get.php?id=123 or ?login=abcdef
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$login = isset($_GET['login']) ? trim($_GET['login']) : null;

$db = new Database();
$pdo = $db->getConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit();
}

try {
    if ($id) {
        $stmt = $pdo->prepare('SELECT id, nome, login, celular, cpf, cep, endereco, bairro, cidade, uf, criado_em FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
    } elseif ($login) {
        $stmt = $pdo->prepare('SELECT id, nome, login, celular, cpf, cep, endereco, bairro, cidade, uf, criado_em FROM usuarios WHERE login = ?');
        $stmt->execute([$login]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Provide id or login']);
        exit();
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        exit();
    }

    echo json_encode($row, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
