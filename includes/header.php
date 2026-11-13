<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Persistencia de Tema por Cookie (por defecto 'oscuro')
if (isset($_GET['cambiar_tema'])) {
    $nuevo_tema = ($_GET['cambiar_tema'] === 'claro') ? 'claro' : 'oscuro';
    setcookie('pref_tema', $nuevo_tema, time() + (86400 * 30), "/");
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

$tema_actual = $_COOKIE['pref_tema'] ?? 'oscuro';

// Sanitización XSS obligatoria con htmlspecialchars
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DARK - Foro de Discusión & Teorías</title>
    
    <link rel="stylesheet" href="css/tema-<?php echo e($tema_actual); ?>.css?v=<?php echo time(); ?>">
</head>
<body>
<header class="header-main">
    <div class="container header-flex">
        <div class="logo-box">
            <h1 class="logo-title">DARK</h1>
            <span class="logo-sub">SIC MUNDUS CREATUS EST</span>
        </div>
        <div class="selector-tema">
            <span>Modo: </span>
            <a href="?cambiar_tema=oscuro" class="btn-tema <?php echo $tema_actual === 'oscuro' ? 'activo' : ''; ?>">Oscuro</a> | 
            <a href="?cambiar_tema=claro" class="btn-tema <?php echo $tema_actual === 'claro' ? 'activo' : ''; ?>">Claro</a>
        </div>
    </div>
</header>