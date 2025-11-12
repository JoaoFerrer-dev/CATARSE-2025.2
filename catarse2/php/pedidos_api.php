<?php
// pedidos_api.php - API para gerenciar pedidos (admin)
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/admin_auth.php';

requireAdmin();

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Verificar se a tabela pedidos existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'pedidos'");
    if ($stmt->rowCount() == 0) {
        throw new Exception('Tabela de pedidos não existe. Execute create_tables.php primeiro.');
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'PUT':
            // Atualizar status do pedido
            $data = json_decode(file_get_contents('php://input'), true);
            
            $id = intval($data['id'] ?? 0);
            $status = trim($data['status'] ?? '');
            $codigo_rastreio = trim($data['codigo_rastreio'] ?? '');
            
            if (!$id || !$status) {
                throw new Exception('ID e status são obrigatórios');
            }
            
            $statuses_validos = ['pendente', 'pago', 'enviado', 'entregue', 'cancelado'];
            if (!in_array($status, $statuses_validos)) {
                throw new Exception('Status inválido');
            }
            
            $stmt = $pdo->prepare("
                UPDATE pedidos 
                SET status = :status, codigo_rastreio = :codigo_rastreio 
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':id' => $id,
                ':status' => $status,
                ':codigo_rastreio' => $codigo_rastreio ?: null
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Pedido atualizado com sucesso'
            ]);
            break;
            
        default:
            throw new Exception('Método não permitido');
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erro PDO em pedidos_api.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erro de banco de dados. Verifique se a tabela pedidos existe.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    error_log("Erro em pedidos_api.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

