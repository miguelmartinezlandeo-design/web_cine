
<html>
	<head>
		<link href="<?= BASE_URL ?>estilo/estilomv.css" media="screen" type="text/css" rel="stylesheet">
		<link href="<?= BASE_URL ?>estilo/estilo2.css" media="screen" type="text/css" rel="stylesheet">
		<?php
		require_once __DIR__ . '/../../php/config.php';
		$numero=$_GET['sigue'];
		$menubar=$_GET['menubar'];
	  unset($_GET['sigue']);
		unset($_GET['menubar']);
		$conn = mysqli_connect($servername, $username, $password, $database);
		$menu = "select * from Categoriaprincipal";

		if($menubar==""){
					if($numero==""){
						$phpnoticia= "select * from Noticias order by Fecha desc Limit 12" ;
					}else{
						$phpnoticia= "select * from Noticias order by Fecha desc Limit 12 OFFSET 12" ;
					}
				}elseif($menubar=="0"){
						if($numero==""){
							$phpnoticia= "select * from Noticias order by Fecha desc Limit 12" ;
						}else{
							$phpnoticia= "select * from Noticias order by Fecha desc Limit 12 OFFSET 12" ;
						}
					}elseif($menubar>0){
								$phpnoticia= 'select * from Noticias join Visualizacion on Noticias.Idcategoria=Visualizacion.Idcategoria JOIN Categorias on  Visualizacion.Idcategoria1 = Categorias.Idcategoria JOIN Categoriaprincipal on Categorias.Idcategoriap=Categoriaprincipal.Idcategoriap
								WHERE Categoriaprincipal.Idcategoriap='. $menubar .' order by Idnoticia desc Limit 12' ;

					}


	if($result = mysqli_query($conn, $phpnoticia)){
?>
<title>'Mi primera vez'</title>
</head>
<body>
<div class="Table">

<?php
			require_once ROOT_PATH . 'app/vistas/cabecera.php';
 ?>
 <div class="fondo">

 									<form action="<?= BASE_URL ?>app/paginas/indexmv.php" method="get">
 										<div class="imag">
										<img src="<?= BASE_URL ?>imagen/user.png" alt="x" class="imag">
 									  </div>
 										<div class="Celdaselect">
 										<?php
 													if ($numero==2){
 														echo "<input type='submit' class='seguir' name='boton' value='<<<' >
 																	<input type='hidden' value='' name='sigue'>
 																	";
 													}else{
 														echo "<input type='submit' class='seguir' name='boton' value='>>>'>
 																	<input type='hidden' value='2' name='sigue'>
 																	";
 													}
 										?>
 									</div>
 										<div class="Celdaselect">
 												<input class="diferente" type="submit" value="--Buscar--">
 										</div>
 										<div class="Celdaselect">
 													<div class="custom-select" style="width:150px;">
 														<select name="menubar">
 															<option value="0">Selecciona:</option>
 															<?php
 															$query=mysqli_query($conn,$menu);
 															while ($valores=mysqli_fetch_array($query)){
 																echo '<option value="'. $valores[Idcategoriap].'">'. $valores[nombre] .'</option>';
 															}

 															?>
 														</select>
 													</div>
 											</div>
 										</form>
 	</div>


 <div class="CabecerayPie">
	 <?php

	 					if(mysqli_num_rows($result) >= 1){

	 						$row = mysqli_fetch_array($result);
	            $id=$row['Idnoticia'];
	            $id_encode = base64_encode($id);
    							echo "<div class='Table2'>";
	 						while($row = mysqli_fetch_array($result)){
	             $id=$row['Idnoticia'];
	             $id_encode= base64_encode($id);
										echo "<div class='CabecerayPie'> <a href='" . BASE_URL . "mostrarnoticia.php?id=" . urlencode($id_encode) . "'>";
	 												$consultafoto= "select * from FotosP where Idnoticia=" . $row['Idnoticia'] ;
	 												$resultadofoto= mysqli_query($conn,$consultafoto);
	 												$arrayfoto= mysqli_fetch_array($resultadofoto);
	 												echo "<p class='Titulo2'>". $row['Titulo']. "</p>
													<img class='fotoizquierda1' src='". $arrayfoto['Foton'] ."'>";

	 											echo "</a></div>";
	 							}

	 							echo "</div>";
	 							mysqli_free_result($result);


	 					} else{
	 							echo "No records matching your query were found.";
	 					}

	 ?>

 </div>


<?php
				require_once ROOT_PATH . 'app/vistas/pie.php';
				require_once ROOT_PATH . 'script/script.php';
 ?>

</div>
</body>
</html>
<?php
} else{
		echo "ERROR: No se puede ejectutar esta coneccion $phpnoticia. " . mysqli_error($conn);
}
mysqli_free_result($result);
mysqli_close($conn);
?>
