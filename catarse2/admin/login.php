<?php
session_start();
// Se já estiver logado, redirecionar
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - CATARSE</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        body {
            background: var(--fundo);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        #mensagem-erro {
            color: var(--destaque);
            margin-top: 15px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="admin-login-box">
        <h2>Área Administrativa</h2>
        <div style="text-align: center;">
            <span class="admin-badge">ADMIN</span>
        </div>
        <form id="form-admin-login">
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" id="login" name="login" required autofocus>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <div class="input-wrapper">
                    <input type="password" id="senha" name="senha" required>
                    <button type="button" id="toggle-password" class="password-toggle">👁️</button>
                </div>
            </div>
            <button type="submit" class="cta" style="width: 100%;">Entrar</button>
            <div id="mensagem-erro"></div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="../index.php" style="color: #667eea; text-decoration: none;">← Voltar ao site</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            const senha = document.getElementById('senha');
            const showing = senha.type === 'text';
            senha.type = showing ? 'password' : 'text';
            this.textContent = showing ? '👁️' : '🙈';
        });

        document.getElementById('form-admin-login').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            const mensagemErro = document.getElementById('mensagem-erro');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Entrando...';
            mensagemErro.textContent = '';
            
            try {
                const response = await fetch('../php/admin_login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    mensagemErro.textContent = data.error || 'Erro ao fazer login';
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            } catch (error) {
                mensagemErro.textContent = 'Erro ao conectar com o servidor';
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    </script>
</body>
</html>

