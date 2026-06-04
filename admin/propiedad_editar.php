<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$id_propiedad = $_GET['id'] ?? null;
$mensaje_exito = '';
$mensaje_error = '';

if (!$id_propiedad) {
    die("Propiedad no encontrada.");
}

$sqlProp = "SELECT * FROM hern_propiedades WHERE id_propiedad = :id";
$stmtProp = $pdo->prepare($sqlProp);
$stmtProp->bindParam(':id', $id_propiedad);
$stmtProp->execute();
$propiedad = $stmtProp->fetch(PDO::FETCH_ASSOC);

if (!$propiedad) {
    die("Propiedad no encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $dormitorios = $_POST['dormitorios'] ?? 0;
    $banos = $_POST['banos'] ?? 0;
    $metros_construidos = $_POST['metros_construidos'] ?? 0;
    $metros_terreno = $_POST['metros_terreno'] ?? 0;
    $descripcion = trim($_POST['descripcion'] ?? '');
    $caracteristicas = trim($_POST['caracteristicas'] ?? '');
    $estado = $_POST['estado'] ?? 'disponible';

    if ($titulo === '' || $precio === '' || $ubicacion === '' || $tipo === '') {
        $mensaje_error = 'Completa los campos obligatorios.';
    } else {
        try {
            $sqlUpdate = "
                UPDATE hern_propiedades
                SET
                    titulo = :titulo,
                    precio = :precio,
                    ubicacion = :ubicacion,
                    tipo = :tipo,
                    dormitorios = :dormitorios,
                    banos = :banos,
                    metros_construidos = :metros_construidos,
                    metros_terreno = :metros_terreno,
                    descripcion = :descripcion,
                    caracteristicas = :caracteristicas,
                    estado = :estado
                WHERE id_propiedad = :id_propiedad
            ";

            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':titulo', $titulo);
            $stmtUpdate->bindParam(':precio', $precio);
            $stmtUpdate->bindParam(':ubicacion', $ubicacion);
            $stmtUpdate->bindParam(':tipo', $tipo);
            $stmtUpdate->bindParam(':dormitorios', $dormitorios);
            $stmtUpdate->bindParam(':banos', $banos);
            $stmtUpdate->bindParam(':metros_construidos', $metros_construidos);
            $stmtUpdate->bindParam(':metros_terreno', $metros_terreno);
            $stmtUpdate->bindParam(':descripcion', $descripcion);
            $stmtUpdate->bindParam(':caracteristicas', $caracteristicas);
            $stmtUpdate->bindParam(':estado', $estado);
            $stmtUpdate->bindParam(':id_propiedad', $id_propiedad);
            $stmtUpdate->execute();

            $mensaje_exito = 'Propiedad actualizada correctamente.';

            $stmtProp->execute();
            $propiedad = $stmtProp->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $mensaje_error = 'No se pudo actualizar la propiedad.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar propiedad | Hern Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/hern.css?v=2">
    <link rel="stylesheet" href="css/hern_admin.css?v=2">
</head>

<body>

<?php include __DIR__ . '/includes/navbar_admin.php'; ?>

<main class="admin-form-page">
    <div class="container">

        <div class="admin-form-header">
            <div>
                <a href="propiedades.php" class="back-link">← Volver a propiedades</a>
                    <br>
                <span class="property-type">Administrador</span>

                <h2>Editar publicación</h2>

                <p>
                    Modifica la información principal, cambia el estado o actualiza los detalles de la propiedad.
                </p>
            </div>
        </div>
        <br>
        <section class="admin-form-card">
            <?php if ($mensaje_exito): ?>
                <div class="admin-success-box">
                    <div>
                        <strong>✅ Cambios guardados correctamente</strong>
                        <p><?= htmlspecialchars($mensaje_exito) ?></p>
                    </div>
        
                    <div class="admin-success-actions">
                        <a href="propiedades.php" class="admin-success-btn primary">
                            Volver a propiedades
                        </a>
        
                        <a href="../detalle_propiedad.php?id=<?= htmlspecialchars($id_propiedad) ?>" target="_blank"
                            class="admin-success-btn secondary">
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

            <form method="POST">

                <div class="admin-form-row">
                    <div class="form-group-custom">
                        <label>Título *</label>
                        <input 
                            type="text" 
                            name="titulo" 
                            value="<?= htmlspecialchars($propiedad['titulo']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group-custom">
                        <label>Precio *</label>
                        <input 
                            type="text" 
                            name="precio" 
                            value="<?= htmlspecialchars($propiedad['precio']) ?>"
                            required
                        >
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="form-group-custom">
                        <label>Ubicación *</label>
                        <input 
                            type="text" 
                            name="ubicacion" 
                            value="<?= htmlspecialchars($propiedad['ubicacion']) ?>"
                            required
                        >
                    </div>

                    <div class="form-group-custom">
                        <label>Tipo *</label>
                        <select name="tipo" required>
                            <option value="">Seleccionar</option>
                            <option value="Casa" <?= $propiedad['tipo'] === 'Casa' ? 'selected' : '' ?>>Casa</option>
                            <option value="Departamento" <?= $propiedad['tipo'] === 'Departamento' ? 'selected' : '' ?>>Departamento</option>
                            <option value="Terreno" <?= $propiedad['tipo'] === 'Terreno' ? 'selected' : '' ?>>Terreno</option>
                            <option value="Parcela" <?= $propiedad['tipo'] === 'Parcela' ? 'selected' : '' ?>>Parcela</option>
                            <option value="Local comercial" <?= $propiedad['tipo'] === 'Local comercial' ? 'selected' : '' ?>>Local comercial</option>
                        </select>
                    </div>
                </div>

                <div class="admin-form-row admin-form-row-4">
                    <div class="form-group-custom">
                        <label>Dormitorios</label>
                        <input 
                            type="number" 
                            name="dormitorios" 
                            min="0" 
                            value="<?= htmlspecialchars($propiedad['dormitorios']) ?>"
                        >
                    </div>

                    <div class="form-group-custom">
                        <label>Baños</label>
                        <input 
                            type="number" 
                            name="banos" 
                            min="0" 
                            value="<?= htmlspecialchars($propiedad['banos']) ?>"
                        >
                    </div>

                    <div class="form-group-custom">
                        <label>m² construidos</label>
                        <input 
                            type="number" 
                            name="metros_construidos" 
                            min="0" 
                            value="<?= htmlspecialchars($propiedad['metros_construidos']) ?>"
                        >
                    </div>

                    <div class="form-group-custom">
                        <label>m² terreno</label>
                        <input 
                            type="number" 
                            name="metros_terreno" 
                            min="0" 
                            value="<?= htmlspecialchars($propiedad['metros_terreno']) ?>"
                        >
                    </div>
                </div>

                <div class="form-group-custom">
                    <label>Descripción</label>
                    <textarea 
                        name="descripcion" 
                        rows="5"
                    ><?= htmlspecialchars($propiedad['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="form-group-custom">
                    <label>Características</label>
                    <textarea 
                        name="caracteristicas" 
                        rows="5"
                    ><?= htmlspecialchars($propiedad['caracteristicas'] ?? '') ?></textarea>
                </div>

                <div class="admin-form-row">
                    <div class="form-group-custom">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="disponible" <?= $propiedad['estado'] === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                            <option value="reservada" <?= $propiedad['estado'] === 'reservada' ? 'selected' : '' ?>>Reservada</option>
                            <option value="vendida" <?= $propiedad['estado'] === 'vendida' ? 'selected' : '' ?>>Vendida</option>
                        </select>
                    </div>

                    <div class="form-group-custom admin-submit-group">
                        <button type="submit" class="btn btn-primary contact-submit-btn">
                            Guardar cambios
                        </button>
                    </div>
                </div>

                <div class="admin-edit-actions">
                    <a 
                        href="imagenes_propiedad.php?id=<?= htmlspecialchars($id_propiedad) ?>" 
                        class="admin-secondary-btn"
                    >
                        Administrar imágenes
                    </a>

                    <a 
                        href="../detalle_propiedad.php?id=<?= htmlspecialchars($id_propiedad) ?>" 
                        target="_blank"
                        class="admin-secondary-btn"
                    >
                        Ver publicación
                    </a>
                </div>

            </form>
        </section>

    </div>
</main>

<?php include __DIR__ . '/includes/footer_admin.php'; ?>