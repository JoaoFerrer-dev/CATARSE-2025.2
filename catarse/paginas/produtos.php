<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATARSE — Purify Yourself</title>
    <link rel="stylesheet" href="../css/produtos.css">
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

    <header class="hero-produtos">
        <h1>NOSSOS PRODUTOS</h1>
        <p class="slogan">"purify your wardrobe, elevate your style."</p>
    </header>

    <section class="catalogo">
        <div class="container">
            <h2 class="titulo-secao">Coleção CATARSE</h2>

            <div class="grade-produtos">

                <div class="produto">
                    <a href="produto1.html">
                        <img src="../img/produto1.jpg" alt="Camisa Oversized Moda Bangu preta">
                        <h3>Camisa Oversized Moda Bangu preta</h3>
                        <div class="preco-container">
                            <span class="preco-original">R$ 98,90</span>
                            <span class="preco-promocional">R$ 89,00</span>
                            <span class="desconto-badge">-10%</span>
                        </div>
                    </a>
                </div>

                <div class="produto">
                    <a href="produto2.html">
                        <img src="../img/produto5.jpg" alt="Camisa Oversized Moda Bangu branca">
                        <h3>Camisa Oversized Moda Bangu branca</h3>
                        <div class="preco-container">
                            <span class="preco-original">R$ 98,90</span>
                            <span class="preco-promocional">R$ 89,00</span>
                            <span class="desconto-badge">-10%</span>
                        </div>
                    </a>
                </div>

                <div class="produto">
                    <a href="produto3.html">
                        <img src="../img/produto3.jpg" alt="Camiseta Purify">
                        <h3>Camiseta Purify</h3>
                        <div class="preco-container">
                            <span class="preco-original">R$ 98,90</span>
                            <span class="preco-promocional">R$ 89,00</span>
                            <span class="desconto-badge">-10%</span>
                        </div>
                    </a>
                </div>

                <div class="produto">
                    <a href="produto4.html">
                        <img src="../img/produto4.jpg" alt="Camiseta Catarse Garments">
                        <h3>Camiseta Catarse Garments</h3>
                        <div class="preco-container">
                            <span class="preco-original">R$ 98,90</span>
                            <span class="preco-promocional">R$ 89,00</span>
                            <span class="desconto-badge">-10%</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

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

    <script src="../js/produtos.js"></script>
    <script src="../js/carrinho.js"></script>
</body>

</html>