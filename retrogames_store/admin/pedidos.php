<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../index.php');
}

// Atualizar status do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_status'])) {
    $order_id = (int)$_POST['order_id'];
    $novo_status = sanitize($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_status, $order_id);
    
    if ($stmt->execute()) {
        // Se marcar como pago, atualizar data de pagamento
        if ($novo_status === 'Pago') {
            $conn->query("UPDATE orders SET data_pagamento = NOW() WHERE id = $order_id");
        }
        showAlert("Status do pedido atualizado!", "success");
    } else {
        showAlert("Erro ao atualizar status!", "danger");
    }
    redirect('pedidos.php');
}

// Buscar pedidos
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$where = $status_filter ? "WHERE o.status = '$status_filter'" : '';

$orders = $conn->query("SELECT o.*, u.nome as cliente_nome, u.email as cliente_email 
                        FROM orders o 
                        LEFT JOIN users u ON o.cliente_id = u.id 
                        $where
                        ORDER BY o.data_criacao DESC")->fetch_all(MYSQLI_ASSOC);

// Pedido específico
$view_order = null;
$view_items = [];
if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT o.*, u.nome as cliente_nome, u.email as cliente_email, u.endereco, u.cidade, u.estado, u.cep
                            FROM orders o 
                            LEFT JOIN users u ON o.cliente_id = u.id 
                            WHERE o.id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $view_order = $stmt->get_result()->fetch_assoc();
    
    if ($view_order) {
        $stmt = $conn->prepare("SELECT oi.*, p.nome, p.imagem 
                                FROM order_items oi 
                                LEFT JOIN products p ON oi.produto_id = p.id 
                                WHERE oi.pedido_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $view_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - Admin RetroGames</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../header.php'; ?>
    
    <?php 
    $alert = getAlert();
    if ($alert): 
    ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show retro-alert">
            <?= htmlspecialchars($alert['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title" style="margin: 0;">
                <span class="retro-bracket">🛒</span> GERENCIAR PEDIDOS
            </h2>
            <a href="index.php" class="btn btn-retro">← VOLTAR AO PAINEL</a>
        </div>
        
        <?php if ($view_order): ?>
        <!-- Detalhes do Pedido -->
        <div class="retro-box p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 style="color: var(--neon-yellow); font-weight: 900; margin: 0;">
                    PEDIDO #<?= str_pad($view_order['id'], 6, '0', STR_PAD_LEFT) ?>
                </h4>
                <a href="pedidos.php" class="btn btn-retro">← VOLTAR À LISTA</a>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-8">
                    <h5 style="color: var(--neon-cyan); font-weight: 900; margin-bottom: 15px;">ITENS DO PEDIDO</h5>
                    <?php foreach ($view_items as $item): ?>
                    <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom: 1px solid rgba(0, 255, 255, 0.2);">
                        <img src="../assets/products/<?= $item['imagem'] ?>" 
                             alt="<?= htmlspecialchars($item['nome']) ?>"
                             style="width: 60px; height: 60px; object-fit: contain; margin-right: 15px; border: 2px solid var(--neon-cyan); border-radius: 5px;"
                             onerror="this.src='../assets/placeholder.jpg'">
                        <div style="flex: 1;">
                            <strong style="color: white; display: block;"><?= htmlspecialchars($item['nome']) ?></strong>
                            <small style="color: var(--neon-pink);">
                                <?= $item['quantidade'] ?> x <?= formatPrice($item['valor_unitario']) ?>
                            </small>
                        </div>
                        <strong style="color: var(--neon-green);">
                            <?= formatPrice($item['quantidade'] * $item['valor_unitario']) ?>
                        </strong>
                    </div>
                    <?php endforeach; ?>
                    
                    <hr style="border-color: var(--neon-cyan); opacity: 0.3;">
                    
                    <div class="p-3" style="background: rgba(0, 255, 255, 0.05); border: 2px solid var(--neon-cyan); border-radius: 10px;">
                        <h6 style="color: var(--neon-yellow); margin-bottom: 10px;">📍 DADOS DO CLIENTE</h6>
                        <p style="color: white; margin: 0;">
                            <strong>Nome:</strong> <?= htmlspecialchars($view_order['cliente_nome']) ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($view_order['cliente_email']) ?><br>
                            <strong>Endereço:</strong> <?= nl2br(htmlspecialchars($view_order['endereco_entrega'])) ?>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="p-4" style="background: rgba(255, 0, 255, 0.1); border: 2px solid var(--neon-pink); border-radius: 10px;">
                        <h6 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 15px;">INFORMAÇÕES DO PEDIDO</h6>
                        
                        <div class="mb-3">
                            <small style="color: var(--neon-cyan); display: block;">DATA DO PEDIDO</small>
                            <strong style="color: white;"><?= date('d/m/Y H:i', strtotime($view_order['data_criacao'])) ?></strong>
                        </div>
                        
                        <?php if ($view_order['data_pagamento']): ?>
                        <div class="mb-3">
                            <small style="color: var(--neon-cyan); display: block;">DATA DO PAGAMENTO</small>
                            <strong style="color: white;"><?= date('d/m/Y H:i', strtotime($view_order['data_pagamento'])) ?></strong>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <small style="color: var(--neon-cyan); display: block;">MÉTODO DE PAGAMENTO</small>
                            <strong style="color: white;"><?= htmlspecialchars($view_order['metodo_pagamento']) ?></strong>
                        </div>
                        
                        <hr style="border-color: var(--neon-cyan); opacity: 0.3;">
                        
                        <div class="d-flex justify-content-between mb-2" style="color: white;">
                            <span>Subtotal:</span>
                            <strong><?= formatPrice($view_order['total'] - $view_order['frete']) ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3" style="color: white;">
                            <span>Frete:</span>
                            <strong><?= formatPrice($view_order['frete']) ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4" style="color: var(--neon-green); font-size: 1.3rem; font-weight: 900;">
                            <span>TOTAL:</span>
                            <strong><?= formatPrice($view_order['total']) ?></strong>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $view_order['id'] ?>">
                            <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">ATUALIZAR STATUS</label>
                            <select name="status" class="form-select retro-input mb-3">
                                <option value="Pendente" <?= $view_order['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="Pago" <?= $view_order['status'] == 'Pago' ? 'selected' : '' ?>>Pago</option>
                                <option value="Enviado" <?= $view_order['status'] == 'Enviado' ? 'selected' : '' ?>>Enviado</option>
                                <option value="Entregue" <?= $view_order['status'] == 'Entregue' ? 'selected' : '' ?>>Entregue</option>
                                <option value="Cancelado" <?= $view_order['status'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                            <button type="submit" name="atualizar_status" class="btn btn-retro w-100">
                                💾 SALVAR STATUS
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Filtros -->
        <div class="mb-4">
            <div class="btn-group" role="group">
                <a href="pedidos.php" class="btn <?= !$status_filter ? 'btn-retro' : '' ?>" style="<?= !$status_filter ? '' : 'background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan);' ?>">
                    TODOS
                </a>
                <a href="?status=Pendente" class="btn <?= $status_filter == 'Pendente' ? 'btn-retro' : '' ?>" style="<?= $status_filter == 'Pendente' ? '' : 'background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan);' ?>">
                    PENDENTES
                </a>
                <a href="?status=Pago" class="btn <?= $status_filter == 'Pago' ? 'btn-retro' : '' ?>" style="<?= $status_filter == 'Pago' ? '' : 'background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan);' ?>">
                    PAGOS
                </a>
                <a href="?status=Enviado" class="btn <?= $status_filter == 'Enviado' ? 'btn-retro' : '' ?>" style="<?= $status_filter == 'Enviado' ? '' : 'background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan);' ?>">
                    ENVIADOS
                </a>
            </div>
        </div>
        
        <!-- Lista de Pedidos -->
        <div class="retro-box p-4">
            <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px;">
                📋 LISTA DE PEDIDOS (<?= count($orders) ?>)
            </h4>
            
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>PEDIDO</th>
                            <th>CLIENTE</th>
                            <th>TOTAL</th>
                            <th>PAGAMENTO</th>
                            <th>STATUS</th>
                            <th>DATA</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                            <td><?= htmlspecialchars($order['cliente_nome']) ?></td>
                            <td><strong style="color: var(--neon-green);"><?= formatPrice($order['total']) ?></strong></td>
                            <td><?= htmlspecialchars($order['metodo_pagamento']) ?></td>
                            <td>
                                <span class="badge bg-<?= match($order['status']) {
                                    'Pendente' => 'warning',
                                    'Pago' => 'success',
                                    'Enviado' => 'info',
                                    'Entregue' => 'primary',
                                    'Cancelado' => 'danger',
                                    default => 'secondary'
                                } ?>">
                                    <?= $order['status'] ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($order['data_criacao'])) ?></td>
                            <td>
                                <a href="pedidos.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-retro">
                                    👁️ VER
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>