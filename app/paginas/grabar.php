<?php
include __DIR__ . '/../vistas/layout_privado_cabezera.php';
require_once __DIR__ . '/../../php/config.php';
$conn = mysqli_connect($servername, $username, $password, $database);
$categoria = $_GET['categoria'] ?? 0;
?>
<h2>Nueva noticia</h2>
			<p>
Usuario:
				<?php echo $_SESSION['nombre']." ".$_SESSION['apellido']; ?>
			</p>

<div class="form-admin">

				<form name="formulario"
					enctype="multipart/form-data"
					action="<?= BASE_URL ?>app/procesar/grabarnuevo1.php"
					method="post"
					onsubmit="return comprobar()">

						<input type="hidden" name="categoria" value="<?php echo $categoria; ?>">

						<label>Tipo de Presentación</label><br><br>
						<label><input type="radio" name="presentacion" value="1"> Noticia</label>
						<label><input type="radio" name="presentacion" value="2"> Evaluación</label>
						<label><input type="radio" name="presentacion" value="3"> Retrospectiva</label>
						<br><br>

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

<?php
mysqli_close($conn);

/* Cargar scripts */
require_once ROOT_PATH . 'script/script.php';
require_once ROOT_PATH . 'script/scriptvalidacion.php';

include ROOT_PATH . 'app/vistas/layout_privado_pie.php';
?>
