<?php
// carrinho.php - Gerenciar carrinho na sessão PHP
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/database.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Inicializar carrinho na sessão se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    
    // Inicializar frete na sessão se não existir
    if (!isset($_SESSION['frete'])) {
        $_SESSION['frete'] = null;
    }
    
    switch ($method) {
        case 'GET':
            // Retornar carrinho atual
            echo json_encode([
                'carrinho' => $_SESSION['carrinho'],
                'frete' => $_SESSION['frete'],
                'total' => calcularTotal($_SESSION['carrinho'], $_SESSION['frete'])
            ]);
            break;
            
        case 'POST':
            // Adicionar item ao carrinho
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['nome']) || !isset($data['preco']) || !isset($data['quantidade'])) {
                throw new Exception('Dados incompletos');
            }
            
            $item = [
                'nome' => $data['nome'],
                'tamanho' => $data['tamanho'] ?? '',
                'quantidade' => intval($data['quantidade']),
                'preco' => $data['preco'],
                'imagem' => $data['imagem'] ?? ''
            ];
            
            $_SESSION['carrinho'][] = $item;
            
            echo json_encode([
                'success' => true,
                'message' => 'Produto adicionado ao carrinho',
                'carrinho' => $_SESSION['carrinho']
            ]);
            break;
            
        case 'PUT':
            // Atualizar item ou frete
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['frete'])) {
                $_SESSION['frete'] = $data['frete'];
                echo json_encode(['success' => true, 'message' => 'Frete atualizado']);
            } elseif (isset($data['index']) && isset($data['quantidade'])) {
                $index = intval($data['index']);
                if (isset($_SESSION['carrinho'][$index])) {
                    $_SESSION['carrinho'][$index]['quantidade'] = intval($data['quantidade']);
                    echo json_encode(['success' => true, 'message' => 'Quantidade atualizada']);
                } else {
                    throw new Exception('Item não encontrado');
                }
            }
            break;
            
        case 'DELETE':
            // Remover item do carrinho
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['index'])) {
                $index = intval($data['index']);
                if (isset($_SESSION['carrinho'][$index])) {
                    array_splice($_SESSION['carrinho'], $index, 1);
                    echo json_encode(['success' => true, 'message' => 'Item removido']);
                } else {
                    throw new Exception('Item não encontrado');
                }
            } elseif (isset($data['limpar']) && $data['limpar'] === true) {
                $_SESSION['carrinho'] = [];
                $_SESSION['frete'] = null;
                echo json_encode(['success' => true, 'message' => 'Carrinho limpo']);
            }
            break;
            
        default:
            throw new Exception('Método não permitido');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

function calcularTotal($carrinho, $frete) {
    $total = 0;
    foreach ($carrinho as $item) {
        $preco = floatval(str_replace(['R$', ','], ['', '.'], $item['preco']));
        $total += $preco * $item['quantidade'];
    }
    if ($frete) {
        $total += floatval($frete['valor']);
    }
    return number_format($total, 2, ',', '.');
}
?>

