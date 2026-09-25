<?php
require_once '../php/config.php';
include (__DIR__.'/../app/vistas/layout_privado_cabezera.php');

//$conn = mysqli_connect($servername, $username, $password, $database);

/* VARIABLES POST SEGURAS */
$seleccionado = $_POST['menubar'] ?? 0;
$mantenimiento = $_POST['Mantenimiento'] ?? -1;

$res = mysqli_query($conn,"
SELECT codigo FROM codigo_invitacion
WHERE activo=1
LIMIT 1
");

$codigo = mysqli_fetch_assoc($res);

/* REDIRECCIONES */
if($_SERVER["REQUEST_METHOD"]=="POST"){

    if($mantenimiento==0 && $seleccionado!=0){
        header("Location:".BASE_URL."admin/grabar.php?categoria=".$seleccionado);
        exit;
    }

    if($mantenimiento==1 && $seleccionado!=0){
        header("Location:".BASE_URL."admin/adminmodificar/modificar.php?categoria=".$seleccionado);
        exit;
    }
}

/* CONSULTA COMBO */
                        $menu = "SELECT * FROM Categoriaprincipal";
                        $query = mysqli_query($conn,$menu);

                        if(isset($_POST['nuevo_codigo'])){

                        mysqli_query($conn,"
                        UPDATE codigo_invitacion
                        SET activo=0
                        WHERE activo=1
                        ");

                        $nuevo = bin2hex(random_bytes(4));

                        mysqli_query($conn,"
                        INSERT INTO codigo_invitacion(codigo,activo)
                        VALUES('$nuevo',1)
                        ");

                        header("Location: usuarioadmin.php");
                        exit;

                        }

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
                            <label><input type="radio" name="Mantenimiento" value="1"> Modificar</label><br><br>
                            
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

                            <br><br>

                    </form>
                                                            <div class="codigo-invite">

                                        Código invitación actual:

                                        <strong><?= $codigo['codigo'] ?></strong>

                                        </div>
                                        <form method="post">
                                        <button name="nuevo_codigo">
                                        Generar nuevo código
                                        </button>
                                        </form>
</div>
<?php
mysqli_close($conn);
include (__DIR__.'/../app/vistas/layout_privado_pie.php');
?>
