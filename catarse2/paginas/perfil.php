<?php
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

// Incluir conexão com banco
require_once '../php/database.php';

// Buscar dados do usuário
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
    <title>Meu Perfil - Catarse</title>
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }
        
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .info-group {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-label {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .actions {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }
        
        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: var(--text-secondary);
        }
        
        .btn-secondary:hover {
            background: var(--text-primary);
        }
        
        @media (max-width: 768px) {
            .profile-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .profile-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Incluir navegação -->
    <?php include '../php/nav_include.php'; ?>
    
    <main>
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php 
                    $nome = $usuario['nome'] ?? '';
                    $initials = '';
                    if (strlen($nome) >= 2) {
                        $initials = strtoupper(substr($nome, 0, 2));
                    }
                    echo $initials;
                    ?>
                </div>
                <h1>Meu Perfil</h1>
                <p>Visualize suas informações pessoais</p>
            </div>
            
            <div class="profile-info">
                <div class="info-group">
                    <div class="info-label">Nome Completo</div>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['nome']); ?></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Login/Username</div>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['login']); ?></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">E-mail</div>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['email'] ?? 'Não informado'); ?></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Telefone</div>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['telefone'] ?? 'Não informado'); ?></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">CPF</div>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['cpf'] ?? 'Não informado'); ?></div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Data de Cadastro</div>
                    <div class="info-value">
                        <?php 
                        if (isset($usuario['data_cadastro'])) {
                            echo date('d/m/Y H:i', strtotime($usuario['data_cadastro']));
                        } else {
                            echo 'Não informado';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="actions">
                <a href="atualizar_dados.php" class="btn">✏️ Atualizar Dados</a>
                <a href="../index.php" class="btn btn-secondary">← Voltar ao Início</a>
            </div>
        </div>
    </main>
    
    <script src="../js/index.js"></script>
</body>
</html>