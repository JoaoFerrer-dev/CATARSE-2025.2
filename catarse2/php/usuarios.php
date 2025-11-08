<?php
// ajustar caminho para database.php que está em php/database.php
require_once __DIR__ . '/database.php';
// definir header para respostas HTML por padrão (mensagens amigáveis)
header('Content-Type: text/html; charset=utf-8');

try {
    // Criar conexão com o banco
    $database = new Database();
    $pdo = $database->getConnection();

    if(!$pdo) {
        throw new Exception("Erro ao conectar ao banco de dados");
    }

    // Receber dados do formulário
$nome = htmlspecialchars(trim($_POST['nome'] ?? ''));
$login = htmlspecialchars(trim($_POST['login'] ?? ''));
$senha = $_POST['senha'] ?? '';
$celular = htmlspecialchars(trim($_POST['celular'] ?? ''));
$cpf = htmlspecialchars(trim($_POST['cpf'] ?? ''));
$cep = htmlspecialchars(trim($_POST['cep'] ?? ''));
$endereco = htmlspecialchars(trim($_POST['endereco'] ?? ''));
$bairro = htmlspecialchars(trim($_POST['bairro'] ?? ''));
$cidade = htmlspecialchars(trim($_POST['cidade'] ?? ''));
$uf = htmlspecialchars(trim($_POST['uf'] ?? ''));

    // Validações
    if (strlen($login) != 6 || !ctype_alpha($login)) {
        throw new Exception("Login deve conter exatamente 6 letras");
    }

    if (!preg_match('/^[0-9]{6,12}$/', $senha)) {
        throw new Exception("Senha deve conter entre 6 e 12 números");
    }

    // Remove caracteres especiais do CPF
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11) {
        throw new Exception("CPF inválido");
    }

    // Verificar se login já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE login = ?");
    $stmt->execute([$login]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Este login já está sendo usado por outro usuário. Escolha um login diferente.");
    }

    // Verificar se CPF já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE cpf = ?");
    $stmt->execute([$cpf]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Este CPF já está cadastrado no sistema. Verifique se você já possui uma conta ou entre em contato com o suporte.");
    }

    // Verificar se celular já existe (se não estiver vazio)
    if (!empty($celular)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE celular = ?");
        $stmt->execute([$celular]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Este número de celular já está cadastrado no sistema. Cada usuário deve ter um número único.");
        }
    }

    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Preparar e executar a query SQL
    $sql = "INSERT INTO usuarios (nome, login, senha, celular, cpf, cep, endereco, bairro, cidade, uf) 
            VALUES (:nome, :login, :senha, :celular, :cpf, :cep, :endereco, :bairro, :cidade, :uf)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':nome' => $nome,
        ':login' => $login,
        ':senha' => $senha_hash,
        ':celular' => $celular,
        ':cpf' => $cpf,
        ':cep' => $cep,
        ':endereco' => $endereco,
        ':bairro' => $bairro,
        ':cidade' => $cidade,
        ':uf' => $uf
    ]);

    // DEBUG: log successful insert (id) to a local file for troubleshooting
    try {
        $insertId = $pdo->lastInsertId();
        $logLine = date('Y-m-d H:i:s') . " - INSERT OK, id=" . $insertId . "\n";
        @file_put_contents(__DIR__ . '/usuarios_debug.log', $logLine, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        // ignore logging failure
    }

        // Cadastro bem sucedido — mostrar mensagem amigável em HTML
        $safeNome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
        $html = <<<HTML
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Cadastro concluído</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;color:#222;padding:30px}
        .card{max-width:640px;margin:40px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08)}
        a.button{display:inline-block;margin-top:12px;padding:10px 14px;background:#0b79d0;color:#fff;border-radius:4px;text-decoration:none}
    </style>
</head>
<body>
    <div class="card">
        <h1>Cadastro realizado com sucesso</h1>
        <p>Olá <strong>{$safeNome}</strong>, seu cadastro foi registrado com sucesso.</p>
        <p>Agora você pode acessar a página de login para entrar na sua conta.</p>
    <p><a class="button" href="../paginas/login.html">Ir para Login</a></p>
    </div>
</body>
</html>
HTML;

        echo $html;
        exit();

} catch (PDOException $e) {
    // Erro de banco de dados - mostrar página HTML amigável
    $errorMessage = "Ocorreu um erro interno. Tente novamente em alguns minutos.";
    $html = <<<HTML
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Erro no Cadastro</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;color:#222;padding:30px}
        .card{max-width:640px;margin:40px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08)}
        .error{background:#ffe6e6;border:1px solid #ff9999;color:#cc0000;padding:15px;border-radius:5px;margin:10px 0}
        a.button{display:inline-block;margin-top:12px;padding:10px 14px;background:#0b79d0;color:#fff;border-radius:4px;text-decoration:none}
        .button-back{background:#666;margin-right:10px}
    </style>
</head>
<body>
    <div class="card">
        <h1>❌ Erro no Cadastro</h1>
        <div class="error">
            <strong>Erro:</strong> {$errorMessage}
        </div>
        <p>Se o problema persistir, entre em contato com nosso suporte.</p>
        <p>
            <a class="button button-back" href="javascript:history.back()">← Voltar</a>
            <a class="button" href="../paginas/suporte.html">Contatar Suporte</a>
        </p>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit();
    
} catch (Exception $e) {
    // Outros erros (validações) - mostrar página HTML amigável
    $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $html = <<<HTML
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Erro no Cadastro</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f7f7f7;color:#222;padding:30px}
        .card{max-width:640px;margin:40px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08)}
        .error{background:#ffe6e6;border:1px solid #ff9999;color:#cc0000;padding:15px;border-radius:5px;margin:10px 0}
        a.button{display:inline-block;margin-top:12px;padding:10px 14px;background:#0b79d0;color:#fff;border-radius:4px;text-decoration:none}
        .button-back{background:#666;margin-right:10px}
        .highlight{background:#fff3cd;border:1px solid #ffeaa7;color:#856404;padding:10px;border-radius:5px;margin:10px 0}
    </style>
</head>
<body>
    <div class="card">
        <h1>⚠️ Problema no Cadastro</h1>
        <div class="error">
            <strong>Erro:</strong> {$errorMessage}
        </div>
        
        <div class="highlight">
            <strong>💡 Dicas:</strong>
            <ul>
                <li>Se o CPF já foi cadastrado, verifique se você já tem uma conta</li>
                <li>Se o login já existe, tente uma combinação diferente de 6 letras</li>
                <li>Certifique-se de que todos os campos obrigatórios estão preenchidos corretamente</li>
            </ul>
        </div>
        
        <p>
            <a class="button button-back" href="javascript:history.back()">← Corrigir Dados</a>
            <a class="button" href="../paginas/login.html">Já tenho conta</a>
        </p>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit();
}
?>