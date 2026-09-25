<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


require_once '../php/config.php';
define('EN_ADMIN', true);
include (__DIR__ . '/../app/vistas/layout_privado_cabezera.php');

if(empty($_SESSION['id'])){
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$categoria = $_GET['categoria'] ?? 0;

?>
<html>
<head></head>
<body>
<h2>Nueva noticia</h2>
			<p>
Usuario:
				<?php echo $_SESSION['nombre']." ".$_SESSION['apellido']; ?>
			</p>


<div class="form-admin">

				<form name="formulario"
					enctype="multipart/form-data"
					action="grabarnuevo1.php"
					method="post"
					onsubmit="return comprobar()">

						<input type="hidden" name="categoria" value="<?php echo $categoria; ?>">

						

						<label>Imagen Principal</label><br>
						<input name="imagen" type="file" accept=".jpg,.jpeg,.png"><br><br>

						<label>Título de Imagen</label><br>
						<input type="text" name="titulo" maxlength="60"><br><br>

						<label>Título del Texto</label><br>
						<input type="text" name="subtitulo" maxlength="60"><br><br>

						<label>Texto de la Noticia</label><br>
						<textarea cols="40" rows="6" name="textoarea"></textarea>
						<br><br>

						<div class="botones-login">
						<input type="reset" value="Borrar">
						<input type="submit" value="Guardar Noticia">
						</div>

				<p id="aviso" class="Textomin"></p>

				</form>

</div>

</body>
</html>

<?php
mysqli_close($conn);

/* Cargar scripts */
require_once '../script/script.php';
require_once '../script/scriptvalidacion.php';

include ROOT_PATH . 'app/vistas/layout_privado_pie.php';
?>
