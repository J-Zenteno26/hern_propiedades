<?php

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '5432');
define('DB_NAME', 'hern_propiedades');
define('DB_USER', 'postgres');
define('DB_PASS', 'Hertn_propie$');

define('ADMIN_USER', 'luis_propiedades');
define('ADMIN_PASS', 'Lpropiedades26$');

try {
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;

    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>


