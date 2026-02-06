<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$order_id = isset($_GET['pedido']) ? (int)$_GET['pedido'] : 0;

if ($order_id <= 0) {
    redirect('index.php');
}

// Buscar pedido
$stmt = $conn->prepare("SELECT o.*, u.nome as cliente_nome, u.email as cliente_email 
                        FROM orders o 
                        LEFT JOIN users u ON o.cliente_id = u.id 
                        WHERE o.id = ? AND o.cliente_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('index.php');
}

$order = $result->fetch_assoc();

// Buscar itens do pedido
$stmt = $conn->prepare("SELECT oi.*, p.nome, p.imagem 
                        FROM order_items oi 
                        LEFT JOIN products p ON oi.produto_id = p.id 
                        WHERE oi.pedido_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Processar pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pagamento'])) {
    // Atualizar status do pedido
    $stmt = $conn->prepare("UPDATE orders SET status = 'Pago', data_pagamento = NOW() WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    showAlert("Pagamento confirmado! Pedido #$order_id processado com sucesso!", "success");
    redirect('pedidos.php');
}

// Gerar códigos simulados
$pix_code = 'PIX' . strtoupper(substr(md5($order_id), 0, 32));
$boleto_code = str_pad($order_id, 14, '0', STR_PAD_LEFT) . '0' . date('Ymd');
$card_code = 'CARD-' . strtoupper(substr(md5($order_id . 'card'), 0, 16));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento - RetroGames Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container my-5">
        <h2 class="section-title text-center mb-5">
            <span class="retro-bracket">💳</span> PAGAMENTO SIMULADO <span class="retro-bracket">💳</span>
        </h2>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="retro-box p-5 text-center">
                    <div class="mb-4">
                        <h3 style="color: var(--neon-yellow); font-family: 'Press Start 2P', cursive; font-size: 1.2rem;">
                            PEDIDO #<?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?>
                        </h3>
                        <p style="color: white; font-size: 1.1rem; margin-top: 10px;">
                            Cliente: <strong><?= htmlspecialchars($order['cliente_nome']) ?></strong>
                        </p>
                    </div>
                    
                    <div class="mb-4 p-4" style="background: rgba(0, 255, 255, 0.1); border: 2px solid var(--neon-cyan); border-radius: 10px;">
                        <h4 style="color: var(--neon-pink); margin-bottom: 15px;">VALOR TOTAL</h4>
                        <div style="font-size: 3rem; font-weight: 900; color: var(--neon-green); text-shadow: 0 0 20px var(--neon-green);">
                            <?= formatPrice($order['total']) ?>
                        </div>
                    </div>
                    
                    <!-- Informações de Pagamento por Método -->
                    <div class="payment-info p-4 mb-4" style="background: #16213e; border: 3px solid var(--neon-yellow); border-radius: 15px; text-align: left;">
                        <h5 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px; text-align: center;">
                            MÉTODO: <?= strtoupper($order['metodo_pagamento']) ?>
                        </h5>
                        
                        <?php if ($order['metodo_pagamento'] === 'PIX'): ?>
                        <div class="pix-payment">
                            <p style="color: white; margin-bottom: 15px;">
                                <strong>📱 Escaneie o QR Code ou copie o código PIX:</strong>
                            </p>
                            <div class="qr-code mb-3" style="background: white; padding: 20px; border-radius: 10px; display: inline-block;">
                                <div style="width: 200px; height: 200px; background: #000; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                    QR CODE
                                </div>
                            </div>
                            <div class="pix-code p-3" style="background: #0a0a0a; border: 2px solid var(--neon-cyan); border-radius: 5px; font-family: monospace; word-break: break-all; color: var(--neon-cyan);">
                                <?= $pix_code ?>
                            </div>
                            <p style="color: var(--neon-yellow); margin-top: 15px; font-size: 0.9rem;">
                                ⏰ O código PIX expira em 30 minutos
                            </p>
                        </div>
                        
                        <?php elseif ($order['metodo_pagamento'] === 'Boleto'): ?>
                        <div class="boleto-payment">
                            <p style="color: white; margin-bottom: 15px;">
                                <strong>📄 Código de Barras do Boleto:</strong>
                            </p>
                            <div class="barcode mb-3" style="background: white; padding: 30px; border-radius: 10px;">
                                <div style="height: 80px; background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px); border-radius: 5px;"></div>
                            </div>
                            <div class="boleto-code p-3" style="background: #0a0a0a; border: 2px solid var(--neon-cyan); border-radius: 5px; font-family: monospace; color: var(--neon-cyan); letter-spacing: 2px;">
                                <?= $boleto_code ?>
                            </div>
                            <p style="color: var(--neon-yellow); margin-top: 15px; font-size: 0.9rem;">
                                ⏰ Vencimento: <?= date('d/m/Y', strtotime('+3 days')) ?>
                            </p>
                        </div>
                        
                        <?php else: // Cartão de Crédito ?>
                        <div class="card-payment">
                            <p style="color: white; margin-bottom: 15px;">
                                <strong>💳 Código de Autorização:</strong>
                            </p>
                            <div class="card-code p-4 mb-3" style="background: linear-gradient(135deg, var(--ps2-blue), var(--gamecube-purple)); border-radius: 15px; color: white; font-family: monospace; font-size: 1.5rem; letter-spacing: 3px; text-align: center; font-weight: 900;">
                                <?= $card_code ?>
                            </div>
                            <p style="color: var(--neon-green); margin-top: 15px; font-size: 0.9rem;">
                                ✅ Transação aprovada instantaneamente
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Resumo dos Itens -->
                    <div class="order-items-summary mb-4 p-4" style="background: rgba(0, 255, 255, 0.05); border: 2px solid var(--neon-cyan); border-radius: 10px; text-align: left;">
                        <h5 style="color: var(--neon-cyan); font-weight: 900; margin-bottom: 15px;">
                            📦 ITENS DO PEDIDO
                        </h5>
                        <?php foreach ($items as $item): ?>
                        <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid rgba(0, 255, 255, 0.2);">
                            <img src="assets/products/<?= $item['imagem'] ?>" 
                                 alt="<?= htmlspecialchars($item['nome']) ?>"
                                 style="width: 60px; height: 60px; object-fit: contain; margin-right: 15px; border: 2px solid var(--neon-cyan); border-radius: 5px;"
                                 onerror="this.src='assets/placeholder.jpg'">
                            <div style="flex: 1;">
                                <strong style="color: white; display: block;"><?= htmlspecialchars($item['nome']) ?></strong>
                                <small style="color: var(--neon-pink);">
                                    Quantidade: <?= $item['quantidade'] ?> x <?= formatPrice($item['valor_unitario']) ?>
                                </small>
                            </div>
                            <strong style="color: var(--neon-green);">
                                <?= formatPrice($item['quantidade'] * $item['valor_unitario']) ?>
                            </strong>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Botão de Confirmação Simulada -->
                    <form method="POST">
                        <div class="alert alert-warning mb-4" style="border: 3px solid var(--dreamcast-orange);">
                            <strong>⚠️ SIMULAÇÃO DE PAGAMENTO</strong><br>
                            Em um ambiente real, o pagamento seria processado automaticamente.<br>
                            Clique no botão abaixo para simular a confirmação do pagamento.
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="confirmar_pagamento" class="btn btn-retro btn-lg">
                                ✅ SIMULAR PAGAMENTO CONFIRMADO
                            </button>
                            <a href="pedidos.php" class="btn" style="background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan); font-weight: 900;">
                                📋 VER MEUS PEDIDOS
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>