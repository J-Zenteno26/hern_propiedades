<?php
require_once __DIR__ . '/config/db.php';

$regionesCiudades = [
    'Región del Biobío' => [
        'Concepción',
        'Talcahuano',
        'San Pedro de la Paz',
        'Chiguayante',
        'Hualpén',
        'Coronel',
        'Lota',
        'Penco',
        'Tomé',
        'Los Ángeles',
        'Cabrero',
        'Yumbel',
    ],
    'Región de Ñuble' => [
        'Chillán',
        'Chillán Viejo',
        'Quillón',
        'Bulnes',
        'San Carlos',
        'Coihueco',
        'Yungay',
        'Pinto',
        'Quirihue',
        'Coelemu',
    ],
];

$tipo = $_GET['tipo'] ?? '';
$region = $_GET['region'] ?? '';
$ciudad = $_GET['ciudad'] ?? '';
$banos = $_GET['banos'] ?? '';
$dormitorios = $_GET['dormitorios'] ?? '';

$where = ["p.estado = 'disponible'"];
$params = [];

if ($tipo !== '') {
    $where[] = "LOWER(p.tipo) = LOWER(:tipo)";
    $params[':tipo'] = $tipo;
}

if ($region !== '') {
    $where[] = "LOWER(p.ubicacion) LIKE LOWER(:region)";
    $params[':region'] = '%' . $region . '%';
}

if ($ciudad !== '') {
    $where[] = "LOWER(p.ubicacion) LIKE LOWER(:ciudad)";
    $params[':ciudad'] = $ciudad . ',%';
}

if ($banos !== '') {
    $where[] = "p.banos >= :banos";
    $params[':banos'] = $banos;
}

if ($dormitorios !== '') {
    $where[] = "p.dormitorios >= :dormitorios";
    $params[':dormitorios'] = $dormitorios;
}

$whereSql = implode(' AND ', $where);

$sql = "
    SELECT DISTINCT ON (p.id_propiedad)
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
    WHERE $whereSql
    ORDER BY p.id_propiedad, img.orden ASC
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$propiedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Propiedades disponibles | Luis Hernández Propiedades</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/hern.css">
</head>

