<?php
require_once 'config.php';

// Verificar se está logado
if (!isLoggedIn()) {
    showAlert("Faça login para finalizar a compra!", "warning");
    redirect('login.php');
}

// Verificar se há itens no carrinho
if (empty($_SESSION['carrinho'])) {
    redirect('carrinho.php');
}

// Buscar produtos do carrinho
$cart_items = [];
$subtotal = 0;

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

// Calcular frete
$frete = $subtotal >= 500 ? 0 : 30;
$total = $subtotal + $frete;

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Processar finalização
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_pedido'])) {
    $endereco = sanitize($_POST['endereco']);
    $cidade = sanitize($_POST['cidade']);
    $estado = sanitize($_POST['estado']);
    $cep = sanitize($_POST['cep']);
    $metodo_pagamento = sanitize($_POST['metodo_pagamento']);
    
    if (empty($endereco) || empty($cidade) || empty($estado) || empty($cep)) {
        showAlert("Preencha todos os campos de endereço!", "danger");
    } else {
        // Atualizar endereço do usuário
        $stmt = $conn->prepare("UPDATE users SET endereco = ?, cidade = ?, estado = ?, cep = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $endereco, $cidade, $estado, $cep, $_SESSION['user_id']);
        $stmt->execute();
        
        // Criar pedido
        $endereco_completo = "$endereco, $cidade - $estado, CEP: $cep";
        $stmt = $conn->prepare("INSERT INTO orders (cliente_id, total, frete, status, metodo_pagamento, endereco_entrega) VALUES (?, ?, ?, 'Pendente', ?, ?)");
        $stmt->bind_param("iddss", $_SESSION['user_id'], $total, $frete, $metodo_pagamento, $endereco_completo);
        
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            
            // Adicionar itens do pedido
            $stmt_item = $conn->prepare("INSERT INTO order_items (pedido_id, produto_id, quantidade, valor_unitario) VALUES (?, ?, ?, ?)");
            
            foreach ($cart_items as $item) {
                $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantidade'], $item['preco']);
                $stmt_item->execute();
                
                // Atualizar estoque
                $novo_estoque = $item['estoque'] - $item['quantidade'];
                $stmt_estoque = $conn->prepare("UPDATE products SET estoque = ? WHERE id = ?");
                $stmt_estoque->bind_param("ii", $novo_estoque, $item['id']);
                $stmt_estoque->execute();
            }
            
            // Limpar carrinho
            $_SESSION['carrinho'] = [];
            
            // Redirecionar para página de pagamento
            redirect("pagamento.php?pedido=$order_id");
        } else {
            showAlert("Erro ao processar pedido. Tente novamente.", "danger");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - RetroGames Store</title>
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
            <span class="retro-bracket">💳</span> FINALIZAR COMPRA <span class="retro-bracket">💳</span>
        </h2>
        
        <form method="POST">
            <div class="row g-4">
                <!-- Dados de Entrega -->
                <div class="col-lg-8">
                    <div class="retro-box p-4 mb-4">
                        <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px;">
                            📍 DADOS DE ENTREGA
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                    RUA E NÚMERO
                                </label>
                                <input type="text" 
                                       name="endereco" 
                                       class="form-control retro-input" 
                                       required
                                       value="<?= htmlspecialchars($user['endereco'] ?? '') ?>"
                                       placeholder="Ex: Rua das Flores, 123">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                    CIDADE
                                </label>
                                <input type="text" 
                                       name="cidade" 
                                       class="form-control retro-input" 
                                       required
                                       value="<?= htmlspecialchars($user['cidade'] ?? '') ?>"
                                       placeholder="Ex: São Paulo">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                    ESTADO
                                </label>
                                <input type="text" 
                                       name="estado" 
                                       class="form-control retro-input" 
                                       required
                                       maxlength="2"
                                       value="<?= htmlspecialchars($user['estado'] ?? '') ?>"
                                       placeholder="SP">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                    CEP
                                </label>
                                <input type="text" 
                                       name="cep" 
                                       class="form-control retro-input" 
                                       required
                                       value="<?= htmlspecialchars($user['cep'] ?? '') ?>"
                                       placeholder="00000-000">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Método de Pagamento -->
                    <div class="retro-box p-4">
                        <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px;">
                            💰 MÉTODO DE PAGAMENTO
                        </h4>
                        
                        <div class="payment-methods">
                            <div class="form-check payment-option">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" id="cartao" value="Cartão de Crédito" checked>
                                <label class="form-check-label" for="cartao">
                                    💳 <strong>CARTÃO DE CRÉDITO</strong>
                                    <small>Simulação de pagamento</small>
                                </label>
                            </div>
                            
                            <div class="form-check payment-option">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" id="pix" value="PIX">
                                <label class="form-check-label" for="pix">
                                    📱 <strong>PIX</strong>
                                    <small>Aprovação instantânea</small>
                                </label>
                            </div>
                            
                            <div class="form-check payment-option">
                                <input class="form-check-input" type="radio" name="metodo_pagamento" id="boleto" value="Boleto">
                                <label class="form-check-label" for="boleto">
                                    📄 <strong>BOLETO BANCÁRIO</strong>
                                    <small>Vencimento em 3 dias</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Resumo do Pedido -->
                <div class="col-lg-4">
                    <div class="retro-box p-4">
                        <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px; text-align: center;">
                            📊 RESUMO DO PEDIDO
                        </h4>
                        
                        <div class="order-items mb-3">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="assets/products/<?= $item['imagem'] ?>" 
                                         alt="<?= htmlspecialchars($item['nome']) ?>"
                                         style="width: 50px; height: 50px; object-fit: contain; margin-right: 10px; border: 2px solid var(--neon-cyan); border-radius: 5px;"
                                         onerror="this.src='assets/placeholder.jpg'">
                                    <div style="flex: 1;">
                                        <small style="color: white; display: block;">
                                            <?= htmlspecialchars($item['nome']) ?>
                                        </small>
                                        <small style="color: var(--neon-pink);">
                                            <?= $item['quantidade'] ?>x <?= formatPrice($item['preco']) ?>
                                        </small>
                                    </div>
                                    <strong style="color: var(--neon-green);">
                                        <?= formatPrice($item['subtotal']) ?>
                                    </strong>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr style="border-color: var(--neon-cyan); opacity: 0.3;">
                        
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span><?= formatPrice($subtotal) ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Frete:</span>
                                <span><?= $frete > 0 ? formatPrice($frete) : '<span style="color: var(--neon-green);">GRÁTIS</span>' ?></span>
                            </div>
                            
                            <hr style="border-color: var(--neon-cyan); opacity: 0.5;">
                            
                            <div class="summary-row total-row">
                                <span>TOTAL:</span>
                                <span><?= formatPrice($total) ?></span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="finalizar_pedido" class="btn btn-retro btn-lg">
                                ✅ CONFIRMAR PEDIDO
                            </button>
                            <a href="carrinho.php" class="btn" style="background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan); font-weight: 900;">
                                ← VOLTAR AO CARRINHO
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
.payment-option {
    background: rgba(0, 255, 255, 0.05);
    border: 2px solid var(--neon-cyan);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.payment-option:hover {
    background: rgba(0, 255, 255, 0.1);
    border-color: var(--neon-pink);
}

.payment-option .form-check-input:checked ~ .form-check-label {
    color: var(--neon-yellow);
}

.payment-option label {
    color: white;
    font-size: 1.1rem;
    cursor: pointer;
    display: block;
    margin-left: 10px;
}

.payment-option label small {
    display: block;
    color: var(--neon-cyan);
    margin-top: 5px;
    font-size: 0.85rem;
}

.order-item {
    border-bottom: 1px solid rgba(0, 255, 255, 0.2);
    padding-bottom: 10px;
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