<?php
// login.php — validação de login
session_start();

require_once __DIR__ . '/database.php';

try {
    // Apenas aceitar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "<script>alert('Método não permitido'); window.history.back();</script>";
        exit();
    }

    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    // Validações básicas
    if (!preg_match('/^[A-Za-z]{1,6}$/', $login)) {
        echo "<script>alert('Login inválido (deve conter de 1 a 6 letras).'); window.history.back();</script>";
        exit();
    }

    if (!preg_match('/^[0-9]{6,12}$/', $senha)) {
        echo "<script>alert('Senha inválida (6 a 12 números).'); window.history.back();</script>";
        exit();
    }

    // Conectar ao banco
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!$pdo) {
        throw new Exception('Erro ao conectar ao banco de dados');
    }

    // Primeiro, verificar se é um administrador
    $stmt_admin = $pdo->prepare('SELECT id, nome, login, senha, nivel, ativo FROM administradores WHERE login = :login AND ativo = 1 LIMIT 1');
    $stmt_admin->execute([':login' => $login]);
    $admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // É um administrador - verificar senha (sempre com hash)
        if (password_verify($senha, $admin['senha'])) {
            // Login de admin bem-sucedido
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_login'] = $admin['login'];
            $_SESSION['admin_nome'] = $admin['nome'];
            $_SESSION['admin_nivel'] = $admin['nivel'];
            $_SESSION['is_admin'] = true;

            // Redirecionar para o dashboard admin
            header('Location: ../admin/dashboard.php');
            exit();
        } else {
            echo "<script>alert('Login ou senha incorretos.'); window.history.back();</script>";
            exit();
        }
    }

    // Se não é admin, buscar como usuário normal
    $stmt = $pdo->prepare('SELECT id, nome, login, senha FROM usuarios WHERE login = :login LIMIT 1');
    $stmt->execute([':login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('Login ou senha incorretos.'); window.history.back();</script>";
        exit();
    }

    // Verificar senha (suporte a hash e texto puro)
    $passwordOk = false;
    $hash = $user['senha'];
    
    if (is_string($hash) && preg_match('/^\$(2y|2a|argon2)/', $hash)) {
        // Senha com hash
        $passwordOk = password_verify($senha, $hash);
    } else {
        // Senha em texto puro (legacy)
        $passwordOk = ($hash === $senha);
    }

    if ($passwordOk) {
        // Login bem-sucedido
        // Preservar possíveis dados no carrinho antes de regenerar o ID da sessão
        $preserve_carrinho = $_SESSION['carrinho'] ?? null;
        $preserve_frete = $_SESSION['frete'] ?? null;

        session_regenerate_id(true);
        // Restaurar dados preservados (se existirem)
        if ($preserve_carrinho !== null) {
            $_SESSION['carrinho'] = $preserve_carrinho;
        }
        if ($preserve_frete !== null) {
            $_SESSION['frete'] = $preserve_frete;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_nome'] = $user['nome'];

        // Redirecionar para a página inicial
        header('Location: ../index.php');
        exit();
    } else {
        echo "<script>alert('Login ou senha incorretos.'); window.history.back();</script>";
        exit();
    }

} catch (Exception $e) {
    echo "<script>alert('Erro no sistema: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    exit();
}
?>
