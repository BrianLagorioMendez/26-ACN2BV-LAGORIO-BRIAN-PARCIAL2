<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

$id_post = $_GET['id'] ?? 0;

$sql_check = "SELECT * FROM publicaciones WHERE id = ? AND usuario_id = ?";
$stmt_check = mysqli_prepare($conexion, $sql_check);
mysqli_stmt_bind_param($stmt_check, "ii", $id_post, $_SESSION['usuario_id']);
mysqli_stmt_execute($stmt_check);
$res_check = mysqli_stmt_get_result($stmt_check);
$post = mysqli_fetch_assoc($res_check);
mysqli_stmt_close($stmt_check);

if (!$post) {
    $_SESSION['flash_mensaje'] = "No tienes permisos para editar esta publicación.";
    $_SESSION['flash_tipo'] = "error";
    header("Location: index.php");
    exit();
}

$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if (empty($titulo) || empty($categoria) || empty($contenido)) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    if (empty($errores)) {
        $sql_upd = "UPDATE publicaciones SET titulo = ?, categoria = ?, contenido = ? WHERE id = ? AND usuario_id = ?";
        $stmt_upd = mysqli_prepare($conexion, $sql_upd);
        mysqli_stmt_bind_param($stmt_upd, "sssii", $titulo, $categoria, $contenido, $id_post, $_SESSION['usuario_id']);

        if (mysqli_stmt_execute($stmt_upd)) {
            mysqli_stmt_close($stmt_upd);
            $_SESSION['flash_mensaje'] = "Publicación actualizada correctamente.";
            $_SESSION['flash_tipo'] = "exito";
            header("Location: ver_post.php?id=" . $id_post);
            exit();
        }
        mysqli_stmt_close($stmt_upd);
    }
}

require_once 'includes/header.php';
require_once 'includes/nav.php';
?>

<h2>Editar Publicación</h2>

<?php if (!empty($errores)): ?>
    <div class="alerta alerta-error">
        <ul><?php foreach ($errores as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form action="editar_publicacion.php?id=<?php echo $id_post; ?>" method="POST" class="formulario">
    <div class="campo">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo e($post['titulo']); ?>" required>
    </div>
    <div class="campo">
        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria" required>
            <option value="Teorías" <?php echo $post['categoria'] === 'Teorías' ? 'selected' : ''; ?>>Teorías</option>
            <option value="Multiverso" <?php echo $post['categoria'] === 'Multiverso' ? 'selected' : ''; ?>>Multiverso</option>
            <option value="Misterios" <?php echo $post['categoria'] === 'Misterios' ? 'selected' : ''; ?>>Misterios</option>
            <option value="Ciencia" <?php echo $post['categoria'] === 'Ciencia' ? 'selected' : ''; ?>>Ciencia</option>
            <option value="Investigación" <?php echo $post['categoria'] === 'Investigación' ? 'selected' : ''; ?>>Investigación</option>
        </select>
    </div>
    <div class="campo">
        <label for="contenido">Contenido:</label>
        <textarea id="contenido" name="contenido" rows="6" required><?php echo e($post['contenido']); ?></textarea>
    </div>
    <button type="submit" class="btn">Guardar Cambios</button>
</form>

<?php require_once 'includes/footer.php'; ?>