================================================================================
  RETROGAMES STORE - E-COMMERCE DE VIDEOGAMES RETRÔ
================================================================================

📦 DESCRIÇÃO
Loja virtual completa de videogames com estética retrô dos anos 2000 (Xbox, 
PS2, GameCube, Dreamcast, Xbox 360). Sistema funcional com catálogo, carrinho,
checkout, pagamento simulado e painel administrativo.

🎮 CARACTERÍSTICAS
- Design retrô com estética de consoles dos anos 2000
- Sistema completo de catálogo com busca e filtros
- Carrinho de compras funcional
- Sistema de checkout com múltiplos métodos de pagamento
- Pagamento simulado (PIX, Boleto, Cartão de Crédito)
- Gerenciamento de estoque automático
- Painel administrativo completo (CRUD)
- Sistema de autenticação de usuários
- Histórico de pedidos
- Design 100% responsivo

================================================================================
  REQUISITOS DO SISTEMA
================================================================================

- XAMPP (ou similar) com:
  * PHP 7.4 ou superior
  * MySQL 5.7 ou superior
  * Apache 2.4 ou superior
- Navegador web moderno (Chrome, Firefox, Edge)

================================================================================
  INSTRUÇÕES DE INSTALAÇÃO
================================================================================

1. INSTALAR O XAMPP
   - Baixe em: https://www.apachefriends.org/
   - Instale e inicie os serviços Apache e MySQL

2. CONFIGURAR O PROJETO
   - Extraia o arquivo .zip
   - Copie a pasta "retrogames_store" para:
     C:\xampp\htdocs\

3. CRIAR O BANCO DE DADOS
   - Abra o navegador e acesse: http://localhost/phpmyadmin
   - Clique em "Novo" para criar um banco de dados
   - Nome do banco: retrogames_store
   - Collation: utf8mb4_general_ci
   - Clique em "Criar"

4. IMPORTAR O SQL
   - Selecione o banco "retrogames_store" criado
   - Clique na aba "Importar"
   - Clique em "Escolher arquivo"
   - Selecione o arquivo "retrogames_store.sql"
   - Clique em "Executar"

5. CONFIGURAR A CONEXÃO (SE NECESSÁRIO)
   - Abra o arquivo: config.php
   - Verifique as configurações:
     * DB_HOST: localhost
     * DB_USER: root
     * DB_PASS: (deixe vazio)
     * DB_NAME: retrogames_store

6. CRIAR PASTA DE IMAGENS
   - Crie a estrutura de pastas:
     retrogames_store/
     └── assets/
         └── products/
             └── (coloque as imagens dos produtos aqui)
   
   - Nome das imagens deve corresponder ao campo 'imagem' no banco
   - Coloque um arquivo "placeholder.jpg" para produtos sem imagem

7. ACESSAR O SISTEMA
   - Abra o navegador
   - Acesse: http://localhost/retrogames_store/

================================================================================
  CREDENCIAIS DE ACESSO
================================================================================

CONTA ADMINISTRADOR:
  Email: admin@retrogames.com
  Senha: password

CRIAR NOVA CONTA:
  Use o botão "REGISTRAR" no menu superior

================================================================================
  ESTRUTURA DO PROJETO
================================================================================

retrogames_store/
├── admin/                  # Painel administrativo
│   ├── index.php          # Dashboard admin
│   ├── produtos.php       # Gerenciar produtos
│   ├── pedidos.php        # Gerenciar pedidos
│   └── categorias.php     # Gerenciar categorias
├── assets/                # Recursos estáticos
│   └── products/          # Imagens dos produtos
├── config.php             # Configuração do banco
├── index.php              # Página principal
├── produto.php            # Detalhes do produto
├── carrinho.php           # Carrinho de compras
├── checkout.php           # Finalização da compra
├── pagamento.php          # Simulação de pagamento
├── pedidos.php            # Histórico de pedidos
├── login.php              # Página de login
├── registro.php           # Cadastro de usuário
├── logout.php             # Logout
├── header.php             # Cabeçalho reutilizável
├── footer.php             # Rodapé reutilizável
├── style.css              # Estilos retrô
└── retrogames_store.sql   # Banco de dados

================================================================================
  FUNCIONALIDADES DO SISTEMA
================================================================================

ÁREA PÚBLICA:
  ✅ Catálogo de produtos com busca e filtros
  ✅ Visualização de produtos por categoria
  ✅ Página de detalhes do produto
  ✅ Carrinho de compras com atualização dinâmica
  ✅ Sistema de checkout completo
  ✅ Cálculo automático de frete (grátis acima de R$ 500)
  ✅ Simulação de pagamento (PIX, Boleto, Cartão)
  ✅ Histórico de pedidos do cliente
  ✅ Sistema de registro e login

