<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';
require_once 'includes/nav.php';

$sql_count = "SELECT COUNT(*) as total FROM publicaciones WHERE usuario_id = ?";
$stmt_count = mysqli_prepare($conexion, $sql_count);
mysqli_stmt_bind_param($stmt_count, "i", $_SESSION['usuario_id']);
mysqli_stmt_execute($stmt_count);
$res_count = mysqli_stmt_get_result($stmt_count);
$total_posts = mysqli_fetch_assoc($res_count)['total'] ?? 0;
mysqli_stmt_close($stmt_count);

$rol_texto = (($_SESSION['usuario_rol'] ?? 'user') === 'admin') ? 'Administrador' : 'Usuario';
?>

<h2>Mi Perfil</h2>
<div class="tarjeta">
    <h3>Información de Cuenta</h3>
    <p><strong>Identificador Único:</strong> #<?php echo e($_SESSION['usuario_id']); ?></p>
    <p><strong>Nombre:</strong> <?php echo e($_SESSION['usuario_nombre']); ?></p>
    <p><strong>Email:</strong> <?php echo e($_SESSION['usuario_email']); ?></p>
    <p><strong>Rol:</strong> <?php echo e($rol_texto); ?></p>
    <p><strong>Teorías publicadas:</strong> <?php echo $total_posts; ?></p>
    <br>
    <a href="crear_publicacion.php" class="btn">+ Publicar Teoría</a>
</div>

<?php require_once 'includes/footer.php'; ?>