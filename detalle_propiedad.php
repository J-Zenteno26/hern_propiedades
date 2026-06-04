<?php
require_once __DIR__ . '/config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Propiedad no encontrada.");
}

$sql = "SELECT * FROM hern_propiedades WHERE id_propiedad = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$propiedad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$propiedad) {
    die("Propiedad no encontrada.");
}

$sqlImg = "SELECT * FROM hern_imagenes_propiedad WHERE id_propiedad = :id ORDER BY orden ASC";
$stmtImg = $pdo->prepare($sqlImg);
$stmtImg->bindParam(':id', $id);
$stmtImg->execute();
$imagenes = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

$imagenPrincipal = $imagenes[0]['ruta_imagen'] ?? 'assets/img/propiedades/upload/sin-imagen.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($propiedad['titulo']) ?> | Luis Hernández Propiedades</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/hern.css?v=20">
</head>

<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main class="property-detail-page">

    <section class="property-detail-hero">
        <div class="container">

            <a href="propiedades.php" class="back-link">
                ← Volver al catálogo
            </a>

            <div class="property-detail-grid">

                <div>
                    <div class="property-gallery">
                        <img 
                            id="galleryMainImage"
                            src="<?= htmlspecialchars($imagenPrincipal) ?>"
                            alt="<?= htmlspecialchars($propiedad['titulo']) ?>"
                            class="property-main-image"
                        >

                        <?php if (count($imagenes) > 1): ?>
                            <div class="property-gallery-controls">
                                <button class="gallery-btn gallery-prev" type="button">‹</button>
                                <button class="gallery-btn gallery-next" type="button">›</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (count($imagenes) > 1): ?>
                        <div class="property-thumbs">
                            <?php foreach ($imagenes as $index => $img): ?>
                                <img 
                                    src="<?= htmlspecialchars($img['ruta_imagen']) ?>"
                                    alt="Imagen de propiedad"
                                    class="<?= $index === 0 ? 'active' : '' ?>"
                                    data-index="<?= $index ?>"
                                >
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="property-summary-card">
                    <span class="property-type">
                        <?= htmlspecialchars($propiedad['tipo']) ?>
                    </span>

                    <h1><?= htmlspecialchars($propiedad['titulo']) ?></h1>

                    <p class="property-price">
                        <?= htmlspecialchars($propiedad['precio']) ?>
                    </p>

                    <p class="property-location">
                        <?= htmlspecialchars($propiedad['ubicacion']) ?>
                    </p>

                    <div class="property-specs">
                        <div>
                            <strong><?= htmlspecialchars($propiedad['dormitorios']) ?></strong>
                            <span>Dormitorios</span>
                        </div>

                        <div>
                            <strong><?= htmlspecialchars($propiedad['banos']) ?></strong>
                            <span>Baños</span>
                        </div>

                        <div>
                            <strong><?= htmlspecialchars($propiedad['metros_construidos']) ?> m²</strong>
                            <span>Construidos</span>
                        </div>

                        <div>
                            <strong><?= htmlspecialchars($propiedad['metros_terreno']) ?> m²</strong>
                            <span>Terreno</span>
                        </div>
                    </div>

                    <a 
                        href="contacto.php?id=<?= htmlspecialchars($propiedad['id_propiedad']) ?>"
                        class="btn btn-primary property-contact-btn"
                    >
                        Solicitar información
                    </a>

                    <p class="contact-note">
                        Luis recibirá tu solicitud y podrá contactarte de forma ordenada.
                    </p>
                </aside>

            </div>
        </div>
    </section>

    <section class="property-description-section">
        <div class="container">
            <div class="property-description-card">
                <h2>Descripción</h2>
                <p><?= nl2br(htmlspecialchars($propiedad['descripcion'])) ?></p>
            </div>
        </div>
    </section>

    <?php if (!empty($propiedad['caracteristicas'])): ?>
        <section class="property-description-section">
            <div class="container">
                <div class="property-description-card">
                    <h2>Características</h2>

                    <div class="property-features-list">
                        <?php
                        $caracteristicas = explode("\n", $propiedad['caracteristicas']);

                        foreach ($caracteristicas as $caracteristica):
                            $caracteristica = trim($caracteristica);

                            if ($caracteristica === '') {
                                continue;
                            }
                        ?>
                            <div class="property-feature-item">
                                <span>✓</span>
                                <p><?= htmlspecialchars($caracteristica) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

</main>

<div id="imageModal" class="image-modal">
    <button class="image-modal-close" type="button">&times;</button>
    <img id="imageModalImg" src="" alt="Imagen ampliada">
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const galleryImages = [
        <?php foreach ($imagenes as $img): ?>
            "<?= htmlspecialchars($img['ruta_imagen']) ?>",
        <?php endforeach; ?>
    ];

    let currentImageIndex = 0;

    const mainImage = document.getElementById("galleryMainImage");
    const thumbs = document.querySelectorAll(".property-thumbs img");
    const prevBtn = document.querySelector(".gallery-prev");
    const nextBtn = document.querySelector(".gallery-next");

    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("imageModalImg");
    const closeBtn = document.querySelector(".image-modal-close");

    function updateGallery() {
        if (!mainImage || galleryImages.length === 0) return;

        mainImage.src = galleryImages[currentImageIndex];

        thumbs.forEach(function (thumb, index) {
            thumb.classList.toggle("active", index === currentImageIndex);
        });
    }

    thumbs.forEach(function (thumb) {
        thumb.addEventListener("click", function () {
            currentImageIndex = Number(this.dataset.index);
            updateGallery();
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener("click", function () {
            currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            updateGallery();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", function () {
            currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
            updateGallery();
        });
    }

    if (mainImage) {
        mainImage.addEventListener("click", function () {
            modalImg.src = mainImage.src;
            modal.classList.add("active");
            document.body.style.overflow = "hidden";
        });
    }

    closeBtn.addEventListener("click", function () {
        modal.classList.remove("active");
        document.body.style.overflow = "";
    });

    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.classList.remove("active");
            document.body.style.overflow = "";
        }
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>