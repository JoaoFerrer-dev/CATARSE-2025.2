<?php
// debug.php - Script de diagnóstico para problemas no painel admin
require_once '../php/database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico - CATARSE Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .check {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        h1 {
            color: #333;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico do Sistema Administrativo</h1>
    
    <?php
    $erros = [];
    $avisos = [];
    $sucessos = [];
    
    // 1. Verificar conexão com banco
    try {
        $database = new Database();
        $pdo = $database->getConnection();
        $sucessos[] = "Conexão com banco de dados: OK";
    } catch (Exception $e) {
        $erros[] = "Erro de conexão: " . $e->getMessage();
    }
    
    // 2. Verificar tabelas
    if (isset($pdo)) {
        $tabelas_necessarias = ['usuarios', 'pedidos', 'itens_pedido', 'pagamentos', 'produtos', 'administradores'];
        
        foreach ($tabelas_necessarias as $tabela) {
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE '$tabela'");
                if ($stmt->rowCount() > 0) {
                    $sucessos[] = "Tabela <code>$tabela</code>: Existe";
                } else {
                    $erros[] = "Tabela <code>$tabela</code>: NÃO EXISTE";
                }
            } catch (Exception $e) {
                $erros[] = "Erro ao verificar tabela <code>$tabela</code>: " . $e->getMessage();
            }
        }
        
        // 3. Verificar se há produtos
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos");
            $total = $stmt->fetch()['total'];
            if ($total > 0) {
                $sucessos[] = "Produtos cadastrados: $total";
            } else {
                $avisos[] = "Nenhum produto cadastrado ainda";
            }
        } catch (Exception $e) {
            $avisos[] = "Não foi possível contar produtos: " . $e->getMessage();
        }
        
        // 4. Verificar se há administradores
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM administradores");
            $total = $stmt->fetch()['total'];
            if ($total > 0) {
                $sucessos[] = "Administradores cadastrados: $total";
            } else {
                $avisos[] = "Nenhum administrador cadastrado. <a href='../php/create_admin.php'>Criar admin</a>";
            }
        } catch (Exception $e) {
            $avisos[] = "Não foi possível contar administradores: " . $e->getMessage();
        }
    }
    
    // 5. Verificar arquivos PHP
    $arquivos_necessarios = [
        '../php/database.php',
        '../php/admin_auth.php',
        '../php/admin_login.php',
        '../php/produtos_api.php',
        '../php/pedidos_api.php'
    ];
    
    foreach ($arquivos_necessarios as $arquivo) {
        if (file_exists($arquivo)) {
            $sucessos[] = "Arquivo <code>$arquivo</code>: Existe";
        } else {
            $erros[] = "Arquivo <code>$arquivo</code>: NÃO ENCONTRADO";
        }
    }
    
    // Exibir resultados
    if (!empty($sucessos)) {
        echo "<h2>✅ Sucessos</h2>";
        foreach ($sucessos as $msg) {
            echo "<div class='check success'>✓ $msg</div>";
        }
    }
    
    if (!empty($avisos)) {
        echo "<h2>⚠️ Avisos</h2>";
        foreach ($avisos as $msg) {
            echo "<div class='check warning'>⚠ $msg</div>";
        }
    }
    
    if (!empty($erros)) {
        echo "<h2>❌ Erros</h2>";
        foreach ($erros as $msg) {
            echo "<div class='check error'>✗ $msg</div>";
        }
    }
    
    if (empty($erros) && empty($avisos)) {
        echo "<div class='check success'><h2>✅ Tudo OK!</h2><p>O sistema está configurado corretamente.</p></div>";
    }
    ?>
    
    <h2>🔧 Soluções Rápidas</h2>
    <div class="check">
        <p><strong>Se a tabela produtos não existe:</strong></p>
        <ol>
            <li>Acesse: <a href="../php/create_tables.php" target="_blank">create_tables.php</a></li>
            <li>Ou importe o arquivo: <code>SQL/database.sql</code> no phpMyAdmin</li>
        </ol>
        
        <p><strong>Se não há administrador:</strong></p>
        <ol>
            <li>Acesse: <a href="../php/create_admin.php" target="_blank">create_admin.php</a></li>
            <li>Preencha o formulário e crie seu primeiro admin</li>
        </ol>
        
        <p><strong>Testar API de produtos:</strong></p>
        <ol>
            <li><a href="../php/produtos_api.php" target="_blank">Testar produtos_api.php</a></li>
            <li>Deve retornar um JSON (array vazio [] se não houver produtos)</li>
        </ol>
    </div>
    
    <p style="margin-top: 30px;">
        <a href="dashboard.php">← Voltar ao Dashboard</a> | 
        <a href="login.php">Ir para Login</a>
    </p>
</body>
</html>

