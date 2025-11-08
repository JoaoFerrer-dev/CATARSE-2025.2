<?php
// admin_login.php — Login de administrador
session_start();

require_once __DIR__ . '/database.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    if (empty($login) || empty($senha)) {
        throw new Exception('Login e senha são obrigatórios');
    }

    // Conectar ao banco
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!$pdo) {
        throw new Exception('Erro ao conectar ao banco de dados');
    }

    // Buscar administrador pelo login
    $stmt = $pdo->prepare('SELECT id, nome, login, senha, nivel, ativo FROM administradores WHERE login = :login AND ativo = 1 LIMIT 1');
    $stmt->execute([':login' => $login]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        throw new Exception('Login ou senha incorretos');
    }

    // Verificar senha
    if (!password_verify($senha, $admin['senha'])) {
        throw new Exception('Login ou senha incorretos');
    }

    // Login bem-sucedido
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_login'] = $admin['login'];
    $_SESSION['admin_nome'] = $admin['nome'];
    $_SESSION['admin_nivel'] = $admin['nivel'];
    $_SESSION['is_admin'] = true;

    echo json_encode([
        'success' => true,
        'message' => 'Login realizado com sucesso',
        'redirect' => '../admin/dashboard.php'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

