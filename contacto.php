<?php
require_once __DIR__ . '/config/db.php';

$id_propiedad = $_GET['id'] ?? $_POST['id_propiedad'] ?? null;
$mensaje_exito = '';
$mensaje_error = '';
$propiedad = null;

if ($id_propiedad) {
    $sqlProp = "SELECT id_propiedad, titulo, precio, ubicacion FROM hern_propiedades WHERE id_propiedad = :id";
    $stmtProp = $pdo->prepare($sqlProp);
    $stmtProp->bindParam(':id', $id_propiedad);
    $stmtProp->execute();
    $propiedad = $stmtProp->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefonoInput = preg_replace('/[^0-9]/', '', $_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $id_propiedad = $_POST['id_propiedad'] ?? $_GET['id'] ?? null;

    if ($id_propiedad === '') {
        $id_propiedad = null;
    }

    $telefono = '';

    if (strlen($telefonoInput) === 8) {
        $telefono = '+569' . $telefonoInput;
    } elseif (strlen($telefonoInput) === 9 && str_starts_with($telefonoInput, '9')) {
        $telefono = '+56' . $telefonoInput;
    }

    if ($nombre === '' || $telefonoInput === '') {
        $mensaje_error = 'Por favor ingresa tu nombre y teléfono.';
    } elseif (!preg_match('/^\+569\d{8}$/', $telefono)) {
        $mensaje_error = 'Por favor ingresa un teléfono válido. Debe tener 8 números después de +56 9.';
    } else {
        try {
            $sql = "
                INSERT INTO hern_contactos (
                    id_propiedad,
                    nombre,
                    telefono,
                    correo,
                    mensaje
                ) VALUES (
                    :id_propiedad,
                    :nombre,
                    :telefono,
                    :correo,
                    :mensaje
                )
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(
                ':id_propiedad',
                $id_propiedad,
                $id_propiedad === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            );

            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->bindValue(':correo', $correo !== '' ? $correo : null, $correo !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':mensaje', $mensaje !== '' ? $mensaje : null, $mensaje !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);

            $stmt->execute();

            $mensaje_exito = 'Solicitud enviada correctamente. Luis se contactará contigo a la brevedad.';

            $_POST = [];
        } catch (PDOException $e) {
            $mensaje_error = 'No se pudo enviar la solicitud. Intenta nuevamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar contacto | Luis Hernández Propiedades</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/hern.css?v=2">
    <link rel="stylesheet" href="admin/css/hern_admin.css?v=2">
</head>

<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main class="contact-page">
    <section class="contact-section">
        <div class="container">
            <a href="<?= $id_propiedad ? 'detalle_propiedad.php?id=' . htmlspecialchars($id_propiedad) : 'propiedades.php' ?>" class="back-link">
                ← Volver
            </a>

            <div class="contact-grid">
                <div class="contact-info-card">
                    <span class="property-type">Contacto</span>

                    <h1>Solicitar información</h1>

                    <p>
                        Completa tus datos y Luis podrá contactarte de forma ordenada para responder tus dudas.
                    </p>

                    <?php if ($propiedad): ?>
                        <div class="contact-property-box">
                            <strong>Propiedad de interés</strong>
                            <h3><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <p><?= htmlspecialchars($propiedad['ubicacion']) ?></p>
                            <span><?= htmlspecialchars($propiedad['precio']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="contact-form-card">
                    <?php if ($mensaje_exito): ?>
                        <div class="admin-success-box contact-success-box">
                            <div>
                                <strong>✅ Solicitud enviada correctamente</strong>
                                <p><?= htmlspecialchars($mensaje_exito) ?></p>
                            </div>
                
                            <div class="admin-success-actions">
                                <a href="index.php" class="admin-success-btn primary">
                                    Volver al inicio
                                </a>
                
                                <a href="propiedades.php" class="admin-success-btn secondary">
                                    Ver más opciones
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($mensaje_error): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($mensaje_error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contacto.php">
                        <input type="hidden" name="id_propiedad" value="<?= htmlspecialchars($id_propiedad ?? '') ?>">

                        <div class="form-group-custom">
                            <label for="nombre">Nombre completo *</label>
                            <input 
                                type="text" 
                                id="nombre" 
                                name="nombre" 
                                placeholder="Ej: María González"
                                value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="form-group-custom">
                            <label for="telefono">Teléfono *</label>
                            <div class="phone-input-group">
                                <span>+56 9</span>

                                <input 
                                    type="tel" 
                                    id="telefono" 
                                    name="telefono" 
                                    placeholder="1234 5678"
                                    maxlength="9"
                                    pattern="[0-9]{8,9}"
                                    title="Ingresa solo números. Ej: 12345678"
                                    value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label for="correo">Correo electrónico</label>
                            <input 
                                type="email" 
                                id="correo" 
                                name="correo" 
                                placeholder="Ej: correo@ejemplo.cl"
                                value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                            >
                        </div>

                        <div class="form-group-custom">
                            <label for="mensaje">Mensaje</label>
                            <textarea 
                                id="mensaje" 
                                name="mensaje" 
                                rows="5"
                                placeholder="Puedes indicar tus dudas, horario de contacto o si deseas coordinar una visita."
                            ><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary contact-submit-btn">
                            Enviar solicitud
                        </button>

                        <p class="contact-small-text">
                            Tus datos serán utilizados solo para responder esta solicitud.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

</body>
</html>