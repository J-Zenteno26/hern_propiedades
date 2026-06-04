<?php
require_once __DIR__ . '/config/db.php';

$sql = "
    SELECT 
        p.id_propiedad,
        p.titulo,
        p.precio,
        p.ubicacion,
        p.tipo,
        p.dormitorios,
        p.banos,
        p.metros_construidos,
        img.ruta_imagen
    FROM hern_propiedades p
    LEFT JOIN hern_imagenes_propiedad img 
        ON p.id_propiedad = img.id_propiedad 
        AND img.orden = 1
    WHERE p.estado = 'disponible'
    ORDER BY p.fecha_publicacion DESC
    LIMIT 3
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="author" content="Untree.co" />
  <link rel="shortcut icon" href="favicon.png" />

  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap5" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="assets/fonts/icomoon/style.css" />
  <link rel="stylesheet" href="assets/fonts/flaticon/font/flaticon.css" />

  <link rel="stylesheet" href="assets/css/tiny-slider.css" />
  <link rel="stylesheet" href="assets/css/aos.css" />
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/hern.css?v=2">

  <title>
    LH Propiedades
  </title>
</head>

<body>
  <div class="site-mobile-menu site-navbar-target">
    <div class="site-mobile-menu-header">
      <div class="site-mobile-menu-close">
        <span class="icofont-close js-menu-toggle"></span>
      </div>
    </div>
    <div class="site-mobile-menu-body"></div>
  </div>

  <?php include __DIR__ . '/includes/navbar.php'; ?>

  <!-- BANNER INICIAL -->
  <div class="hero hern-hero">
  <div class="hero-slide">
    <div class="img overlay" style="background-image: url('assets/img/propiedades/fachada_1.png')"></div>
  </div>

  <!-- HERO -->
  <div class="container">
    <div class="row justify-content-center align-items-center">
      <div class="col-lg-11 text-center">
        <span class="hero-kicker" data-aos="fade-up">
          ASESORÍA INMOBILIARIA
        </span>

        <h1 class="heading mb-4" data-aos="fade-up">
          Luis Hernández
        </h1>
          
        <p class="hero-text" data-aos="fade-up" data-aos-delay="100">
          Revisa fotografías, ubicación y detalles de cada publicación.
          Si alguna te interesa, deja tus datos y Luis se pondrá en contacto contigo.
        </p>

        <div data-aos="fade-up" data-aos-delay="200" class="hero-actions">
          <a href="propiedades.php" class="btn btn-primary">
            Ver opciones disponibles
          </a>

          <a href="#contacto" class="btn btn-outline-light">
            Hablar con Luis
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

  <!-- PROPIEDADES -->
  <div class="section">
    <div class="container">
      <div class="row mb-5 align-items-center">
        <div class="col-lg-6">
          <h2 class="font-weight-bold text-primary heading">
            Propiedades recientes
          </h2>
        </div>
        <div class="col-lg-6 text-lg-end">
          <p>
            <a href="propiedades.php" class="btn btn-primary text-white py-3 px-4">
              Ver todo
            </a>
          </p>
        </div>
      </div>
      <br>
      <div class="row">
        <?php if (count($propiedades) > 0): ?>
          <?php foreach ($propiedades as $propiedad): ?>
            <div class="col-md-6 col-lg-4 mb-4">
              <div class="property-item">
                <a href="detalle_propiedad.php?id=<?= $propiedad['id_propiedad'] ?>" class="img">
                  <img src="<?= htmlspecialchars($propiedad['ruta_imagen'] ?: 'assets/img/propiedades/sin-imagen.jpg') ?>"
                    alt="<?= htmlspecialchars($propiedad['titulo']) ?>" class="img-fluid" />
            </a>

                <div class="property-content">
                  <div class="price mb-2">
                    <span><?= htmlspecialchars($propiedad['precio']) ?></span>
                  </div>

                  <span class="d-block mb-2 text-black-50">
                    <?= htmlspecialchars($propiedad['titulo']) ?>
                  </span>

                  <span class="city d-block mb-3">
                    <?= htmlspecialchars($propiedad['ubicacion']) ?>
                  </span>

                  <div class="specs d-flex mb-4">
                    <span class="d-block d-flex align-items-center me-3">
                      <span class="icon-bed me-2"></span>
                      <span class="caption"><?= htmlspecialchars($propiedad['dormitorios']) ?> dorm.</span>
                    </span>

                    <span class="d-block d-flex align-items-center">
                      <span class="icon-bath me-2"></span>
                      <span class="caption"><?= htmlspecialchars($propiedad['banos']) ?> baños</span>
                    </span>
                  </div>
                  <a href="detalle_propiedad.php?id=<?= $propiedad['id_propiedad'] ?>" class="btn btn-primary py-2 px-3">
                    Conocer más
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

        <?php else: ?>
          <div class="col-12">
            <p>No hay propiedades disponibles por el momento.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- INFO -->
  <section class="features-1">
    <div class="container">
      <div class="row">
        <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
          <div class="box-feature">
            <span class="flaticon-house"></span>
            <h3 class="mb-3">Propiedades disponibles</h3>
            <p>Revisa casas, terrenos y oportunidades publicadas con información clara.</p>
          </div>
        </div>

        <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="500">
          <div class="box-feature">
            <span class="flaticon-building"></span>
            <h3 class="mb-3">Fotografías y detalles</h3>
            <p>Consulta imágenes, ubicación, precio y características antes de llamar.</p>
          </div>
        </div>

        <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="500">
          <div class="box-feature">
            <span class="flaticon-house-3"></span>
            <h3 class="mb-3">Contacto ordenado</h3>
            <p>Deja tus datos y Luis podrá responderte de forma más clara y tranquila.</p>
          </div>
        </div>

        <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="600">
          <div class="box-feature">
            <span class="flaticon-house-1"></span>
            <h3 class="mb-3">Atención personalizada</h3>
            <p>Recibe orientación directa según la propiedad que sea de tu interés.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="como-funciona" class="how-section">
  <div class="container">
    <div class="how-header text-center">
      <span class="section-kicker">Proceso simple</span>
      <h2>¿Cómo funciona?</h2>
      <p>
        Revisa cada opción con calma antes de solicitar contacto.
      </p>
    </div>

    <div class="how-grid">
      <div class="how-card">
        <span class="how-number">01</span>
        <h3>Revisa las publicaciones</h3>
        <p>
          Explora fotografías, ubicación, precio y características principales.
        </p>
      </div>

      <div class="how-card">
        <span class="how-number">02</span>
        <h3>Encuentra una opción de interés</h3>
        <p>
          Compara la información disponible y revisa los detalles antes de llamar.
        </p>
      </div>

      <div class="how-card">
        <span class="how-number">03</span>
        <h3>Solicita información</h3>
        <p>
          Deja tus datos en el formulario y Luis podrá contactarte de forma ordenada.
        </p>
      </div>
    </div>
  </div>
  </section>



  <!-- Preloader -->
  <div id="overlayer"></div>
  <div class="loader">
    <div class="spinner-border" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/tiny-slider.js"></script>
  <script src="assets/js/aos.js"></script>
  <script src="assets/js/navbar.js"></script>
  <script src="assets/js/counter.js"></script>
  <script src="assets/js/custom.js"></script>
    <script>
  document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("hernMenuToggle");
  const menu = document.getElementById("hernMenu");

  if (toggle && menu) {
    toggle.addEventListener("click", function () {
      menu.classList.toggle("active");
    });
  }
});
</script>

 <?php include __DIR__ . '/includes/footer.php'; ?>