<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_contacto = $_POST['id_contacto'] ?? null;
    $nuevo_estado = $_POST['estado'] ?? null;

    if ($id_contacto && $nuevo_estado) {
        $sqlUpdate = "
            UPDATE hern_contactos
            SET estado = :estado
            WHERE id_contacto = :id_contacto
        ";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':estado', $nuevo_estado);
        $stmtUpdate->bindParam(':id_contacto', $id_contacto);
        $stmtUpdate->execute();
    }
}

$sql = "
    SELECT 
        c.id_contacto,
        c.nombre,
        c.telefono,
        c.correo,
        c.mensaje,
        c.fecha_contacto,
        c.estado,
        p.titulo AS propiedad,
        p.precio,
        p.ubicacion
    FROM hern_contactos c
    LEFT JOIN hern_propiedades p
        ON c.id_propiedad = p.id_propiedad
    ORDER BY c.fecha_contacto DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function limpiarTelefonoWhatsapp($telefono) {
    $telefono = preg_replace('/[^0-9]/', '', $telefono);

    if (str_starts_with($telefono, '56')) {
        return $telefono;
    }

    if (str_starts_with($telefono, '9')) {
        return '56' . $telefono;
    }

    return '56' . $telefono;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes recibidas | Hern Admin</title>
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
                <span class="property-type">Panel simple</span>
                <h1>Solicitudes recibidas</h1>
                <p>Contactos enviados desde el formulario de propiedades.</p>
            </div>

            <a href="../index.php" class="btn btn-primary admin-btn">
                Volver al sitio
            </a>
        </div>

        <?php if (count($contactos) > 0): ?>
            <div class="admin-contact-list">
                <?php foreach ($contactos as $contacto): ?>
                    <?php
                        $telefonoWhatsapp = limpiarTelefonoWhatsapp($contacto['telefono']);
                        $esContactado = $contacto['estado'] === 'contactado';
                        $tienePropiedad = !empty($contacto['propiedad']);
                    ?>

                    <article class="admin-contact-card <?= $esContactado ? 'contactado' : 'pendiente' ?>">
                        <div class="admin-contact-top">
                            <div>
                                <h2><?= htmlspecialchars($contacto['nombre']) ?></h2>

                                <div class="admin-contact-data">
                                    <p>
                                        📞 
                                        <a href="tel:<?= htmlspecialchars($contacto['telefono']) ?>">
                                            <?= htmlspecialchars($contacto['telefono']) ?>
                                        </a>
                                    </p>

                                    <?php if (!empty($contacto['correo'])): ?>
                                        <p>
                                            ✉️ 
                                            <a href="mailto:<?= htmlspecialchars($contacto['correo']) ?>">
                                                <?= htmlspecialchars($contacto['correo']) ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <span class="admin-status <?= $esContactado ? 'contactado' : 'pendiente' ?>">
                                <?= htmlspecialchars($contacto['estado']) ?>
                            </span>
                        </div>

                        <?php if ($tienePropiedad): ?>
                            <div class="admin-property-box">
                                <strong>Propiedad consultada</strong>
                                <h3><?= htmlspecialchars($contacto['propiedad']) ?></h3>
                                <p><?= htmlspecialchars($contacto['ubicacion']) ?></p>
                                <span><?= htmlspecialchars($contacto['precio']) ?></span>
                            </div>
                        <?php else: ?>
                            <div class="admin-property-box general-query">
                                <strong>Consulta general</strong>
                                <p>No asociada a una propiedad específica.</p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($contacto['mensaje'])): ?>
                            <div class="admin-message">
                                <strong>Mensaje</strong>
                                <p><?= nl2br(htmlspecialchars($contacto['mensaje'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="admin-actions">
                            <a 
                                href="https://wa.me/<?= htmlspecialchars($telefonoWhatsapp) ?>" 
                                target="_blank"
                                class="admin-whatsapp-btn"
                            >
                                Contactar por WhatsApp
                            </a>

                            <?php if (!$esContactado): ?>
                                <form method="POST">
                                    <input type="hidden" name="id_contacto" value="<?= htmlspecialchars($contacto['id_contacto']) ?>">
                                    <input type="hidden" name="estado" value="contactado">

                                    <button type="submit" class="admin-secondary-btn">
                                        Marcar como contactado
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST">
                                    <input type="hidden" name="id_contacto" value="<?= htmlspecialchars($contacto['id_contacto']) ?>">
                                    <input type="hidden" name="estado" value="pendiente">

                                    <button type="submit" class="admin-secondary-btn">
                                        Volver a pendiente
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <div class="admin-date">
                            Recibido el <?= htmlspecialchars($contacto['fecha_contacto']) ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="admin-empty">
                <h2>Aún no hay solicitudes</h2>
                <p>Cuando alguien complete el formulario, aparecerá aquí.</p>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/includes/footer_admin.php'; ?>