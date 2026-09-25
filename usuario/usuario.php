<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once '../php/config.php';
    

$error="";

$conn = mysqli_connect($servername, $username, $password, $database);

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $nombre = $_POST['usuario'] ?? '';
    $password = $_POST['contrasena'] ?? '';

                $sql="SELECT * FROM Usuarios WHERE Alias='$nombre'";
                $resultado=mysqli_query($conn,$sql);

                    if($resultado && mysqli_num_rows($resultado)==1){
                        $fila=mysqli_fetch_assoc($resultado);
                        // Verificar contraseña segura
                            if(password_verify($password,$fila['contrasena'])){

                                  $fila['Alias']=$nombre;
                                      session_regenerate_id(true);

                                                    $_SESSION['id'] = $fila['Idusuario'];
                                                    $_SESSION['usuario'] = $fila['Alias'];
                                                    $_SESSION['nombre'] = $fila['Nombre'];
                                                    $_SESSION['apellido'] = $fila['Apellidos'];

                                
                                  header("Location: usuarioadmin.php");
                                exit;

                            }else{
                                $error="Usuario incorrectos";
                            }
                    }else{
                                $error="Usuario o contraseña incorrectos";
                    }
    
}
 
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi primer usuario</title>
<link rel="stylesheet" href="<?= BASE_URL ?>estilo/usuario.css">
<link rel="stylesheet" href="<?= BASE_URL ?>estilo/estilo1.css?v=6">

<?php 
include (__DIR__.'/../app/vistas/layout_privado_cabezera.php');?>
</head>

<body>
        
                <div class="main-content">
                    <div class="login-box"> 
                                    <h2>Login</h2>

                            <?php if($error): ?>
                            <p style="color:red;"><?php echo $error; ?></p>
                            <?php endif; ?>

                                <form method="post">
                                    
                                      <label>  Usuario   : </label><br><input type="text" name="usuario" maxlength="30"><br>
                                      <label>  Contraseña   :</label><br><input type="password" name="contrasena" maxlength="50"><br>
                                            <div class="botones-login">
                                                <input type="submit" value="Enviar">
                                                <input type="reset" value="Borrar">
                                            </div>
                                </form>
                                 <hr>

                                            <div style="text-align:center;margin-top:15px;">

                                            <a href="registro.php" class="btn-invitacion">
                                            Crear usuario con invitación
                                            </a>

                                            </div>
                    </div>
                </div>
                
              <?php
                            include (__DIR__.'/../app/vistas/pie.php');

                ?>
        

</body>
</html>
