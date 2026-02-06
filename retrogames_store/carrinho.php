<?php
require_once 'config.php';

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['atualizar_carrinho'])) {
        foreach ($_POST['quantidade'] as $product_id => $quantidade) {
            $quantidade = (int)$quantidade;
            
            if ($quantidade <= 0) {
                unset($_SESSION['carrinho'][$product_id]);
            } else {
                // Verificar estoque
                $stmt = $conn->prepare("SELECT estoque FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $max_estoque = $row['estoque'];
                    $_SESSION['carrinho'][$product_id] = min($quantidade, $max_estoque);
                }
            }
        }
        showAlert("Carrinho atualizado!", "success");
        redirect('carrinho.php');
    }
    
    if (isset($_POST['remover_item'])) {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['carrinho'][$product_id]);
        showAlert("Item removido do carrinho!", "info");
        redirect('carrinho.php');
    }
    
    if (isset($_POST['limpar_carrinho'])) {
        $_SESSION['carrinho'] = [];
        showAlert("Carrinho esvaziado!", "info");
        redirect('carrinho.php');
    }
}

// Buscar produtos do carrinho
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = array_keys($_SESSION['carrinho']);
    $ids_placeholder = implode(',', array_fill(0, count($ids), '?'));
    
    $query = "SELECT * FROM products WHERE id IN ($ids_placeholder)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($product = $result->fetch_assoc()) {
        $product['quantidade'] = $_SESSION['carrinho'][$product['id']];
        $product['subtotal'] = $product['preco'] * $product['quantidade'];
        $subtotal += $product['subtotal'];
        $cart_items[] = $product;
    }
}

// Calcular frete
$frete = $subtotal >= 500 ? 0 : 30;
$total = $subtotal + $frete;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - RetroGames Store</title>
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
    
    <div class="container my-5">
        <h2 class="section-title text-center mb-5">
            <span class="retro-bracket">🛒</span> MEU CARRINHO <span class="retro-bracket">🛒</span>
        </h2>
        
        <?php if (empty($cart_items)): ?>
        <div class="text-center py-5">
            <div class="retro-box p-5">
                <h3 style="color: white; font-size: 2rem;">😢 SEU CARRINHO ESTÁ VAZIO</h3>
                <p style="color: var(--neon-cyan); font-size: 1.2rem; margin: 20px 0;">
                    Que tal adicionar alguns produtos incríveis?
                </p>
                <a href="index.php" class="btn btn-retro btn-lg mt-3">
                    🎮 EXPLORAR PRODUTOS
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <!-- Produtos no Carrinho -->
            <div class="col-lg-8">
                <div class="retro-box p-4">
                    <form method="POST">
                        <div class="table-responsive">
                            <table class="table cart-table">
                                <thead>
                                    <tr>
                                        <th style="color: var(--neon-cyan);">PRODUTO</th>
                                        <th style="color: var(--neon-cyan);">PREÇO</th>
                                        <th style="color: var(--neon-cyan);">QTDE</th>
                                        <th style="color: var(--neon-cyan);">SUBTOTAL</th>
                                        <th style="color: var(--neon-cyan);">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="assets/products/<?= $item['imagem'] ?>" 
                                                     alt="<?= htmlspecialchars($item['nome']) ?>"
                                                     style="width: 80px; height: 80px; object-fit: contain; margin-right: 15px; border: 2px solid var(--neon-cyan); border-radius: 5px;"
                                                     onerror="this.src='assets/placeholder.jpg'">
                                                <div>
                                                    <strong style="color: white; display: block;">
                                                        <?= htmlspecialchars($item['nome']) ?>
                                                    </strong>
                                                    <small style="color: var(--neon-pink);">
                                                        Estoque: <?= $item['estoque'] ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="color: var(--neon-green); font-weight: 900; vertical-align: middle;">
                                            <?= formatPrice($item['preco']) ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <input type="number" 
                                                   name="quantidade[<?= $item['id'] ?>]" 
                                                   value="<?= $item['quantidade'] ?>"
                                                   min="1"
                                                   max="<?= $item['estoque'] ?>"
                                                   class="form-control retro-input"
                                                   style="width: 80px; text-align: center;">
                                        </td>
                                        <td style="color: var(--neon-yellow); font-weight: 900; font-size: 1.2rem; vertical-align: middle;">
                                            <?= formatPrice($item['subtotal']) ?>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <button type="submit" 
                                                    name="remover_item"
                                                    value="<?= $item['id'] ?>"
                                                    class="btn btn-sm"
                                                    style="background: #dc3545; color: white; border: 2px solid var(--neon-pink);"
                                                    onclick="return confirm('Remover este item?')">
                                                ❌
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" name="atualizar_carrinho" class="btn btn-retro">
                                🔄 ATUALIZAR CARRINHO
                            </button>
                            <button type="submit" 
                                    name="limpar_carrinho" 
                                    class="btn"
                                    style="background: #dc3545; color: white; border: 2px solid var(--neon-pink); font-weight: 900;"
                                    onclick="return confirm('Esvaziar carrinho?')">
                                🗑️ LIMPAR CARRINHO
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Resumo do Pedido -->
            <div class="col-lg-4">
                <div class="retro-box p-4">
                    <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px; text-align: center;">
                        📊 RESUMO DO PEDIDO
                    </h4>
                    
                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span><?= formatPrice($subtotal) ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Frete:</span>
                            <span><?= $frete > 0 ? formatPrice($frete) : '<span style="color: var(--neon-green);">GRÁTIS</span>' ?></span>
                        </div>
                        
                        <?php if ($subtotal >= 500): ?>
                        <div class="alert alert-success mt-3 mb-3" style="border: 2px solid var(--neon-green);">
                            <strong>🎉 Frete Grátis!</strong> Você ganhou frete grátis!
                        </div>
                        <?php elseif ($subtotal >= 300): ?>
                        <div class="alert alert-warning mt-3 mb-3" style="border: 2px solid var(--neon-yellow);">
                            Faltam <?= formatPrice(500 - $subtotal) ?> para frete grátis!
                        </div>
                        <?php endif; ?>
                        
                        <hr style="border-color: var(--neon-cyan); opacity: 0.5;">
                        
                        <div class="summary-row total-row">
                            <span>TOTAL:</span>
                            <span><?= formatPrice($total) ?></span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <?php if (isLoggedIn()): ?>
                            <a href="checkout.php" class="btn btn-retro btn-lg">
                                💳 FINALIZAR COMPRA
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-retro btn-lg">
                                🔑 FAZER LOGIN PARA CONTINUAR
                            </a>
                            <small style="color: white; text-align: center; margin-top: 10px;">
                                Você precisa estar logado para finalizar a compra
                            </small>
                        <?php endif; ?>
                        
                        <a href="index.php" class="btn" style="background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan); font-weight: 900;">
                            ← CONTINUAR COMPRANDO
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
.cart-table {
    color: white;
}

.cart-table th {
    border-bottom: 2px solid var(--neon-cyan);
    padding: 15px 10px;
    font-weight: 900;
    text-transform: uppercase;
}

.cart-table td {
    border-bottom: 1px solid rgba(0, 255, 255, 0.2);
    padding: 15px 10px;
}

.order-summary .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    color: white;
    font-size: 1.1rem;
}

.order-summary .total-row {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--neon-yellow);
    margin-top: 10px;
}
</style>