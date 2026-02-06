<?php
require_once '../config.php';

if (!isAdmin()) {
    redirect('../index.php');
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar_categoria'])) {
        $nome = sanitize($_POST['nome']);
        $descricao = sanitize($_POST['descricao']);
        $icone = sanitize($_POST['icone']);
        
        $stmt = $conn->prepare("INSERT INTO categories (nome, descricao, icone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $descricao, $icone);
        
        if ($stmt->execute()) {
            showAlert("Categoria adicionada com sucesso!", "success");
        } else {
            showAlert("Erro ao adicionar categoria!", "danger");
        }
        redirect('categorias.php');
    }
    
    if (isset($_POST['editar_categoria'])) {
        $id = (int)$_POST['id'];
        $nome = sanitize($_POST['nome']);
        $descricao = sanitize($_POST['descricao']);
        $icone = sanitize($_POST['icone']);
        
        $stmt = $conn->prepare("UPDATE categories SET nome = ?, descricao = ?, icone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $descricao, $icone, $id);
        
        if ($stmt->execute()) {
            showAlert("Categoria atualizada com sucesso!", "success");
        } else {
            showAlert("Erro ao atualizar categoria!", "danger");
        }
        redirect('categorias.php');
    }
    
    if (isset($_POST['deletar_categoria'])) {
        $id = (int)$_POST['id'];
        
        // Verificar se há produtos nesta categoria
        $check = $conn->query("SELECT COUNT(*) as count FROM products WHERE categoria_id = $id")->fetch_assoc();
        
        if ($check['count'] > 0) {
            showAlert("Não é possível deletar! Existem {$check['count']} produtos nesta categoria.", "danger");
        } else {
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                showAlert("Categoria deletada com sucesso!", "success");
            } else {
                showAlert("Erro ao deletar categoria!", "danger");
            }
        }
        redirect('categorias.php');
    }
}

// Buscar categorias com contagem de produtos
$categories = $conn->query("SELECT c.*, COUNT(p.id) as total_produtos 
                            FROM categories c 
                            LEFT JOIN products p ON c.id = p.categoria_id 
                            GROUP BY c.id 
                            ORDER BY c.nome")->fetch_all(MYSQLI_ASSOC);

// Categoria sendo editada
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_category = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - Admin RetroGames</title>
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
                <span class="retro-bracket">📁</span> GERENCIAR CATEGORIAS
            </h2>
            <a href="index.php" class="btn btn-retro">← VOLTAR AO PAINEL</a>
        </div>
        
        <!-- Formulário de Categoria -->
        <div class="retro-box p-4 mb-5">
            <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px;">
                <?= $edit_category ? '✏️ EDITAR CATEGORIA' : '➕ ADICIONAR NOVA CATEGORIA' ?>
            </h4>
            
            <form method="POST">
                <?php if ($edit_category): ?>
                <input type="hidden" name="id" value="<?= $edit_category['id'] ?>">
                <?php endif; ?>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">NOME DA CATEGORIA</label>
                        <input type="text" name="nome" class="form-control retro-input" required
                               value="<?= $edit_category ? htmlspecialchars($edit_category['nome']) : '' ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">ÍCONE (EMOJI)</label>
                        <input type="text" name="icone" class="form-control retro-input" required
                               placeholder="Ex: 🎮"
                               value="<?= $edit_category ? htmlspecialchars($edit_category['icone']) : '' ?>">
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">DESCRIÇÃO</label>
                        <textarea name="descricao" class="form-control retro-input" rows="2"><?= $edit_category ? htmlspecialchars($edit_category['descricao']) : '' ?></textarea>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" name="<?= $edit_category ? 'editar_categoria' : 'adicionar_categoria' ?>" class="btn btn-retro btn-lg">
                            <?= $edit_category ? '✅ SALVAR ALTERAÇÕES' : '➕ ADICIONAR CATEGORIA' ?>
                        </button>
                        <?php if ($edit_category): ?>
                        <a href="categorias.php" class="btn btn-lg" style="background: transparent; color: var(--neon-cyan); border: 2px solid var(--neon-cyan); font-weight: 900; margin-left: 10px;">
                            CANCELAR
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Lista de Categorias -->
        <div class="retro-box p-4">
            <h4 style="color: var(--neon-yellow); font-weight: 900; margin-bottom: 20px;">
                📋 LISTA DE CATEGORIAS (<?= count($categories) ?>)
            </h4>
            
            <div class="row g-4">
                <?php foreach ($categories as $category): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="category-card-admin">
                        <div class="category-icon-large"><?= $category['icone'] ?></div>
                        <h5 style="color: white; font-weight: 900; margin: 15px 0 10px;"><?= htmlspecialchars($category['nome']) ?></h5>
                        <p style="color: var(--neon-cyan); font-size: 0.9rem; margin-bottom: 15px;">
                            <?= htmlspecialchars($category['descricao']) ?>
                        </p>
                        <div class="badge bg-primary mb-3" style="font-size: 0.9rem;">
                            <?= $category['total_produtos'] ?> produtos
                        </div>
                        <div class="d-flex gap-2">
                            <a href="categorias.php?edit=<?= $category['id'] ?>" class="btn btn-sm btn-retro flex-fill">
                                ✏️ EDITAR
                            </a>
                            <form method="POST" style="flex: 1;" onsubmit="return confirm('Deletar categoria?')">
                                <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                <button type="submit" name="deletar_categoria" class="btn btn-sm w-100" 
                                        style="background: #dc3545; color: white; border: 2px solid var(--neon-pink); font-weight: 900;">
                                    🗑️ DELETAR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
.category-card-admin {
    background: linear-gradient(135deg, #16213e 0%, #1a1a2e 100%);
    border: 3px solid var(--neon-cyan);
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.category-card-admin:hover {
    transform: translateY(-5px);
    border-color: var(--neon-pink);
    box-shadow: 0 10px 25px rgba(255, 0, 255, 0.5);
}

.category-icon-large {
    font-size: 4rem;
    filter: drop-shadow(0 0 15px var(--neon-cyan));
}
</style>