<?php
require_once 'php/config.php';
require_once 'php/reacciones.php';
require_once 'php/comentario.php';
require_once 'php/media.php';
require_once 'php/visitas.php';
/* ===============================
   1. Obtener ID seguro
================================= */
$id_url = $_GET['id'] ?? $_POST['id'] ?? '';
$id = base64_decode(urldecode($id_url));

if (!$id) {
    header('Location: index.php');
    exit;
}

/* ===============================
   2. Obtener noticia principal
================================= */
$sql = "SELECT * FROM Noticias WHERE Idnoticia = '$id'";
$res = mysqli_query($conn, $sql);
$noticia = mysqli_fetch_array($res);

if (!$noticia) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($noticia['Titulo']); ?></title>
<link href="estilo/estilo1.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="wrapper">
<?php require_once 'app/vistas/cabecera.php'; ?>

<article class="noticia-completa">

<header class="header-noticia">
<h1><?php echo htmlspecialchars($noticia['Titulo']); ?></h1>
<span class="fecha">Publicado el: <?php echo $noticia['Fecha']; ?></span>
</header>
				<div class="votos-form">

					<button type="button"
							class="btn-like"
							data-id="<?= $id ?>"
							data-tipo="like">
						👍 <span id="likes-count"><?= $noticia['me_gusta'] ?></span>
					</button>

					<button type="button"
							class="btn-dislike"
							data-id="<?= $id ?>"
							data-tipo="dislike">
						👎 <span id="dislikes-count"><?= $noticia['no_me_gusta'] ?></span>
					</button>

				</div>

<?php
/* ===============================
   3. Imagen principal
================================= */
$res_f = mysqli_query($conn, "SELECT * FROM FotosP WHERE Idnoticia = '$id'");
if($foto_p = mysqli_fetch_array($res_f)){
    echo "<div class='foto-noticia-principal'>
        <img src='".BASE_URL.$foto_p['Foton']."' alt='Portada'>
          </div>";
}

$ip = $_SERVER['REMOTE_ADDR'];

$visitas_actuales = controlarVisitas($conn,$id,$ip);

echo "<div class='contador-visitas'>
👁 ".$visitas_actuales." visitas
</div>";

/*====================================
La opción de me gusta y no me gusta y el contador de visitas que tenemos arriba
======================================*/
?>
<div class="contenido-noticia">
<?php # nl2br convierte los saltos de línea en etiquetas <br> para que se muestren correctamente en HTML ?>
<?php echo nl2br($noticia['Texto']); ?>
</div>

<div class="subnoticias-contenedor">

<?php
/* ===============================
   4. Subnoticias
================================= */
$res_sub = mysqli_query($conn, "SELECT * FROM subnoticias WHERE Idnoticia = '$id' ORDER BY Idsubnoticias ASC");

while($sub = mysqli_fetch_array($res_sub)){

    echo "<div class='subnoticia-item'>";

    if(!empty($sub['Titulo'])){
        echo "<h2>".htmlspecialchars($sub['Titulo'])."</h2>";
    }

    $id_sub = $sub['Idsubnoticias'];
    $res_sf = mysqli_query($conn, "SELECT * FROM Subnoticiafoto WHERE Idsubnoticias = '$id_sub'");

	/*=======================================================
	 aquí me están dando los videos y las fotos y el YouTube mediante la web media.php
============================================================ */
				while($sf = mysqli_fetch_array($res_sf)){
						renderMedia($sf['foto']); # Render media (photo, video, YouTube) for the subnews item
				}

    $texto_mostrar = $sub['Texto'] ?? $sub['subtitulo'] ?? '';
    if(!empty($texto_mostrar)){
        echo "<div class='texto-sub'><b>".nl2br($texto_mostrar)."</b></div>";
		echo "<div class='texto-sub'>".nl2br($sub['descripcion'])."</div>";
    }

    echo "</div>";
}
?>

</div>
</article>

				<div class="comentarios-seccion">
					<h3>💬 Comentarios</h3>
					<form id="form-comentario" class="form-comentario">
					<input type="hidden" name="id" value="<?= $id ?>">	
					<input type="text" name="nombre" placeholder="Tu nombre" required maxlength="80">
						<textarea name="comentario"
								placeholder="Escribe tu comentario..."
								maxlength="200"
								required></textarea>

						<button type="submit" name="enviar_comentario">
							Enviar comentario
						</button>
					</form>
					<div id="mensaje-comentario"></div>
				</div>

											<?php
																			
											$res_com = mysqli_query($conn,"
												SELECT * FROM comentarios_noticias
												WHERE Idnoticia='$id'
												AND estado='aprobado'
												AND parent_id IS NULL
												ORDER BY fecha DESC
											");
													?>		
											<div class="lista-comentarios">
													<?php
													while($com = mysqli_fetch_assoc($res_com)){
														renderComentario($conn, $com);
													}
													?>
											</div>



					
				<?php 
				require_once 'app/vistas/pie.php'; 
				?>
</div>
<input type="text" id="id-noticia" value="<?= $id ?>">
<script type="module" src="js/core.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>
