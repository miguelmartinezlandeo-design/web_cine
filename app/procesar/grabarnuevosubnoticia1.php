<?php
session_start();
require_once __DIR__ . '/../../php/config.php';

$conn = mysqli_connect($servername,$username,$password,$database);

if(empty($_SESSION['id'])){
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$idx = (int) base64_decode($_POST['id']);

$stmt = $conn->prepare("SELECT Idusuario FROM Noticias WHERE Idnoticia=?");
$stmt->bind_param("i", $idx);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if(!$row || $row['Idusuario'] != $_SESSION['id']){
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$subnoti = $_POST['subnoticiax'];
$textoam = $_POST['textogrande'];
$tipo = $_POST['rad'];

/* VALIDACION VIDEO ANTES DE GUARDAR */
if($tipo==1){

    $youtube=$_POST['youtubev'];

    if(!preg_match('/(youtube\.com|youtu\.be)/',$youtube)){
        header("Location: " . BASE_URL . "app/paginas/subnoticiagregar.php?id=".base64_encode($idx)."&error=video");
        exit;
    }

}

/* INSERTAR SUBNOTICIA SOLO SI TODO ESTA BIEN */
$sql="INSERT INTO subnoticias(Idnoticia,subtitulo,descripcion)
      VALUES($idx,'$subnoti','$textoam')";
mysqli_query($conn,$sql);

$idsub = mysqli_insert_id($conn);

/* FOTO */
if($tipo==0 && !empty($_FILES['imagen']['name'])){

    $nombre = time().$_FILES['imagen']['name'];
    $ruta_fisica = $_SERVER['DOCUMENT_ROOT']."/ov1/imagen/fotoserieypelis/".$nombre;
    $ruta_bd = "/imagen/fotoserieypelis/".$nombre;

    move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta_fisica);

    mysqli_query($conn,"INSERT INTO Subnoticiafoto(Idsubnoticias,foto)
                        VALUES($idsub,'$ruta_bd')");
}

/* VIDEO */
if($tipo==1){

    $video=str_replace(
        ["https://www.youtube.com/watch?v=",
         "youtube.com/watch?v=",
         "https://youtu.be/"],
        "",
        $_POST['youtubev']
    );

    mysqli_query($conn,"INSERT INTO Subnoticiafoto(Idsubnoticias,foto)
                        VALUES($idsub,'$video')");
}

/* REDIRECCION */
header("Location: " . BASE_URL . "app/paginas/subnoticiagregar.php?id=".base64_encode($idx));
exit;
?>
