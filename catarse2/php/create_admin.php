<?php
// create_admin.php - Script para criar primeiro administrador
require_once __DIR__ . '/database.php';

// Apenas permitir acesso direto (remover após criar o primeiro admin)
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';
$nivel = $_POST['nivel'] ?? 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($nome) && !empty($email) && !empty($login) && !empty($senha)) {
    try {
        $database = new Database();
        $pdo = $database->getConnection();
        
        // Verificar se já existe admin com esse login ou email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM administradores WHERE login = ? OR email = ?");
        $stmt->execute([$login, $email]);
        if ($stmt->fetchColumn() > 0) {
            $erro = "Login ou email já existe!";
        } else {
            // Criar admin
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO administradores (nome, email, login, senha, nivel) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nome, $email, $login, $senha_hash, $nivel]);
            $sucesso = "Administrador criado com sucesso!";
        }
    } catch (Exception $e) {
        $erro = "Erro: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Administrador</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background: #5568d3;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Criar Administrador</h2>
        
        <?php if (isset($sucesso)): ?>
            <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <p><a href="../admin/login.php">Ir para Login</a></p>
        <?php else: ?>
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                
                <div class="form-group">
                    <label for="nivel">Nível</label>
                    <select id="nivel" name="nivel">
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Criar Administrador</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

