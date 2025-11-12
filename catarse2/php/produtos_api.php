<?php
// produtos_api.php - API REST para gerenciar produtos
// Desabilitar exibição de erros para não quebrar o JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
// Iniciar output buffering para capturar qualquer output não desejado
ob_start();

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/admin_auth.php';

// Função para verificar admin sem redirecionar (para API)
function checkAdminAPI() {
    if (!isAdmin()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Acesso negado. Autenticação de administrador necessária.'
        ]);
        exit();
    }
}

// Verificar se é admin (exceto para GET que pode ser público)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    checkAdminAPI();
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Verificar se a tabela produtos existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'produtos'");
    if ($stmt->rowCount() == 0) {
        throw new Exception('Tabela de produtos não existe. Execute create_tables.php primeiro.');
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Listar produtos (público ou filtrado)
            $ativo = isset($_GET['ativo']) ? intval($_GET['ativo']) : null;
            $id = isset($_GET['id']) ? intval($_GET['id']) : null;
            
            if ($id) {
                // Buscar produto específico
                $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $produto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$produto) {
                    throw new Exception('Produto não encontrado');
                }
                
                echo json_encode($produto);
            } else {
                // Listar todos os produtos
                if ($ativo !== null) {
                    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE ativo = :ativo ORDER BY data_cadastro DESC");
                    $stmt->execute([':ativo' => $ativo]);
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM produtos ORDER BY data_cadastro DESC");
                    $stmt->execute();
                }
                
                $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($produtos);
            }
            break;
            
        case 'POST':
            // Criar novo produto
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                throw new Exception('Dados JSON inválidos: ' . json_last_error_msg());
            }
            
            $nome = trim($data['nome'] ?? '');
            $descricao = trim($data['descricao'] ?? '');
            $preco_original = floatval($data['preco_original'] ?? 0);
            $preco_promocional = isset($data['preco_promocional']) && $data['preco_promocional'] !== '' ? floatval($data['preco_promocional']) : null;
            $desconto = intval($data['desconto'] ?? 0);
            $imagem = trim($data['imagem'] ?? '');
            $tamanhos = trim($data['tamanhos_disponiveis'] ?? 'P,M,G,GG');
            $estoque = intval($data['estoque'] ?? 0);
            $ativo = isset($data['ativo']) ? intval($data['ativo']) : 1;
            
            // Validações
            if (empty($nome) || $preco_original <= 0 || empty($imagem)) {
                throw new Exception('Nome, preço original e imagem são obrigatórios');
            }
            
            // Calcular desconto se preço promocional foi informado
            if ($preco_promocional && $preco_promocional < $preco_original) {
                $desconto = round((($preco_original - $preco_promocional) / $preco_original) * 100);
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO produtos (nome, descricao, preco_original, preco_promocional, desconto, imagem, tamanhos_disponiveis, estoque, ativo) 
                VALUES (:nome, :descricao, :preco_original, :preco_promocional, :desconto, :imagem, :tamanhos, :estoque, :ativo)
            ");
            
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':preco_original' => $preco_original,
                ':preco_promocional' => $preco_promocional,
                ':desconto' => $desconto,
                ':imagem' => $imagem,
                ':tamanhos' => $tamanhos,
                ':estoque' => $estoque,
                ':ativo' => $ativo
            ]);
            
            $produto_id = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Produto criado com sucesso',
                'id' => $produto_id
            ]);
            break;
            
        case 'PUT':
            // Atualizar produto
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                throw new Exception('Dados JSON inválidos: ' . json_last_error_msg());
            }
            
            $id = intval($data['id'] ?? 0);
            if (!$id) {
                throw new Exception('ID do produto é obrigatório');
            }
            
            $nome = trim($data['nome'] ?? '');
            $descricao = trim($data['descricao'] ?? '');
            $preco_original = floatval($data['preco_original'] ?? 0);
            $preco_promocional = isset($data['preco_promocional']) && $data['preco_promocional'] !== '' ? floatval($data['preco_promocional']) : null;
            $desconto = intval($data['desconto'] ?? 0);
            $imagem = trim($data['imagem'] ?? '');
            $tamanhos = trim($data['tamanhos_disponiveis'] ?? 'P,M,G,GG');
            $estoque = intval($data['estoque'] ?? 0);
            $ativo = isset($data['ativo']) ? intval($data['ativo']) : 1;
            
            if (empty($nome) || $preco_original <= 0 || empty($imagem)) {
                throw new Exception('Nome, preço original e imagem são obrigatórios');
            }
            
            // Calcular desconto se preço promocional foi informado
            if ($preco_promocional && $preco_promocional < $preco_original) {
                $desconto = round((($preco_original - $preco_promocional) / $preco_original) * 100);
            }
            
            $stmt = $pdo->prepare("
                UPDATE produtos 
                SET nome = :nome, descricao = :descricao, preco_original = :preco_original, 
                    preco_promocional = :preco_promocional, desconto = :desconto, imagem = :imagem, 
                    tamanhos_disponiveis = :tamanhos, estoque = :estoque, ativo = :ativo
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':id' => $id,
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':preco_original' => $preco_original,
                ':preco_promocional' => $preco_promocional,
                ':desconto' => $desconto,
                ':imagem' => $imagem,
                ':tamanhos' => $tamanhos,
                ':estoque' => $estoque,
                ':ativo' => $ativo
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Produto atualizado com sucesso'
            ]);
            break;
            
        case 'DELETE':
            // Deletar produto
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                throw new Exception('Dados JSON inválidos: ' . json_last_error_msg());
            }
            
            $id = intval($data['id'] ?? 0);
            
            if (!$id) {
                throw new Exception('ID do produto é obrigatório');
            }
            
            $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Produto deletado com sucesso'
            ]);
            break;
            
        default:
            throw new Exception('Método não permitido');
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erro PDO em produtos_api.php: " . $e->getMessage());
    // Limpar qualquer output anterior
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'success' => false,
        'error' => 'Erro de banco de dados. Verifique se a tabela produtos existe.',
        'details' => (ini_get('display_errors') ? $e->getMessage() : null)
    ]);
    exit();
} catch (Exception $e) {
    http_response_code(400);
    error_log("Erro em produtos_api.php: " . $e->getMessage());
    // Limpar qualquer output anterior
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit();
} catch (Error $e) {
    http_response_code(500);
    error_log("Erro fatal em produtos_api.php: " . $e->getMessage());
    // Limpar qualquer output anterior
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'success' => false,
        'error' => 'Erro interno do servidor. Verifique os logs para mais detalhes.'
    ]);
    exit();
}
?>

