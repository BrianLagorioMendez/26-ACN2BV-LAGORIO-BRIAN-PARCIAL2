<?php
require_once 'includes/conexion.php';
require_once 'includes/nav.php';

$busqueda = trim($_GET['buscar'] ?? '');
$categoria_filtro = trim($_GET['categoria'] ?? '');
$es_admin = isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';

if (!empty($busqueda)) {
    $sql = "SELECT p.*, u.nombre as autor, (SELECT COUNT(*) FROM comentarios c WHERE c.publicacion_id = p.id) as cant_comentarios 
            FROM publicaciones p 
            JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.titulo LIKE ? OR p.contenido LIKE ? 
            ORDER BY p.fecha_creacion DESC";
    $stmt = mysqli_prepare($conexion, $sql);
    $term = "%" . $busqueda . "%";
    mysqli_stmt_bind_param($stmt, "ss", $term, $term);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
} elseif (!empty($categoria_filtro)) {
    $sql = "SELECT p.*, u.nombre as autor, (SELECT COUNT(*) FROM comentarios c WHERE c.publicacion_id = p.id) as cant_comentarios 
            FROM publicaciones p 
            JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.categoria = ? 
            ORDER BY p.fecha_creacion DESC";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $categoria_filtro);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT p.*, u.nombre as autor, (SELECT COUNT(*) FROM comentarios c WHERE c.publicacion_id = p.id) as cant_comentarios 
            FROM publicaciones p 
            JOIN usuarios u ON p.usuario_id = u.id 
            ORDER BY p.fecha_creacion DESC";
    $resultado = mysqli_query($conexion, $sql);
}
?>

<div class="muro-header">
    <h2>Teorías y Publicaciones del Tiempo</h2>
    <p>Explora las teorías de los viajeros o comparte tus descubrimientos.</p>
</div>

<form action="index.php" method="GET" class="form-filtro">
    <input type="text" name="buscar" placeholder="Buscar palabras clave..." value="<?php echo e($busqueda); ?>">
    <select name="categoria">
        <option value="">Todas las Categorías</option>
        <option value="Teorías" <?php echo $categoria_filtro === 'Teorías' ? 'selected' : ''; ?>>Teorías</option>
        <option value="Multiverso" <?php echo $categoria_filtro === 'Multiverso' ? 'selected' : ''; ?>>Multiverso</option>
        <option value="Misterios" <?php echo $categoria_filtro === 'Misterios' ? 'selected' : ''; ?>>Misterios</option>
        <option value="Ciencia" <?php echo $categoria_filtro === 'Ciencia' ? 'selected' : ''; ?>>Ciencia</option>
        <option value="Investigación" <?php echo $categoria_filtro === 'Investigación' ? 'selected' : ''; ?>>Investigación</option>
    </select>
    <button type="submit" class="btn">Filtrar</button>
    <?php if (!empty($busqueda) || !empty($categoria_filtro)): ?>
        <a href="index.php" class="btn btn-secundario">Limpiar</a>
    <?php endif; ?>
</form>

<div class="foro-grid">
    <?php if (mysqli_num_rows($resultado) > 0): ?>
        <?php while ($pub = mysqli_fetch_assoc($resultado)): ?>
            <?php 
                $es_dueno = isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $pub['usuario_id'];
            ?>
            <article class="post-card">
                <div class="post-meta">
                    <span class="badge-cat"><?php echo e($pub['categoria']); ?></span>
                    <span class="post-fecha"><?php echo date('d/m/Y H:i', strtotime($pub['fecha_creacion'])); ?></span>
                </div>
                <h3 class="post-titulo">
                    <a href="ver_post.php?id=<?php echo $pub['id']; ?>"><?php echo e($pub['titulo']); ?></a>
                </h3>
                <p class="post-contenido"><?php echo e(mb_strimwidth($pub['contenido'], 0, 180, "...")); ?></p>
                
                <div class="post-footer">
                    <span>Autor: <strong><?php echo e($pub['autor']); ?></strong> | Comentarios: (<?php echo $pub['cant_comentarios']; ?>)</span>
                    <div>
                        <a href="ver_post.php?id=<?php echo $pub['id']; ?>" class="btn-link">Ver Discusión</a>
                        <?php if ($es_dueno || $es_admin): ?>
                            | <a href="editar_publicacion.php?id=<?php echo $pub['id']; ?>" class="btn-editar">Editar</a>
                            | <a href="eliminar_publicacion.php?id=<?php echo $pub['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar publicación?');">Eliminar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="tarjeta"><p>No se encontraron publicaciones en este ciclo.</p></div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>