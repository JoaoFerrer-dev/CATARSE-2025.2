<?php
require_once '../php/admin_auth.php';
requireAdmin();

require_once '../php/database.php';

$pedidos = [];
$erro = null;

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Verificar se as tabelas existem
    $stmt = $pdo->query("SHOW TABLES LIKE 'pedidos'");
    if ($stmt->rowCount() == 0) {
        throw new Exception('Tabela de pedidos não existe. Execute create_tables.php primeiro.');
    }
    
    // Buscar pedidos
    $stmt = $pdo->query("
        SELECT p.*, u.nome as usuario_nome, u.email as usuario_email
        FROM pedidos p
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.data_pedido DESC
    ");
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar itens de cada pedido
    foreach ($pedidos as &$pedido) {
        $stmt_itens = $pdo->prepare("SELECT * FROM itens_pedido WHERE pedido_id = ?");
        $stmt_itens->execute([$pedido['id']]);
        $pedido['itens'] = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $erro = 'Erro de banco de dados: ' . $e->getMessage();
    error_log("Erro PDO em pedidos.php: " . $e->getMessage());
} catch (Exception $e) {
    $erro = $e->getMessage();
    error_log("Erro em pedidos.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - CATARSE Admin</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-header">
        <h1>Gerenciar Pedidos</h1>
        <div class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="produtos.php">Produtos</a>
            <a href="pedidos.php">Pedidos</a>
            <a href="usuarios.php">Usuários</a>
            <a href="../php/logout.php?admin=1">Sair</a>
        </div>
    </div>

    <div class="admin-container">
        <?php if ($erro): ?>
            <div class="admin-section">
                <div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
                    <h3 style="margin-top: 0;">❌ Erro</h3>
                    <p><?php echo htmlspecialchars($erro); ?></p>
                    <p style="margin-top: 15px;">
                        <strong>Solução:</strong> Acesse <a href="../php/create_tables.php" target="_blank">create_tables.php</a> 
                        para criar as tabelas necessárias.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="admin-section">
                <h2>Lista de Pedidos</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Rastreio</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pedidos)): ?>
                            <tr><td colspan="7" style="text-align: center;">Nenhum pedido encontrado</td></tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td>#<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['usuario_nome'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                                    <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $pedido['status']; ?>">
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
                                    </td>
                                    <td><?php echo htmlspecialchars($pedido['codigo_rastreio'] ?? '-'); ?></td>
                                    <td>
                                        <button class="btn" onclick="verDetalhes(<?php echo $pedido['id']; ?>)">Ver</button>
                                        <button class="btn btn-info" onclick="editarStatus(<?php echo $pedido['id']; ?>, '<?php echo htmlspecialchars($pedido['status']); ?>', '<?php echo htmlspecialchars($pedido['codigo_rastreio'] ?? '', ENT_QUOTES); ?>')">Editar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de detalhes -->
    <div id="modal-detalhes" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2>Detalhes do Pedido</h2>
            <div id="detalhes-conteudo"></div>
        </div>
    </div>

    <!-- Modal de editar status -->
    <div id="modal-editar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2>Editar Status do Pedido</h2>
            <form id="form-editar-status">
                <input type="hidden" id="pedido-id-edit">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="pendente">Pendente</option>
                        <option value="pago">Pago</option>
                        <option value="enviado">Enviado</option>
                        <option value="entregue">Entregue</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="codigo_rastreio">Código de Rastreio</label>
                    <input type="text" id="codigo_rastreio" name="codigo_rastreio" placeholder="Ex: BR123456789BR">
                </div>
                <button type="submit" class="btn btn-success">Salvar</button>
                <button type="button" class="btn" onclick="fecharModal()">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        const pedidos = <?php echo json_encode($pedidos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        function verDetalhes(id) {
            const pedido = pedidos.find(p => p.id == id);
            if (!pedido) return;

            let html = `
                <p><strong>Pedido #${String(pedido.id).padStart(6, '0')}</strong></p>
                <p><strong>Cliente:</strong> ${pedido.usuario_nome || 'N/A'}</p>
                <p><strong>Data:</strong> ${new Date(pedido.data_pedido).toLocaleString('pt-BR')}</p>
                <p><strong>Total:</strong> R$ ${parseFloat(pedido.total).toFixed(2).replace('.', ',')}</p>
                <p><strong>Frete:</strong> R$ ${parseFloat(pedido.frete).toFixed(2).replace('.', ',')}</p>
                <p><strong>Status:</strong> ${pedido.status}</p>
                ${pedido.codigo_rastreio ? `<p><strong>Código de Rastreio:</strong> ${pedido.codigo_rastreio}</p>` : ''}
                <h3 style="margin-top: 20px;">Itens do Pedido:</h3>
                <ul>
            `;

            pedido.itens.forEach(item => {
                html += `<li>${item.nome_produto} ${item.tamanho ? '(' + item.tamanho + ')' : ''} - Qtd: ${item.quantidade} - R$ ${parseFloat(item.preco).toFixed(2).replace('.', ',')}</li>`;
            });

            html += `</ul>`;
            document.getElementById('detalhes-conteudo').innerHTML = html;
            document.getElementById('modal-detalhes').style.display = 'block';
        }

        function editarStatus(id, status, rastreio) {
            document.getElementById('pedido-id-edit').value = id;
            document.getElementById('status').value = status;
            document.getElementById('codigo_rastreio').value = rastreio || '';
            document.getElementById('modal-editar').style.display = 'block';
        }

        function fecharModal() {
            document.getElementById('modal-detalhes').style.display = 'none';
            document.getElementById('modal-editar').style.display = 'none';
        }

        document.getElementById('form-editar-status').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                id: formData.get('pedido-id-edit'),
                status: formData.get('status'),
                codigo_rastreio: formData.get('codigo_rastreio')
            };

            try {
                const response = await fetch('../php/pedidos_api.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    alert('Status atualizado com sucesso!');
                    location.reload();
                } else {
                    alert('Erro: ' + (result.error || 'Não foi possível atualizar'));
                }
            } catch (error) {
                alert('Erro ao atualizar: ' + error.message);
            }
        });

        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target == modal) {
                    fecharModal();
                }
            });
        }
    </script>
</body>
</html>

