<?php
require_once 'config.php';

// Destruir sessão
session_destroy();

// Redirecionar para home
header("Location: index.php");
exit();
?>