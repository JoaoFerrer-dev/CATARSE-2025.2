<?php
// php/user_update.php (POST) - fields: id (required) and any of nome, celular, cep, endereco, bairro, cidade, uf, senha
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/database.php';

$data = $_POST;
$id = isset($data['id']) ? intval($data['id']) : 0;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'id required']);
    exit();
}

$db = new Database();
$pdo = $db->getConnection();
if (!$pdo) { http_response_code(500); echo json_encode(['error' => 'DB connection failed']); exit(); }

$fields = [];
$params = [];
foreach (['nome','celular','cep','endereco','bairro','cidade','uf'] as $f) {
    if (isset($data[$f])) { $fields[] = "$f = :$f"; $params[":$f"] = $data[$f]; }
}
if (isset($data['senha']) && $data['senha'] !== '') {
    $fields[] = "senha = :senha";
    $params[':senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
}

if (empty($fields)) { http_response_code(400); echo json_encode(['error' => 'no fields to update']); exit(); }

$params[':id'] = $id;
$sql = 'UPDATE usuarios SET ' . implode(', ', $fields) . ' WHERE id = :id';

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['ok' => true, 'rows' => $stmt->rowCount()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