PAINEL ADMINISTRATIVO:
  ✅ Dashboard com estatísticas
  ✅ CRUD completo de produtos
  ✅ CRUD completo de categorias
  ✅ Gerenciamento de pedidos
  ✅ Atualização de status de pedidos
  ✅ Controle de estoque automático
  ✅ Filtros e busca de produtos/pedidos

================================================================================
  CATEGORIAS DE PRODUTOS
================================================================================

1. Consoles Retro (PS2, Xbox, GameCube, Dreamcast, Xbox 360)
2. Consoles Atuais (PS5, Xbox Series, Nintendo Switch)
3. Computadores (PCs Gamers completos e notebooks)
4. Peças (Placas de vídeo, processadores, memórias)
5. Jogos (Jogos físicos e digitais)
6. Acessórios (Controles, headsets, teclados, mouses)

================================================================================
  FLUXO DE COMPRA
================================================================================

1. Cliente navega pelo catálogo
2. Adiciona produtos ao carrinho
3. Revisa o carrinho (pode atualizar quantidades)
4. Faz login ou cria conta
5. Preenche dados de entrega
6. Escolhe método de pagamento
7. Confirma o pedido
8. Simula o pagamento
9. Acompanha o status do pedido

================================================================================
  ADMINISTRAÇÃO DE PEDIDOS
================================================================================

STATUS DISPONÍVEIS:
  - Pendente: Aguardando pagamento
  - Pago: Pagamento confirmado
  - Enviado: Pedido em trânsito
  - Entregue: Pedido finalizado
  - Cancelado: Pedido cancelado

O admin pode atualizar o status através do painel administrativo.

================================================================================
  SOLUÇÃO DE PROBLEMAS
================================================================================

ERRO: "Erro na conexão com banco de dados"
  → Verifique se o MySQL está rodando no XAMPP
  → Confira as credenciais no config.php
  → Certifique-se que o banco foi criado

ERRO: "Call to undefined function mysqli_connect"
  → Ative a extensão mysqli no PHP
  → Edite php.ini e descomente: extension=mysqli

IMAGENS NÃO APARECEM:
  → Verifique se a pasta assets/products/ existe
  → Coloque as imagens com os nomes corretos
  → Use placeholder.jpg para produtos sem imagem

ERRO 404:
  → Verifique se a URL está correta
  → Certifique-se que o Apache está rodando
  → Confira se os arquivos estão em htdocs/

SESSÃO NÃO FUNCIONA:
  → Verifique permissões da pasta de sessão do PHP
  → Reinicie o Apache no XAMPP

================================================================================
  CARACTERÍSTICAS TÉCNICAS
================================================================================

FRONT-END:
  - HTML5 semântico
  - CSS3 com gradientes e animações
  - Bootstrap 5.3
  - Design responsivo mobile-first
  - Fontes: Press Start 2P, Orbitron

BACK-END:
  - PHP 7.4+ com MySQLi
  - Prepared Statements (segurança SQL injection)
  - Sessões PHP para carrinho e autenticação
  - Password hashing (password_hash)
  - Sanitização de dados

BANCO DE DADOS:
  - MySQL/MariaDB
  - Relacionamentos com Foreign Keys
  - Tabelas normalizadas
  - Índices otimizados

================================================================================
  SEGURANÇA
================================================================================

✅ Prepared Statements em todas as queries
✅ Sanitização de inputs do usuário
✅ Password hashing com bcrypt
✅ Verificação de permissões (admin/cliente)
✅ Proteção contra SQL Injection
✅ Validação de dados no servidor

================================================================================
  PERSONALIZAÇÃO
================================================================================

ALTERAR CORES:
  - Edite as variáveis CSS no início do style.css
  - Principais cores: --neon-cyan, --neon-pink, --neon-yellow

ADICIONAR PRODUTOS:
  - Use o painel admin em /admin/produtos.php
  - Ou insira direto no banco via SQL

MODIFICAR FRETE:
  - Edite a função de cálculo em carrinho.php e checkout.php
  - Valor atual: R$ 30 (grátis acima de R$ 500)

================================================================================
  CRÉDITOS E SUPORTE
================================================================================

Desenvolvido como projeto de e-commerce educacional
Estética inspirada em: Xbox Original, PS2, GameCube, Dreamcast, Xbox 360

Para dúvidas ou suporte:
  - Revise este README
  - Verifique os comentários no código
  - Confira a documentação do PHP e MySQL

================================================================================

🎮 BOM USO E GAME ON! 🎮

================================================================================