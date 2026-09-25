<?php
include __DIR__ . '/../vistas/layout_privado_cabezera.php';

require_once __DIR__ . '/../../php/config.php';
$conn = mysqli_connect($servername, $username, $password, $database);

/* VARIABLES POST SEGURAS */
$seleccionado = $_POST['menubar'] ?? 0;
$mantenimiento = $_POST['Mantenimiento'] ?? -1;

/* REDIRECCIONES */
if($_SERVER["REQUEST_METHOD"]=="POST"){

    if($mantenimiento==0 && $seleccionado!=0){
        header("Location: " . BASE_URL . "app/paginas/grabar.php?categoria=".$seleccionado);
        exit;
    }

    if($mantenimiento==1 && $seleccionado!=0){
        header("Location: grabar1.php?categoria=".$seleccionado);
        exit;
    }
}

/* CONSULTA COMBO */
$menu = "SELECT * FROM Categoriaprincipal";
$query = mysqli_query($conn,$menu);
?>
<div class="admin-panel">
        <h2>Panel de usuario</h2>

        <p>
        Bienvenido:
        <?php echo $_SESSION['nombre']." ".$_SESSION['apellido']; ?>
        </p>

                    <form method="post">

                            <h3>Mantenimiento</h3>

                            <label><input type="radio" name="Mantenimiento" value="0"> Agregar</label><br>
                            <label><input type="radio" name="Mantenimiento" value="1"> Modificar</label><br>
                            <label><input type="radio" name="Mantenimiento" value="2"> Eliminar</label><br><br>

                            <select name="menubar">
                            <option value="0">Selecciona:</option>

                            <?php
                            while ($valores=mysqli_fetch_assoc($query)){
                                echo '<option value="'.$valores['Idcategoriap'].'">'.$valores['nombre'].'</option>';
                            }
                            ?>

                            </select>

                            <br><br>

                            <input type="submit" value="Continuar">

                    </form>
</div>
<?php
mysqli_close($conn);
include ROOT_PATH . 'app/vistas/layout_privado_pie.php';
?>
