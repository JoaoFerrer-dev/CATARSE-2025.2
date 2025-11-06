<?php
// nav_include.php - Include de navegação com controle de usuário
if (!session_id()) {
    session_start();
}
?>

<style>
/* Estilos para o ícone do usuário */
.user-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    color: white;
    display: flex !important;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid rgba(255,255,255,0.9);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    position: relative;
    margin-left: 8px;
    text-transform: uppercase;
}

.user-icon:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    background: linear-gradient(135deg, #ff5252, #d84315);
    border-color: white;
}

.user-icon:active {
    transform: scale(0.95);
}

.user-menu {
    position: absolute;
    top: 45px;
    right: 0;
    background: #000000;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    min-width: 160px;
    z-index: 1000;
    overflow: hidden;
    border: 1px solid #333;
    animation: slideDown 0.2s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.user-menu-header {
    padding: 10px 14px;
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    color: white;
    text-align: center;
}

.user-menu-header strong {
    display: block;
    font-size: 12px;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    color: #ffffff;
}

.user-menu-options {
    padding: 4px 0;
}

.user-menu-item {
    display: block;
    padding: 10px 14px;
    color: #ffffff;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

.user-menu-item:hover {
    background: #333333;
    color: #ff6b6b;
    font-weight: 700;
}

.user-menu-divider {
    margin: 4px 10px;
    border: none;
    border-top: 1px solid #444;
    height: 1px;
}

.logout-btn {
    color: #ff4757 !important;
    font-weight: 700;
}

.logout-btn:hover {
    background: #2c2c2c !important;
    color: #ff3742 !important;
    font-weight: 700;
}

/* Responsividade */
@media (max-width: 768px) {
    .user-icon {
        width: 32px;
        height: 32px;
        font-size: 11px;
        margin-left: 6px;
    }
    
    .user-menu {
        right: -8px;
        min-width: 140px;
        top: 40px;
    }
    
    .user-menu-item {
        padding: 12px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #ffffff;
    }
    
    .user-menu-item:hover {
        color: #ff6b6b;
        font-weight: 700;
        background: #333333;
    }
    
    .user-menu-header {
        padding: 12px 12px;
    }
    
    .user-menu-header strong {
        font-size: 13px;
        font-weight: 700;
    }
}

@media (max-width: 480px) {
    .user-icon {
        width: 30px;
        height: 30px;
        font-size: 10px;
        margin-left: 4px;
    }
    
    .user-menu {
        right: -12px;
        min-width: 120px;
        top: 38px;
    }
    
    .user-menu-item {
        padding: 10px 10px;
        font-size: 12px;
        gap: 4px;
        font-weight: 600;
        color: #ffffff;
    }
    
    .user-menu-item:hover {
        color: #ff6b6b;
        font-weight: 700;
        background: #333333;
    }
    
    .user-menu-header {
        padding: 10px 10px;
    }
    
    .user-menu-header strong {
        font-size: 11px;
        font-weight: 700;
    }
}
</style>

<nav>
    <div class="nav-brand">
        <?php 
        // Detectar se estamos na pasta raiz ou em subpasta
        $isInSubfolder = (basename(dirname($_SERVER['PHP_SELF'])) === 'paginas');
        $basePath = $isInSubfolder ? '../' : './';
        ?>
        <a href="<?php echo $basePath; ?>index.php" class="logo-link">
            <img src="<?php echo $basePath; ?>img/logo2-removebg-preview.png" alt="Logo Catarse" class="nav-logo">
            <span class="brand-name">CATARSE</span>
        </a>
    </div>
    
    <!-- Botão do menu hamburguer -->
    <div class="hamburger-menu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </div>
    
    <!-- Lista de navegação -->
    <ul class="nav-list">
        <li style="position: relative;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div id="user-icon" style="display: flex;" class="user-icon">
                    <?php 
                    $nome = $_SESSION['user_nome'] ?? '';
                    $initials = '';
                    if (strlen($nome) >= 2) {
                        $initials = strtoupper(substr($nome, 0, 2));
                    }
                    echo $initials;
                    ?>
                    <div id="user-menu" class="user-menu" style="display:none;">
                        <div class="user-menu-header">
                            <strong><?php echo htmlspecialchars($_SESSION['user_login'] ?? ''); ?></strong>
                        </div>
                        <div class="user-menu-options">
                            <a href="<?php echo $isInSubfolder ? '' : 'paginas/'; ?>perfil.php" class="user-menu-item">
                                👤 Perfil
                            </a>
                            <a href="<?php echo $isInSubfolder ? '' : 'paginas/'; ?>atualizar_dados.php" class="user-menu-item">
                                ⚙️ Configurações
                            </a>
                            <hr class="user-menu-divider">
                            <form method="POST" action="<?php echo $isInSubfolder ? '../' : ''; ?>php/logout.php" style="margin: 0;">
                                <button type="submit" class="user-menu-item logout-btn">
                                    🚪 Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </li>
        <li><a href="<?php echo $basePath; ?>index.php">Home</a></li>
        <li><a href="<?php echo $isInSubfolder ? '' : 'paginas/'; ?>sobre.html">Sobre</a></li>
        <li><a href="<?php echo $isInSubfolder ? '' : 'paginas/'; ?>produtos.php">Produtos</a></li>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <li><a href="<?php echo $isInSubfolder ? '' : 'paginas/'; ?>login.html">Login</a></li>
        <?php endif; ?>
        <li><a href="https://www.instagram.com/catarsegarments?igsh=MWNvOW5hYXFmNm1hMg==" target="_blank">Instagram</a></li>
        <li><button id="abrir-carrinho" class="cart-btn">🛒 <span id="contador-carrinho">0</span></button></li>
    </ul>
</nav>

<?php if (isset($_SESSION['user_id'])): ?>
<script>
    // Funcionalidade do menu do usuário
    document.addEventListener('DOMContentLoaded', function() {
        const userIcon = document.getElementById('user-icon');
        const userMenu = document.getElementById('user-menu');
        
        if (userIcon && userMenu) {
            let menuTimeout;
            
            // Mostra menu no hover
            userIcon.addEventListener('mouseenter', function() {
                clearTimeout(menuTimeout);
                userMenu.style.display = 'block';
            });
            
            // Esconde menu quando sai do hover
            userIcon.addEventListener('mouseleave', function() {
                menuTimeout = setTimeout(() => {
                    userMenu.style.display = 'none';
                }, 300);
            });
            
            // Mantém menu aberto quando hover no menu
            userMenu.addEventListener('mouseenter', function() {
                clearTimeout(menuTimeout);
            });
            
            userMenu.addEventListener('mouseleave', function() {
                userMenu.style.display = 'none';
            });
            
            // Click no ícone - ação principal
            userIcon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Se está em uma subpasta (paginas), vai para atualizar_dados.php
                // Se está na raiz, vai para paginas/atualizar_dados.php
                const isInSubfolder = window.location.pathname.includes('/paginas/');
                const targetUrl = isInSubfolder ? 'atualizar_dados.php' : 'paginas/atualizar_dados.php';
                window.location.href = targetUrl;
            });
            
            // Para dispositivos móveis - toggle no click
            if (window.innerWidth <= 768) {
                userIcon.removeEventListener('mouseenter', function() {});
                userIcon.removeEventListener('mouseleave', function() {});
                
                userIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (userMenu.style.display === 'none' || userMenu.style.display === '') {
                        userMenu.style.display = 'block';
                    } else {
                        userMenu.style.display = 'none';
                    }
                });
                
                // Fecha menu quando clica fora
                document.addEventListener('click', function(e) {
                    if (!userIcon.contains(e.target)) {
                        userMenu.style.display = 'none';
                    }
                });
            }
        }
    });
</script>
<?php endif; ?>