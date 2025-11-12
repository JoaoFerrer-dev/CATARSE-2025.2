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
                <?php
                $produtos = [];
                $produtos_fallback = [
                    [
                        'id' => 1,
                        'nome' => 'Camisa Oversized Moda Bangu preta',
                        'preco_original' => 98.90,
                        'preco_promocional' => 89.00,
                        'desconto' => 10,
                        'imagem' => '../img/produto1.jpg',
                        'link' => 'produto.php?id=1'
                    ],
                    [
                        'id' => 2,
                        'nome' => 'Camisa Oversized Moda Bangu branca',
                        'preco_original' => 98.90,
                        'preco_promocional' => 89.00,
                        'desconto' => 10,
                        'imagem' => '../img/produto5.jpg',
                        'link' => 'produto.php?id=2'
                    ],
                    [
                        'id' => 3,
                        'nome' => 'Camiseta Purify',
                        'preco_original' => 98.90,
                        'preco_promocional' => 89.00,
                        'desconto' => 10,
                        'imagem' => '../img/produto3.jpg',
                        'link' => 'produto.php?id=3'
                    ],
                    [
                        'id' => 4,
                        'nome' => 'Camiseta Catarse Garments',
                        'preco_original' => 98.90,
                        'preco_promocional' => 89.00,
                        'desconto' => 10,
                        'imagem' => '../img/produto4.jpg',
                        'link' => 'produto.php?id=4'
                    ]
                ];
                
                // Tentar buscar do banco
                try {
                    require_once '../php/database.php';
                    $database = new Database();
                    $pdo = $database->getConnection();
                    
                    // Verificar se tabela existe
                    $stmt = $pdo->query("SHOW TABLES LIKE 'produtos'");
                    if ($stmt->rowCount() > 0) {
                        $stmt = $pdo->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY data_cadastro DESC");
                        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }
                } catch (Exception $e) {
                    // Se der erro, usa fallback
                    error_log("Erro ao buscar produtos: " . $e->getMessage());
                }
                
                // Se não houver produtos no banco, usa fallback
                if (empty($produtos)) {
                    $produtos = $produtos_fallback;
                }
                
                if (empty($produtos)):
                ?>
                    <p style="text-align: center; padding: 40px;">Nenhum produto disponível no momento.</p>
                <?php else: ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="produto">
                            <a href="<?php echo isset($produto['link']) ? $produto['link'] : 'produto.php?id=' . $produto['id']; ?>">
                                <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" 
                                     alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                     onerror="this.src='../img/logo2-removebg-preview.png'">
                                <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                                <div class="preco-container">
                                    <span class="preco-original">R$ <?php echo number_format($produto['preco_original'], 2, ',', '.'); ?></span>
                                    <?php if (isset($produto['preco_promocional']) && $produto['preco_promocional']): ?>
                                        <span class="preco-promocional">R$ <?php echo number_format($produto['preco_promocional'], 2, ',', '.'); ?></span>
                                        <?php if (isset($produto['desconto']) && $produto['desconto'] > 0): ?>
                                            <span class="desconto-badge">-<?php echo $produto['desconto']; ?>%</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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