<?php
// php/test_connection.php
// Small script to verify database connectivity using php/database.php

header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn instanceof PDO) {
    echo "Conexão bem-sucedida ao banco de dados '" . htmlspecialchars($db->db_name ?? 'CATARSE') . "'.\n";
    // Optional: show server version
    try {
        $version = $conn->getAttribute(PDO::ATTR_SERVER_VERSION);
        echo "Servidor MySQL: " . $version . "\n";
    } catch (Exception $e) {
        // ignore
    }
} else {
    echo "Falha ao conectar ao banco de dados.\n";
    // The Database class already echoes exception messages on failure; no need to duplicate sensitive info.
}

// Reminder: remove this file or protect it in production.

?>
