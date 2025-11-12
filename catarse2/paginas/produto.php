<?php
session_start();

// Buscar ID do produto
$produto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Dados fallback dos produtos originais
$produtos_fallback = [
    1 => [
        'nome' => 'Camisa Oversized Moda Bangu preta',
        'descricao' => 'Camiseta oversized com caimento boxy, produzida em algodão 100% penteado de alta gramatura (220g). Modelagem ampla com ombros caídos e barra reta. Ideal para looks despojados com toque premium.',
        'preco_original' => 98.90,
        'preco_promocional' => 89.00,
        'desconto' => 10,
        'imagem' => '../img/produto1.jpg',
        'tamanhos' => ['P', 'M', 'G', 'GG']
    ],
    2 => [
        'nome' => 'Camisa Oversized Moda Bangu branca',
        'descricao' => 'Camiseta oversized com caimento boxy, produzida em algodão 100% penteado de alta gramatura (220g). Modelagem ampla com ombros caídos e barra reta. Ideal para looks despojados com toque premium.',
        'preco_original' => 98.90,
        'preco_promocional' => 89.00,
        'desconto' => 10,
        'imagem' => '../img/produto5.jpg',
        'tamanhos' => ['P', 'M', 'G', 'GG']
    ],
    3 => [
        'nome' => 'Camiseta Purify',
        'descricao' => 'Camiseta oversized com caimento boxy, produzida em algodão 100% penteado de alta gramatura (220g). Modelagem ampla com ombros caídos e barra reta. Ideal para looks despojados com toque premium.',
        'preco_original' => 98.90,
        'preco_promocional' => 89.00,
        'desconto' => 10,
        'imagem' => '../img/produto3.jpg',
        'tamanhos' => ['P', 'M', 'G', 'GG']
    ],
    4 => [
        'nome' => 'Camiseta Catarse Garments',
        'descricao' => 'Camiseta oversized com caimento boxy, produzida em algodão 100% penteado de alta gramatura (220g). Modelagem ampla com ombros caídos e barra reta. Ideal para looks despojados com toque premium.',
        'preco_original' => 98.90,
        'preco_promocional' => 89.00,
        'desconto' => 10,
        'imagem' => '../img/produto4.jpg',
        'tamanhos' => ['P', 'M', 'G', 'GG']
    ]
];

$produto = null;

