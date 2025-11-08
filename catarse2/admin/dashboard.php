<?php
require_once '../php/admin_auth.php';
requireAdmin();

require_once '../php/database.php';
$database = new Database();
$pdo = $database->getConnection();

// Estatísticas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos");
$total_produtos = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM pedidos");
$total_pedidos = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(total) as total FROM pedidos WHERE status = 'pago'");
$receita_total = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM pedidos WHERE status = 'pendente'");
$pedidos_pendentes = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CATARSE Admin</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-header">
        <h1>Painel Administrativo - CATARSE</h1>
        <div class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="produtos.php">Produtos</a>
            <a href="pedidos.php">Pedidos</a>
            <a href="usuarios.php">Usuários</a>
            <a href="../php/logout.php?admin=1">Sair</a>
        </div>
    </div>

    <div class="admin-container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total de Produtos</h3>
                <div class="value"><?php echo $total_produtos; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total de Pedidos</h3>
                <div class="value"><?php echo $total_pedidos; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total de Usuários</h3>
                <div class="value"><?php echo $total_usuarios; ?></div>
            </div>
            <div class="stat-card">
                <h3>Receita Total</h3>
                <div class="value">R$ <?php echo number_format($receita_total, 2, ',', '.'); ?></div>
            </div>
        </div>

        <div class="admin-section">
            <h2>Resumo Rápido</h2>
            <p><strong>Pedidos Pendentes:</strong> <?php echo $pedidos_pendentes; ?></p>
            <p><strong>Usuário Logado:</strong> <?php echo htmlspecialchars($_SESSION['admin_nome']); ?> (<?php echo htmlspecialchars($_SESSION['admin_nivel']); ?>)</p>
        </div>

        <div class="admin-section">
            <h2>Ações Rápidas</h2>
            <div class="admin-actions">
                <a href="produtos.php?action=novo" class="btn btn-success">Adicionar Produto</a>
                <a href="pedidos.php" class="btn">Ver Pedidos</a>
                <a href="usuarios.php" class="btn">Gerenciar Usuários</a>
                <a href="../index.php" class="btn">Ver Site</a>
            </div>
        </div>
    </div>
</body>
</html>

