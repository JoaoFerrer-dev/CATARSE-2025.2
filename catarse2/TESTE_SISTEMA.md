# 🧪 Guia de Teste do Sistema CATARSE

## 📋 Passo a Passo para Testar

### 1️⃣ **Configuração Inicial do Banco de Dados**

#### Opção A: Usando o script PHP (Recomendado)
1. Abra o navegador e acesse:
   ```
   http://localhost/catarse2/php/create_tables.php
   ```
2. Você deve ver a mensagem: "Tabelas criadas com sucesso!"
3. Clique no link "Criar Admin" ou acesse diretamente:
   ```
   http://localhost/catarse2/php/create_admin.php
   ```

#### Opção B: Usando o arquivo SQL
1. Abra o phpMyAdmin: `http://localhost/phpmyadmin`
2. Selecione o banco de dados `catarse` (ou crie se não existir)
3. Vá na aba "Importar"
4. Selecione o arquivo: `SQL/database.sql`
5. Clique em "Executar"

---

### 2️⃣ **Criar Primeiro Administrador**

1. Acesse: `http://localhost/catarse/php/create_admin.php`
2. Preencha o formulário:
   - **Nome**: Seu nome completo
   - **Email**: seu@email.com
   - **Login**: admin (ou outro de sua escolha)
   - **Senha**: uma senha segura
   - **Nível**: Admin ou Super Admin
3. Clique em "Criar Administrador"
4. Você verá a mensagem de sucesso

---

### 3️⃣ **Testar Login de Administrador**

1. Acesse: `http://localhost/catarse/admin/login.php`
2. Digite o login e senha criados
3. Você deve ser redirecionado para o Dashboard

---

### 4️⃣ **Testar Dashboard Administrativo**

No dashboard você verá:
- ✅ Total de Produtos
- ✅ Total de Pedidos
- ✅ Total de Usuários
- ✅ Receita Total
- ✅ Pedidos Pendentes

**Teste**: Verifique se os números estão corretos (inicialmente devem ser 0 ou valores existentes).

---

### 5️⃣ **Testar Gerenciamento de Produtos**

1. No menu, clique em **"Produtos"**
2. Você verá a lista de produtos (inicialmente vazia)

#### Adicionar Produto:
1. Clique em **"➕ Novo Produto"**
2. Preencha o formulário:
   - **Nome**: "Camisa Oversized Moda Bangu preta"
   - **Descrição**: "Camisa oversized estilo bangu..."
   - **Preço Original**: 98.90
   - **Preço Promocional**: 89.00 (opcional)
   - **URL da Imagem**: `../img/produto1.jpg`
   - **Tamanhos**: P,M,G,GG
   - **Estoque**: 10
   - **Status**: Ativo
3. Clique em **"Salvar"**
4. O produto deve aparecer na lista

#### Editar Produto:
1. Na lista, clique em **"✏️ Editar"** em qualquer produto
2. Altere algum campo (ex: preço ou estoque)
3. Clique em **"Salvar"**
4. Verifique se as alterações foram salvas

#### Deletar Produto:
1. Clique em **"🗑️ Deletar"** em um produto
2. Confirme a exclusão
3. O produto deve ser removido da lista

---

### 6️⃣ **Testar Visualização de Produtos no Site**

1. Acesse: `http://localhost/catarse/paginas/produtos.php`
2. Você deve ver os produtos que cadastrou no painel admin
3. Os produtos devem aparecer com:
   - Imagem
   - Nome
   - Preço original
   - Preço promocional (se houver)
   - Badge de desconto (se houver)

---

### 7️⃣ **Testar Gerenciamento de Pedidos**

1. No painel admin, clique em **"Pedidos"**
2. Você verá a lista de pedidos (pode estar vazia se não houver pedidos)

#### Para testar com pedidos reais:
1. Faça um cadastro de usuário normal: `http://localhost/catarse/paginas/cadastro.html`
2. Faça login: `http://localhost/catarse/paginas/login.html`
3. Adicione produtos ao carrinho
4. Finalize uma compra (pagamento)
5. Volte ao painel admin e veja o pedido na lista

#### Editar Status de Pedido:
1. Na lista de pedidos, clique em **"Editar"**
2. Altere o status (ex: de "Pendente" para "Enviado")
3. Adicione um código de rastreio (ex: "BR123456789BR")
4. Clique em **"Salvar"**
5. Verifique se o status foi atualizado

