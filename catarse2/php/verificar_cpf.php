<?php
header('Content-Type: application/json');
require_once __DIR__ . '/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
    
    if (strlen($cpf) !== 11) {
        echo json_encode(['exists' => false, 'message' => 'CPF inválido']);
        exit();
    }

    $database = new Database();
    $pdo = $database->getConnection();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        echo json_encode([
            'exists' => true, 
            'message' => 'Este CPF já está cadastrado no sistema.'
        ]);
    } else {
        echo json_encode([
            'exists' => false, 
            'message' => 'CPF disponível para cadastro.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao verificar CPF']);
}
?>