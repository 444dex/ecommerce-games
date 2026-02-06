<?php
require_once 'config.php';

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitize($_POST['nome']);
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    
    // Validações
    if (empty($nome) || empty($email) || empty($senha) || empty($confirma_senha)) {
        $error = 'Preencha todos os campos!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido!';
    } elseif (strlen($senha) < 6) {
        $error = 'A senha deve ter no mínimo 6 caracteres!';
    } elseif ($senha !== $confirma_senha) {
        $error = 'As senhas não coincidem!';
    } else {
        // Verificar se email já existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Este email já está cadastrado!';
        } else {
            // Criar novo usuário
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente')");
            $stmt->bind_param("sss", $nome, $email, $senha_hash);
            
            if ($stmt->execute()) {
                $success = 'Cadastro realizado com sucesso! Faça login para continuar.';
                
                // Auto-login
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_nome'] = $nome;
                $_SESSION['user_tipo'] = 'cliente';
                
                showAlert("Bem-vindo(a) à RetroGames, $nome!", "success");
                redirect('index.php');
            } else {
                $error = 'Erro ao criar conta. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - RetroGames Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="retro-box p-5">
                    <h2 class="text-center mb-4" style="color: var(--neon-cyan); font-family: 'Press Start 2P', cursive; font-size: 1.5rem;">
                        📝 REGISTRAR
                    </h2>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger" style="border: 3px solid #dc3545;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success" style="border: 3px solid #28a745;">
                        <?= htmlspecialchars($success) ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                👤 NOME COMPLETO
                            </label>
                            <input type="text" 
                                   name="nome" 
                                   class="form-control retro-input" 
                                   required
                                   value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>"
                                   placeholder="Seu nome completo">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                📧 EMAIL
                            </label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control retro-input" 
                                   required
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                                   placeholder="seu@email.com">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                🔒 SENHA
                            </label>
                            <input type="password" 
                                   name="senha" 
                                   class="form-control retro-input" 
                                   required
                                   minlength="6"
                                   placeholder="Mínimo 6 caracteres">
                            <small style="color: var(--neon-yellow);">Mínimo 6 caracteres</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                🔒 CONFIRMAR SENHA
                            </label>
                            <input type="password" 
                                   name="confirma_senha" 
                                   class="form-control retro-input" 
                                   required
                                   minlength="6"
                                   placeholder="Digite a senha novamente">
                        </div>
                        
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-retro btn-lg">
                                CRIAR CONTA
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <p style="color: white;">
                                Já tem uma conta? 
                                <a href="login.php" style="color: var(--neon-pink); font-weight: 900; text-decoration: none;">
                                    FAÇA LOGIN AQUI
                                </a>
                            </p>
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