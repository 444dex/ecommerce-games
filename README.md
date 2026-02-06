# RetroGames Store - E-commerce de Video Games

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5"/>
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3"/>
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License"/>
</p>

<p align="center">
  <strong>Protótipo funcional de loja virtual especializada em video games</strong>
</p>

<p align="center">
  Um e-commerce completo desenvolvido para praticar conceitos de desenvolvimento web, incluindo catálogo de produtos, carrinho de compras, sistema de busca e interface responsiva.
</p>

---

## Sobre o Projeto

**RetroGames Store** é um protótipo de e-commerce especializado em video games, desenvolvido para demonstrar habilidades em:
- Desenvolvimento web full-stack com PHP
- Design responsivo e experiência do usuário
- Manipulação de sessões e cookies
- Estruturação de projetos web
- Integração frontend/backend

Este projeto simula uma loja virtual real, com todas as funcionalidades essenciais de um e-commerce moderno.

---

## Funcionalidades

### Catálogo de Produtos
- Listagem completa de jogos disponíveis
- Detalhes do produto (nome, preço, descrição, plataforma)
- Imagens dos produtos
- Categorização por console/plataforma
- Ordenação (preço, nome, lançamento)

### Sistema de Busca e Filtros
- Busca por nome do jogo
- Filtro por plataforma (PlayStation, Xbox, Nintendo, PC)
- Filtro por faixa de preço
- Filtro por gênero (ação, aventura, RPG, esportes, etc.)

### Carrinho de Compras
- Adicionar/remover produtos
- Atualizar quantidades
- Cálculo automático do total
- Persistência de dados com sessão PHP
- Resumo do pedido

### Checkout (Simulado)
- Formulário de dados do cliente
- Seleção de método de pagamento
- Confirmação do pedido
- Página de agradecimento

### Interface
- Design moderno e responsivo
- Compatível com mobile, tablet e desktop
- Animações CSS suaves
- Navegação intuitiva
- Tema gamer (cores vibrantes, fontes modernas)

---

## Tecnologias Utilizadas

| Tecnologia | Descrição | Uso no Projeto |
|------------|-----------|----------------|
| **PHP 7.4+** | Linguagem server-side | Backend, lógica de negócio, sessões |
| **HTML5** | Estrutura das páginas | Markup semântico |
| **CSS3** | Estilização | Design responsivo, animações |
| **JavaScript** | Interatividade | Carrinho dinâmico, validações |
| **MySQL** (opcional) | Banco de dados | Persistência de produtos e pedidos |

---

## Estrutura do Projeto

```
ecommerce-games/
├── retrogames_store/
│   ├── index.php              # Página inicial
│   ├── produtos.php           # Catálogo de produtos
│   ├── carrinho.php           # Carrinho de compras
│   ├── checkout.php           # Finalização de pedido
│   ├── produto_detalhes.php   # Detalhes de um produto
│   │
│   ├── css/
│   │   ├── style.css          # Estilos principais
│   │   ├── responsive.css     # Media queries
│   │   └── cart.css           # Estilos do carrinho
│   │
│   ├── js/
│   │   ├── main.js            # Scripts principais
│   │   └── cart.js            # Lógica do carrinho
│   │
│   ├── assets/
│   │   ├── images/            # Imagens dos produtos
│   │   └── icons/             # Ícones da interface
│   │
│   ├── includes/
│   │   ├── header.php         # Cabeçalho
│   │   ├── footer.php         # Rodapé
│   │   └── db_connection.php  # Conexão com banco (se usar)
│   │
│   └── config/
│       └── config.php         # Configurações gerais
│
└── README.md
```

---

## Como Executar

### Pré-requisitos
- **XAMPP**, **WAMP** ou **LAMP** (Apache + PHP)
- Navegador web moderno
- (Opcional) MySQL/MariaDB se for usar banco de dados

### Passo 1: Clone o Repositório
```bash
git clone https://github.com/444dex/ecommerce-games.git
cd ecommerce-games
```

### Passo 2: Configure o Servidor Local

#### Usando XAMPP (Windows/Mac/Linux)
```bash
# Copie a pasta para o diretório do Apache
cp -r retrogames_store /xampp/htdocs/

# Ou no Windows:
# Copie retrogames_store para C:\xampp\htdocs\
```

#### Usando WAMP (Windows)
```bash
# Copie para:
C:\wamp64\www\retrogames_store
```