#### Ver Detalhes do Pedido:
1. Clique em **"Ver"** em qualquer pedido
2. Você verá:
   - Informações do cliente
   - Itens do pedido
   - Valores
   - Status
   - Código de rastreio

---

### 8️⃣ **Testar Gerenciamento de Usuários**

1. No painel admin, clique em **"Usuários"**
2. Você verá a lista de todos os usuários cadastrados
3. Verifique se os dados estão corretos

---

### 9️⃣ **Testar Fluxo Completo (Cliente)**

#### Cadastro:
1. Acesse: `http://localhost/catarse/paginas/cadastro.html`
2. Preencha todos os campos
3. Submeta o formulário
4. Você deve ver mensagem de sucesso

#### Login:
1. Acesse: `http://localhost/catarse/paginas/login.html`
2. Use o login e senha criados
3. Você deve ser redirecionado para a home

#### Adicionar ao Carrinho:
1. Vá em Produtos: `http://localhost/catarse/paginas/produtos.php`
2. Clique em um produto
3. Selecione tamanho e quantidade
4. Clique em "Adicionar ao Carrinho"
5. O produto deve aparecer no carrinho

#### Finalizar Compra:
1. Abra o carrinho
2. Clique em "Finalizar Compra"
3. Preencha os dados do cartão
4. Clique em "Finalizar Compra"
5. Você deve ver mensagem de sucesso

#### Rastrear Pedido:
1. Acesse: `http://localhost/catarse/php/rastreio.php`
2. Você deve ver seus pedidos listados

---

### 🔟 **Testar APIs (Opcional - Desenvolvedores)**

#### Testar API de Produtos:
```bash
# Listar produtos
GET http://localhost/catarse/php/produtos_api.php

# Buscar produto específico
GET http://localhost/catarse/php/produtos_api.php?id=1
```

#### Testar API de Carrinho:
```bash
# Buscar carrinho
GET http://localhost/catarse/php/carrinho.php
```

---

## ✅ Checklist de Testes

- [ ] Banco de dados criado com sucesso
- [ ] Primeiro administrador criado
- [ ] Login de admin funciona
- [ ] Dashboard exibe estatísticas
- [ ] Adicionar produto funciona
- [ ] Editar produto funciona
- [ ] Deletar produto funciona
- [ ] Produtos aparecem no site
- [ ] Cadastro de usuário funciona
- [ ] Login de usuário funciona
- [ ] Adicionar ao carrinho funciona
- [ ] Finalizar compra funciona
- [ ] Pedidos aparecem no painel admin
- [ ] Editar status de pedido funciona
- [ ] Rastreio de pedidos funciona

---

## 🐛 Problemas Comuns e Soluções

### Erro: "Tabela não existe"
**Solução**: Execute `create_tables.php` ou importe `database.sql`

### Erro: "Acesso negado" no painel admin
**Solução**: Verifique se fez login corretamente em `admin/login.php`

### Produtos não aparecem no site
**Solução**: 
1. Verifique se os produtos estão marcados como "Ativo" no painel admin
2. Verifique se a URL da imagem está correta

### Erro ao adicionar produto
**Solução**: 
1. Verifique se todos os campos obrigatórios estão preenchidos
2. Verifique se a URL da imagem é válida
3. Verifique se o preço é um número válido

### Carrinho não funciona
**Solução**: 
1. Verifique se as sessões PHP estão habilitadas
2. Verifique se o arquivo `php/carrinho.php` existe

---

## 📝 Notas Importantes

1. **Sessões PHP**: Certifique-se de que as sessões estão funcionando
2. **Permissões**: Verifique permissões de escrita nas pastas se houver upload de imagens
3. **Banco de Dados**: Certifique-se de que o MySQL está rodando no XAMPP
4. **URLs**: Ajuste os caminhos se sua estrutura de pastas for diferente

---

## 🎯 Próximos Passos Após Testes

1. Adicione produtos reais com imagens
2. Configure os tamanhos disponíveis
3. Teste com múltiplos usuários
4. Configure códigos de rastreio reais
5. Personalize o design do painel admin se necessário

---

**Boa sorte com os testes! 🚀**

