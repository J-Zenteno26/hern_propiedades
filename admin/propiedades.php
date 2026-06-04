<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$sql = "
    SELECT DISTINCT ON (p.id_propiedad)
        p.id_propiedad,
        p.titulo,
        p.precio,
        p.ubicacion,
        p.tipo,
        p.estado,
        p.fecha_publicacion,
        img.ruta_imagen
    FROM hern_propiedades p
    LEFT JOIN hern_imagenes_propiedad img
        ON p.id_propiedad = img.id_propiedad
    ORDER BY p.id_propiedad, img.orden ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar propiedades | Hern Propiedades</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/hern.css?v=2">
    <link rel="stylesheet" href="css/hern_admin.css?v=2">
</head>

<body>

<?php include __DIR__ . '/includes/navbar_admin.php'; ?>

<main class="admin-page">
    <div class="container">

        <div class="admin-header">
            <div>
                <span class="property-type">Administrador</span>
                <h1>Propiedades publicadas</h1>
                <p>Revisa, administra imágenes y accede a cada publicación.</p>
            </div>

            <a href="propiedad_nueva.php" class="btn btn-primary admin-btn">
                + Nueva propiedad
            </a>
        </div>

        <?php if (count($propiedades) > 0): ?>
            <div class="admin-properties-grid">
                <?php foreach ($propiedades as $propiedad): ?>
                    <article class="admin-property-card">
                        <div class="admin-property-img">
                            <img 
                                src="../<?= htmlspecialchars($propiedad['ruta_imagen'] ?: '/assets/img/propiedades/sin-imagen.jpg') ?>" 
                                alt="<?= htmlspecialchars($propiedad['titulo']) ?>"
                            >
                        </div>

                        <div class="admin-property-content">
                            <div class="admin-property-top">
                                <span class="admin-status <?= htmlspecialchars($propiedad['estado']) ?>">
                                    <?= htmlspecialchars($propiedad['estado']) ?>
                                </span>

                                <span class="admin-property-type">
                                    <?= htmlspecialchars($propiedad['tipo']) ?>
                                </span>
                            </div>

                            <h2><?= htmlspecialchars($propiedad['titulo']) ?></h2>

                            <p class="admin-property-location">
                                <?= htmlspecialchars($propiedad['ubicacion']) ?>
                            </p>

                            <p class="admin-property-price">
                                <?= htmlspecialchars($propiedad['precio']) ?>
                            </p>

                            <div class="admin-property-actions">
                                <a 
                                    href="../detalle_propiedad.php?id=<?= htmlspecialchars($propiedad['id_propiedad']) ?>" 
                                    target="_blank"
                                    class="admin-secondary-btn"
                                >
                                    Ver 
                                </a>
                                <a 
                                    href="propiedad_editar.php?id=<?= htmlspecialchars($propiedad['id_propiedad']) ?>" 
                                    class="admin-secondary-btn"
                                >
                                    Editar
                                </a>

                                <a 
                                    href="imagenes_propiedad.php?id=<?= htmlspecialchars($propiedad['id_propiedad']) ?>" 
                                    class="admin-secondary-btn"
                                >
                                    Imágenes
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="admin-empty">
                <h2>Aún no hay propiedades</h2>
                <p>Cuando agregues una publicación, aparecerá aquí.</p>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/includes/footer_admin.php'; ?>