<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATARSE — Purify Yourself</title>
    <link rel="stylesheet" href="../css/sobre.css">
    <link rel="stylesheet" href="../css/index.css">
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
        <button id="reset-font" aria-label="Resetar tamanho da fonte">A⏱</button>
        <button id="toggle-theme" aria-label="Alternar entre modo claro e escuro">🌓</button>
    </div>
</div>
<?php include '../php/nav_include.php'; ?>

   <!-- Header com Vídeo -->
<header class="video-hero">
    <div class="video-container">
        <video id="catarse-video" controls playsinline>
            <source src="../img/22.09prestigiem a Arte. ☪️.mp4" type="video/mp4">
            <source src="../img/22.09prestigiem a Arte. ☪️.webm" type="video/webm">
            Seu navegador não suporta o elemento de vídeo.
        </video>
        <div class="video-overlay">
           
            <div class="video-controls">
                <button id="play-pause-btn" class="video-btn">▶</button>
            </div>
        </div>
    </div>
</header>

    <footer>
    <p>CATARSE © 2024 — "purify yourself"</p>
    <p><a href="https://www.instagram.com/catarsegarments?igsh=MWNvOW5hYXFmNm1hMg==" target="_blank">@catarsegarments</a></p>
    
    <!-- Botão de Ajuda -->
    <div class="footer-help">
        <a href="suporte.html" class="btn-ajuda">Precisa de Ajuda?</a>
    </div>
</footer>

    <!-- Modal do Carrinho Padrão -->
<div id="carrinho-modal" class="modal">
    <span class="fechar" id="fechar-carrinho">&times;</span>
    <h2>Seu Carrinho</h2>
    <div id="carrinho-itens"></div>
    <button id="limpar-carrinho">Limpar Carrinho</button>
    <button id="finalizar-compra">Finalizar Compra</button>
</div>
<div id="toast" class="toast"></div>

    <script src="../js/sobre.js"></script>
    <script src="../js/carrinho.js"></script>
</body>
</html>

