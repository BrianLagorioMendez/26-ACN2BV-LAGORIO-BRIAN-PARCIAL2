<?php require_once __DIR__ . '/header.php'; ?>
<nav class="nav-main">
    <div class="container">
        <ul>
            <li><a href="index.php">Foro / Muro</a></li>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <li><a href="crear_publicacion.php" class="btn-destacado">+ Nueva Teoría</a></li>
                <li><a href="perfil.php">Mi Perfil</a></li>
                <li class="nav-derecha">
                    Viajero: <strong><?php echo e($_SESSION['usuario_nombre']); ?></strong> 
                    (<a href="logout.php" class="link-salir">Cerrar Sesión</a>)
                </li>
            <?php else: ?>
                <li class="nav-derecha"><a href="login.php">Iniciar Sesión</a></li>
                <li class="nav-derecha"><a href="registro.php">Registrarse</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<main class="container contenido-principal">
<?php
if (isset($_SESSION['flash_mensaje'])) {
    $tipo = $_SESSION['flash_tipo'] ?? 'info';
    echo '<div class="alerta alerta-' . e($tipo) . '">' . e($_SESSION['flash_mensaje']) . '</div>';
    unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);
}
?>