<nav class="navbar navbar-expand-lg navbar-dark retro-header">
    <div class="container">
        <a class="navbar-brand logo" href="index.php">◆ RETROGAMES ◆</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        🏠 HOME
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="index.php#produtos">
                        🎮 PRODUTOS
                    </a>
                </li>
                
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="pedidos.php">
                            📦 MEUS PEDIDOS
                        </a>
                    </li>
                    
                    <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/">
                            ⚙️ ADMIN
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            🚪 SAIR
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <span class="nav-link">
                            👤 <?= htmlspecialchars($_SESSION['user_nome']) ?>
                        </span>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            🔑 LOGIN
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="registro.php">
                            📝 REGISTRAR
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link position-relative" href="carrinho.php">
                        🛒 CARRINHO
                        <?php if (getCartCount() > 0): ?>
                            <span class="cart-badge"><?= getCartCount() ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>