### Passo 3: (Opcional) Configure o Banco de Dados
Se o projeto usar banco de dados:
```sql
-- Crie o banco de dados
CREATE DATABASE retrogames_store;

-- Importe o SQL (se houver arquivo)
mysql -u root -p retrogames_store < database.sql
```

Edite `config/config.php` com suas credenciais:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'retrogames_store');
```

### Passo 4: Inicie o Servidor
```bash
# XAMPP: Inicie Apache pelo painel de controle

# Ou via terminal (se tiver PHP standalone):
php -S localhost:8000
```

### Passo 5: Acesse no Navegador
```
http://localhost/retrogames_store
# ou
http://localhost:8000
```

---

## Exemplos de Produtos

O catálogo inclui jogos clássicos e modernos, como:

| Jogo | Plataforma | Preço | Gênero |
|------|-----------|-------|--------|
| The Last of Us Part II | PlayStation 5 | R$ 249,90 | Ação/Aventura |
| God of War Ragnarök | PlayStation 5 | R$ 299,90 | Ação/Aventura |
| Halo Infinite | Xbox Series X | R$ 249,90 | FPS |
| Zelda: Tears of the Kingdom | Nintendo Switch | R$ 349,90 | Aventura |
| Elden Ring | PC | R$ 199,90 | RPG |
| FIFA 24 | Multi-plataforma | R$ 299,90 | Esportes |

---

## Funcionalidades Implementadas vs. Planejadas

### Implementado
- [x] Catálogo de produtos
- [x] Sistema de busca
- [x] Carrinho de compras
- [x] Filtros por categoria
- [x] Design responsivo
- [x] Sessões PHP

### Em Desenvolvimento
- [ ] Sistema de avaliações e comentários
- [ ] Wishlist (lista de desejos)
- [ ] Histórico de pedidos
- [ ] Imagem dos produtos

### Futuras Melhorias
- [ ] API RESTful
- [ ] Sistema de recomendação de jogos
- [ ] Chat de suporte ao cliente
- [ ] Cupons de desconto
- [ ] Sistema de pontos/fidelidade
- [ ] Notificações por email
- [ ] Rastreamento de entrega

---

## Paleta de Cores

```css
/* Tema Gamer */
:root {
  --primary-color: #6C5CE7;      /* Roxo vibrante */
  --secondary-color: #00D4FF;    /* Azul neon */
  --accent-color: #FF6B6B;       /* Vermelho */
  --dark-bg: #1A1A2E;            /* Fundo escuro */
  --light-text: #FFFFFF;         /* Texto claro */
  --card-bg: #16213E;            /* Cards */
}
```

---

## Testando o Projeto

### Teste Manual
1. Navegue pelo catálogo
2. Adicione produtos ao carrinho
3. Atualize quantidades
4. Remova itens
5. Finalize uma compra simulada
6. Teste os filtros e busca

### Teste Responsivo
- Abra o DevTools do navegador (F12)
- Teste em diferentes resoluções:
  - Mobile: 375px, 414px
  - Tablet: 768px, 1024px
  - Desktop: 1440px, 1920px

---

## Contribuindo

Contribuições são bem-vindas! Se você quer melhorar este projeto:

1. Fork este repositório
2. Crie uma branch para sua feature (`git checkout -b feature/NovaFuncionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/NovaFuncionalidade`)
5. Abra um Pull Request

---

## Aprendizados

Este projeto me permitiu praticar:
- **PHP**: Sessões, cookies, manipulação de dados
- **Frontend**: HTML semântico, CSS responsivo, JavaScript
- **UX/UI**: Design de e-commerce, experiência do usuário
- **Arquitetura**: Organização de código, separação de responsabilidades
- **Boas práticas**: Código limpo, comentários, documentação

---

## Licença

Este projeto está sob a licença **MIT**. Sinta-se livre para usar, modificar e distribuir.

---

## Autor

**444dex**
- GitHub: [Miguel "444dex" Kuipers](https://github.com/444dex)
- LinkedIn: [Miguel Kuipers](https://www.linkedin.com/in/miguel-erick-assun%C3%A7%C3%A3o-kuipers-9665382b4)

---

## Agradecimentos

- Inspiração de design: Steam, Epic Games Store, PlayStation Store
- Imagens de produtos: Divulgação oficial dos jogos
- Ícones: Font Awesome / Material Icons

---

<p align="center">
  <sub>Se este projeto foi útil, considere dar uma ⭐!</sub>
</p>

---
