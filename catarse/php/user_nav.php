<?php
// user_nav.php - Include para navegação com informações do usuário
if (!session_id()) {
    session_start();
}
?>

<script>
// Função para gerenciar o menu do usuário (caso exista)
document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('user-icon');
    const logoutMenu = document.getElementById('logout-menu');
    
    if (userIcon && logoutMenu) {
        // Mostra/oculta o menu ao clicar no ícone
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            logoutMenu.style.display = logoutMenu.style.display === 'none' || logoutMenu.style.display === '' ? 'block' : 'none';
        });
        
        // Fecha o menu se clicar fora
        document.addEventListener('click', function(e) {
            if (!userIcon.contains(e.target)) {
                logoutMenu.style.display = 'none';
            }
        });
    }
});
</script>

<?php
// Função para gerar o HTML do ícone do usuário
function renderUserIcon($basePath = '') {
    if (isset($_SESSION['user_id'])) {
        $nome = $_SESSION['user_nome'] ?? '';
        $initials = '';
        if (strlen($nome) >= 2) {
            $initials = strtoupper(substr($nome, 0, 2));
        }
        
        echo '<div id="user-icon" style="display: flex;" class="user-icon">';
        echo $initials;
        echo '<div id="logout-menu" class="logout-menu" style="display:none;">';
        echo '<form method="POST" action="' . $basePath . 'php/logout.php" style="margin: 0;">';
        echo '<button type="submit" class="logout-btn">Logout</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }
}

// Função para gerar o link de login se o usuário não estiver logado
function renderLoginLink($basePath = '') {
    if (!isset($_SESSION['user_id'])) {
        echo '<li><a href="' . $basePath . 'paginas/login.html">Login</a></li>';
    }
}
?>