<?php

session_start();
require_once '../php/config.php';

$error="";
$ok="";


if($_SERVER["REQUEST_METHOD"]==="POST"){

$codigo = $_POST['codigo'];

$res = mysqli_query($conn,"
SELECT codigo
FROM codigo_invitacion
WHERE activo=1
LIMIT 1
");

$codigoBD = mysqli_fetch_assoc($res)['codigo'];

if($codigo != $codigoBD){

$error="Código de invitación incorrecto";

}else{

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$email = $_POST['email'];
$alias = $_POST['alias'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt=$conn->prepare("
INSERT INTO Usuarios
(Alias,Nombre,Apellidos,email,contrasena)
VALUES(?,?,?,?,?)
");

$stmt->bind_param("sssss",$alias,$nombre,$apellido,$email,$password);
$stmt->execute();

$ok="Usuario creado correctamente";

}
}
?>

<html>
<head>

<link rel="stylesheet" href="<?= BASE_URL ?>estilo/usuario.css">
</head>
<div class="Table">

<?php include (__DIR__.'/../app/vistas/cabecera.php'); ?>

                <div class="wrapper">

                <div class="main-content">

                <div class="registro-box">

                <h2>Crear usuario</h2>

                <?php if($error): ?>
                <p class="error-msg"><?php echo $error; ?></p>
                <?php endif; ?>

                <?php if($ok): ?>
                <p class="ok-msg"><?php echo $ok; ?></p>
                <?php endif; ?>

                <form method="post">

                <label>Código de invitación</label>
                <input type="text" name="codigo" required>

                <label>Alias</label>
                <input type="text" name="alias" required>

                <label>Nombre</label>
                <input type="text" name="nombre">

                <label>Apellidos</label>
                <input type="text" name="apellido">

                <label>Email</label>
                <input type="email" name="email">

                <label>Contraseña</label>
                <input type="password" name="password" required>

                <div class="botones-login">

                <input type="submit" value="Crear usuario">

                <input type="reset" value="Borrar">

                </div>

                </form>

                </div>

        </div>

<?php include (__DIR__.'/../app/vistas/pie.php'); ?>

        </div>
</div></html>