<?php
require_once 'includes/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: perfil.php");
    exit();
}

$errores = [];
$nombre = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($nombre) || empty($email) || empty($password)) {
        $errores[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña requiere al menos 6 caracteres.";
    } elseif ($password !== $password_confirm) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (empty($errores)) {
        $sql_check = "SELECT id FROM usuarios WHERE email = ?";
        $stmt_check = mysqli_prepare($conexion, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $errores[] = "El correo ya pertenece a un usuario registrado.";
        }
        mysqli_stmt_close($stmt_check);
    }

    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql_insert = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conexion, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "sss", $nombre, $email, $hash);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            $_SESSION['flash_mensaje'] = "¡Registro exitoso! Ya puedes iniciar sesión.";
            $_SESSION['flash_tipo'] = "exito";
            header("Location: login.php");
            exit();
        }
        mysqli_stmt_close($stmt_insert);
    }
}

require_once 'includes/header.php';
require_once 'includes/nav.php';
?>

<h2>Registro de Usuarios</h2>

<?php if (!empty($errores)): ?>
    <div class="alerta alerta-error">
        <ul><?php foreach ($errores as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form action="registro.php" method="POST" class="formulario">
    <div class="campo">
        <label for="nombre">Nombre Completo:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo e($nombre); ?>" required>
    </div>
    <div class="campo">
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" value="<?php echo e($email); ?>" required>
    </div>
    <div class="campo">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="campo">
        <label for="password_confirm">Confirmar Contraseña:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
    </div>
    <button type="submit" class="btn">Registrarse</button>
</form>

<?php require_once 'includes/footer.php'; ?>