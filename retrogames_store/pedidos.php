<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Buscar pedidos do usuário
$stmt = $conn->prepare("SELECT * FROM orders WHERE cliente_id = ? ORDER BY data_criacao DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Função para cor do status
function getStatusColor($status) {
    return match($status) {
        'Pendente' => 'warning',
        'Pago' => 'success',
        'Enviado' => 'info',
        'Entregue' => 'primary',
        'Cancelado' => 'danger',
        default => 'secondary'
    };
}

function getStatusIcon($status) {
    return match($status) {
        'Pendente' => '⏳',
        'Pago' => '✅',
        'Enviado' => '🚚',
        'Entregue' => '📦',
        'Cancelado' => '❌',
        default => '❓'
    };
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - RetroGames Store</title>
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
            <span class="retro-bracket">📦</span> MEUS PEDIDOS <span class="retro-bracket">📦</span>
        </h2>
        
        <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <div class="retro-box p-5">
                <h3 style="color: white; font-size: 2rem;">📭 NENHUM PEDIDO ENCONTRADO</h3>
                <p style="color: var(--neon-cyan); font-size: 1.2rem; margin: 20px 0;">
                    Você ainda não fez nenhum pedido em nossa loja.
                </p>
                <a href="index.php" class="btn btn-retro btn-lg mt-3">
                    🎮 COMEÇAR A COMPRAR
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($orders as $order): 
                // Buscar itens do pedido
                $stmt = $conn->prepare("SELECT oi.*, p.nome, p.imagem 
                                        FROM order_items oi 
                                        LEFT JOIN products p ON oi.produto_id = p.id 
                                        WHERE oi.pedido_id = ?");
                $stmt->bind_param("i", $order['id']);
                $stmt->execute();
                $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            ?>
            <div class="col-12">
                <div class="retro-box p-4">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <h4 style="color: var(--neon-yellow); font-weight: 900; margin: 0;">
                                PEDIDO #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                            </h4>
                            <small style="color: var(--neon-cyan);">
                                📅 <?= date('d/m/Y H:i', strtotime($order['data_criacao'])) ?>
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-<?= getStatusColor($order['status']) ?>" style="font-size: 1rem; padding: 10px 20px;">
                                <?= getStatusIcon($order['status']) ?> <?= strtoupper($order['status']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <hr style="border-color: var(--neon-cyan); opacity: 0.3;">
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <h6 style="color: var(--neon-cyan); font-weight: 900; margin-bottom: 15px;">
                                ITENS DO PEDIDO
                            </h6>
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
                            
                            <div class="mt-3 p-3" style="background: rgba(0, 255, 255, 0.05); border: 2px solid var(--neon-cyan); border-radius: 10px;">
                                <h6 style="color: var(--neon-yellow); margin-bottom: 10px;">📍 ENDEREÇO DE ENTREGA</h6>
                                <p style="color: white; margin: 0;">
                                    <?= nl2br(htmlspecialchars($order['endereco_entrega'])) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 mt-4 mt-lg-0">
                            <div class="p-3" style="background: rgba(255, 0, 255, 0.1); border: 2px solid var(--neon-pink); border-radius: 10px;">
                                <h6 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 15px;">
                                    💰 RESUMO
                                </h6>
                                
                                <div class="d-flex justify-content-between mb-2" style="color: white;">
                                    <span>Subtotal:</span>
                                    <strong><?= formatPrice($order['total'] - $order['frete']) ?></strong>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2" style="color: white;">
                                    <span>Frete:</span>
                                    <strong><?= $order['frete'] > 0 ? formatPrice($order['frete']) : '<span style="color: var(--neon-green);">GRÁTIS</span>' ?></strong>
                                </div>
                                
                                <hr style="border-color: var(--neon-cyan); opacity: 0.3;">
                                
                                <div class="d-flex justify-content-between mb-3" style="color: var(--neon-green); font-size: 1.3rem; font-weight: 900;">
                                    <span>TOTAL:</span>
                                    <strong><?= formatPrice($order['total']) ?></strong>
                                </div>
                                
                                <div style="background: rgba(0, 255, 255, 0.1); border: 1px solid var(--neon-cyan); border-radius: 5px; padding: 10px; text-align: center;">
                                    <small style="color: var(--neon-cyan); display: block; margin-bottom: 5px;">
                                        MÉTODO DE PAGAMENTO
                                    </small>
                                    <strong style="color: white;">
                                        <?= htmlspecialchars($order['metodo_pagamento']) ?>
                                    </strong>
                                </div>
                                
                                <?php if ($order['status'] === 'Pendente'): ?>
                                <a href="pagamento.php?pedido=<?= $order['id'] ?>" class="btn btn-retro w-100 mt-3">
                                    💳 IR PARA PAGAMENTO
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>