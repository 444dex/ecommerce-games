<?php
require_once 'config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    redirect('index.php');
}

// Buscar produto
$stmt = $conn->prepare("SELECT p.*, c.nome as categoria_nome 
                        FROM products p 
                        LEFT JOIN categories c ON p.categoria_id = c.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('index.php');
}

$product = $result->fetch_assoc();

// Adicionar ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
    
    if ($quantidade > 0 && $quantidade <= $product['estoque']) {
        if (isset($_SESSION['carrinho'][$product_id])) {
            $_SESSION['carrinho'][$product_id] += $quantidade;
        } else {
            $_SESSION['carrinho'][$product_id] = $quantidade;
        }
        
        // Verificar se não excede estoque
        if ($_SESSION['carrinho'][$product_id] > $product['estoque']) {
            $_SESSION['carrinho'][$product_id] = $product['estoque'];
        }
        
        showAlert("Produto adicionado ao carrinho!", "success");
        redirect('carrinho.php');
    } else {
        showAlert("Quantidade inválida!", "danger");
    }
}

// Produtos relacionados
$related_query = "SELECT * FROM products 
                  WHERE categoria_id = ? AND id != ? AND estoque > 0 
                  ORDER BY RAND() LIMIT 4";
$stmt_related = $conn->prepare($related_query);
$stmt_related->bind_param("ii", $product['categoria_id'], $product_id);
$stmt_related->execute();
$related_result = $stmt_related->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['nome']) ?> - RetroGames Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container my-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="background: transparent;">
                <li class="breadcrumb-item"><a href="index.php" style="color: var(--neon-cyan);">Home</a></li>
                <li class="breadcrumb-item"><a href="index.php?categoria=<?= $product['categoria_id'] ?>" style="color: var(--neon-cyan);"><?= htmlspecialchars($product['categoria_nome']) ?></a></li>
                <li class="breadcrumb-item active" style="color: var(--neon-pink);"><?= htmlspecialchars($product['nome']) ?></li>
            </ol>
        </nav>
        
        <div class="row g-4">
            <!-- Imagem do Produto -->
            <div class="col-lg-6">
                <div class="retro-box p-4">
                    <div class="product-detail-image">
                        <img src="assets/products/<?= $product['imagem'] ?>" 
                             alt="<?= htmlspecialchars($product['nome']) ?>" 
                             class="img-fluid pixelated"
                             onerror="this.src='assets/placeholder.jpg'">
                    </div>
                </div>
            </div>
            
            <!-- Informações do Produto -->
            <div class="col-lg-6">
                <div class="retro-box p-4">
                    <div class="product-category mb-2" style="font-size: 1rem;">
                        <?= htmlspecialchars($product['categoria_nome']) ?>
                    </div>
                    
                    <h1 class="mb-3" style="color: white; font-size: 2rem; font-weight: 900;">
                        <?= htmlspecialchars($product['nome']) ?>
                    </h1>
                    
                    <div class="product-price mb-4" style="font-size: 3rem;">
                        <?= formatPrice($product['preco']) ?>
                    </div>
                    
                    <div class="mb-4">
                        <?php if ($product['estoque'] > 0): ?>
                            <span class="badge" style="background: var(--xbox-green); font-size: 1rem; padding: 10px 20px;">
                                ✓ EM ESTOQUE (<?= $product['estoque'] ?> unidades)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger" style="font-size: 1rem; padding: 10px 20px;">
                                ✗ FORA DE ESTOQUE
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-description mb-4">
                        <h5 style="color: var(--neon-cyan); font-weight: 900; margin-bottom: 15px;">
                            DESCRIÇÃO
                        </h5>
                        <p style="color: white; line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($product['descricao'])) ?>
                        </p>
                    </div>
                    
                    <?php if ($product['estoque'] > 0): ?>
                    <form method="POST" class="mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                    QUANTIDADE
                                </label>
                                <input type="number" 
                                       name="quantidade" 
                                       class="form-control retro-input" 
                                       value="1" 
                                       min="1" 
                                       max="<?= $product['estoque'] ?>"
                                       style="text-align: center; font-size: 1.2rem; font-weight: 900;">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" style="opacity: 0;">.</label>
                                <button type="submit" name="add_to_cart" class="btn btn-retro btn-lg w-100">
                                    🛒 ADICIONAR AO CARRINHO
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-warning" style="border: 3px solid var(--dreamcast-orange);">
                        <strong>😢 Produto esgotado!</strong> Volte em breve para conferir novidades.
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-4 p-3" style="background: rgba(0, 255, 255, 0.1); border: 2px solid var(--neon-cyan); border-radius: 10px;">
                        <h6 style="color: var(--neon-yellow); font-weight: 900;">🚚 INFORMAÇÕES DE ENTREGA</h6>
                        <ul style="color: white; margin: 10px 0 0 0; padding-left: 20px;">
                            <li>Frete grátis para compras acima de R$ 500</li>
                            <li>Entrega em até 7 dias úteis</li>
                            <li>Produto com garantia de 90 dias</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Produtos Relacionados -->
        <?php if ($related_result->num_rows > 0): ?>
        <div class="mt-5">
            <h2 class="section-title text-center mb-4">
                <span class="retro-bracket">[</span> PRODUTOS RELACIONADOS <span class="retro-bracket">]</span>
            </h2>
            
            <div class="row g-4">
                <?php while ($related = $related_result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/products/<?= $related['imagem'] ?>" 
                                 alt="<?= htmlspecialchars($related['nome']) ?>"
                                 onerror="this.src='assets/placeholder.jpg'">
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><?= htmlspecialchars($related['nome']) ?></h5>
                            <div class="product-price"><?= formatPrice($related['preco']) ?></div>
                            <div class="d-grid gap-2 mt-3">
                                <a href="produto.php?id=<?= $related['id'] ?>" class="btn btn-retro btn-sm">
                                    VER DETALHES
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
.product-detail-image {
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
    border: 3px solid var(--neon-cyan);
    border-radius: 10px;
    padding: 30px;
}

.product-detail-image img {
    max-width: 100%;
    max-height: 500px;
    object-fit: contain;
    filter: drop-shadow(0 0 30px rgba(0, 255, 255, 0.5));
}
</style>