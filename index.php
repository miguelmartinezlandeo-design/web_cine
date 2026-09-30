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

    <div class="barra-superior" role="group" aria-label="Controles de navegacion y paginacion">

        <!-- IZQUIERDA -->
        <div class="superior-izq">
            <a href="<?= BASE_URL ?>usuario/usuario.php" class="btn-usuario">
                <img src="<?= BASE_URL ?>imagen/user.png" alt="" width="24" height="24">
                <span>Cuenta</span>
            </a>
        </div>

        <!-- CENTRO -->
        <nav class="superior-centro filtro-botones" aria-label="Filtrar noticias por categoria">

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

        <!-- DERECHA -->
        <div class="superior-der">

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

<?php
require_once 'script/script.php';
require_once 'app/vistas/pie.php';
?>

</body>
</html>
<?php mysqli_close($conn); ?>
