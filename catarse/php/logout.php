<?php
// logout.php — encerra a sessão do usuário
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

    // Redirecionar para a página inicial
    header('Location: ../index.php');
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo "<script>alert('Erro ao encerrar sessão: " . addslashes($e->getMessage()) . "'); window.location.href='../index.php';</script>";
    exit();
}

?>