<body>

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <main class="catalog-page">
        <section class="catalog-section">
            <div class="container catalog-container">
                <div class="catalog-header">
                    <span class="property-type">Catálogo</span>
                    <h1>Encuentra tu próxima propiedad</h1>
                    <p>Revisa casas, parcelas, terrenos y otras oportunidades publicadas. 
                        Filtra por ubicación, tipo de propiedad o cantidad de habitaciones para encontrar lo que buscas más rápidamente..</p>

                </div>
                
                <form method="GET" class="catalog-filter">
                    <div>
                        <label>Tipo</label>
                        <select name="tipo">
                            <option value="">Todos</option>
                            <option value="Casa" <?= $tipo === 'Casa' ? 'selected' : '' ?>>Casa</option>
                            <option value="Departamento" <?= $tipo === 'Departamento' ? 'selected' : '' ?>>Departamento
                            </option>
                            <option value="Parcela" <?= $tipo === 'Parcela' ? 'selected' : '' ?>>Parcela</option>
                            <option value="Terreno" <?= $tipo === 'Terreno' ? 'selected' : '' ?>>Terreno</option>
                        </select>
                    </div>

                        <div>
                            <label>Región</label>
                            <select name="region" id="regionSelect">
                                <option value="">Todas</option>
                                <?php foreach ($regionesCiudades as $regionNombre => $ciudades): ?>
                                    <option value="<?= htmlspecialchars($regionNombre) ?>" <?= $region === $regionNombre ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($regionNombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label>Ciudad / Comuna</label>
                            <select name="ciudad" id="ciudadSelect" <?= $region === '' ? 'disabled' : '' ?>>
                                <option value="">Todas</option>
                        
                                <?php if ($region !== '' && isset($regionesCiudades[$region])): ?>
                                    <?php foreach ($regionesCiudades[$region] as $ciudadNombre): ?>
                                        <option value="<?= htmlspecialchars($ciudadNombre) ?>" <?= $ciudad === $ciudadNombre ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ciudadNombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                    <div>
                        <label>Dormitorios</label>
                        <select name="dormitorios">
                            <option value="">Todos</option>
                            <option value="1" <?= $dormitorios === '1' ? 'selected' : '' ?>>1+</option>
                            <option value="2" <?= $dormitorios === '2' ? 'selected' : '' ?>>2+</option>
                            <option value="3" <?= $dormitorios === '3' ? 'selected' : '' ?>>3+</option>
                            <option value="4" <?= $dormitorios === '4' ? 'selected' : '' ?>>4+</option>
                        </select>
                    </div>

                    <div>
                        <label>Baños</label>
                        <select name="banos">
                            <option value="">Todos</option>
                            <option value="1" <?= $banos === '1' ? 'selected' : '' ?>>1+</option>
                            <option value="2" <?= $banos === '2' ? 'selected' : '' ?>>2+</option>
                            <option value="3" <?= $banos === '3' ? 'selected' : '' ?>>3+</option>
                        </select>
                    </div>

                    <div class="catalog-filter-actions">
                        <button type="submit">Buscar</button>
                        <a href="propiedades.php">Limpiar</a>
                    </div>
                </form>
                <div class="catalog-grid">
                    <?php if (count($propiedades) > 0): ?>
                        <?php foreach ($propiedades as $propiedad): ?>
                            <article class="catalog-card">
                                <a href="detalle-propiedad.php?id=<?= $propiedad['id_propiedad'] ?>" class="catalog-img">
                                    <img src="<?= htmlspecialchars($propiedad['ruta_imagen'] ?: 'assets/img/propiedades/sin-imagen.jpg') ?>"
                                        alt="<?= htmlspecialchars($propiedad['titulo']) ?>">
                                </a>

                                <div class="catalog-content">
                                    <span class="catalog-type"><?= htmlspecialchars($propiedad['tipo']) ?></span>

                                    <h2><?= htmlspecialchars($propiedad['titulo']) ?></h2>

                                    <p class="catalog-location">
                                        <?= htmlspecialchars($propiedad['ubicacion']) ?>
                                    </p>

                                    <p class="catalog-price">
                                        <?= htmlspecialchars($propiedad['precio']) ?>
                                    </p>

                                    <div class="catalog-specs">
                                        <span><?= htmlspecialchars($propiedad['dormitorios']) ?> dorm.</span>
                                        <span><?= htmlspecialchars($propiedad['banos']) ?> baños</span>
                                        <span><?= htmlspecialchars($propiedad['metros_construidos']) ?> m²</span>
                                    </div>

                                    <a href="detalle_propiedad.php?id=<?= $propiedad['id_propiedad'] ?>" class="catalog-btn">
                                        Ver propiedad
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>

                        <div class="admin-empty">
                            <h2>No hay propiedades disponibles</h2>
                            <p>Cuando agregues propiedades, aparecerán aquí.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
<script>
const regionesCiudades = <?= json_encode($regionesCiudades, JSON_UNESCAPED_UNICODE) ?>;

const regionSelect = document.getElementById('regionSelect');
const ciudadSelect = document.getElementById('ciudadSelect');

regionSelect.addEventListener('change', function () {
    const region = this.value;

    ciudadSelect.innerHTML = '<option value="">Todas</option>';

    if (!region || !regionesCiudades[region]) {
        ciudadSelect.disabled = true;
        return;
    }

    ciudadSelect.disabled = false;

    regionesCiudades[region].forEach(function (ciudad) {
        const option = document.createElement('option');
        option.value = ciudad;
        option.textContent = ciudad;
        ciudadSelect.appendChild(option);
    });
});
</script>
    <?php include __DIR__ . '/includes/footer.php'; ?>