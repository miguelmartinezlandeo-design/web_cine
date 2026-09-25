<!DOCTYPE html>


<div class="wrapper">
<header class="cabecera-app">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi sitio</title>
<link rel="stylesheet" href="<?= BASE_URL ?>estilo/estilo1.css?v=<?= filemtime(ROOT_PATH.'estilo/estilo1.css') ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>estilo/botonvuelta.css?v=<?= filemtime(ROOT_PATH.'estilo/botonvuelta.css') ?>">

                    <!-- hamburguesa -->
                    <button class="hamburger" id="hamburger">☰</button>

                    <!-- logo -->
                    <div class="logo-app">
                    <img src="<?= BASE_URL ?>imagen/plantilla1.svg" class="logo-desktop">
                    <span class="marca-movil">🎬 Cinéfilo</span>
                    </div>

                    <!-- acciones derecha -->
                    <div class="acciones-app">

                    <?php
                    $archivoActual = basename($_SERVER['PHP_SELF']);
                    if($archivoActual !== 'index.php'):
                    ?>

                    <a href="<?= BASE_URL ?>index.php" class="btn-ver-sitio" title="Volver a la página principal">
                    Inicio
                    </a>

                    <?php
                    endif;
                    ?>

                    </div>

                    <?php if($archivoActual == 'modificar.php' || $archivoActual == 'grabar.php' || $archivoActual == 'subnoticiagregar.php'): ?>

                    <div class="acciones-app-derecha">

                    <a href="<?= BASE_URL ?>logout/logout.php"
                    class="btn-logout"
                    onclick="return confirm('¿Cerrar sesión?')">
                    🚪 Salir
                    </a>

                    </div>

                    <?php endif; ?>

</header>

                    <nav class="menu-movil" id="menuMovil">

                    <a href="<?= BASE_URL ?>index.php">Inicio</a>
                    <a href="<?= BASE_URL ?>usuario/usuario.php">Cuenta</a>
                    <?php if(isset($query_menu)): ?>
                    <?php while ($cat = mysqli_fetch_assoc($query_menu)): ?>

                    <a href="<?= BASE_URL ?>index.php?menubar=<?= $cat['Idcategoriap'] ?>">
                    <?= htmlspecialchars($cat['nombre']) ?>
                    </a>

                    <?php endwhile; ?>
                    <?php endif; ?>

                    <?php if($archivoActual == 'modificar.php' || $archivoActual == 'grabar.php' || $archivoActual == 'subnoticiagregar.php'): ?>

                    <a href="<?= BASE_URL ?>logout/logout.php"
                    class="link-salir"
                    onclick="return confirm('¿Cerrar sesión?')">
                    🚪 Salir
                    </a>

                    <?php endif; ?>

                    </nav>

<script>
                            document.addEventListener("DOMContentLoaded", function(){

                    const hamburger = document.getElementById("hamburger");
                    const menuMovil = document.getElementById("menuMovil");

                    if(hamburger && menuMovil){

                    hamburger.addEventListener("click", function(){
                    menuMovil.classList.toggle("activo");
                    });

                    document.addEventListener("click", function(e){
                    if(!menuMovil.contains(e.target) && !hamburger.contains(e.target)){
                    menuMovil.classList.remove("activo");
                    }
                    });

                    }

                    });
</script>
