<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'retrogames_store');

// Conectar ao banco
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Funções auxiliares
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'admin';
}

function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function showAlert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

// Calcular total do carrinho
function getCartTotal() {
    global $conn;
    $total = 0;
    
    if (!empty($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $product_id => $quantity) {
            $stmt = $conn->prepare("SELECT preco FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $total += $row['preco'] * $quantity;
            }
        }
    }
    
    return $total;
}

// Contar itens no carrinho
function getCartCount() {
    return array_sum($_SESSION['carrinho']);
}
?>