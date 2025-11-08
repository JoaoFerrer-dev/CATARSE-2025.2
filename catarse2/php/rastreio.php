<?php
// rastreio.php - Buscar pedidos do usuário
session_start();
require_once __DIR__ . '/database.php';

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../paginas/login.html');
    exit();
}

$database = new Database();
$pdo = $database->getConnection();

// Buscar pedidos do usuário
$stmt = $pdo->prepare("
    SELECT p.*, 
           COUNT(ip.id) as total_itens,
           SUM(ip.quantidade) as total_produtos
    FROM pedidos p
    LEFT JOIN itens_pedido ip ON p.id = ip.pedido_id
    WHERE p.usuario_id = :usuario_id
    GROUP BY p.id
    ORDER BY p.data_pedido DESC
");

$stmt->execute([':usuario_id' => $_SESSION['user_id']]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar itens de cada pedido
foreach ($pedidos as &$pedido) {
    $stmt_itens = $pdo->prepare("
        SELECT * FROM itens_pedido 
        WHERE pedido_id = :pedido_id
    ");
    $stmt_itens->execute([':pedido_id' => $pedido['id']]);
    $pedido['itens'] = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastreamento de Pedidos - CATARSE</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/rastreio.css">
    <link rel="icon" href="../img/logo2-removebg-preview.png" type="image/png">
</head>
<body>
    <?php include '../php/nav_include.php'; ?>
    
    <main class="rastreio-container">
        <h1>Meus Pedidos</h1>
        
        <?php if (empty($pedidos)): ?>
            <div class="sem-pedidos">
                <p>Você ainda não possui pedidos.</p>
                <a href="../paginas/produtos.php" class="cta">Ver Produtos</a>
            </div>
        <?php else: ?>
            <div class="pedidos-lista">
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedido-card">
                        <div class="pedido-header">
                            <h2>Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></h2>
                            <span class="status status-<?php echo $pedido['status']; ?>">
                                <?php 
                                $status_labels = [
                                    'pendente' => 'Pendente',
                                    'pago' => 'Pago',
                                    'enviado' => 'Enviado',
                                    'entregue' => 'Entregue',
                                    'cancelado' => 'Cancelado'
                                ];
                                echo $status_labels[$pedido['status']] ?? $pedido['status'];
                                ?>
                            </span>
                        </div>
                        
                        <div class="pedido-info">
                            <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
                            <p><strong>Total:</strong> R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></p>
                            <?php if ($pedido['frete'] > 0): ?>
                                <p><strong>Frete:</strong> R$ <?php echo number_format($pedido['frete'], 2, ',', '.'); ?></p>
                            <?php endif; ?>
                            <?php if ($pedido['codigo_rastreio']): ?>
                                <p><strong>Código de Rastreio:</strong> <?php echo htmlspecialchars($pedido['codigo_rastreio']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pedido-itens">
                            <h3>Itens do Pedido:</h3>
                            <ul>
                                <?php foreach ($pedido['itens'] as $item): ?>
                                    <li>
                                        <?php echo htmlspecialchars($item['nome_produto']); ?>
                                        <?php if ($item['tamanho']): ?>
                                            - Tamanho: <?php echo htmlspecialchars($item['tamanho']); ?>
                                        <?php endif; ?>
                                        - Qtd: <?php echo $item['quantidade']; ?>
                                        - R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>CATARSE © 2024 — "purify yourself"</p>
        <p><a href="https://www.instagram.com/catarsegarments?igsh=MWNvOW5hYXFmNm1hMg==" target="_blank">@catarsegarments</a></p>
    </footer>
</body>
</html>

