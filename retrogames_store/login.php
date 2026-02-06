<?php
require_once 'config.php';

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $senha = $_POST['senha'];
    
    if (empty($email) || empty($senha)) {
        $error = 'Preencha todos os campos!';
    } else {
        $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verificar senha
            if (password_verify($senha, $user['senha'])) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nome'] = $user['nome'];
                $_SESSION['user_tipo'] = $user['tipo'];
                
                showAlert("Bem-vindo(a), {$user['nome']}!", "success");
                
                // Redirecionar para admin se for administrador
                if ($user['tipo'] === 'admin') {
                    redirect('admin/');
                } else {
                    redirect('index.php');
                }
            } else {
                $error = 'Email ou senha incorretos!';
            }
        } else {
            $error = 'Email ou senha incorretos!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RetroGames Store</title>
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
                        🔑 LOGIN
                    </h2>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger" style="border: 3px solid #dc3545;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                📧 EMAIL
                            </label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control retro-input" 
                                   required
                                   placeholder="seu@email.com">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label" style="color: var(--neon-cyan); font-weight: 700;">
                                🔒 SENHA
                            </label>
                            <input type="password" 
                                   name="senha" 
                                   class="form-control retro-input" 
                                   required
                                   placeholder="••••••••">
                        </div>
                        
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-retro btn-lg">
                                ENTRAR
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <p style="color: white;">
                                Não tem uma conta? 
                                <a href="registro.php" style="color: var(--neon-pink); font-weight: 900; text-decoration: none;">
                                    REGISTRE-SE AQUI
                                </a>
                            </p>
                        </div>
                    </form>
                    
                    <hr style="border-color: var(--neon-cyan); opacity: 0.3; margin: 30px 0;">
                    
                    <div class="text-center">
                        <p style="color: var(--neon-yellow); font-size: 0.9rem; font-weight: 700;">
                            🎮 CONTA ADMIN DE TESTE 🎮
                        </p>
                        <p style="color: white; font-size: 0.85rem;">
                            <strong>Email:</strong> admin@retrogames.com<br>
                            <strong>Senha:</strong> password
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>