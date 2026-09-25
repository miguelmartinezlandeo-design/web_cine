<?php
session_start();
require_once '../php/config.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if(empty($_SESSION['id'])){
    header("Location: " . BASE_URL . "index.php");
    exit;
}


$idx = (int) base64_decode($_POST['id']);
//========================================================
$stmt = $conn->prepare("SELECT Idusuario FROM Noticias WHERE Idnoticia=?");
$stmt->bind_param("i", $idx);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if(!$row || $row['Idusuario'] != $_SESSION['id']){
    header("Location: " . BASE_URL . "index.php");
    exit;
}
//==========================================================

$subnoti = $_POST['subnoticiax'];
$textoam = $_POST['textogrande'];
$tipo = $_POST['rad'];

/* VALIDACION VIDEO ANTES DE GUARDAR */
if($tipo==1){

    $youtube=$_POST['youtubev'];

    if(!preg_match('/(youtube\.com|youtu\.be)/',$youtube)){
        header("Location: subnoticiagregar.php?id=".base64_encode($idx)."&error=video");
        exit;
    }

}

/* INSERTAR SUBNOTICIA SOLO SI TODO ESTA BIEN */

$stmt = $conn->prepare("
    INSERT INTO subnoticias(Idnoticia,subtitulo,descripcion)
    VALUES(?,?,?)
");
$stmt->bind_param("iss", $idx, $subnoti, $textoam);
$stmt->execute();

//=================================================

$idsub = mysqli_insert_id($conn);

/* FOTO */
if($tipo==0 && !empty($_FILES['imagen']['name'])){

    $extPermitidas = ['jpg','jpeg','png','gif','webp','avif','jfif'];
    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

    if(!in_array($ext, $extPermitidas)){
        header("Location: subnoticiagregar.php?id=".base64_encode($idx)."&error=formato");
        exit;
    }

    $nombre = time().'_'.basename($_FILES['imagen']['name']);
    $ruta_fisica = $_SERVER['DOCUMENT_ROOT']."/ov1/imagen/fotoserieypelis/".$nombre;
    $ruta_bd = "/imagen/fotoserieypelis/".$nombre;

    move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta_fisica);

    $stmtFoto = $conn->prepare("INSERT INTO Subnoticiafoto(Idsubnoticias,foto) VALUES(?,?)");
    $stmtFoto->bind_param("is", $idsub, $ruta_bd);
    $stmtFoto->execute();
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

    $stmtVideo = $conn->prepare("INSERT INTO Subnoticiafoto(Idsubnoticias,foto) VALUES(?,?)");
    $stmtVideo->bind_param("is", $idsub, $video);
    $stmtVideo->execute();
}

/* REDIRECCION */
header("Location: subnoticiagregar.php?id=".base64_encode($idx));
exit;
?>
