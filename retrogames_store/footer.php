<footer class="retro-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5>◆ SOBRE NÓS</h5>
                <p style="color: var(--neon-cyan);">
                    RetroGames Store é a sua loja especializada em videogames clássicos e atuais. 
                    Reviva a nostalgia dos anos 2000 com os melhores produtos!
                </p>
                <div class="social-links mt-3">
                    <a href="#" title="Facebook">📘</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="Twitter">🐦</a>
                    <a href="#" title="YouTube">📺</a>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5>◆ LINKS RÁPIDOS</h5>
                <a href="index.php">🏠 Home</a>
                <a href="index.php#produtos">🎮 Produtos</a>
                <a href="carrinho.php">🛒 Carrinho</a>
                <?php if (isLoggedIn()): ?>
                    <a href="pedidos.php">📦 Meus Pedidos</a>
                <?php else: ?>
                    <a href="login.php">🔑 Login</a>
                    <a href="registro.php">📝 Registrar</a>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5>◆ CONTATO</h5>
                <p style="color: var(--neon-cyan);">
                    📧 Email: contato@retrogames.com<br>
                    📱 WhatsApp: (11) 99999-9999<br>
                    📍 São Paulo, SP - Brasil<br>
                    ⏰ Seg-Sex: 9h às 18h
                </p>
            </div>
        </div>
        
        <hr style="border-color: var(--neon-cyan); opacity: 0.3; margin: 30px 0;">
        
        <div class="row">
            <div class="col-12 text-center">
                <p style="color: var(--neon-cyan); font-family: 'Press Start 2P', cursive; font-size: 0.7rem;">
                    &copy; <?= date('Y') ?> RETROGAMES STORE - TODOS OS DIREITOS RESERVADOS
                </p>
                <p style="color: var(--neon-pink); font-size: 0.8rem; margin-top: 10px;">
                    🎮 GAME ON! 🎮
                </p>
            </div>
        </div>
    </div>
</footer>