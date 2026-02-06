<?php
require_once 'config.php';

// Buscar produtos em destaque
$destaque_query = "SELECT p.*, c.nome as categoria_nome FROM products p 
                   LEFT JOIN categories c ON p.categoria_id = c.id 
                   WHERE p.destaque = TRUE ORDER BY RAND() LIMIT 6";
$destaque_result = $conn->query($destaque_query);

// Buscar categorias
$categories_query = "SELECT * FROM categories";
$categories_result = $conn->query($categories_query);

// Processar busca
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$orderby = isset($_GET['order']) ? sanitize($_GET['order']) : 'nome';

$where = [];
if ($search) {
    $where[] = "(p.nome LIKE '%$search%' OR p.descricao LIKE '%$search%')";
}
if ($categoria > 0) {
    $where[] = "p.categoria_id = $categoria";
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$order_clause = match($orderby) {
    'preco_asc' => 'ORDER BY p.preco ASC',
    'preco_desc' => 'ORDER BY p.preco DESC',
    'nome' => 'ORDER BY p.nome ASC',
    default => 'ORDER BY p.nome ASC'
};

$products_query = "SELECT p.*, c.nome as categoria_nome FROM products p 
                   LEFT JOIN categories c ON p.categoria_id = c.id 
                   $where_clause $order_clause";
$products_result = $conn->query($products_query);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroGames Store - Sua loja de games retrô</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <?php 
    $alert = getAlert();
    if ($alert): 
    ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show retro-alert" role="alert">
            <?= htmlspecialchars($alert['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="retro-title glitch" data-text="RETROGAMES">RETROGAMES</h1>
                    <p class="lead retro-text">Reviva a era de ouro dos videogames! Consoles, jogos e acessórios que marcaram gerações.</p>
                    <a href="#produtos" class="btn btn-retro btn-lg">EXPLORAR LOJA</a>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="console-showcase">
                        <img src="assets/console-collage.png" alt="Consoles" class="img-fluid pixelated">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categorias -->
    <div class="container my-5">
        <h2 class="section-title text-center mb-4">
            <span class="retro-bracket">[</span> CATEGORIAS <span class="retro-bracket">]</span>
        </h2>
        <div class="row g-3">
            <?php while ($cat = $categories_result->fetch_assoc()): ?>
            <div class="col-md-4 col-lg-2">
                <a href="?categoria=<?= $cat['id'] ?>" class="category-card">
                    <div class="category-icon"><?= $cat['icone'] ?></div>
                    <div class="category-name"><?= htmlspecialchars($cat['nome']) ?></div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Produtos em Destaque -->
    <?php if ($destaque_result->num_rows > 0): ?>
    <div class="container my-5">
        <h2 class="section-title text-center mb-4">
            <span class="retro-bracket">&gt;&gt;</span> DESTAQUES DA SEMANA <span class="retro-bracket">&lt;&lt;</span>
        </h2>
        <div class="row g-4">
            <?php while ($product = $destaque_result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="product-card">
                    <div class="product-badge">HOT!</div>
                    <div class="product-image">
                        <img src="assets/products/<?= $product['imagem'] ?>" alt="<?= htmlspecialchars($product['nome']) ?>" onerror="this.src='assets/placeholder.jpg'">
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($product['categoria_nome']) ?></div>
                        <h5 class="product-title"><?= htmlspecialchars($product['nome']) ?></h5>
                        <div class="product-price"><?= formatPrice($product['preco']) ?></div>
                        <div class="product-stock">
                            <?= $product['estoque'] > 0 ? "✓ Em estoque ({$product['estoque']} unidades)" : "✗ Fora de estoque" ?>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="produto.php?id=<?= $product['id'] ?>" class="btn btn-retro">VER DETALHES</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Busca e Filtros -->
    <div class="container my-5" id="produtos">
        <div class="filter-panel">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control retro-input" placeholder="BUSCAR PRODUTOS..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select name="categoria" class="form-select retro-input">
                        <option value="0">Todas Categorias</option>
                        <?php 
                        $categories_result->data_seek(0);
                        while ($cat = $categories_result->fetch_assoc()): 
                        ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoria == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="order" class="form-select retro-input">
                        <option value="nome" <?= $orderby == 'nome' ? 'selected' : '' ?>>Nome A-Z</option>
                        <option value="preco_asc" <?= $orderby == 'preco_asc' ? 'selected' : '' ?>>Menor Preço</option>
                        <option value="preco_desc" <?= $orderby == 'preco_desc' ? 'selected' : '' ?>>Maior Preço</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-retro w-100">FILTRAR</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Listagem de Produtos -->
    <div class="container my-5">
        <?php if ($products_result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while ($product = $products_result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-3">
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/products/<?= $product['imagem'] ?>" alt="<?= htmlspecialchars($product['nome']) ?>" onerror="this.src='assets/placeholder.jpg'">
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($product['categoria_nome']) ?></div>
                        <h5 class="product-title"><?= htmlspecialchars($product['nome']) ?></h5>
                        <div class="product-price"><?= formatPrice($product['preco']) ?></div>
                        <div class="product-stock">
                            <?= $product['estoque'] > 0 ? "✓ Disponível" : "✗ Esgotado" ?>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="produto.php?id=<?= $product['id'] ?>" class="btn btn-retro btn-sm">VER MAIS</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <div class="retro-box p-5">
                <h3>😢 NENHUM PRODUTO ENCONTRADO</h3>
                <p>Tente ajustar os filtros de busca</p>
                <a href="index.php" class="btn btn-retro mt-3">VOLTAR À LOJA</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>