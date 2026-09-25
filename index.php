<?php
require_once 'php/config.php';
$numero  = $_GET['sigue']   ?? '';
$menubar = $_GET['menubar'] ?? '';

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) { die("Error de conexión: " . mysqli_connect_error()); }

$menu = "SELECT * FROM Categoriaprincipal";
$query_menu = mysqli_query($conn, $menu);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal de Noticias</title>
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

    $phpnoticia  = $base_query . " LIMIT 7";
    $phpnoticia1 = $base_query . " LIMIT 6 OFFSET 7";
}

$result = mysqli_query($conn, $phpnoticia);
?>
</head>
<body>

<?php require_once 'app/vistas/cabecera.php'; ?>
                                                        
                                                              

                                                                
                        <div class="barra-superior">

                            <!-- IZQUIERDA -->
                            <div class="superior-izq">
                                <a href="usuario/usuario.php" class="btn-usuario">
                                    <img src="imagen/user.png" alt="Usuario">
                                    <span>Cuenta</span>
                                </a>
                            </div>

                            <!-- CENTRO -->
                            <div class="superior-centro filtro-botones">

                                <a href="index.php"
                                class="<?= ($menubar == "" || $menubar == "0") ? 'activo' : '' ?>">
                                Todas
                                </a>

                                <?php
                                $query_menu = mysqli_query($conn, $menu);
                                if($query_menu && mysqli_num_rows($query_menu) > 0){
                                    while ($valores = mysqli_fetch_assoc($query_menu)) {

                                        $activo = ($menubar == $valores['Idcategoriap']) ? "activo" : "";

                                        echo "<a href='index.php?menubar=".$valores['Idcategoriap']."' class='$activo'>
                                                ".htmlspecialchars($valores['nombre'])."
                                            </a>";
                                    }
                                }
                                ?>

                            </div>

                            <!-- DERECHA -->
                            <div class="superior-der">

                                <?php if ($numero == 2): ?>
                                    <a href="index.php?sigue=&menubar=<?= $menubar ?>" class="btn-paginacion">
                                        <span>←</span> Anterior
                                    </a>
                                <?php else: ?>
                                   <a href="index.php?sigue=2&menubar=<?= $menubar ?>" class="btn-paginacion">
                                        Siguiente <span>→</span>
                                    </a>
                                <?php endif; ?>

                            </div>

                        </div>
           
<main class="area-noticias-moderna">
<?php 
if ($result && mysqli_num_rows($result) > 0) {
    require_once 'app/vistas/3columnas.php'; 
}
?>
</main>

<section class="seccion-archivo">
<h2 style="text-align:center;margin-top:50px;color:#444;font-family:sans-serif;">
Más Noticias
</h2>
<?php require_once 'app/vistas/2columnas.php'; ?>
</section>

<?php
require_once 'app/vistas/pie.php';
require_once 'script/script.php';
?>

</div>
             </body>
</html>
<?php mysqli_close($conn); ?>