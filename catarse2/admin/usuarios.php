<?php
require_once '../php/admin_auth.php';
requireAdmin();

require_once '../php/database.php';
$database = new Database();
$pdo = $database->getConnection();

// Buscar usuários
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY data_cadastro DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - CATARSE Admin</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-header">
        <h1>Gerenciar Usuários</h1>
        <div class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="produtos.php">Produtos</a>
            <a href="pedidos.php">Pedidos</a>
            <a href="usuarios.php">Usuários</a>
            <a href="../php/logout.php?admin=1">Sair</a>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-section">
            <h2>Lista de Usuários</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Login</th>
                        <th>CPF</th>
                        <th>Celular</th>
                        <th>Cidade/UF</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr><td colspan="8" style="text-align: center;">Nenhum usuário encontrado</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['login']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['cpf']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['celular']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['cidade'] . '/' . $usuario['uf']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></td>
                                <td>
                                    <button class="btn" onclick="verDetalhes(<?php echo $usuario['id']; ?>)">Ver</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function verDetalhes(id) {
            // Implementar modal de detalhes se necessário
            alert('Detalhes do usuário #' + id);
        }
    </script>
</body>
</html>

