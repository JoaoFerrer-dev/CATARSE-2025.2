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
                    <input type="text" id="imagem" name="imagem" required placeholder="../img/produto1.jpg" oninput="atualizarPreviewImagem()">
                    <small style="color: var(--destaque); font-size: 12px; display: block; margin-top: 5px;">
                        Exemplos: ../img/produto1.jpg, ../img/camisa.jpg, ../img/produto2.jpg
                    </small>
                    
                    <!-- Preview da imagem -->
                    <div id="preview-imagem-container" style="margin-top: 15px; display: none;">
                        <label style="font-size: 12px; color: var(--destaque); margin-bottom: 5px; display: block;">Preview:</label>
                        <img id="preview-imagem" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border: 1px solid var(--borda); border-radius: 4px; padding: 5px; background: var(--fundo-input);">
                    </div>
                    
                    <!-- Lista de imagens disponíveis -->
                    <div style="margin-top: 15px;">
                        <label style="font-size: 12px; color: var(--destaque); margin-bottom: 5px; display: block;">Imagens disponíveis:</label>
                        <div id="lista-imagens" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; max-height: 200px; overflow-y: auto; padding: 10px; background: var(--fundo-input); border-radius: 4px; border: 1px solid var(--borda);">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>
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
        // Variáveis globais
        let produtos = [];

        // Lista de imagens disponíveis
        const imagensDisponiveis = [
            '../img/produto1.jpg',
            '../img/produto2.jpg',
            '../img/produto3.jpg',
            '../img/produto4.jpg',
            '../img/produto5.jpg',
            '../img/camisa.jpg',
            '../img/camisa2.jpg',
            '../img/logo2-removebg-preview.png',
            '../img/logo.jpg',
            '../img/logo2.jpg',
            '../img/back2.jpeg',
            '../img/background1.JPEG',
            '../img/background2.jpeg',
            '../img/baixados (9).jpeg'
        ];

        // Função para mostrar mensagens (definida primeiro para evitar erros)
        function mostrarMensagem(mensagem, tipo) {
            try {
                const msgDiv = document.getElementById('mensagem');
                if (msgDiv) {
                    msgDiv.textContent = mensagem;
                    msgDiv.className = `mensagem-${tipo}`;
                    msgDiv.style.display = 'block';
                    setTimeout(() => {
                        if (msgDiv) {
                            msgDiv.style.display = 'none';
                        }
                    }, 5000);
                }
            } catch (error) {
                console.error('Erro ao mostrar mensagem:', error);
            }
        }

        // Função para atualizar preview da imagem
        function atualizarPreviewImagem() {
            const inputImagem = document.getElementById('imagem');
            const previewContainer = document.getElementById('preview-imagem-container');
            const previewImagem = document.getElementById('preview-imagem');
            
            if (inputImagem && inputImagem.value) {
                previewImagem.src = inputImagem.value;
                previewContainer.style.display = 'block';
                
                // Verificar se a imagem carregou
                previewImagem.onerror = function() {
                    previewImagem.alt = 'Imagem não encontrada';
                    previewImagem.style.border = '2px solid red';
                };
                
                previewImagem.onload = function() {
                    previewImagem.style.border = '1px solid var(--borda)';
                };
            } else {
                previewContainer.style.display = 'none';
            }
        }

        // Função para selecionar imagem da lista
        function selecionarImagem(url) {
            const inputImagem = document.getElementById('imagem');
            if (inputImagem) {
                inputImagem.value = url;
                atualizarPreviewImagem();
                
                // Destacar a imagem selecionada
                document.querySelectorAll('.imagem-miniatura').forEach(img => {
                    img.style.border = '1px solid var(--borda)';
                    if (img.dataset.url === url) {
                        img.style.border = '2px solid var(--preto)';
                    }
                });
            }
        }

        // Função para carregar lista de imagens
        function carregarListaImagens() {
            const listaImagens = document.getElementById('lista-imagens');
            if (!listaImagens) return;

            listaImagens.innerHTML = imagensDisponiveis.map(url => {
                const nomeArquivo = url.split('/').pop();
                return `
                    <div style="cursor: pointer; text-align: center;" onclick="selecionarImagem('${url}')" title="${nomeArquivo}">
                        <img src="${url}" 
                             alt="${nomeArquivo}" 
                             class="imagem-miniatura"
                             data-url="${url}"
                             style="width: 100%; height: 60px; object-fit: cover; border: 1px solid var(--borda); border-radius: 4px; cursor: pointer; transition: transform 0.2s;"
                             onerror="this.src='../img/logo2-removebg-preview.png'"
                             onmouseover="this.style.transform='scale(1.1)'"
                             onmouseout="this.style.transform='scale(1)'">
                        <small style="font-size: 10px; color: var(--destaque); display: block; margin-top: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${nomeArquivo}</small>
                    </div>
                `;
            }).join('');
        }

        // Função para abrir modal de novo produto
        function abrirModalNovo() {
            console.log('abrirModalNovo chamada');
            try {
                const modal = document.getElementById('modal-produto');
                console.log('Modal encontrado:', modal);
                
                if (!modal) {
                    console.error('Modal não encontrado no DOM');
                    alert('Erro: Modal não encontrado. Verifique se a página carregou completamente.');
                    return;
                }

                const form = document.getElementById('form-produto');
                const titulo = document.getElementById('modal-titulo');
                const produtoId = document.getElementById('produto-id');
                
                if (!form || !titulo || !produtoId) {
                    console.error('Elementos do modal não encontrados:', { form, titulo, produtoId });
                    alert('Erro ao abrir o modal. Alguns elementos não foram encontrados.');
                    return;
                }
                
                titulo.textContent = 'Novo Produto';
                form.reset();
                produtoId.value = '';
                
                // Limpar preview da imagem
                const previewContainer = document.getElementById('preview-imagem-container');
                if (previewContainer) {
                    previewContainer.style.display = 'none';
                }
                
                // Exibir o modal
                modal.style.display = 'block';
                
                console.log('Modal aberto com sucesso');
            } catch (error) {
                console.error('Erro ao abrir modal:', error);
                alert('Erro ao abrir o modal: ' + error.message);
            }
        }

        // Função para fechar modal
        function fecharModal() {
            try {
                const modal = document.getElementById('modal-produto');
                if (modal) {
                    modal.style.display = 'none';
                }
            } catch (error) {
                console.error('Erro ao fechar modal:', error);
            }
        }

        // Função para exibir produtos
        function exibirProdutos() {
            try {
                const tbody = document.getElementById('produtos-tbody');
                if (!tbody) return;

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
            } catch (error) {
                console.error('Erro ao exibir produtos:', error);
            }
        }

        // Função para carregar produtos
        async function carregarProdutos() {
            try {
                const response = await fetch('../php/produtos_api.php');
                
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                
                const text = await response.text();
                let data;
                
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('Resposta da API não é JSON válido:', text);
                    throw new Error('Resposta inválida do servidor. Verifique se há erros PHP.');
                }
                
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

        // Função para editar produto
        function editarProduto(id) {
            try {
                const produto = produtos.find(p => p.id == id);
                if (!produto) {
                    alert('Produto não encontrado');
                    return;
                }

                document.getElementById('modal-titulo').textContent = 'Editar Produto';
                document.getElementById('produto-id').value = produto.id;
                document.getElementById('nome').value = produto.nome || '';
                document.getElementById('descricao').value = produto.descricao || '';
                document.getElementById('preco_original').value = produto.preco_original || '';
                document.getElementById('preco_promocional').value = produto.preco_promocional || '';
                document.getElementById('imagem').value = produto.imagem || '';
                document.getElementById('tamanhos_disponiveis').value = produto.tamanhos_disponiveis || 'P,M,G,GG';
                document.getElementById('estoque').value = produto.estoque || 0;
                document.getElementById('ativo').value = produto.ativo || 1;
                
                // Atualizar preview da imagem
                atualizarPreviewImagem();
                
                // Destacar a imagem selecionada na lista
                if (produto.imagem) {
                    document.querySelectorAll('.imagem-miniatura').forEach(img => {
                        img.style.border = '1px solid var(--borda)';
                        if (img.dataset.url === produto.imagem) {
                            img.style.border = '2px solid var(--preto)';
                        }
                    });
                }
                
                document.getElementById('modal-produto').style.display = 'block';
            } catch (error) {
                console.error('Erro ao editar produto:', error);
                alert('Erro ao editar produto: ' + error.message);
            }
        }

        // Função para deletar produto
        async function deletarProduto(id) {
            if (!confirm('Tem certeza que deseja deletar este produto?')) return;

            try {
                const response = await fetch('../php/produtos_api.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });

                const text = await response.text();
                let data;
                
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('Resposta da API não é JSON válido:', text);
                    throw new Error('Resposta inválida do servidor.');
                }

                if (data.success) {
                    mostrarMensagem('Produto deletado com sucesso!', 'success');
                    carregarProdutos();
                } else {
                    mostrarMensagem(data.error || 'Erro ao deletar produto', 'error');
                }
            } catch (error) {
                console.error('Erro ao deletar produto:', error);
                mostrarMensagem('Erro ao deletar produto: ' + error.message, 'error');
            }
        }

        // Inicialização quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado');
            
            // Verificar se o modal existe
            const modal = document.getElementById('modal-produto');
            if (!modal) {
                console.error('Modal não encontrado no DOM após carregamento');
            } else {
                console.log('Modal encontrado:', modal);
            }

            // Adicionar event listener ao formulário
            const form = document.getElementById('form-produto');
            if (form) {
                form.addEventListener('submit', async function(e) {
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

                        const text = await response.text();
                        let result;
                        
                        try {
                            result = JSON.parse(text);
                        } catch (parseError) {
                            console.error('Resposta da API não é JSON válido:', text);
                            throw new Error('Resposta inválida do servidor. Verifique se há erros PHP.');
                        }
                        
                        if (result.success) {
                            mostrarMensagem(isEdit ? 'Produto atualizado com sucesso!' : 'Produto criado com sucesso!', 'success');
                            fecharModal();
                            carregarProdutos();
                        } else {
                            mostrarMensagem(result.error || 'Erro ao salvar produto', 'error');
                        }
                    } catch (error) {
                        console.error('Erro ao salvar produto:', error);
                        mostrarMensagem('Erro ao salvar produto: ' + error.message, 'error');
                    }
                });
            }

            // Fechar modal ao clicar fora
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('modal-produto');
                if (modal && event.target === modal) {
                    fecharModal();
                }
            });

            // Carregar lista de imagens
            carregarListaImagens();
            
            // Adicionar listener para atualizar preview quando modal abrir
            const inputImagem = document.getElementById('imagem');
            if (inputImagem) {
                inputImagem.addEventListener('input', atualizarPreviewImagem);
            }
            
            // Carregar produtos
            carregarProdutos();
        });

        // Garantir que as funções estejam disponíveis globalmente mesmo antes do DOMContentLoaded
        window.abrirModalNovo = abrirModalNovo;
        window.fecharModal = fecharModal;
        window.editarProduto = editarProduto;
        window.deletarProduto = deletarProduto;
        window.atualizarPreviewImagem = atualizarPreviewImagem;
        window.selecionarImagem = selecionarImagem;
        window.carregarListaImagens = carregarListaImagens;
    </script>
</body>
</html>

