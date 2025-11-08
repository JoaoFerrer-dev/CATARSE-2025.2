<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Incluir conexão com banco
require_once '../php/database.php';

$message = '';
$message_type = '';

// Processar formulário se enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';
        
        // Validações básicas
        if (empty($nome)) {
            throw new Exception('Nome é obrigatório');
        }
        
        // Se quer alterar senha, verificar senha atual
        if (!empty($nova_senha)) {
            if (empty($senha_atual)) {
                throw new Exception('Para alterar a senha, informe a senha atual');
            }
            
            if ($nova_senha !== $confirmar_senha) {
                throw new Exception('Confirmação de senha não confere');
            }
            
            if (strlen($nova_senha) < 6) {
                throw new Exception('Nova senha deve ter pelo menos 6 caracteres');
            }
            
            // Verificar senha atual
            $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $senha_db = $stmt->fetchColumn();
            
            if (!password_verify($senha_atual, $senha_db)) {
                throw new Exception('Senha atual incorreta');
            }
        }
        
        // Atualizar dados
        if (!empty($nova_senha)) {
            // Verificar se colunas existem antes de atualizar
            $stmt = $pdo->prepare("SHOW COLUMNS FROM usuarios");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $updateFields = ['nome = ?'];
            $updateValues = [$nome];
            
            if (in_array('email', $columns)) {
                $updateFields[] = 'email = ?';
                $updateValues[] = $email;
            }
            if (in_array('telefone', $columns)) {
                $updateFields[] = 'telefone = ?';
                $updateValues[] = $telefone;
            }
            if (in_array('cpf', $columns)) {
                $updateFields[] = 'cpf = ?';
                $updateValues[] = $cpf;
            }
            
            $updateFields[] = 'senha = ?';
            $updateValues[] = password_hash($nova_senha, PASSWORD_DEFAULT);
            $updateValues[] = $_SESSION['user_id'];
            
            $sql = "UPDATE usuarios SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($updateValues);
        } else {
            // Atualizar sem alterar senha
            $stmt = $pdo->prepare("SHOW COLUMNS FROM usuarios");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $updateFields = ['nome = ?'];
            $updateValues = [$nome];
            
            if (in_array('email', $columns)) {
                $updateFields[] = 'email = ?';
                $updateValues[] = $email;
            }
            if (in_array('telefone', $columns)) {
                $updateFields[] = 'telefone = ?';
                $updateValues[] = $telefone;
            }
            if (in_array('cpf', $columns)) {
                $updateFields[] = 'cpf = ?';
                $updateValues[] = $cpf;
            }
            
            $updateValues[] = $_SESSION['user_id'];
            
            $sql = "UPDATE usuarios SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($updateValues);
        }
        
        // Atualizar sessão
        $_SESSION['user_nome'] = $nome;
        
        $message = 'Dados atualizados com sucesso!';
        $message_type = 'success';
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'error';
    } catch (PDOException $e) {
        $message = 'Erro no banco de dados. Tente novamente.';
        $message_type = 'error';
    }
}

// Buscar dados atuais do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Dados - Catarse</title>
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .update-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .update-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .password-section {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .password-section h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: var(--text-secondary);
            color: white;
        }
        
        .btn-secondary:hover {
            background: var(--text-primary);
        }
        
        .message {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .update-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Incluir navegação -->
    <?php include '../php/nav_include.php'; ?>
    
    <main>
        <div class="update-container">
            <div class="update-header">
                <h1>Atualizar Dados</h1>
                <p>Mantenha suas informações sempre atualizadas</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" 
                           id="nome" 
                           name="nome" 
                           value="<?php echo htmlspecialchars($usuario['nome']); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" 
                           id="telefone" 
                           name="telefone" 
                           value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" 
                           id="cpf" 
                           name="cpf" 
                           value="<?php echo htmlspecialchars($usuario['cpf'] ?? ''); ?>"
                           pattern="[0-9]{3}\.?[0-9]{3}\.?[0-9]{3}-?[0-9]{2}">
                </div>
                
                <div class="password-section">
                    <h3>Alterar Senha (opcional)</h3>
                    
                    <div class="form-group">
                        <label for="senha_atual">Senha Atual</label>
                        <input type="password" 
                               id="senha_atual" 
                               name="senha_atual"
                               placeholder="Digite sua senha atual para alterar">
                    </div>
                    
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha</label>
                        <input type="password" 
                               id="nova_senha" 
                               name="nova_senha"
                               placeholder="Nova senha (mínimo 6 caracteres)">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Nova Senha</label>
                        <input type="password" 
                               id="confirmar_senha" 
                               name="confirmar_senha"
                               placeholder="Digite a nova senha novamente">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
                    <a href="perfil.php" class="btn btn-secondary">← Voltar ao Perfil</a>
                </div>
            </form>
        </div>
    </main>
    
    <script src="../js/index.js"></script>
    <script>
        // Validação de confirmação de senha
        document.getElementById('confirmar_senha').addEventListener('input', function() {
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmarSenha = this.value;
            
            if (novaSenha && confirmarSenha && novaSenha !== confirmarSenha) {
                this.setCustomValidity('Senhas não conferem');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Formatação do CPF
        document.getElementById('cpf').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = value;
        });
        
        // Formatação do telefone
        document.getElementById('telefone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            this.value = value;
        });
    </script>
</body>
</html>