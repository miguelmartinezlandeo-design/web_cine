<?php
require_once 'php/config.php';
$numero  = $_GET['sigue']   ?? '';
$menubar = $_GET['menubar'] ?? '';

if (!$conn) { die("Error de conexión: " . mysqli_connect_error()); }

$menu = "SELECT * FROM Categoriaprincipal";
$query_menu = mysqli_query($conn, $menu);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<meta name="description" content="Portal de Noticias: las ultimas PELICULAS, Terror, Comedia y Ficcion en un solo lugar.">
<title>Portal de Noticias</title>
<link rel="stylesheet" href="<?= BASE_URL ?>estilo/index-modern.css?v=<?= filemtime(ROOT_PATH.'estilo/index-modern.css') ?>">
<?php
// Detección de móviles

/* ===============================
   CONSULTAS (NO TOCADAS)
================================= */
if ($menubar == "" || $menubar == "0") {

    $offset = ($numero == "2") ? 7 : 0;

    $phpnoticia  = "SELECT * FROM Noticias ORDER BY Fecha DESC LIMIT 7 OFFSET $offset";
    $nuevo_offset = $offset + 7;
    $phpnoticia1 = "SELECT * FROM Noticias ORDER BY Fecha DESC LIMIT 6 OFFSET $nuevo_offset";

} else {

    $menubar_clean = mysqli_real_escape_string($conn, $menubar);

    $base_query = "SELECT n.* FROM Noticias n 
                   JOIN Categoriaprincipal cp ON n.Idcategoria = cp.Idcategoriap
                   WHERE cp.Idcategoriap = '$menubar_clean' 
                   ORDER BY n.Idnoticia DESC";

    $offset = ($numero == "2") ? 7 : 0;
    $nuevo_offset = $offset + 7;
    $phpnoticia  = $base_query . " LIMIT 7 OFFSET $offset";
    $phpnoticia1 = $base_query . " LIMIT 6 OFFSET $nuevo_offset";
}

$result = mysqli_query($conn, $phpnoticia);
?>
</head>
<body class="portada">

<a class="saltar-al-contenido" href="#contenido">Saltar al contenido</a>

<?php require_once 'app/vistas/cabecera.php'; ?>

<main id="contenido" class="area-noticias-moderna">

    <div class="navbar-filtros" role="group" aria-label="Controles de navegacion y paginacion">

        <!-- 1. IZQUIERDA: Cuenta -->
        <div class="nav-cuenta">
            <a href="<?= BASE_URL ?>usuario/usuario.php" class="btn-usuario">
                <img src="<?= BASE_URL ?>imagen/user.png" alt="" width="24" height="24">
                <span>Cuenta</span>
            </a>
        </div>

        <!-- 2. CENTRO: Categorias -->
        <nav class="nav-filtros filtro-botones" aria-label="Filtrar noticias por categoria">

            <a href="index.php"
            class="<?= ($menubar == "" || $menubar == "0") ? 'activo' : '' ?>"
            <?= ($menubar == "" || $menubar == "0") ? 'aria-current="page"' : '' ?>>
            Todas
            </a>

            <?php
            $query_menu = mysqli_query($conn, $menu);
            if($query_menu && mysqli_num_rows($query_menu) > 0){
                while ($valores = mysqli_fetch_assoc($query_menu)) {

                    $activo = ($menubar == $valores['Idcategoriap']) ? "activo" : "";

                    echo "<a href='index.php?menubar=".$valores['Idcategoriap']."' class='$activo'"
                        . ($activo ? ' aria-current="page"' : '') . ">"
                        . htmlspecialchars($valores['nombre'])
                        . "</a>";
                }
            }
            ?>

        </nav>

        <!-- 3. DERECHA: Paginacion -->

        <div class="nav-paginacion">

            <?php if ($numero == 2): ?>
                <a href="index.php?sigue=&menubar=<?= $menubar ?>" class="btn-paginacion">
                    <span aria-hidden="true">&larr;</span> Anterior
                </a>
            <?php else: ?>
               <a href="index.php?sigue=2&menubar=<?= $menubar ?>" class="btn-paginacion">
                    Siguiente <span aria-hidden="true">&rarr;</span>
                </a>
            <?php endif; ?>

        </div>

    </div>

<?php
if ($result && mysqli_num_rows($result) > 0) {
    require_once 'app/vistas/3columnas.php';
}
?>

    <section class="seccion-archivo" aria-labelledby="titulo-archivo">
        <h2 class="titulo-seccion" id="titulo-archivo">
            <span class="antetitulo-seccion">Archivo</span>
            Mas Noticias
        </h2>
        <?php require_once 'app/vistas/2columnas.php'; ?>
    </section>

</main>

<script>
/* AGENTS.md - Carril de categorias con scroll horizontal flotante.
   Prioritario: que se pueda desplazar con la rueda del raton y, tambien, con
   arrastre (click y arrastrar) en escritorio. El arrastre con el dedo ya
   funciona via -webkit-overflow-scrolling:touch.

   Reglas:
   - Traduce deltaY a scrollLeft cuando hay desbordamiento y cursor sobre carril.
   - No intercepta si hay deltaX (trackpad) o si esta en borde (deja pasar).
   - Permite drag con raton: isDown + mousemove actualiza scrollLeft usando
     el offset inicial del cursor respecto al carril. */
(function () {
    var carril = document.querySelector('.nav-filtros');
    if (!carril) return;

    // 1) Rueda del raton / trackpad
    carril.addEventListener('wheel', function (e) {
        var max = carril.scrollWidth - carril.clientWidth;
        if (max <= 0) return;
        if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) return;

        var antes = carril.scrollLeft;
        if ((antes <= 0 && e.deltaY < 0) || (antes >= max && e.deltaY > 0)) return;

        e.preventDefault();
        carril.scrollLeft = antes + e.deltaY;
    }, { passive: false });

    // 2) Arrastre con raton (click y arrastrar)
    var UMbral = 5;           // px de margen para distinguir clic de arrastre
    var isDown = false;
    var movido = false;
    var inicioX = 0;
    var inicioScroll = 0;
    var inicioClienteX = 0;

    carril.addEventListener('mousedown', function (e) {
        if (e.button !== 0) return;
        isDown = true;
        movido = false;
        inicioClienteX = e.clientX;
        inicioScroll = carril.scrollLeft;
        carril.classList.add('filtro-botones--dragging');
        e.preventDefault();
    });

    document.addEventListener('mousemove', function (e) {
        if (!isDown) return;
        e.preventDefault();
        if (Math.abs(e.clientX - inicioClienteX) > UMbral) movido = true;
        carril.scrollLeft = inicioScroll - (e.clientX - inicioClienteX);
    });

    document.addEventListener('mouseup', function () {
        if (!isDown) return;
        isDown = false;
        carril.classList.remove('filtro-botones--dragging');
    });

    // Tras un arrastre real el navegador aun dispara click sobre la pilora:
    // se cancela para no navegar a la categoria por accident.
    carril.addEventListener('click', function (e) {
        if (!movido) return;
        movido = false;
        e.preventDefault();
        e.stopPropagation();
    }, true);

    // 3) Cursor de "agarrar" solo cuando hay contenido que desplazar
    if (carril.scrollWidth - carril.clientWidth > 0) {
        carril.style.cursor = 'grab';
    }
})();
</script>

<?php
require_once 'script/script.php';
require_once 'app/vistas/pie.php';
?>

</body>
</html>
<?php mysqli_close($conn); ?>
