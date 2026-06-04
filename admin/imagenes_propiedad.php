<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$id_propiedad = $_GET['id'] ?? null;
$mensaje_exito = '';
$mensaje_error = '';

if (!$id_propiedad) {
    die("Propiedad no encontrada.");
}

$sqlProp = "SELECT id_propiedad, titulo FROM hern_propiedades WHERE id_propiedad = :id";
$stmtProp = $pdo->prepare($sqlProp);
$stmtProp->bindParam(':id', $id_propiedad);
$stmtProp->execute();
$propiedad = $stmtProp->fetch(PDO::FETCH_ASSOC);

if (!$propiedad) {
    die("Propiedad no encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['imagenes'])) {
        $mensaje_error = 'Debes seleccionar al menos una imagen.';
    } else {
        $carpetaDestino = __DIR__ . '/../assets/img/propiedades/uploads/'; # ruta física
        $rutaPublicaBase = 'assets/img/propiedades/uploads/'; # Ruta pública

        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true);
        }

        $permitidos = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $totalSubidas = 0;

        $sqlOrden = "
            SELECT COALESCE(MAX(orden), 0) + 1 AS siguiente_orden
            FROM hern_imagenes_propiedad
            WHERE id_propiedad = :id
        ";
        $stmtOrden = $pdo->prepare($sqlOrden);
        $stmtOrden->bindParam(':id', $id_propiedad);
        $stmtOrden->execute();
        $ordenActual = $stmtOrden->fetchColumn();

        foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['imagenes']['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            $tipoArchivo = $_FILES['imagenes']['type'][$index];

            if (!in_array($tipoArchivo, $permitidos)) {
                continue;
            }

            $nombreOriginal = $_FILES['imagenes']['name'][$index];
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);

            $nombreSeguro = 'propiedad_' . $id_propiedad . '_' . time() . '_' . $index . '.' . strtolower($extension);

            $rutaDestino = $carpetaDestino . $nombreSeguro;
            $rutaPublica = $rutaPublicaBase . $nombreSeguro;

            if (move_uploaded_file($tmpName, $rutaDestino)) {
                $sql = "
                    INSERT INTO hern_imagenes_propiedad (
                        id_propiedad,
                        ruta_imagen,
                        orden
                    ) VALUES (
                        :id_propiedad,
                        :ruta_imagen,
                        :orden
                    )
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_propiedad', $id_propiedad);
                $stmt->bindParam(':ruta_imagen', $rutaPublica);
                $stmt->bindParam(':orden', $ordenActual);
                $stmt->execute();

                $ordenActual++;
                $totalSubidas++;
            }
        }

        if ($totalSubidas > 0) {
            $mensaje_exito = 'Imágenes subidas correctamente.';
        } else {
            $mensaje_error = 'No se pudo subir ninguna imagen. Revisa el formato.';
        }
    }
}

$sqlImg = "
    SELECT id_imagen, ruta_imagen, orden
    FROM hern_imagenes_propiedad
    WHERE id_propiedad = :id
    ORDER BY orden ASC
";

$stmtImg = $pdo->prepare($sqlImg);
$stmtImg->bindParam(':id', $id_propiedad);
$stmtImg->execute();
$imagenes = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Imágenes de publicación | Admin Hern Propiedades</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/hern.css?v=2">
    <link rel="stylesheet" href="css/hern_admin.css?v=2">
</head>

<body>

    <?php include __DIR__ . '/includes/navbar_admin.php'; ?>

    <main class="contact-page">
        <section class="contact-section">
            <div class="container">
                <a href="propiedad_nueva.php" class="back-link">← Nueva publicación</a>

                <div class="contact-grid">
                    <div class="contact-info-card">
                        <span class="property-type">Imágenes</span>
                        <h1>Cargar imágenes</h1>

                        <p>
                            Agrega las rutas de las fotografías asociadas a:
                        </p>

                        <div class="contact-property-box">
                            <strong>Publicación</strong>
                            <h3><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                        </div>
                        <br>
                        <a href="../detalle_propiedad.php?id=<?= htmlspecialchars($id_propiedad) ?>"
                            class="btn btn-primary contact-submit-btn">
                            Ver publicación
                        </a>
                    </div>

                    <div class="contact-form-card">
                        <?php if ($mensaje_exito): ?>
                            <div class="admin-success-box">
                                <div>
                                    <strong>✅ Imágenes cargadas</strong>
                                    <p><?= htmlspecialchars($mensaje_exito) ?></p>
                                </div>

                                <div class="admin-success-actions">
                                    <a 
                                        href="propiedades.php" 
                                        class="admin-success-btn primary"
                                    >
                                        Volver a propiedades
                                    </a>

                                    <a 
                                        href="../detalle_propiedad.php?id=<?= htmlspecialchars($id_propiedad) ?>" 
                                        target="_blank" 
                                        class="admin-success-btn secondary"
                                    >
                                        Ver publicación
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($mensaje_error): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($mensaje_error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group-custom">
                                <label>Seleccionar imágenes *</label>
                                <input type="file" name="imagenes[]" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    multiple required>
                                <small>Puede seleccionar una o varias imágenes desde el celular o computador.</small>
                            </div>

                            <button type="submit" class="btn btn-primary contact-submit-btn">
                                Subir imágenes
                            </button>
                        </form>

                        <?php if (count($imagenes) > 0): ?>
                            <div class="admin-images-list">
                                <?php foreach ($imagenes as $img): ?>
                                    <div class="admin-image-item">
                                        <img src="../<?= htmlspecialchars($img['ruta_imagen']) ?>" alt="Imagen cargada">

                                        <div>
                                            <strong>Orden <?= htmlspecialchars($img['orden']) ?></strong>
                                            <p><?= htmlspecialchars($img['ruta_imagen']) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?php include __DIR__ . '/includes/footer_admin.php'; ?>