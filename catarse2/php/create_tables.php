<?php
// create_tables.php - Criar tabelas automaticamente
require_once __DIR__ . '/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Criar tabela usuarios se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            login VARCHAR(6) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            celular VARCHAR(15) NOT NULL,
            cpf VARCHAR(11) NOT NULL UNIQUE,
            cep VARCHAR(8) NOT NULL,
            endereco VARCHAR(100) NOT NULL,
            bairro VARCHAR(50) NOT NULL,
            cidade VARCHAR(50) NOT NULL,
            uf VARCHAR(2) NOT NULL,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Criar tabela pedidos se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pedidos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            total DECIMAL(10,2) NOT NULL,
            frete DECIMAL(10,2) DEFAULT 0.00,
            status ENUM('pendente', 'pago', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
            codigo_rastreio VARCHAR(50) NULL,
            data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        )
    ");
    
    // Criar tabela itens_pedido se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS itens_pedido (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            nome_produto VARCHAR(255) NOT NULL,
            tamanho VARCHAR(10) NULL,
            quantidade INT NOT NULL,
            preco DECIMAL(10,2) NOT NULL,
            imagem VARCHAR(255) NULL,
            FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
        )
    ");
    
    // Criar tabela pagamentos se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pagamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            nome_cartao VARCHAR(100) NOT NULL,
            numero_cartao VARCHAR(19) NOT NULL,
            validade VARCHAR(5) NOT NULL,
            cvv VARCHAR(4) NOT NULL,
            status ENUM('pendente', 'aprovado', 'recusado') DEFAULT 'pendente',
            data_pagamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
        )
    ");
    
    // Criar tabela produtos se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS produtos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            descricao TEXT NULL,
            preco_original DECIMAL(10,2) NOT NULL,
            preco_promocional DECIMAL(10,2) NULL,
            desconto INT DEFAULT 0,
            imagem VARCHAR(255) NOT NULL,
            tamanhos_disponiveis VARCHAR(100) DEFAULT 'P,M,G,GG',
            estoque INT DEFAULT 0,
            ativo TINYINT(1) DEFAULT 1,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Criar tabela administradores se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS administradores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            login VARCHAR(50) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            nivel ENUM('admin', 'super_admin') DEFAULT 'admin',
            ativo TINYINT(1) DEFAULT 1,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    echo "Tabelas criadas com sucesso!<br>";
    echo "Agora você pode criar um administrador acessando: <a href='create_admin.php'>Criar Admin</a>";
    
} catch (Exception $e) {
    echo "Erro ao criar tabelas: " . $e->getMessage();
}
?>

