<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

$id_com = $_GET['id'] ?? 0;
$id_post = $_GET['post_id'] ?? 0;

if ($id_com > 0) {
    $sql = "DELETE FROM comentarios WHERE id = ? AND usuario_id = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id_com, $_SESSION['usuario_id']);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['flash_mensaje'] = "Comentario borrado.";
        $_SESSION['flash_tipo'] = "exito";
    }
    mysqli_stmt_close($stmt);
}

header("Location: ver_post.php?id=" . $id_post);
exit();