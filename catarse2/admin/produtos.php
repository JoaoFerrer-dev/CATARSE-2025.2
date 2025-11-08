<?php
require_once '../php/admin_auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - CATARSE Admin</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="admin-header">
        <h1>Gerenciar Produtos</h1>
        <div class="admin-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="produtos.php">Produtos</a>
            <a href="pedidos.php">Pedidos</a>
            <a href="usuarios.php">Usuários</a>
            <a href="../php/logout.php?admin=1">Sair</a>
        </div>
    </div>

    <div class="admin-container">
        <div id="mensagem"></div>
        
        <div class="admin-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Lista de Produtos</h2>
                <button class="btn btn-success" onclick="abrirModalNovo()">Novo Produto</button>
            </div>
            
            <table id="tabela-produtos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço Original</th>
                        <th>Preço Promo</th>
                        <th>Desconto</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="produtos-tbody">
                    <tr><td colspan="9" style="text-align: center;">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para adicionar/editar produto -->
    <div id="modal-produto" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <h2 id="modal-titulo">Novo Produto</h2>
            <form id="form-produto">
                <input type="hidden" id="produto-id">
                
                <div class="form-group">
                    <label for="nome">Nome do Produto *</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="preco_original">Preço Original (R$) *</label>
                    <input type="number" id="preco_original" name="preco_original" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="preco_promocional">Preço Promocional (R$)</label>
                    <input type="number" id="preco_promocional" name="preco_promocional" step="0.01" min="0">
                </div>
                
                <div class="form-group">
                    <label for="imagem">URL da Imagem *</label>
                    <input type="text" id="imagem" name="imagem" required placeholder="../img/produto1.jpg">
                </div>
                
                <div class="form-group">
                    <label for="tamanhos_disponiveis">Tamanhos Disponíveis (separados por vírgula)</label>
                    <input type="text" id="tamanhos_disponiveis" name="tamanhos_disponiveis" value="P,M,G,GG">
                </div>
                
                <div class="form-group">
                    <label for="estoque">Estoque</label>
                    <input type="number" id="estoque" name="estoque" min="0" value="0">
                </div>
                
                <div class="form-group">
                    <label for="ativo">Status</label>
                    <select id="ativo" name="ativo">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">Salvar</button>
                    <button type="button" class="btn" onclick="fecharModal()" style="flex: 1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let produtos = [];

        // Carregar produtos
        async function carregarProdutos() {
            try {
                const response = await fetch('../php/produtos_api.php');
                
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Verificar se é um array ou objeto de erro
                if (Array.isArray(data)) {
                    produtos = data;
                } else if (data.error) {
                    throw new Error(data.error);
                } else if (data.success === false) {
                    throw new Error(data.error || 'Erro desconhecido');
                } else {
                    produtos = [];
                }
                
                exibirProdutos();
            } catch (error) {
                console.error('Erro ao carregar produtos:', error);
                mostrarMensagem('Erro ao carregar produtos: ' + error.message, 'error');
                const tbody = document.getElementById('produtos-tbody');
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; color: var(--destaque);">Erro ao carregar produtos. Verifique o console para mais detalhes.</td></tr>';
                }
            }
        }

        function exibirProdutos() {
            const tbody = document.getElementById('produtos-tbody');
            if (produtos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align: center;">Nenhum produto cadastrado</td></tr>';
                return;
            }

            tbody.innerHTML = produtos.map(produto => `
                <tr>
                    <td>${produto.id}</td>
                    <td><img src="${produto.imagem}" alt="${produto.nome}" class="produto-img" onerror="this.src='../img/logo2-removebg-preview.png'"></td>
                    <td>${produto.nome}</td>
                    <td>R$ ${parseFloat(produto.preco_original).toFixed(2).replace('.', ',')}</td>
                    <td>${produto.preco_promocional ? 'R$ ' + parseFloat(produto.preco_promocional).toFixed(2).replace('.', ',') : '-'}</td>
                    <td>${produto.desconto > 0 ? produto.desconto + '%' : '-'}</td>
                    <td>${produto.estoque}</td>
                    <td><span class="badge ${produto.ativo == 1 ? 'badge-success' : 'badge-danger'}">${produto.ativo == 1 ? 'Ativo' : 'Inativo'}</span></td>
                    <td>
                        <button class="btn btn-small" onclick="editarProduto(${produto.id})">Editar</button>
                        <button class="btn btn-danger btn-small" onclick="deletarProduto(${produto.id})">Deletar</button>
                    </td>
                </tr>
            `).join('');
        }

        function abrirModalNovo() {
            document.getElementById('modal-titulo').textContent = 'Novo Produto';
            document.getElementById('form-produto').reset();
            document.getElementById('produto-id').value = '';
            document.getElementById('modal-produto').style.display = 'block';
        }

        function editarProduto(id) {
            const produto = produtos.find(p => p.id == id);
            if (!produto) return;

            document.getElementById('modal-titulo').textContent = 'Editar Produto';
            document.getElementById('produto-id').value = produto.id;
            document.getElementById('nome').value = produto.nome;
            document.getElementById('descricao').value = produto.descricao || '';
            document.getElementById('preco_original').value = produto.preco_original;
            document.getElementById('preco_promocional').value = produto.preco_promocional || '';
            document.getElementById('imagem').value = produto.imagem;
            document.getElementById('tamanhos_disponiveis').value = produto.tamanhos_disponiveis;
            document.getElementById('estoque').value = produto.estoque;
            document.getElementById('ativo').value = produto.ativo;
            document.getElementById('modal-produto').style.display = 'block';
        }

        function fecharModal() {
            document.getElementById('modal-produto').style.display = 'none';
        }

        async function deletarProduto(id) {
            if (!confirm('Tem certeza que deseja deletar este produto?')) return;

            try {
                const response = await fetch('../php/produtos_api.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });

                const data = await response.json();
                if (data.success) {
                    mostrarMensagem('Produto deletado com sucesso!', 'success');
                    carregarProdutos();
                } else {
                    mostrarMensagem(data.error || 'Erro ao deletar produto', 'error');
                }
            } catch (error) {
                mostrarMensagem('Erro ao deletar produto: ' + error.message, 'error');
            }
        }

        document.getElementById('form-produto').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const id = document.getElementById('produto-id').value;
            const isEdit = id !== '';

            const data = {
                nome: formData.get('nome'),
                descricao: formData.get('descricao'),
                preco_original: formData.get('preco_original'),
                preco_promocional: formData.get('preco_promocional') || null,
                imagem: formData.get('imagem'),
                tamanhos_disponiveis: formData.get('tamanhos_disponiveis'),
                estoque: formData.get('estoque'),
                ativo: formData.get('ativo')
            };

            if (isEdit) {
                data.id = id;
            }

            try {
                const response = await fetch('../php/produtos_api.php', {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    mostrarMensagem(isEdit ? 'Produto atualizado com sucesso!' : 'Produto criado com sucesso!', 'success');
                    fecharModal();
                    carregarProdutos();
                } else {
                    mostrarMensagem(result.error || 'Erro ao salvar produto', 'error');
                }
            } catch (error) {
                mostrarMensagem('Erro ao salvar produto: ' + error.message, 'error');
            }
        });

        function mostrarMensagem(mensagem, tipo) {
            const msgDiv = document.getElementById('mensagem');
            msgDiv.textContent = mensagem;
            msgDiv.className = `mensagem-${tipo}`;
            msgDiv.style.display = 'block';
            setTimeout(() => {
                msgDiv.style.display = 'none';
            }, 5000);
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('modal-produto');
            if (event.target == modal) {
                fecharModal();
            }
        }

        // Carregar produtos ao iniciar
        carregarProdutos();
    </script>
</body>
</html>

