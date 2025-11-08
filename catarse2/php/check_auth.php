<?php
// check_auth.php - Retorna JSON indicando se o usuário está autenticado
header('Content-Type: application/json');
if (!session_id()) {
    session_start();
}


$authenticated = isset($_SESSION['user_id']);

// Informações úteis para depuração local
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

$response = [
    'authenticated' => (bool) $authenticated,
    'user_id' => $authenticated ? $_SESSION['user_id'] : null
];

if ($debug) {
    $response['session_id'] = session_id();
    // enviar lista de chaves da sessão (evita expor valores sensíveis)
    $response['session_keys'] = array_keys($_SESSION);
}

echo json_encode($response);

// Logging temporário para depuração: grava informações sobre cada chamada
try {
    $log = [];
    $log[] = "[" . date('Y-m-d H:i:s') . "] check_auth called";
    $log[] = "Request URI: " . ($_SERVER['REQUEST_URI'] ?? '');
    $log[] = "Remote Addr: " . ($_SERVER['REMOTE_ADDR'] ?? '');
    $log[] = "Session ID: " . session_id();
    $log[] = "Authenticated: " . ($authenticated ? '1' : '0');
    $log[] = "Session keys: " . implode(',', array_keys($_SESSION));
    $log[] = "Cookies: " . (isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : json_encode($_COOKIE));
    $log[] = "User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? '');
    $log[] = "----";

    $logText = implode("\n", $log) . "\n";
    @file_put_contents(__DIR__ . '/debug.log', $logText, FILE_APPEND | LOCK_EX);
} catch (Exception $e) {
    // não fazer nada em caso de erro de log
}

?>
