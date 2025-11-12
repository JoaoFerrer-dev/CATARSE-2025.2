<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Verificar se carrinho não está vazio
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header('Location: produtos.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATARSE — Pagamento</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="icon" href="../img/logo2-removebg-preview.png" type="image/png">
</head>
<body>
<div class="accessibility-container" id="accessibility-container">
    <button class="accessibility-btn" id="accessibility-main-btn" aria-label="Controles de acessibilidade">
        <span>A</span>
    </button>
    <div class="accessibility-options" id="accessibility-options">
        <button id="increase-font" aria-label="Aumentar tamanho da fonte">A+</button>
        <button id="decrease-font" aria-label="Diminuir tamanho da fonte">A-</button>
        <button id="reset-font" aria-label="Resetar tamanho da fonte">A⏱️</button>
        <button id="toggle-theme" aria-label="Alternar entre modo claro e escuro">🌓</button>
    </div>
</div>
<?php include '../php/nav_include.php'; ?>
<main class="login-container pagamento-container">
        <div class="pagamento-box">
            <h2 class="pagamento-title">Pagamento</h2>
            <div class="pagamento-summary">
                <h3>Resumo do Pedido</h3>
                <ul class="pagamento-lista-produtos" id="pagamento-lista-produtos">
                    <!-- Produtos do carrinho serão inseridos via JS -->
                </ul>
                <div class="pagamento-total">
                    <span>Total:</span>
                    <span id="pagamento-total">R$ 0,00</span>
                </div>
            </div>
            <form id="form-pagamento" class="pagamento-form">
                <h3>Dados do Cartão</h3>
                <div class="form-group">
                    <label for="nome-cartao">Nome impresso no cartão</label>
                    <input type="text" id="nome-cartao" name="nome-cartao" placeholder="Nome no cartão" required>
                </div>
                <div class="form-group">
                    <label for="numero-cartao">Número do cartão</label>
                    <input type="text" id="numero-cartao" name="numero-cartao" placeholder="•••• •••• •••• ••••" maxlength="19" pattern="[0-9 ]{16,19}" required>
                </div>
                <div class="form-group pagamento-cartao-row">
                    <div>
                        <label for="validade-cartao">Validade</label>
                        <input type="text" id="validade-cartao" name="validade-cartao" placeholder="MM/AA" maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" required>
                    </div>
                    <div>
                        <label for="cvv-cartao">CVV</label>
                        <input type="text" id="cvv-cartao" name="cvv-cartao" placeholder="CVV" maxlength="4" pattern="\d{3,4}" required>
                    </div>
                </div>
                <button type="submit" class="cta pagamento-btn">Finalizar Compra</button>
            </form>
            <div id="mensagem-pagamento" style="margin-top: 1rem;"></div>
            <button id="btn-rastreio" class="cta pagamento-btn" type="button" style="margin-top:1rem;background:#43a047;">Rastrear Pedido</button>
            <div class="pagamento-seguro">
                <img src="../img/logo2-removebg-preview.png" alt="Catarse" style="height: 32px; vertical-align: middle; margin-right: 8px;">
                <span>Ambiente 100% seguro e criptografado</span>
            </div>
        </div>
    </main>
<footer>
    <p>CATARSE © 2024 — "purify yourself"</p>
    <p><a href="https://www.instagram.com/catarsegarments?igsh=MWNvOW5hYXFmNm1hMg==" target="_blank">@catarsegarments</a></p>
    
    <!-- Botão de Ajuda -->
    <div class="footer-help">
        <a href="suporte.html" class="btn-ajuda">Precisa de Ajuda?</a>
    </div>
</footer>
<div id="carrinho-modal" class="modal">
    <span class="fechar" id="fechar-carrinho">&times;</span>
    <h2>Seu Carrinho</h2>
    <div id="carrinho-itens"></div>
    <button id="limpar-carrinho">Limpar Carrinho</button>
    <button id="finalizar-compra">Finalizar Compra</button>
</div>
<div id="toast" class="toast"></div>
<script src="../js/pagamento.js"></script>
<script src="../js/login.js"></script>
<script src="../js/carrinho.js"></script>
<script>
document.getElementById('form-pagamento').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processando...';
    
    try {
        const response = await fetch('../php/processar_pagamento.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('mensagem-pagamento').innerHTML = 
                '<span style="color:green;font-weight:bold;">✅ Pagamento realizado com sucesso! Obrigado pela sua compra.</span>';
            setTimeout(() => {
                window.location.href = '../index.php';
            }, 2500);
        } else {
            document.getElementById('mensagem-pagamento').innerHTML = 
                '<span style="color:red;font-weight:bold;">❌ Erro: ' + (data.error || 'Não foi possível processar o pagamento') + '</span>';
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    } catch (error) {
        document.getElementById('mensagem-pagamento').innerHTML = 
            '<span style="color:red;font-weight:bold;">❌ Erro ao processar pagamento. Tente novamente.</span>';
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});

document.getElementById('btn-rastreio').addEventListener('click', function() {
    window.location.href = '../php/rastreio.php';
});
</script>
</body>
</html>

