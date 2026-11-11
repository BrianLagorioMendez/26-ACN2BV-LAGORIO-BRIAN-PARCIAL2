<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

$id_comentario = $_GET['id'] ?? 0;
$post_id = $_GET['post_id'] ?? 0;
$es_admin = (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin');

if ($id_comentario > 0) {
    if ($es_admin) {
        $sql = "DELETE FROM comentarios WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_comentario);
    } else {
        $sql = "DELETE FROM comentarios WHERE id = ? AND usuario_id = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $id_comentario, $_SESSION['usuario_id']);
    }

    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['flash_mensaje'] = "Comentario eliminado correctamente.";
        $_SESSION['flash_tipo'] = "exito";
    } else {
        $_SESSION['flash_mensaje'] = "No tienes permisos para eliminar este comentario.";
        $_SESSION['flash_tipo'] = "error";
    }
    mysqli_stmt_close($stmt);
}

header("Location: ver_post.php?id=" . $post_id);
exit();