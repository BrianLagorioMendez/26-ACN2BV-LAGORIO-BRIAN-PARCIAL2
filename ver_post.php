<?php
require_once 'includes/conexion.php';
require_once 'includes/nav.php';

$id_post = $_GET['id'] ?? 0;
$es_admin = isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';

$sql = "SELECT p.*, u.nombre as autor FROM publicaciones p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_post);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$post) {
    echo "<div class='tarjeta'><p>La publicación no existe o fue eliminada del ciclo.</p></div>";
    require_once 'includes/footer.php';
    exit();
}

$es_dueno = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $post['usuario_id'];

$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $comentario = trim($_POST['comentario'] ?? '');

    if (empty($comentario)) {
        $errores[] = "El comentario no puede estar vacío.";
    } else {
        $sql_com = "INSERT INTO comentarios (publicacion_id, usuario_id, comentario) VALUES (?, ?, ?)";
        $stmt_com = mysqli_prepare($conexion, $sql_com);
        mysqli_stmt_bind_param($stmt_com, "iis", $id_post, $_SESSION['usuario_id'], $comentario);

        if (mysqli_stmt_execute($stmt_com)) {
            mysqli_stmt_close($stmt_com);
            $_SESSION['flash_mensaje'] = "Comentario publicado.";
            $_SESSION['flash_tipo'] = "exito";
            header("Location: ver_post.php?id=" . $id_post);
            exit();
        }
        mysqli_stmt_close($stmt_com);
    }
}

$sql_c = "SELECT c.*, u.nombre as autor FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE c.publicacion_id = ? ORDER BY c.fecha_creacion ASC";
$stmt_c = mysqli_prepare($conexion, $sql_c);
mysqli_stmt_bind_param($stmt_c, "i", $id_post);
mysqli_stmt_execute($stmt_c);
$comentarios = mysqli_stmt_get_result($stmt_c);
?>

<article class="post-detalle">
    <div class="post-meta">
        <span class="badge-cat"><?php echo e($post['categoria']); ?></span>
        <span><?php echo date('d/m/Y H:i', strtotime($post['fecha_creacion'])); ?></span>
    </div>
    <h2><?php echo e($post['titulo']); ?></h2>
    <p class="post-autor">Publicado por: <strong><?php echo e($post['autor']); ?></strong></p>
    <div class="post-cuerpo">
        <p><?php echo nl2br(e($post['contenido'])); ?></p>
    </div>

    <?php if ($es_dueno || $es_admin): ?>
        <div class="post-acciones">
            <a href="editar_publicacion.php?id=<?php echo $post['id']; ?>" class="btn">Editar</a>
            <a href="eliminar_publicacion.php?id=<?php echo $post['id']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar publicación?');">Eliminar Publicación</a>
        </div>
    <?php endif; ?>
</article>

<section class="seccion-comentarios">
    <h3>Comentarios</h3>

    <?php if (!empty($errores)): ?>
        <div class="alerta alerta-error">
            <ul><?php foreach ($errores as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <form action="ver_post.php?id=<?php echo $id_post; ?>" method="POST" class="form-comentario">
            <textarea name="comentario" rows="3" placeholder="Escribe tu comentario..." required></textarea>
            <button type="submit" class="btn">Comentar</button>
        </form>
    <?php else: ?>
        <p><em>Debes <a href="login.php">iniciar sesión</a> para comentar.</em></p>
    <?php endif; ?>

    <div class="lista-comentarios">
        <?php if (mysqli_num_rows($comentarios) > 0): ?>
            <?php while ($com = mysqli_fetch_assoc($comentarios)): ?>
                <?php 
                    $es_dueno_com = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $com['usuario_id'];
                ?>
                <div class="comentario-card">
                    <div class="com-header">
                        <strong><?php echo e($com['autor']); ?></strong>
                        <span><?php echo date('d/m/Y H:i', strtotime($com['fecha_creacion'])); ?></span>
                    </div>
                    <p><?php echo nl2br(e($com['comentario'])); ?></p>
                    <?php if ($es_dueno_com || $es_admin): ?>
                        <a href="eliminar_comentario.php?id=<?php echo $com['id']; ?>&post_id=<?php echo $id_post; ?>" class="btn-eliminar-sm" onclick="return confirm('¿Eliminar comentario?');">Eliminar</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hay comentarios aún en este post.</p>
        <?php endif; ?>
    </div>
</section>

<?php 
mysqli_stmt_close($stmt_c);
require_once 'includes/footer.php'; 
?>