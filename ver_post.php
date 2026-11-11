<?php
require_once 'includes/conexion.php';
require_once 'includes/header.php';

$id_post = $_GET['id'] ?? 0;

$sql_post = "SELECT p.*, u.nombre as autor FROM publicaciones p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?";
$stmt = mysqli_prepare($conexion, $sql_post);
mysqli_stmt_bind_param($stmt, "i", $id_post);
mysqli_stmt_execute($stmt);
$res_post = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($res_post);
mysqli_stmt_close($stmt);

if (!$post) {
    header("Location: index.php");
    exit();
}

$error_comentario = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_comentario'])) {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }

    $comentario_texto = trim($_POST['comentario'] ?? '');
    if (empty($comentario_texto)) {
        $error_comentario = "El comentario no puede estar vacío.";
    } else {
        $sql_com = "INSERT INTO comentarios (publicacion_id, usuario_id, comentario) VALUES (?, ?, ?)";
        $stmt_com = mysqli_prepare($conexion, $sql_com);
        mysqli_stmt_bind_param($stmt_com, "iis", $id_post, $_SESSION['usuario_id'], $comentario_texto);
        if (mysqli_stmt_execute($stmt_com)) {
            mysqli_stmt_close($stmt_com);
            $_SESSION['flash_mensaje'] = "Comentario agregado con éxito.";
            $_SESSION['flash_tipo'] = "exito";
            header("Location: ver_post.php?id=" . $id_post);
            exit();
        }
    }
}

$sql_comentarios = "SELECT c.*, u.nombre as autor FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.publicacion_id = ? ORDER BY c.fecha_creacion ASC";
$stmt_list = mysqli_prepare($conexion, $sql_comentarios);
mysqli_stmt_bind_param($stmt_list, "i", $id_post);
mysqli_stmt_execute($stmt_list);
$res_comentarios = mysqli_stmt_get_result($stmt_list);

require_once 'includes/nav.php';
?>

<article class="post-card post-detalle">
    <div class="post-meta">
        <span class="badge-cat"><?php echo e($post['categoria']); ?></span>
        <span><?php echo date('d/m/Y H:i', strtotime($post['fecha_creacion'])); ?></span>
    </div>
    <h2><?php echo e($post['titulo']); ?></h2>
    <p class="post-contenido-completo"><?php echo nl2br(e($post['contenido'])); ?></p>
    <div class="post-footer">
        <span>Publicado por: <strong><?php echo e($post['autor']); ?></strong></span>
        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $post['usuario_id']): ?>
            <div>
                <a href="editar_publicacion.php?id=<?php echo $post['id']; ?>" class="btn-editar">Editar</a> | 
                <a href="eliminar_publicacion.php?id=<?php echo $post['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Borrar esta teoría?');">Eliminar</a>
            </div>
        <?php endif; ?>
    </div>
</article>

<section class="seccion-comentarios">
    <h3>Discusión y Comentarios</h3>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <form action="ver_post.php?id=<?php echo $id_post; ?>" method="POST" class="form-comentario">
            <?php if ($error_comentario): ?>
                <div class="alerta alerta-error"><?php echo e($error_comentario); ?></div>
            <?php endif; ?>
            <div class="campo">
                <textarea name="comentario" rows="3" placeholder="Escribe tu opinión sobre esta teoría..." required></textarea>
            </div>
            <button type="submit" name="btn_comentario" class="btn">Comentar</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Inicia sesión</a> para comentar en esta publicación.</p>
    <?php endif; ?>

    <div class="lista-comentarios">
        <?php if (mysqli_num_rows($res_comentarios) > 0): ?>
            <?php while ($com = mysqli_fetch_assoc($res_comentarios)): ?>
                <div class="item-comentario">
                    <div class="com-header">
                        <strong><?php echo e($com['autor']); ?></strong> 
                        <span class="com-fecha"><?php echo date('d/m/Y H:i', strtotime($com['fecha_creacion'])); ?></span>
                    </div>
                    <p><?php echo nl2br(e($com['comentario'])); ?></p>
                    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $com['usuario_id']): ?>
                        <a href="eliminar_comentario.php?id=<?php echo $com['id']; ?>&post_id=<?php echo $id_post; ?>" class="btn-eliminar" onclick="return confirm('¿Borrar este comentario?');">Eliminar comentario</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="texto-vacio">Aún no hay comentarios en esta teoría.</p>
        <?php endif; ?>
    </div>
</section>

<?php 
mysqli_stmt_close($stmt_list);
require_once 'includes/footer.php'; 
?>