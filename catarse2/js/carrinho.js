// Aguarde o DOM carregar antes de executar os scripts
document.addEventListener("DOMContentLoaded", function () {
    // Detectar se estamos dentro da pasta /paginas/ para ajustar paths
    const _inPages = window.location.pathname.includes('/paginas/');
    const _phpPath = _inPages ? '../php/carrinho.php' : 'php/carrinho.php';

    // Verifica se os elementos existem antes de adicionar eventos
    const btnAbrirCarrinho = document.getElementById('abrir-carrinho');
    const btnFecharCarrinho = document.querySelector('.fechar');
    const btnLimparCarrinho = document.getElementById('limpar-carrinho');
    const btnAdicionarCarrinho = document.querySelector('.btn-carrinho');

    if (btnAbrirCarrinho) {
        btnAbrirCarrinho.addEventListener('click', () => {
            const modal = document.getElementById('carrinho-modal');
            // Alterna abrir/fechar ao clicar no ícone do carrinho
            if (modal.classList.contains('show')) {
                modal.classList.remove('show');
            } else {
                modal.classList.add('show');
                exibirCarrinho();
            }
        });
    }

    if (btnFecharCarrinho) {
        btnFecharCarrinho.addEventListener('click', () => {
            document.getElementById('carrinho-modal').classList.remove('show');
        });
    }

    if (btnLimparCarrinho) {
        btnLimparCarrinho.addEventListener('click', () => {
            if (confirm("Tem certeza que deseja esvaziar o carrinho?")) {
                limparCarrinho();
            }
        });
    }

    // Função para buscar carrinho da API PHP
    async function buscarCarrinho() {
        try {
            const response = await fetch(_phpPath);
            const data = await response.json();
            return data.carrinho || [];
        } catch (error) {
            console.error('Erro ao buscar carrinho:', error);
            // Fallback para localStorage
            return JSON.parse(localStorage.getItem('carrinho')) || [];
        }
    }

    // Função para buscar frete da API PHP
    async function buscarFrete() {
        try {
            const response = await fetch(_phpPath);
            const data = await response.json();
            return data.frete || null;
        } catch (error) {
            console.error('Erro ao buscar frete:', error);
            // Fallback para localStorage
            return localStorage.getItem('frete') ? JSON.parse(localStorage.getItem('frete')) : null;
        }
    }

    // Atualiza contador de itens do carrinho
    async function atualizarContadorCarrinho() {
        const carrinho = await buscarCarrinho();
        const contador = document.getElementById('contador-carrinho');
        if (contador) {
            contador.textContent = carrinho.length || '0';
        }
    }

    // Exibir itens no carrinho (com frete)
    async function exibirCarrinho() {
        const carrinhoContainer = document.getElementById('carrinho-itens');
        if (!carrinhoContainer) return;
        
        // Adiciona scroll ao carrinho
        carrinhoContainer.style.maxHeight = '300px';
        carrinhoContainer.style.overflowY = 'auto';
        carrinhoContainer.innerHTML = '<p>Carregando...</p>';

        const carrinho = await buscarCarrinho();
        const frete = await buscarFrete();
        let totalProdutos = 0;

        if (carrinho.length === 0) {
            carrinhoContainer.innerHTML = '<p style="text-align: center; color: var(--destaque);">Seu carrinho está vazio 😔</p>';
            atualizarContadorCarrinho();
            return;
        }

        carrinhoContainer.innerHTML = '';
        carrinho.forEach((item, index) => {
            const divProduto = document.createElement('div');
            divProduto.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    ${item.imagem ? `<img src="${item.imagem}" width="50" height="50" style="border-radius: 4px;">` : ''}
                    <p style="flex: 1;">${item.nome} ${item.tamanho ? '- ' + item.tamanho : ''} - Qtd: <strong>${item.quantidade}</strong> - <strong>${item.preco}</strong></p>
                    <button class="remover" data-index="${index}">❌</button>
                </div>
            `;
            carrinhoContainer.appendChild(divProduto);
            // Soma total dos produtos
            let preco = parseFloat((item.preco || '0').replace('R$', '').replace(',', '.'));
            if (isNaN(preco)) preco = 0;
            totalProdutos += preco * (parseInt(item.quantidade) || 1);
        });

        // Exibe frete se houver
        if (frete) {
            const divFrete = document.createElement('div');
            divFrete.innerHTML = `<div style="margin-top:10px;color:var(--destaque);font-weight:bold;">Frete (${frete.tipo}): R$ ${parseFloat(frete.valor).toFixed(2).replace('.', ',')}</div>`;
            carrinhoContainer.appendChild(divFrete);
        }
        // Exibe total geral
        const divTotal = document.createElement('div');
        let totalGeral = totalProdutos + (frete ? parseFloat(frete.valor) : 0);
        divTotal.innerHTML = `<div style="margin-top:10px;font-size:1.1em;font-weight:bold;">Total: R$ ${totalGeral.toFixed(2).replace('.', ',')}</div>`;
        carrinhoContainer.appendChild(divTotal);

        // Remover item
        document.querySelectorAll('.remover').forEach(btn => {
            btn.addEventListener('click', async function () {
                const index = parseInt(this.getAttribute('data-index'));
                await removerItemCarrinho(index);
                exibirCarrinho();
                atualizarContadorCarrinho();
            });
        });
        
        atualizarContadorCarrinho();
    }

    // Função para adicionar item ao carrinho via API
    async function adicionarItemCarrinho(produto) {
        try {
            const response = await fetch(_phpPath, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(produto)
            });
            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('Erro ao adicionar item:', error);
            // Fallback para localStorage
            let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
            carrinho.push(produto);
            localStorage.setItem('carrinho', JSON.stringify(carrinho));
            return true;
        }
    }

    // Função para remover item do carrinho via API
    async function removerItemCarrinho(index) {
        try {
            const response = await fetch(_phpPath, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ index: index })
            });
            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('Erro ao remover item:', error);
            // Fallback para localStorage
            let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
            carrinho.splice(index, 1);
            localStorage.setItem('carrinho', JSON.stringify(carrinho));
            return true;
        }
    }

    // Função para limpar carrinho via API
    async function limparCarrinho() {
        try {
            const response = await fetch(_phpPath, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ limpar: true })
            });
            const data = await response.json();
            if (data.success) {
                exibirCarrinho();
                atualizarContadorCarrinho();
            }
        } catch (error) {
            console.error('Erro ao limpar carrinho:', error);
            // Fallback para localStorage
            localStorage.removeItem('carrinho');
            localStorage.removeItem('frete');
            exibirCarrinho();
            atualizarContadorCarrinho();
        }
    }

    // Adicionar produto ao carrinho na página de detalhes
    if (btnAdicionarCarrinho) {
        btnAdicionarCarrinho.addEventListener('click', async function () {
            const nomeProduto = document.querySelector('.produto-info h1')?.textContent;
            const tamanhoSelecionado = document.querySelector('input[name="tamanho"]:checked')?.value;
            const quantidadeSelecionada = document.querySelector('.controle-quantidade input')?.value;
            
            // Buscar preço promocional ou original como fallback
            let precoProduto = document.querySelector('.preco-promocional')?.textContent;
            if (!precoProduto) {
                precoProduto = document.querySelector('.preco-original')?.textContent;
            }
            
            // Se ainda não encontrou, tenta usar produtoData do produto.php
            if (!precoProduto && typeof produtoData !== 'undefined') {
                precoProduto = produtoData.preco;
            }
            
            const imagemProduto = document.querySelector('.imagem-principal img')?.src;

            if (!nomeProduto || !tamanhoSelecionado || !quantidadeSelecionada || !precoProduto) {
                alert('Erro ao adicionar produto. Verifique se todos os campos estão preenchidos.\nNome: ' + (nomeProduto || 'não encontrado') + '\nTamanho: ' + (tamanhoSelecionado || 'não selecionado') + '\nQuantidade: ' + (quantidadeSelecionada || 'não encontrada') + '\nPreço: ' + (precoProduto || 'não encontrado'));
                return;
            }

            const produto = { 
                nome: nomeProduto, 
                tamanho: tamanhoSelecionado, 
                quantidade: parseInt(quantidadeSelecionada) || 1, 
                preco: precoProduto, 
                imagem: imagemProduto || (typeof produtoData !== 'undefined' ? produtoData.imagem : '')
            };

            const sucesso = await adicionarItemCarrinho(produto);
            
            if (sucesso) {
                // Garante que o modal do carrinho continue acessível após adicionar
                setTimeout(() => {
                    const modal = document.getElementById('carrinho-modal');
                    if (modal) {
                        modal.classList.add('show');
                        exibirCarrinho();
                    }
                }, 100);

                alert('✅ Produto adicionado ao carrinho!');
                atualizarContadorCarrinho();
            } else {
                alert('Erro ao adicionar produto ao carrinho.');
            }
        });
    }

    // Atualiza ao carregar a página
    atualizarContadorCarrinho();
});

document.addEventListener("DOMContentLoaded", function () {
    const btnFinalizarCompra = document.getElementById('finalizar-compra');

    if (btnFinalizarCompra) {
        btnFinalizarCompra.addEventListener('click', async function () {
            // Comportamento desejado: se estiver logado -> ir para pagamento; se não -> ir para login
            try {
                // Determinar base do projeto (ex: '/catarse/' ou '/') de forma robusta
                const path = window.location.pathname;
                const m = path.match(/^\/([^\/]+)(?:\/|$)/);
                let projectBase = '/';
                if (m && m[1] && !m[1].includes('.')) {
                    projectBase = '/' + m[1] + '/';
                }
                const origin = window.location.origin;

                const authPath = origin + projectBase + 'php/check_auth.php';
                console.debug('[carrinho] checando auth em', authPath);
                const authResp = await fetch(authPath, { cache: 'no-store' });
                if (!authResp.ok) {
                    console.error('Erro ao verificar autenticação, status:', authResp.status);
                    // fallback: enviar para login
                    window.location.href = origin + projectBase + 'paginas/login.html';
                    return;
                }

                const authData = await authResp.json();
                console.debug('[carrinho] authData:', authData);
                if (authData.authenticated) {
                    // Usuário autenticado: ir para pagamento
                    const pagamentoUrl = origin + projectBase + 'paginas/pagamento.html';
                    console.debug('[carrinho] redirect to pagamentoUrl:', pagamentoUrl);
                    window.location.href = pagamentoUrl;
                } else {
                    // Não autenticado: ir para a página de login (absolute)
                    const loginUrl = origin + projectBase + 'paginas/login.html';
                    console.debug('[carrinho] redirect to loginUrl:', loginUrl);
                    window.location.href = loginUrl;
                }
            } catch (err) {
                console.error('Erro ao processar Finalizar Compra:', err);
                // fallback para login
                const origin = window.location.origin;
                const path = window.location.pathname;
                const m = path.match(/^\/([^\/]+)(?:\/|$)/);
                let projectBase = '/';
                if (m && m[1] && !m[1].includes('.')) {
                    projectBase = '/' + m[1] + '/';
                }
                window.location.href = origin + projectBase + 'paginas/login.html';
            }
        });
    }
});
function showToast(message, type = '') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show' + (type ? ' ' + type : '');
    setTimeout(() => {
        toast.className = 'toast';
    }, 2500);
}