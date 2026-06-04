<?php
session_start();

require_once __DIR__ . '/../config/db.php';

$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logueado'] = true;
        $_SESSION['admin_usuario'] = $usuario;

        header('Location: contactos.php');
        exit;
    } else {
        $mensaje_error = 'Usuario o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingreso administrador | Hern Propiedades</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/hern.css">
    <link rel="stylesheet" href="css/hern_admin.css">
</head>

<body>

<main class="admin-login-page">
    <section class="admin-login-card">
        <span class="property-type">Administrador</span>

        <h1>Ingreso al panel</h1>

        <p>
            Accede para revisar solicitudes, publicar propiedades y administrar imágenes.
        </p>

        <?php if ($mensaje_error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($mensaje_error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group-custom">
                <label>Usuario</label>
                <input type="text" name="usuario" required>
            </div>

            <div class="form-group-custom">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary contact-submit-btn">
                Ingresar
            </button>
        </form>
    </section>
</main>

</body>
</html>