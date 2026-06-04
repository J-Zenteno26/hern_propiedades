<nav class="admin-navbar">
    <div class="container admin-navbar-inner">
        
        <a href="contactos.php" class="admin-logo">
            <span class="admin-logo-mark">H</span>
            <span>Hern Admin</span>
        </a>

        <button class="admin-menu-toggle" type="button" id="adminMenuToggle">
            ☰
        </button>

        <div class="admin-nav-links" id="adminMenu">
            <a href="contactos.php">Solicitudes</a>
            <a href="propiedades.php">Propiedades</a>
            <a href="propiedad_nueva.php">Nueva propiedad</a>
            <a href="../propiedades.php" target="_blank">Ver sitio</a>
            <a href="logout.php">Salir</a>
        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("adminMenuToggle");
    const menu = document.getElementById("adminMenu");

    if (toggle && menu) {
        toggle.addEventListener("click", function () {
            menu.classList.toggle("active");
        });
    }
});
</script>