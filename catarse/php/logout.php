<?php
// logout.php — encerra a sessão do usuário
header('Content-Type: application/json; charset=utf-8');
session_start();

try {
    // Limpa todas as variáveis de sessão
    $_SESSION = [];

    // Apaga o cookie de sessão, se existir
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }

    // Destrói a sessão
    session_destroy();

    echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso.']);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao encerrar sessão: ' . $e->getMessage()]);
    exit();
}

?>
