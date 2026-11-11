<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

$errores = [];
$titulo = '';
$categoria = '';
$contenido = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if (empty($titulo) || empty($categoria) || empty($contenido)) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    if (empty($errores)) {
        $sql = "INSERT INTO publicaciones (usuario_id, titulo, categoria, contenido) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "isss", $_SESSION['usuario_id'], $titulo, $categoria, $contenido);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            $_SESSION['flash_mensaje'] = "¡Publicación creada con éxito!";
            $_SESSION['flash_tipo'] = "exito";
            header("Location: index.php");
            exit();
        } else {
            $errores[] = "Error al guardar la publicación.";
        }
        mysqli_stmt_close($stmt);
    }
}

require_once 'includes/nav.php';
?>

<h2>Publicar Nueva Teoría</h2>

<?php if (!empty($errores)): ?>
    <div class="alerta alerta-error">
        <ul><?php foreach ($errores as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form action="crear_publicacion.php" method="POST" class="formulario">
    <div class="campo">
        <label for="titulo">Título de la Teoría:</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo e($titulo); ?>" required>
    </div>
    <div class="campo">
        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria" required>
            <option value="">Seleccione una opción</option>
            <option value="Teorías">Teorías</option>
            <option value="Multiverso">Multiverso</option>
            <option value="Misterios">Misterios</option>
            <option value="Ciencia">Ciencia</option>
            <option value="Investigación">Investigación</option>
        </select>
    </div>
    <div class="campo">
        <label for="contenido">Contenido:</label>
        <textarea id="contenido" name="contenido" rows="6" required><?php echo e($contenido); ?></textarea>
    </div>
    <button type="submit" class="btn">Publicar</button>
</form>

<?php require_once 'includes/footer.php'; ?>