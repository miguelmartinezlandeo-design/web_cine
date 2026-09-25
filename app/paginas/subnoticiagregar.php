<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
require_once __DIR__ . '/../../php/config.php';

$conn = mysqli_connect($servername,$username,$password,$database);

if(empty($_SESSION['id'])){
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id = isset($_GET['id']) ? (int) base64_decode($_GET['id']) : 0;

/* NOTICIA (y comprobar que pertenece al usuario logueado) */
$stmt = $conn->prepare("SELECT * FROM Noticias WHERE Idnoticia=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$noticia = $stmt->get_result()->fetch_assoc();

if(!$noticia || $noticia['Idusuario'] != $_SESSION['id']){
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* FOTO PRINCIPAL */
$stmtf = $conn->prepare("SELECT * FROM FotosP WHERE Idnoticia=?");
$stmtf->bind_param("i", $id);
$stmtf->execute();
$foto = $stmtf->get_result()->fetch_assoc();

?>

<?php include ROOT_PATH . 'app/vistas/layout_privado_cabezera.php'; ?>

<div class="wrapper">

<h2 class="titulo-admin">Agregar Subnoticias</h2>

<!-- NOTICIA PRINCIPAL -->
<div class="noticia-principal">

    <h3><?php echo $noticia['Titulo']; ?></h3>

    <?php if($foto): ?>
        <img src="/ov1/<?php echo ltrim($foto['Foton']); ?>" class="img-principal">
    <?php endif; ?>

    <p class="subtitulo"><?php echo $noticia['Descrititulo']; ?></p>
    <p class="texto"><?php echo nl2br($noticia['Texto']); ?></p>

</div>

<!-- FORMULARIO -->
<div class="form-admin">

<form   id="formSubnoticia"
        enctype="multipart/form-data"
        method="post"
        action="<?= BASE_URL ?>app/procesar/grabarnuevosubnoticia1.php" >

            <input type="hidden" name="id" value="<?php echo base64_encode($id); ?>">

            <label>Subtítulo</label>
            <input type="text" name="subnoticiax" required>

            <label>Tipo</label>
            <div class="radio-group">
            <label><input type="radio" name="rad" value="0" required> Imagen</label>
            <label><input type="radio" name="rad" value="1"> Youtube</label>
            </div>

            <label>Imagen</label>
            <input type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp">

            <label>Youtube</label>
            <input type="text" name="youtubev" placeholder="https://youtu.be/...">

            <label>Texto</label>
            <textarea name="textogrande" rows="6" required></textarea>

            <button type="submit" class="btn-primario">Guardar Subnoticia</button>

            <p id="avisoSub" style="color:red"></p>

</form>

</div>

<!-- SUBNOTICIAS -->
<h3 class="titulo-secundario">Subnoticias agregadas</h3>

<?php

$sqlsub="
SELECT s.*, f.foto
FROM subnoticias s
LEFT JOIN Subnoticiafoto f
ON s.Idsubnoticias = f.Idsubnoticias
WHERE s.Idnoticia=$id
ORDER BY s.Idsubnoticias DESC
";

$ressub=mysqli_query($conn,$sqlsub);

while($sub=mysqli_fetch_assoc($ressub)){
?>

<div class="subnoticia">

<h4><?php echo $sub['subtitulo']; ?></h4>

<?php if($sub['foto']): ?>

    <?php if(strpos($sub['foto'],'imagen/')!==false): ?>
        <img src="/ov1/<?php echo $sub['foto']; ?>" class="img-sub">
    <?php else: ?>
         <iframe
                class="video-sub"
                src="https://www.youtube.com/embed/<?php echo $sub['foto']; ?>"
                frameborder="0"
                allowfullscreen>
        </iframe>
    <?php endif; ?>

<?php endif; ?>

<p><?php echo nl2br($sub['descripcion']); ?></p>

</div>

<?php } ?>

</div>

<?php include ROOT_PATH . 'app/vistas/layout_privado_pie.php'; ?>
<?php require_once ROOT_PATH . 'script/scriptvalidacion.php'; ?>

<?php mysqli_close($conn); ?>


