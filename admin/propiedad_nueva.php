<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config/db.php';

$mensaje_exito = '';
$mensaje_error = '';

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
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $dormitorios = $_POST['dormitorios'] ?? 0;
    $banos = $_POST['banos'] ?? 0;
    $metros_construidos = $_POST['metros_construidos'] ?? 0;
    $metros_terreno = $_POST['metros_terreno'] ?? 0;
    $descripcion = trim($_POST['descripcion'] ?? '');
    $caracteristicas = trim($_POST['caracteristicas'] ?? '');
    $estado = $_POST['estado'] ?? 'disponible';

    $ubicacion = '';

    if ($ciudad !== '' && $region !== '') {
        $ubicacion = $ciudad . ', ' . $region;
    }

    if ($titulo === '' || $precio === '' || $region === '' || $ciudad === '' || $tipo === '') {
        $mensaje_error = 'Completa los campos obligatorios.';
    } else {
        $sql = "
            INSERT INTO hern_propiedades (
                titulo,
                precio,
                ubicacion,
                tipo,
                dormitorios,
                banos,
                metros_construidos,
                metros_terreno,
                descripcion,
                caracteristicas,
                estado,
                fecha_publicacion
            ) VALUES (
                :titulo,
                :precio,
                :ubicacion,
                :tipo,
                :dormitorios,
                :banos,
                :metros_construidos,
                :metros_terreno,
                :descripcion,
                :caracteristicas,
                :estado,
                CURRENT_TIMESTAMP
            )
            RETURNING id_propiedad
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':ubicacion', $ubicacion);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':dormitorios', $dormitorios);
        $stmt->bindParam(':banos', $banos);
        $stmt->bindParam(':metros_construidos', $metros_construidos);
        $stmt->bindParam(':metros_terreno', $metros_terreno);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':caracteristicas', $caracteristicas);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();

        $nuevaPropiedad = $stmt->fetch(PDO::FETCH_ASSOC);
        $idNuevaPropiedad = $nuevaPropiedad['id_propiedad'];

        header("Location: imagenes_propiedad.php?id=" . $idNuevaPropiedad);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva propiedad | Admin Hern Propiedades</title>
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
                <a href="contactos.php" class="back-link">← Volver al panel</a>
                <br>
                <span class="property-type">Nueva publicación</span>
                <h5>Ingresa la información principal. Luego podrás cargar las imágenes.</h5>
            </div>
        </div>

        <section class="admin-form-card">
            <?php if ($mensaje_error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($mensaje_error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="admin-form-row">
                    <div class="form-group-custom">
                        <label>Título *</label>
                        <input type="text" name="titulo" placeholder="Ej: Casa amplia en sector residencial" required>
                    </div>

                    <div class="form-group-custom">
                        <label>Precio *</label>
                        <input type="text" name="precio" placeholder="Ej: $85.000.000" required>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="form-group-custom">
                        <label>Región *</label>
                        <select name="region" id="regionSelect" required>
                            <option value="">Seleccionar región</option>
                            <?php foreach ($regionesCiudades as $regionNombre => $ciudades): ?>
                                <option value="<?= htmlspecialchars($regionNombre) ?>">
                                    <?= htmlspecialchars($regionNombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label>Ciudad / Comuna *</label>
                        <select name="ciudad" id="ciudadSelect" required disabled>
                            <option value="">Primero selecciona una región</option>
                        </select>
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="form-group-custom">
                        <label>Tipo *</label>
                        <select name="tipo" required>
                            <option value="">Seleccionar</option>
                            <option value="Casa">Casa</option>
                            <option value="Departamento">Departamento</option>
                            <option value="Terreno">Terreno</option>
                            <option value="Parcela">Parcela</option>
                            <option value="Local comercial">Local comercial</option>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="disponible">Disponible</option>
                            <option value="reservada">Reservada</option>
                            <option value="vendida">Vendida</option>
                        </select>
                    </div>
                </div>

                <div class="admin-form-row admin-form-row-4">
                    <div class="form-group-custom">
                        <label>Dormitorios</label>
                        <input type="number" name="dormitorios" min="0" value="0">
                    </div>

                    <div class="form-group-custom">
                        <label>Baños</label>
                        <input type="number" name="banos" min="0" value="0">
                    </div>

                    <div class="form-group-custom">
                        <label>m² construidos</label>
                        <input type="number" name="metros_construidos" min="0" value="0">
                    </div>

                    <div class="form-group-custom">
                        <label>m² terreno</label>
                        <input type="number" name="metros_terreno" min="0" value="0">
                    </div>
                </div>

                <div class="form-group-custom">
                    <label>Descripción</label>
                    <textarea 
                        name="descripcion" 
                        rows="4"
                        placeholder="Describe la propiedad de forma clara y sencilla."
                    ></textarea>
                </div>

                <div class="form-group-custom">
                    <label>Características</label>
                    <textarea 
                        name="caracteristicas" 
                        rows="4"
                        placeholder="Escribe una característica por línea. Ej:
Estacionamiento
Portón eléctrico
Agua potable
Cercano a locomoción"
                    ></textarea>
                </div>

                <div class="form-group-custom admin-submit-group">
                    <button type="submit" class="btn btn-primary contact-submit-btn">
                        Guardar y cargar imágenes
                    </button>
                </div>

            </form>
        </section>

    </div>
</main>

<script>
const regionesCiudades = <?= json_encode($regionesCiudades, JSON_UNESCAPED_UNICODE) ?>;

const regionSelect = document.getElementById('regionSelect');
const ciudadSelect = document.getElementById('ciudadSelect');

regionSelect.addEventListener('change', function () {
    const region = this.value;

    ciudadSelect.innerHTML = '';

    if (!region || !regionesCiudades[region]) {
        ciudadSelect.disabled = true;
        ciudadSelect.innerHTML = '<option value="">Primero selecciona una región</option>';
        return;
    }

    ciudadSelect.disabled = false;
    ciudadSelect.innerHTML = '<option value="">Seleccionar ciudad/comuna</option>';

    regionesCiudades[region].forEach(function (ciudad) {
        const option = document.createElement('option');
        option.value = ciudad;
        option.textContent = ciudad;
        ciudadSelect.appendChild(option);
    });
});
</script>

<?php include __DIR__ . '/includes/footer_admin.php'; ?>