// Tentar buscar do banco
try {
    require_once '../php/database.php';
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Verificar se tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'produtos'");
    if ($stmt->rowCount() > 0 && $produto_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id AND ativo = 1");
        $stmt->execute([':id' => $produto_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($produto) {
            // Converter tamanhos_disponiveis para array
            $produto['tamanhos'] = explode(',', $produto['tamanhos_disponiveis'] ?? 'P,M,G,GG');
        }
    }
} catch (Exception $e) {
    error_log("Erro ao buscar produto: " . $e->getMessage());
}

// Se não encontrou no banco, usa fallback
if (!$produto && isset($produtos_fallback[$produto_id])) {
    $produto = $produtos_fallback[$produto_id];
    $produto['id'] = $produto_id;
}

// Se ainda não encontrou, redireciona
if (!$produto) {
    header('Location: produtos.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['nome']); ?> - CATARSE</title>
    <link rel="stylesheet" href="../css/produto1.css">
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

    <main class="produto-detalhe-container">
        <div class="produto-galeria">
            <div class="imagem-principal">
                <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" 
                     alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                     onerror="this.src='../img/logo2-removebg-preview.png'">
            </div>
        </div>

        <div class="produto-info">
            <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
            <div class="avaliacao">
                <span class="estrelas">★★★★★</span>
                <span class="numero-avaliacoes">(47 avaliações)</span>
            </div>
            
            <div class="preco">
                <span class="preco-original">R$ <?php echo number_format($produto['preco_original'], 2, ',', '.'); ?></span>
                <?php if (isset($produto['preco_promocional']) && $produto['preco_promocional']): ?>
                    <span class="preco-promocional">R$ <?php echo number_format($produto['preco_promocional'], 2, ',', '.'); ?></span>
                    <?php if (isset($produto['desconto']) && $produto['desconto'] > 0): ?>
                        <span class="desconto-badge">-<?php echo $produto['desconto']; ?>%</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <p class="descricao">
                <?php echo htmlspecialchars($produto['descricao'] ?? 'Produto de alta qualidade da CATARSE.'); ?>
            </p>
            
            <div class="tamanhos">
                <h3>Tamanho:</h3>
                <div class="opcoes-tamanho">
                    <?php 
                    $tamanhos = $produto['tamanhos'] ?? ['P', 'M', 'G', 'GG'];
                    foreach ($tamanhos as $index => $tamanho): 
                        $tamanho = trim($tamanho);
                    ?>
                        <input type="radio" id="tamanho-<?php echo strtolower($tamanho); ?>" 
                               name="tamanho" value="<?php echo htmlspecialchars($tamanho); ?>" 
                               <?php echo $index === 1 ? 'checked' : ''; ?>>
                        <label for="tamanho-<?php echo strtolower($tamanho); ?>"><?php echo htmlspecialchars($tamanho); ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="quantidade">
                <h3>Quantidade:</h3>
                <div class="controle-quantidade">
                    <button type="button" class="diminuir">-</button>
                    <input type="number" value="1" min="1" max="10">
                    <button type="button" class="aumentar">+</button>
                </div>
            </div>
            
    
    <div class="frete">
        <h3>Calcular Frete</h3>
        <div class="frete-input">
            <input type="text" id="cep" placeholder="Digite seu CEP" maxlength="9">
            <button id="calcular-frete">Calcular</button>
        </div>
        <div id="resultado-frete" class="resultado-frete"></div>
        <div id="opcoes-frete" style="margin-top:10px; display:none;">
            <label><input type="radio" name="tipo-frete" value="PAC" required> PAC</label>
            <label><input type="radio" name="tipo-frete" value="SEDEX"> SEDEX</label>
            <label><input type="radio" name="tipo-frete" value="PREMIUM"> Premium</label>
            <button id="selecionar-frete" style="margin-left:10px;">Selecionar Frete</button>
        </div>
        <div id="valor-frete-selecionado" style="margin-top:10px; font-weight:bold;"></div>
    </div>
    
            <div class="acoes">
                <button class="btn-carrinho">Adicionar ao Carrinho</button>
            </div>
            
            <div class="detalhes-tecnicos">
                <h3>Detalhes Técnicos:</h3>
                <ul>
                    <li><strong>Material:</strong> 100% algodão penteado</li>
                    <li><strong>Gramatura:</strong> 220g (alta qualidade)</li>
                    <li><strong>Modelo:</strong> Oversized com caimento boxy</li>
                    <li><strong>Origem:</strong> Produzido no Brasil</li>
                    <li><strong>Lavagem:</strong> Lavar à mão ou máquina (modo delicado)</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Seção de Avaliações -->
    <section class="avaliacoes-container">
        <div class="container">
            <h2>Comentários</h2>
            
            <!-- Formulário de Avaliação -->
            <div class="form-avaliacao">
                <h3>Deixe seu comentário</h3>
                <form id="form-avaliacao">
                    <div class="avaliacao-estrelas">
                        <label>Sua nota:</label>
                        <div class="estrelas-avaliacao">
                            <input type="radio" id="estrela5" name="estrelas" value="5">
                            <label for="estrela5">★</label>
                            <input type="radio" id="estrela4" name="estrelas" value="4">
                            <label for="estrela4">★</label>
                            <input type="radio" id="estrela3" name="estrelas" value="3">
                            <label for="estrela3">★</label>
                            <input type="radio" id="estrela2" name="estrelas" value="2">
                            <label for="estrela2">★</label>
                            <input type="radio" id="estrela1" name="estrelas" value="1">
                            <label for="estrela1">★</label>
                        </div>
                    </div>
                    
                    <div class="campo-texto">
                        <label for="comentario">Seu comentário:</label>
                        <textarea id="comentario" name="comentario" placeholder="Conte sua experiência com o produto..." rows="4" required></textarea>
                    </div>
                    
                    <div class="campo-foto">
                        <label for="foto-produto">Foto do produto recebido (opcional):</label>
                        <input type="file" id="foto-produto" name="foto-produto" accept="image/*">
                        <div class="preview-foto" id="preview-foto"></div>
                    </div>
                    
                    <button type="submit" class="btn-enviar-avaliacao">Enviar Avaliação</button>
                </form>
            </div>
            
            <!-- Lista de Avaliações -->
            <div class="lista-avaliacoes" id="lista-avaliacoes">
                <h3>Comentários</h3>
                <div class="avaliacoes-vazia" id="avaliacoes-vazia">
                    <p>Seja o primeiro a avaliar este produto!</p>
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

    <script>
        // Dados do produto para JavaScript
        const produtoData = {
            id: <?php echo $produto['id']; ?>,
            nome: <?php echo json_encode($produto['nome']); ?>,
            preco: <?php echo json_encode(isset($produto['preco_promocional']) && $produto['preco_promocional'] ? 'R$ ' . number_format($produto['preco_promocional'], 2, ',', '.') : 'R$ ' . number_format($produto['preco_original'], 2, ',', '.')); ?>,
            imagem: <?php echo json_encode($produto['imagem']); ?>,
            precoOriginal: <?php echo json_encode('R$ ' . number_format($produto['preco_original'], 2, ',', '.')); ?>,
            precoPromocional: <?php echo json_encode(isset($produto['preco_promocional']) && $produto['preco_promocional'] ? 'R$ ' . number_format($produto['preco_promocional'], 2, ',', '.') : null); ?>
        };
    </script>
    <script src="../js/carrinho.js"></script>
    <script src="../js/produto1.js"></script>
</body>
</html>

