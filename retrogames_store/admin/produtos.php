<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../index.php');
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar_produto'])) {
        $nome = sanitize($_POST['nome']);
        $categoria_id = (int)$_POST['categoria_id'];
        $preco = (float)$_POST['preco'];
        $estoque = (int)$_POST['estoque'];
        $descricao = sanitize($_POST['descricao']);
        $imagem = sanitize($_POST['imagem']);
        $destaque = isset($_POST['destaque']) ? 1 : 0;
        
        $stmt = $conn->prepare("INSERT INTO products (nome, categoria_id, preco, estoque, descricao, imagem, destaque) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sidissi", $nome, $categoria_id, $preco, $estoque, $descricao, $imagem, $destaque);
        
        if ($stmt->execute()) {
            showAlert("Produto adicionado com sucesso!", "success");
        } else {
            showAlert("Erro ao adicionar produto!", "danger");
        }
        redirect('produtos.php');
    }
    
    if (isset($_POST['editar_produto'])) {
        $id = (int)$_POST['id'];
        $nome = sanitize($_POST['nome']);
        $categoria_id = (int)$_POST['categoria_id'];
        $preco = (float)$_POST['preco'];
        $estoque = (int)$_POST['estoque'];
        $descricao = sanitize($_POST['descricao']);
        $imagem = sanitize($_POST['imagem']);
        $destaque = isset($_POST['destaque']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE products SET nome = ?, categoria_id = ?, preco = ?, estoque = ?, descricao = ?, imagem = ?, destaque = ? WHERE id = ?");
        $stmt->bind_param("sidiisii", $nome, $categoria_id, $preco, $estoque, $descricao, $imagem, $destaque, $id);
        
        if ($stmt->execute()) {
            showAlert("Produto atualizado com sucesso!", "success");
        } else {
            showAlert("Erro ao atualizar produto!", "danger");
        }
        redirect('produtos.php');
    }
    
    if (isset($_POST['deletar_produto'])) {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            showAlert("Produto deletado com sucesso!", "success");
        } else {
            showAlert("Erro ao deletar produto!", "danger");
        }
        redirect('produtos.php');
    }
}

// Buscar produtos
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE nome LIKE '%$search%' OR descricao LIKE '%$search%'" : '';

$products = $conn->query("SELECT p.*, c.nome as categoria_nome 
                          FROM products p 
                          LEFT JOIN categories c ON p.categoria_id = c.id 
                          $where
                          ORDER BY p.id DESC")->fetch_all(MYSQLI_ASSOC);

// Buscar categorias para o formulário
$categories = $conn->query("SELECT * FROM categories ORDER BY nome")->fetch_all(MYSQLI_ASSOC);

// Produto sendo editado
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_product = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - Admin RetroGames</title>
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
                <span class="retro-bracket">📦</span> GERENCIAR PRODUTOS
            </h2>
            <a href="index.php" class="btn btn-retro">← VOLTAR AO PAINEL</a>
        </div>
        
        <!-- Formulário de Produto -->
        <div class="retro-box p-4 mb-5">
            <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px;">
                <?= $edit_product ? '✏️ EDITAR PRODUTO' : '➕ ADICIONAR NOVO PRODUTO' ?>
            </h4>
            
            <form method="POST">
                <?php if ($edit_product): ?>
                <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
                <?php endif; ?>
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">NOME DO PRODUTO</label>
                        <input type="text" name="nome" class="form-control retro-input" required
                               value="<?= $edit_product ? htmlspecialchars($edit_product['nome']) : '' ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">CATEGORIA</label>
                        <select name="categoria_id" class="form-select retro-input" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $edit_product && $edit_product['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">PREÇO (R$)</label>
                        <input type="number" name="preco" class="form-control retro-input" step="0.01" required
                               value="<?= $edit_product ? $edit_product['preco'] : '' ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">ESTOQUE</label>
                        <input type="number" name="estoque" class="form-control retro-input" required
                               value="<?= $edit_product ? $edit_product['estoque'] : '' ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">IMAGEM</label>
                        <input type="text" name="imagem" class="form-control retro-input" required
                               placeholder="nome-arquivo.jpg"
                               value="<?= $edit_product ? htmlspecialchars($edit_product['imagem']) : '' ?>">
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">DESCRIÇÃO</label>
                        <textarea name="descricao" class="form-control retro-input" rows="3" required><?= $edit_product ? htmlspecialchars($edit_product['descricao']) : '' ?></textarea>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="destaque" id="destaque"
                                   <?= $edit_product && $edit_product['destaque'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="destaque" style="color: var(--neon-yellow); font-weight: 700;">
                                ⭐ PRODUTO EM DESTAQUE
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" name="<?= $edit_product ? 'editar_produto' : 'adicionar_produto' ?>" class="btn btn-retro btn-lg">
                            <?= $edit_product ? '✅ SALVAR ALTERAÇÕES' : '➕ ADICIONAR PRODUTO' ?>
                        </button>
                        <?php if ($edit_product): ?>
                        <a href="produtos.php" class="btn btn-lg" style="background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan); font-weight: 900; margin-left: 10px;">
                            CANCELAR
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Busca -->
        <div class="mb-4">
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control retro-input" 
                           placeholder="BUSCAR PRODUTOS..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-retro w-100">🔍 BUSCAR</button>
                </div>
            </form>
        </div>
        
        <!-- Lista de Produtos -->
        <div class="retro-box p-4">
            <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px;">
                📋 LISTA DE PRODUTOS (<?= count($products) ?>)
            </h4>
            
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IMAGEM</th>
                            <th>NOME</th>
                            <th>CATEGORIA</th>
                            <th>PREÇO</th>
                            <th>ESTOQUE</th>
                            <th>DESTAQUE</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><strong>#<?= $product['id'] ?></strong></td>
                            <td>
                                <img src="../assets/products/<?= $product['imagem'] ?>" 
                                     alt="<?= htmlspecialchars($product['nome']) ?>"
                                     style="width: 50px; height: 50px; object-fit: contain; border: 2px solid var(--neon-cyan); border-radius: 5px;"
                                     onerror="this.src='../assets/placeholder.jpg'">
                            </td>
                            <td><strong><?= htmlspecialchars($product['nome']) ?></strong></td>
                            <td><?= htmlspecialchars($product['categoria_nome']) ?></td>
                            <td><strong style="color: var(--neon-green);"><?= formatPrice($product['preco']) ?></strong></td>
                            <td>
                                <span class="badge bg-<?= $product['estoque'] < 5 ? 'danger' : 'success' ?>">
                                    <?= $product['estoque'] ?>
                                </span>
                            </td>
                            <td><?= $product['destaque'] ? '⭐' : '-' ?></td>
                            <td>
                                <a href="produtos.php?edit=<?= $product['id'] ?>" class="btn btn-sm btn-retro">✏️</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Deletar produto?')">
                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                    <button type="submit" name="deletar_produto" class="btn btn-sm" 
                                            style="background: #dc3545; color: white; border: 2px solid var(--neon-pink);">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>