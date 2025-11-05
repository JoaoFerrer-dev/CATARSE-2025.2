<?php
// login.php — validação de login
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/database.php';

try {
    // Apenas aceitar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        exit();
    }

    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    // Validações básicas
    if (!preg_match('/^[A-Za-z]{6}$/', $login)) {
        echo json_encode(['success' => false, 'message' => 'Login inválido (deve conter exatamente 6 letras).']);
        exit();
    }

    if (!preg_match('/^[0-9]{6,12}$/', $senha)) {
        echo json_encode(['success' => false, 'message' => 'Senha inválida (6 a 12 números).']);
        exit();
    }

    // Conectar ao banco
    $database = new Database();
    $pdo = $database->getConnection();
    if (!$pdo) {
        throw new Exception('Erro ao conectar ao banco de dados');
    }

    // Buscar usuário pelo login
    $stmt = $pdo->prepare('SELECT id, nome, login, senha FROM usuarios WHERE login = :login LIMIT 1');
    $stmt->execute([':login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Login ou senha incorretos.']);
        exit();
    }

    $hash = $user['senha'];
    // Verifica hash — se o campo estiver usando password_hash, use password_verify
    if (password_verify($senha, $hash)) {
        // Autenticação bem-sucedida
        // Regenerar id da sessão por segurança
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_nome'] = $user['nome'];

        echo json_encode(['success' => true, 'message' => 'Login realizado com sucesso.', 'user' => ['id' => $user['id'], 'nome' => $user['nome'], 'login' => $user['login']]]);
        exit();
    } else {
        // Senha incorreta
        echo json_encode(['success' => false, 'message' => 'Login ou senha incorretos.']);
        exit();
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
    exit();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}

?>
