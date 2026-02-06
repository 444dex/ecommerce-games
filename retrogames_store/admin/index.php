<?php
require_once '../config.php';

// Verificar se é admin
if (!isAdmin()) {
    showAlert("Acesso negado! Área restrita a administradores.", "danger");
    redirect('../index.php');
}

// Buscar estatísticas
$stats = [];

// Total de pedidos
$result = $conn->query("SELECT COUNT(*) as total FROM orders");
$stats['total_pedidos'] = $result->fetch_assoc()['total'];

// Receita total
$result = $conn->query("SELECT SUM(total) as receita FROM orders WHERE status = 'Pago'");
$stats['receita'] = $result->fetch_assoc()['receita'] ?? 0;

// Total de produtos
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$stats['total_produtos'] = $result->fetch_assoc()['total'];

// Total de clientes
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE tipo = 'cliente'");
$stats['total_clientes'] = $result->fetch_assoc()['total'];

// Pedidos pendentes
$result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'Pendente'");
$stats['pedidos_pendentes'] = $result->fetch_assoc()['total'];

// Produtos com estoque baixo
$result = $conn->query("SELECT COUNT(*) as total FROM products WHERE estoque < 5");
$stats['estoque_baixo'] = $result->fetch_assoc()['total'];

// Últimos pedidos
$last_orders = $conn->query("SELECT o.*, u.nome as cliente_nome 
                              FROM orders o 
                              LEFT JOIN users u ON o.cliente_id = u.id 
                              ORDER BY o.data_criacao DESC 
                              LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - RetroGames Store</title>
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
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show retro-alert" role="alert">
            <?= htmlspecialchars($alert['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="container my-5">
        <h2 class="section-title text-center mb-5">
            <span class="retro-bracket">⚙️</span> PAINEL ADMINISTRATIVO <span class="retro-bracket">⚙️</span>
        </h2>
        
        <!-- Menu Admin -->
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <a href="produtos.php" class="admin-menu-card">
                    <div class="icon">📦</div>
                    <div class="title">PRODUTOS</div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="categorias.php" class="admin-menu-card">
                    <div class="icon">📁</div>
                    <div class="title">CATEGORIAS</div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="pedidos.php" class="admin-menu-card">
                    <div class="icon">🛒</div>
                    <div class="title">PEDIDOS</div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="../index.php" class="admin-menu-card">
                    <div class="icon">🏠</div>
                    <div class="title">VOLTAR À LOJA</div>
                </a>
            </div>
        </div>
        
        <!-- Estatísticas -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value"><?= formatPrice($stats['receita']) ?></div>
                    <div class="stat-label">Receita Total</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-value"><?= $stats['total_pedidos'] ?></div>
                    <div class="stat-label">Total de Pedidos</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?= $stats['total_clientes'] ?></div>
                    <div class="stat-label">Clientes Cadastrados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon">🎮</div>
                    <div class="stat-value"><?= $stats['total_produtos'] ?></div>
                    <div class="stat-label">Produtos Cadastrados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card alert-warning">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-value"><?= $stats['pedidos_pendentes'] ?></div>
                    <div class="stat-label">Pedidos Pendentes</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card alert-danger">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-value"><?= $stats['estoque_baixo'] ?></div>
                    <div class="stat-label">Produtos com Estoque Baixo</div>
                </div>
            </div>
        </div>
        
        <!-- Últimos Pedidos -->
        <div class="retro-box p-4">
            <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px;">
                📋 ÚLTIMOS PEDIDOS
            </h4>
            
            <?php if (!empty($last_orders)): ?>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>PEDIDO</th>
                            <th>CLIENTE</th>
                            <th>TOTAL</th>
                            <th>STATUS</th>
                            <th>DATA</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($last_orders as $order): ?>
                        <tr>
                            <td><strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                            <td><?= htmlspecialchars($order['cliente_nome']) ?></td>
                            <td><strong style="color: var(--neon-green);"><?= formatPrice($order['total']) ?></strong></td>
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
                                    VER
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-3">
                <a href="pedidos.php" class="btn btn-retro">VER TODOS OS PEDIDOS</a>
            </div>
            <?php else: ?>
            <p style="color: white; text-align: center;">Nenhum pedido encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
.admin-menu-card {
    background: linear-gradient(135deg, #16213e 0%, #1a1a2e 100%);
    border: 3px solid var(--neon-cyan);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    text-decoration: none;
    display: block;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 255, 255, 0.3);
}

.admin-menu-card:hover {
    transform: translateY(-10px);
    border-color: var(--neon-pink);
    box-shadow: 0 10px 30px rgba(255, 0, 255, 0.5);
}

.admin-menu-card .icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.admin-menu-card .title {
    color: var(--neon-cyan);
    font-weight: 900;
    font-size: 1.1rem;
    letter-spacing: 1px;
}

.admin-menu-card:hover .title {
    color: var(--neon-yellow);
}

.stat-card {
    background: linear-gradient(135deg, #16213e 0%, #1a1a2e 100%);
    border: 3px solid var(--neon-cyan);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 255, 255, 0.3);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--neon-green);
    text-shadow: 0 0 15px var(--neon-green);
    margin-bottom: 10px;
}

.stat-label {
    color: var(--neon-cyan);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.admin-table {
    color: white;
}

.admin-table th {
    color: var(--neon-cyan);
    border-bottom: 2px solid var(--neon-cyan);
    padding: 15px;
    font-weight: 900;
}

.admin-table td {
    border-bottom: 1px solid rgba(0, 255, 255, 0.2);
    padding: 15px;
    vertical-align: middle;
}
</style>