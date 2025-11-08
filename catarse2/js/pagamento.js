// Preenche o resumo do pedido na página de pagamento
window.addEventListener('DOMContentLoaded', async function() {
    const lista = document.getElementById('pagamento-lista-produtos');
    const totalSpan = document.getElementById('pagamento-total');
    
    if (!lista || !totalSpan) return;
    
    let total = 0;
    let carrinho = [];
    let frete = null;
    
    try {
        // Buscar carrinho da API PHP
        const response = await fetch('../php/carrinho.php');
        const data = await response.json();
        carrinho = data.carrinho || [];
        frete = data.frete || null;
    } catch (error) {
        console.error('Erro ao buscar carrinho:', error);
        // Fallback para localStorage
        carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
        frete = localStorage.getItem('frete') ? JSON.parse(localStorage.getItem('frete')) : null;
    }
    
    if (carrinho.length === 0) {
        lista.innerHTML = '<li style="text-align:center;color:#888;">Seu carrinho está vazio. <a href="produtos.php">Ver produtos</a></li>';
        totalSpan.textContent = 'R$ 0,00';
        return;
    }
    
    lista.innerHTML = '';
    carrinho.forEach(item => {
        let preco = parseFloat((item.preco || '0').replace('R$', '').replace(',', '.'));
        if (isNaN(preco)) preco = 0;
        total += preco * (parseInt(item.quantidade) || 1);
        const li = document.createElement('li');
        li.innerHTML = `<span>${item.nome} ${item.tamanho ? '('+item.tamanho+')' : ''} x${item.quantidade || 1}</span><span>${item.preco}</span>`;
        lista.appendChild(li);
    });
    
    // Exibe frete se houver
    if (frete) {
        const liFrete = document.createElement('li');
        liFrete.innerHTML = `<span style="color:var(--destaque);font-weight:bold;">Frete (${frete.tipo})</span><span style="color:var(--destaque);font-weight:bold;">R$ ${parseFloat(frete.valor).toFixed(2).replace('.', ',')}</span>`;
        lista.appendChild(liFrete);
        total += parseFloat(frete.valor);
    }
    
    totalSpan.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
});

// Máscara para número do cartão
const numeroCartao = document.getElementById('numero-cartao');
numeroCartao.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 16) value = value.slice(0, 16);
    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    this.value = value.trim();
});

// Máscara para validade (MM/AA)
const validadeCartao = document.getElementById('validade-cartao');
validadeCartao.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 4) value = value.slice(0, 4);
    if (value.length > 2) {
        value = value.replace(/(\d{2})(\d{1,2})/, '$1/$2');
    }
    this.value = value;
});

// Máscara para CVV (3 ou 4 dígitos)
const cvvCartao = document.getElementById('cvv-cartao');
cvvCartao.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 4) value = value.slice(0, 4);
    this.value = value;
});

// Limitador para nome no cartão (apenas letras e espaços, até 30 caracteres)
const nomeCartao = document.getElementById('nome-cartao');
nomeCartao.addEventListener('input', function(e) {
    let value = this.value.replace(/[^A-Za-zÀ-ú\s]/g, '');
    if (value.length > 30) value = value.slice(0, 30);
    this.value = value;
});

document.getElementById('btn-rastreio').addEventListener('click', function() {
    window.location.href = 'rastreio.html';
});

document.addEventListener('DOMContentLoaded', function() {
    // ====== ACESSIBILIDADE ======
    const accessibilityContainer = document.getElementById('accessibility-container');
    if (accessibilityContainer) {
        accessibilityContainer.style.top = '70vh'; // 70% da altura da tela
        accessibilityContainer.style.left = '';
        accessibilityContainer.style.right = '20px';
    }
});
