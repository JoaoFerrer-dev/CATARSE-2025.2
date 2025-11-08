<?php
// php/user_delete.php (POST) - fields: id
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/database.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if (!$id) { http_response_code(400); echo json_encode(['error' => 'id required']); exit(); }

$db = new Database();
$pdo = $db->getConnection();
if (!$pdo) { http_response_code(500); echo json_encode(['error' => 'DB connection failed']); exit(); }

try {
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['ok' => true, 'rows' => $stmt->rowCount()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
