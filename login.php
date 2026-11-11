<?php
require_once 'includes/conexion.php';
require_once 'includes/header.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: perfil.php");
    exit();
}

$errores = [];
$email_recordado = $_COOKIE['remember_user'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $recordar = isset($_POST['recordar']);

    if (empty($email) || empty($password)) {
        $errores[] = "Complete correo y contraseña.";
    } else {
        $sql = "SELECT id, nombre, email, password FROM usuarios WHERE email = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($res)) {
            // Verifica contra hash encriptado O contra texto plano (compatibilidad total de pruebas)
            $password_valida = password_verify($password, $user['password']) || ($password === $user['password']);

            if ($password_valida) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_email'] = $user['email'];

                if ($recordar) {
                    setcookie('remember_user', $email, time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_user', '', time() - 3600, "/");
                }

                $_SESSION['flash_mensaje'] = "Bienvenido/a " . $user['nombre'];
                $_SESSION['flash_tipo'] = "exito";
                header("Location: perfil.php");
                exit();
            } else {
                $errores[] = "Credenciales incorrectas.";
            }
        } else {
            $errores[] = "Credenciales incorrectas.";
        }
        mysqli_stmt_close($stmt);
    }
}

require_once 'includes/nav.php';
?>

<h2>Iniciar Sesión</h2>

<?php if (!empty($errores)): ?>
    <div class="alerta alerta-error">
        <ul><?php foreach ($errores as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form action="login.php" method="POST" class="formulario">
    <div class="campo">
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" value="<?php echo e($email_recordado); ?>" required>
    </div>
    <div class="campo">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="campo campo-checkbox">
        <label>
            <input type="checkbox" name="recordar" <?php echo !empty($email_recordado) ? 'checked' : ''; ?>> 
            Recordar mi correo electrónico
        </label>
    </div>
    <button type="submit" class="btn">Ingresar</button>
</form>

<?php require_once 'includes/footer.php'; ?>