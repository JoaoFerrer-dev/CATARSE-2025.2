// Aguarde o DOM carregar antes de executar os scripts
document.addEventListener("DOMContentLoaded", function () {

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
                localStorage.removeItem('carrinho');
                exibirCarrinho();
                atualizarContadorCarrinho();
            }
        });
    }

    // Atualiza contador de itens do carrinho
    function atualizarContadorCarrinho() {
        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
        document.getElementById('contador-carrinho').textContent = carrinho.length || '0';
    }

    // Exibir itens no carrinho (com frete)
    function exibirCarrinho() {
        const carrinhoContainer = document.getElementById('carrinho-itens');
        // Adiciona scroll ao carrinho
        carrinhoContainer.style.maxHeight = '300px';
        carrinhoContainer.style.overflowY = 'auto';
        carrinhoContainer.innerHTML = '';

        let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
        let frete = localStorage.getItem('frete') ? JSON.parse(localStorage.getItem('frete')) : null;
        let totalProdutos = 0;

        if (carrinho.length === 0) {
            carrinhoContainer.innerHTML = '<p style="text-align: center; color: var(--destaque);">Seu carrinho está vazio 😔</p>';
            return;
        }

        carrinho.forEach((item, index) => {
            const divProduto = document.createElement('div');
            divProduto.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${item.imagem}" width="50" height="50" style="border-radius: 4px;">
                    <p>${item.nome} - ${item.tamanho} - Quantidade: <strong>${item.quantidade}</strong> - <strong>${item.preco}</strong></p>
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

        // Remover item com animação
        document.querySelectorAll('.remover').forEach(btn => {
            btn.addEventListener('click', function () {
                let index = this.getAttribute('data-index');
                carrinho.splice(index, 1);
                localStorage.setItem('carrinho', JSON.stringify(carrinho));
                exibirCarrinho();
                atualizarContadorCarrinho();
            });
        });
    }

    // Adicionar produto ao carrinho na página de detalhes
    if (btnAdicionarCarrinho) {
        btnAdicionarCarrinho.addEventListener('click', function () {
            const nomeProduto = document.querySelector('.produto-info h1').textContent;
            const tamanhoSelecionado = document.querySelector('input[name="tamanho"]:checked').value;
            const quantidadeSelecionada = document.querySelector('.controle-quantidade input').value;
            const precoProduto = document.querySelector('.preco-promocional').textContent;
            const imagemProduto = document.querySelector('.imagem-principal img').src;

            const produto = { nome: nomeProduto, tamanho: tamanhoSelecionado, quantidade: quantidadeSelecionada, preco: precoProduto, imagem: imagemProduto };

            let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
            carrinho.push(produto);
            localStorage.setItem('carrinho', JSON.stringify(carrinho));

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
        });
    }

    // Atualiza ao carregar a página
    atualizarContadorCarrinho();
});

document.addEventListener("DOMContentLoaded", function () {
    const btnFinalizarCompra = document.getElementById('finalizar-compra');

    if (btnFinalizarCompra) {
        btnFinalizarCompra.addEventListener('click', function () {
            // Checa se o usuário está logado pelo localStorage
            if (localStorage.getItem('isLoggedIn') === 'true') {
                window.location.href = "pagamento.html";
            } else {
                window.location.href = "login.html";
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