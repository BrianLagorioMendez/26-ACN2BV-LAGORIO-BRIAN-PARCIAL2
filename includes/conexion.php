<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "parcial_dark";

$conexion = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conexion) {
    die("Error crítico de conexión a la base de datos de Winden: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");