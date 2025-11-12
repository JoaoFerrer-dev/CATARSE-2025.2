<?php
// processar_pagamento.php - Processar pagamento e criar pedido
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }
    
    // Verificar se usuário está logado
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Usuário não autenticado. Faça login para continuar.');
    }
    
    // Verificar se carrinho não está vazio
    if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
        throw new Exception('Carrinho vazio');
    }
    
    // Receber dados do pagamento
    $nome_cartao = trim($_POST['nome-cartao'] ?? '');
    $numero_cartao = preg_replace('/\s+/', '', $_POST['numero-cartao'] ?? '');
    $validade = trim($_POST['validade-cartao'] ?? '');
    $cvv = trim($_POST['cvv-cartao'] ?? '');
    
    // Validações
    if (empty($nome_cartao) || strlen($nome_cartao) > 100) {
        throw new Exception('Nome no cartão inválido');
    }
    
    if (!preg_match('/^\d{13,19}$/', $numero_cartao)) {
        throw new Exception('Número do cartão inválido');
    }
    
    if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $validade)) {
        throw new Exception('Validade do cartão inválida');
    }
    
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        throw new Exception('CVV inválido');
    }
    
    // Conectar ao banco
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Calcular total
    $total_produtos = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $preco = floatval(str_replace(['R$', ','], ['', '.'], $item['preco']));
        $total_produtos += $preco * $item['quantidade'];
    }
    
    $frete_valor = 0;
    if (isset($_SESSION['frete']) && $_SESSION['frete']) {
        $frete_valor = floatval($_SESSION['frete']['valor']);
    }
    
    $total_geral = $total_produtos + $frete_valor;
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    try {
        // Criar pedido
        $stmt = $pdo->prepare("
            INSERT INTO pedidos (usuario_id, total, frete, status) 
            VALUES (:usuario_id, :total, :frete, 'pendente')
        ");
        
        $stmt->execute([
            ':usuario_id' => $_SESSION['user_id'],
            ':total' => $total_geral,
            ':frete' => $frete_valor
        ]);
        
        $pedido_id = $pdo->lastInsertId();
        
        // Adicionar itens do pedido
        $stmt_item = $pdo->prepare("
            INSERT INTO itens_pedido (pedido_id, nome_produto, tamanho, quantidade, preco, imagem) 
            VALUES (:pedido_id, :nome_produto, :tamanho, :quantidade, :preco, :imagem)
        ");
        
        foreach ($_SESSION['carrinho'] as $item) {
            $preco = floatval(str_replace(['R$', ','], ['', '.'], $item['preco']));
            
            $stmt_item->execute([
                ':pedido_id' => $pedido_id,
                ':nome_produto' => $item['nome'],
                ':tamanho' => $item['tamanho'] ?? '',
                ':quantidade' => $item['quantidade'],
                ':preco' => $preco,
                ':imagem' => $item['imagem'] ?? ''
            ]);
        }
        
        // Criar registro de pagamento (mascarar número do cartão)
        $numero_mascarado = substr($numero_cartao, 0, 4) . ' **** **** ' . substr($numero_cartao, -4);
        
        $stmt_pagamento = $pdo->prepare("
            INSERT INTO pagamentos (pedido_id, nome_cartao, numero_cartao, validade, cvv, status) 
            VALUES (:pedido_id, :nome_cartao, :numero_cartao, :validade, :cvv, 'aprovado')
        ");
        
        $stmt_pagamento->execute([
            ':pedido_id' => $pedido_id,
            ':nome_cartao' => $nome_cartao,
            ':numero_cartao' => $numero_mascarado,
            ':validade' => $validade,
            ':cvv' => '***'
        ]);
        
        // Atualizar status do pedido para pago
        $stmt_update = $pdo->prepare("UPDATE pedidos SET status = 'pago' WHERE id = :pedido_id");
        $stmt_update->execute([':pedido_id' => $pedido_id]);
        
        // Confirmar transação
        $pdo->commit();
        
        // Limpar carrinho
        $_SESSION['carrinho'] = [];
        $_SESSION['frete'] = null;
        
        echo json_encode([
            'success' => true,
            'message' => 'Pagamento processado com sucesso!',
            'pedido_id' => $pedido_id
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

