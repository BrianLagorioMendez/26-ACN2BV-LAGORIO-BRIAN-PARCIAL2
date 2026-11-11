<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['flash_mensaje'] = "Debes iniciar sesión para realizar esta acción en el foro.";
    $_SESSION['flash_tipo'] = "error";
    header("Location: login.php");
    exit();
}