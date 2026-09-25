<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

require_once __DIR__ . '/../../php/config.php';
define('EN_ADMIN', true);

if(empty($_SESSION['id'])){
    header("Location: " . BASE_URL . "index.php");
    exit;
}
include (__DIR__ . '/../../app/vistas/layout_privado_cabezera.php');

$categoria = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;
$noticiaSeleccionada = isset($_GET['noticia']) ? (int) $_GET['noticia'] : 0;

if(!$categoria){
      header("Location: ". BASE_URL ."usuarioadmin.php");
    exit;
}
/* 🔒 Seguridad: solo el dueño */
$res_noticias = mysqli_query($conn,"
    SELECT * FROM Noticias
    WHERE Idusuario='".$_SESSION['id']."'
    AND Idcategoria='$categoria'
    ORDER BY Fecha DESC
");

//============================================================================

if(isset($_POST['accion']) && $_POST['accion'] == 'actualizar_noticia'){

    $idn = intval($_POST['idnoticia']);
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];

                                    $stmt = $conn->prepare("
                                        UPDATE Noticias
                                        SET Titulo=?,
                                            Texto=?
                                        WHERE Idnoticia=?
                                        AND Idusuario=?
                                    ");

                                    $stmt->bind_param(
                                        "ssii",
                                        $titulo,
                                        $texto,
                                        $idn,
                                        $_SESSION['id']
                                    );

                                    $stmt->execute();

    // Imagen nueva
    if(!empty($_FILES['imagen']['name'])){

        $extPermitidas = ['jpg','jpeg','png','gif','webp','avif','jfif'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if(!in_array($ext, $extPermitidas)){
            $_SESSION['mensaje_error'] = "Formato de imagen no permitido";
            header("Location: modificar.php?categoria=$categoria&noticia=$idn");
            exit;
        }

        $nombreImagen = time() . '_' . basename($_FILES['imagen']['name']);
        $ruta = "imagen/fotoserieypelis/" . $nombreImagen;
        $rutaFisica = ROOT_PATH . $ruta;

        $old = mysqli_fetch_assoc(
            mysqli_query($conn,"SELECT Foton FROM FotosP WHERE Idnoticia='$idn'")
        );

        if(!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFisica)){
            $_SESSION['mensaje_error'] = "No se pudo guardar la imagen";
            header("Location: modificar.php?categoria=$categoria&noticia=$idn");
            exit;
        }

        if($old){
            if(file_exists(ROOT_PATH . $old['Foton'])){
                unlink(ROOT_PATH . $old['Foton']);
            }

            $stmtFoto = $conn->prepare("UPDATE FotosP SET Foton=? WHERE Idnoticia=?");
            $stmtFoto->bind_param("si", $ruta, $idn);
        } else {
            $stmtFoto = $conn->prepare("INSERT INTO FotosP (Idnoticia, Foton) VALUES (?, ?)");
            $stmtFoto->bind_param("is", $idn, $ruta);
        }

        $stmtFoto->execute();
    }
    $_SESSION['mensaje_error'] = "Error...";
    $_SESSION['mensaje'] = "Noticia actualizada correctamente ✅";    
    header("Location: modificar.php?categoria=$categoria&noticia=$idn");
    exit;
}

//===========================================================================================

if(isset($_POST['accion']) && $_POST['accion']=='actualizar_sub'){

                            $idsub = intval($_POST['idsub']);
                            $titulo = trim($_POST['titulo_sub']);
                            $texto  = trim($_POST['texto_sub']);
                            $youtube = trim($_POST['youtube_sub']);

                            // 1️⃣ Actualizar texto
                           $stmt = $conn->prepare("
                                        UPDATE subnoticias
                                        SET subtitulo=?,
                                            descripcion=?
                                        WHERE Idsubnoticias=?
                                    ");

                                    $stmt->bind_param("ssi", $titulo, $texto, $idsub);
                                    $stmt->execute();

                            

                            // 2️⃣ Obtener media actual
                            $resMedia = mysqli_query($conn,"
                                SELECT foto FROM Subnoticiafoto
                                WHERE Idsubnoticias='$idsub'
                            ");

                            $mediaActual = mysqli_fetch_assoc($resMedia);
                            $mediaVieja = $mediaActual['foto'] ?? '';

                            $nuevaMedia = '';

                            // 3️⃣ Si sube archivo nuevo
                            if(!empty($_FILES['media_sub']['name'])){

                                $extPermitidas = ['jpg','jpeg','png','gif','webp','avif','jfif'];
                                $ext = strtolower(pathinfo($_FILES['media_sub']['name'], PATHINFO_EXTENSION));

                                if(!in_array($ext, $extPermitidas)){
                                    $_SESSION['mensaje_error'] = "Formato de archivo no permitido";
                                    header("Location: ".$_SERVER['HTTP_REFERER']);
                                    exit;
                                }

                                $nuevaMedia = "imagen/fotoserieypelis/" . time() . '_' . basename($_FILES['media_sub']['name']);
                                move_uploaded_file($_FILES['media_sub']['tmp_name'], ROOT_PATH.$nuevaMedia);

                                // borrar archivo anterior si era imagen/video local
                                if($mediaVieja && file_exists(ROOT_PATH.$mediaVieja)){
                                    unlink(ROOT_PATH.$mediaVieja);
                                }
                            }

                            // 4️⃣ Si escribe YouTube
                            elseif(!empty($youtube)){

                                        // Validar formato YouTube
                                        if(preg_match('%(?:youtube\.com.*[?&]v=|youtu\.be/)([^"&?/ ]{11})%i',$youtube,$match)){
                                            $youtube = $match[1];
                                            $nuevaMedia = $youtube;

                                            if($mediaVieja && file_exists(ROOT_PATH.$mediaVieja)){
                                                unlink(ROOT_PATH.$mediaVieja);
                                            }

                                        } else {

                                            $_SESSION['mensaje_error'] = "❌ El enlace de YouTube no es válido";
                                            header("Location: ".$_SERVER['HTTP_REFERER']);
                                            exit;
                                        }
                                    }

                            // 5️⃣ Si hubo cambio de media
                            if(!empty($nuevaMedia)){
                                mysqli_query($conn,"
                                    UPDATE Subnoticiafoto
                                    SET foto='$nuevaMedia'
                                    WHERE Idsubnoticias='$idsub'
                                ");
                            }
                            $_SESSION['mensaje'] = "Subnoticia actualizada correctamente ✅";
                            header("Location: ".$_SERVER['HTTP_REFERER']);
                            exit;
}

if(isset($_POST['accion']) && $_POST['accion']=='eliminar_sub'){

                            $idsub = intval($_POST['idsub']);

                            // borrar media
                            $resMedia = mysqli_query($conn,"
                                SELECT foto FROM Subnoticiafoto
                                WHERE Idsubnoticias='$idsub'
                            ");

                            while($m = mysqli_fetch_assoc($resMedia)){
                                if(file_exists(ROOT_PATH.$m['foto'])){
                                    unlink(ROOT_PATH.$m['foto']);
                                }
                            }

                            mysqli_query($conn,"
                                DELETE FROM Subnoticiafoto
                                WHERE Idsubnoticias='$idsub'
                            ");

                            mysqli_query($conn,"
                                DELETE FROM subnoticias
                                WHERE Idsubnoticias='$idsub'
                            ");
                            $_SESSION['mensaje'] = "Subnoticia eliminada correctamente 🗑";
                            header("Location: ".$_SERVER['HTTP_REFERER']);
                            exit;
}

//=======================================================================
                                    if(isset($_POST['accion']) && $_POST['accion']=='crear_sub'){

                                        $idNoticia = intval($_POST['idnoticia']);
                                        $titulo = trim($_POST['titulo_sub']);
                                        $texto = trim($_POST['texto_sub']);
                                        $tipo = $_POST['tipo_sub'];

                                        // Verificar que la noticia pertenece al usuario
                                        $stmt = $conn->prepare("
                                            SELECT Idusuario FROM Noticias WHERE Idnoticia=?
                                        ");
                                        $stmt->bind_param("i", $idNoticia);
                                        $stmt->execute();
                                        $res = $stmt->get_result();
                                        $row = $res->fetch_assoc();

                                        if(!$row || $row['Idusuario'] != $_SESSION['id']){
                                            header("Location: ".BASE_URL."index.php");
                                            exit;
                                        }

                                        // Insertar subnoticia
                                        $stmt2 = $conn->prepare("
                                            INSERT INTO subnoticias(Idnoticia,subtitulo,descripcion)
                                            VALUES(?,?,?)
                                        ");
                                        $stmt2->bind_param("iss", $idNoticia, $titulo, $texto);
                                        $stmt2->execute();

                                        $idsub = $conn->insert_id;

                                        // Imagen
                                        if($tipo == "imagen" && !empty($_FILES['media_sub']['name'])){

                                            $extPermitidas = ['jpg','jpeg','png','gif','webp','avif','jfif'];
                                            $ext = strtolower(pathinfo($_FILES['media_sub']['name'], PATHINFO_EXTENSION));

                                            if(!in_array($ext, $extPermitidas)){
                                                $_SESSION['mensaje_error'] = "Formato de archivo no permitido";
                                                header("Location: ".$_SERVER['HTTP_REFERER']);
                                                exit;
                                            }

                                            $nombre = time().basename($_FILES['media_sub']['name']);
                                            $rutaFisica = $_SERVER['DOCUMENT_ROOT']."/ov1/imagen/fotoserieypelis/".$nombre;
                                            $rutaBD = "/imagen/fotoserieypelis/".$nombre;

                                            move_uploaded_file($_FILES['media_sub']['tmp_name'],$rutaFisica);

                                            $stmt3 = $conn->prepare("
                                                INSERT INTO Subnoticiafoto(Idsubnoticias,foto)
                                                VALUES(?,?)
                                            ");
                                            $stmt3->bind_param("is", $idsub, $rutaBD);
                                            $stmt3->execute();
                                        }

                                        // YouTube
                                        if($tipo == "youtube" && !empty($_POST['youtube_sub'])){

                                            if(preg_match('%(?:youtube\.com.*[?&]v=|youtu\.be/)([^"&?/ ]{11})%i',
                                                $_POST['youtube_sub'],$match)){

                                                $video = $match[1];

                                                $stmt4 = $conn->prepare("
                                                    INSERT INTO Subnoticiafoto(Idsubnoticias,foto)
                                                    VALUES(?,?)
                                                ");
                                                $stmt4->bind_param("is", $idsub, $video);
                                                $stmt4->execute();
                                            }
                                        }

                                        $_SESSION['mensaje'] = "Subnoticia creada correctamente ✅";

                                        header("Location: ".$_SERVER['HTTP_REFERER']);
                                        exit;
                                    }


     if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_noticia'){

                                                                                $id = intval($_POST['idnoticia']);

                                                                                /* Verificar que la noticia pertenece al usuario */

                                                                                $stmt = $conn->prepare("
                                                                                    SELECT Idusuario FROM Noticias
                                                                                    WHERE Idnoticia=?
                                                                                ");

                                                                                $stmt->bind_param("i",$id);
                                                                                $stmt->execute();

                                                                                $res = $stmt->get_result();
                                                                                $row = $res->fetch_assoc();

                                                                                if(!$row || $row['Idusuario'] != $_SESSION['id']){
                                                                                    header("Location: ".BASE_URL."index.php");
                                                                                    exit;
                                                                                }

                                                                                /* BORRAR FOTO PRINCIPAL */

                                                                                $resFoto = mysqli_query($conn,"
                                                                                    SELECT Foton FROM FotosP
                                                                                    WHERE Idnoticia='$id'
                                                                                ");

                                                                                while($f = mysqli_fetch_assoc($resFoto)){

                                                                                    if(file_exists(ROOT_PATH.$f['Foton'])){
                                                                                        unlink(ROOT_PATH.$f['Foton']);
                                                                                    }

                                                                                }

                                                                                mysqli_query($conn,"
                                                                                    DELETE FROM FotosP
                                                                                    WHERE Idnoticia='$id'
                                                                                ");

                                                                                /* BORRAR SUBNOTICIAS Y SUS MEDIAS */

                                                                                $resSub = mysqli_query($conn,"
                                                                                    SELECT Idsubnoticias FROM subnoticias
                                                                                    WHERE Idnoticia='$id'
                                                                                ");

                                                                                while($sub = mysqli_fetch_assoc($resSub)){

                                                                                    $idsub = $sub['Idsubnoticias'];

                                                                                    $resMedia = mysqli_query($conn,"
                                                                                        SELECT foto FROM Subnoticiafoto
                                                                                        WHERE Idsubnoticias='$idsub'
                                                                                    ");

                                                                                    while($m = mysqli_fetch_assoc($resMedia)){

                                                                                        if(file_exists(ROOT_PATH.$m['foto'])){
                                                                                            unlink(ROOT_PATH.$m['foto']);
                                                                                        }

                                                                                    }

                                                                                    mysqli_query($conn,"
                                                                                        DELETE FROM Subnoticiafoto
                                                                                        WHERE Idsubnoticias='$idsub'
                                                                                    ");

                                                                                }

                                                                                mysqli_query($conn,"
                                                                                    DELETE FROM subnoticias
                                                                                    WHERE Idnoticia='$id'
                                                                                ");

                                                                                /* BORRAR COMENTARIOS Y RESPUESTAS */

                                                                                $resCom = mysqli_query($conn,"
                                                                                    SELECT id FROM comentarios_noticias
                                                                                    WHERE Idnoticia='$id'
                                                                                ");

                                                                                while($c = mysqli_fetch_assoc($resCom)){

                                                                                    mysqli_query($conn,"
                                                                                        DELETE FROM reacciones_comentarios
                                                                                        WHERE Idcomentario='".$c['id']."'
                                                                                    ");

                                                                                }

                                                                                mysqli_query($conn,"
                                                                                    DELETE FROM comentarios_noticias
                                                                                    WHERE Idnoticia='$id'
                                                                                ");

                                                                                /* BORRAR VISITAS */

                                                                                mysqli_query($conn,"
                                                                                    DELETE FROM visitas_noticias
                                                                                    WHERE Idnoticia='$id'
                                                                                ");

                                                                                /* BORRAR NOTICIA */

                                                                                mysqli_query($conn,"
                                                                                    DELETE FROM Noticias
                                                                                    WHERE Idnoticia='$id'
                                                                                ");

                                                                                $_SESSION['mensaje'] = "🗑 Noticia eliminada correctamente";

                                                                                header("Location: ".BASE_URL."usuario/usuarioadmin.php");
                                                                                exit;
                                                                            }


                                    
//=====================================================================
                                    if(isset($_POST['accion']) && $_POST['accion']=='eliminar_respuesta'){

                                        $id = intval($_POST['idcom']);

                                        /* 🔒 Verificar que el comentario pertenece a una noticia del usuario */
                                        $stmt = $conn->prepare("
                                            SELECT n.Idusuario FROM comentarios_noticias c
                                            JOIN Noticias n ON n.Idnoticia = c.Idnoticia
                                            WHERE c.id=?
                                        ");
                                        $stmt->bind_param("i", $id);
                                        $stmt->execute();
                                        $row = $stmt->get_result()->fetch_assoc();

                                        if(!$row || $row['Idusuario'] != $_SESSION['id']){
                                            header("Location: ".BASE_URL."index.php");
                                            exit;
                                        }

                                        // eliminar reacciones
                                        mysqli_query($conn,"
                                            DELETE FROM reacciones_comentarios
                                            WHERE Idcomentario='$id'
                                        ");

                                        // eliminar comentario
                                        mysqli_query($conn,"
                                            DELETE FROM comentarios_noticias
                                            WHERE id='$id'
                                        ");

                                        $_SESSION['mensaje'] = "Respuesta eliminada 🗑";

                                        header("Location: ".$_SERVER['HTTP_REFERER']);
                                        exit;
                                    }
//=========================================================================

                                if(isset($_POST['accion']) && $_POST['accion']=='eliminar_completo'){

                                    $id = intval($_POST['idcom']);

                                    /* 🔒 Verificar que el comentario pertenece a una noticia del usuario */
                                    $stmt = $conn->prepare("
                                        SELECT n.Idusuario FROM comentarios_noticias c
                                        JOIN Noticias n ON n.Idnoticia = c.Idnoticia
                                        WHERE c.id=?
                                    ");
                                    $stmt->bind_param("i", $id);
                                    $stmt->execute();
                                    $row = $stmt->get_result()->fetch_assoc();

                                    if(!$row || $row['Idusuario'] != $_SESSION['id']){
                                        header("Location: ".BASE_URL."index.php");
                                        exit;
                                    }

                                    // eliminar reacciones del comentario principal
                                    mysqli_query($conn,"
                                        DELETE FROM reacciones_comentarios
                                        WHERE Idcomentario='$id'
                                    ");

                                    // obtener respuestas
                                    $res = mysqli_query($conn,"
                                        SELECT id FROM comentarios_noticias
                                        WHERE parent_id='$id'
                                    ");

                                    while($r = mysqli_fetch_assoc($res)){

                                        mysqli_query($conn,"
                                            DELETE FROM reacciones_comentarios
                                            WHERE Idcomentario='".$r['id']."'
                                        ");
                                    }

                                    // eliminar respuestas
                                    mysqli_query($conn,"
                                        DELETE FROM comentarios_noticias
                                        WHERE parent_id='$id'
                                    ");

                                    // eliminar comentario principal
                                    mysqli_query($conn,"
                                        DELETE FROM comentarios_noticias
                                        WHERE id='$id'
                                    ");

                                    $_SESSION['mensaje'] = "Comentario completo eliminado 🗑";

                                    header("Location: ".$_SERVER['HTTP_REFERER']);
                                    exit;
                                }


                                            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                                            header("Cache-Control: post-check=0, pre-check=0", false);
                                            header("Pragma: no-cache");
?>

<html>
    <head>
    <link href="../../estilo/admin.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    </head>
<body>

                                        <div class="admin-tabs">
                                            <div class="admin-tab active" data-tab="noticia">Noticia</div>
                                            <div class="admin-tab" data-tab="sub">Subnoticias</div>
                                            <div class="admin-tab" data-tab="com">Comentarios</div>
                                        </div>

        <div class="admin-container">
            <div class="tab-content active" id="tab-noticia">             
            <h2>Seleccionar noticia</h2>
                                <?php
                                if(isset($_SESSION['mensaje'])){
                                    echo "<div class='mensaje-ok'>".$_SESSION['mensaje']."</div>";
                                    unset($_SESSION['mensaje']);
                                }elseif(isset($_SESSION['mensaje_error'])){
                                        echo "<div class='mensaje-error'>".$_SESSION['mensaje_error']."</div>";
                                        unset($_SESSION['mensaje_error']);
                                    }
                                ?>
                        <form method="GET">
                            <input type="hidden" name="categoria" value="<?= $categoria ?>">

                            <select name="noticia" onchange="this.form.submit()">
                                <option value="">-- Selecciona noticia --</option>

                                <?php while($n = mysqli_fetch_assoc($res_noticias)){ ?>
                                    <option value="<?= $n['Idnoticia'] ?>"
                                        <?= $noticiaSeleccionada == $n['Idnoticia'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($n['Titulo']) ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </form>
                                                            <?php

                                                            /* VISITAS */
                                                            $resVisitas = mysqli_query($conn,"
                                                            SELECT visitas
                                                            FROM Noticias
                                                            WHERE Idnoticia='$noticiaSeleccionada'
                                                            ");

                                                            $visitas = mysqli_fetch_assoc($resVisitas)['visitas'] ?? 0;


                                                            /* COMENTARIOS */
                                                            $resComentarios = mysqli_query($conn,"
                                                            SELECT COUNT(*) total
                                                            FROM comentarios_noticias
                                                            WHERE Idnoticia='$noticiaSeleccionada'
                                                            ");

                                                            $comentarios = mysqli_fetch_assoc($resComentarios)['total'];


                                                            /* SUBNOTICIAS */
                                                            $resSub = mysqli_query($conn,"
                                                            SELECT COUNT(*) total
                                                            FROM subnoticias
                                                            WHERE Idnoticia='$noticiaSeleccionada'
                                                            ");

                                                            $subnoticias = mysqli_fetch_assoc($resSub)['total'];


                                                            /* REACCIONES */
                                                            $resReacciones = mysqli_query($conn,"
                                                            SELECT COUNT(*) total
                                                            FROM reacciones_comentarios rc
                                                            JOIN comentarios_noticias c
                                                            ON rc.Idcomentario = c.id
                                                            WHERE c.Idnoticia='$noticiaSeleccionada'
                                                            ");

                                                            $reacciones = mysqli_fetch_assoc($resReacciones)['total'];

                                                            ?>
                        <?php

                        if($noticiaSeleccionada){

                            $sql = mysqli_query($conn,"
                                SELECT * FROM Noticias
                                WHERE Idnoticia='$noticiaSeleccionada'
                                AND Idusuario='".$_SESSION['id']."'
                            ");

                            $noticia = mysqli_fetch_assoc($sql);

                            if($noticia){
                        ?>

                                        <h3>Modificar Noticia</h3>
                                                                        <div class="panel-estadisticas">

                                                                        <div class="stat-box">
                                                                        <span class="stat-icon">👁</span>
                                                                        <span class="stat-num"><?= $visitas ?></span>
                                                                        <span class="stat-label">Visitas</span>
                                                                        </div>

                                                                        <div class="stat-box">
                                                                        <span class="stat-icon">💬</span>
                                                                        <span class="stat-num"><?= $comentarios ?></span>
                                                                        <span class="stat-label">Comentarios</span>
                                                                        </div>

                                                                        <div class="stat-box">
                                                                        <span class="stat-icon">🧩</span>
                                                                        <span class="stat-num"><?= $subnoticias ?></span>
                                                                        <span class="stat-label">Subnoticias</span>
                                                                        </div>

                                                                        <div class="stat-box">
                                                                        <span class="stat-icon">❤️</span>
                                                                        <span class="stat-num"><?= $reacciones ?></span>
                                                                        <span class="stat-label">Reacciones</span>
                                                                        </div>

                                                                        </div>
                                                <form method="POST"
                                                    enctype="multipart/form-data">

                                                    <input type="hidden" name="accion" value="actualizar_noticia">
                                                    <input type="hidden" name="idnoticia"
                                                    value="<?= $noticia['Idnoticia'] ?>">

                                                    <label>Título</label>
                                                    <input type="text" name="titulo"
                                                    value="<?= htmlspecialchars($noticia['Titulo']) ?>">

                                                    <label>Texto</label>
                                                    <textarea name="texto">
                                                    <?= htmlspecialchars($noticia['Texto']) ?>
                                                    </textarea>
                                                    <?php
                                                        $res_foto = mysqli_query($conn,"
                                                            SELECT * FROM FotosP
                                                            WHERE Idnoticia='".$noticia['Idnoticia']."'
                                                        ");
                                                        $foto = mysqli_fetch_assoc($res_foto);
                                                        ?>

                                                        <?php if($foto): ?>
                                                        <img src="<?= BASE_URL.$foto['Foton'] ?>" width="200">
                                                        <?php else: ?>
                                                        <p>Esta noticia no tiene imagen principal.</p>
                                                        <?php endif; ?>

                                                        <br><br>

                                                        <label><?= $foto ? 'Cambiar imagen' : 'Añadir imagen' ?></label>
                                                        <input type="file" name="imagen">

                                                        <br><br>

                                                        <button type="submit">Guardar cambios</button>

                                                </form>
                                                            <form method="POST" 
                                                                onsubmit="return confirm('¿Eliminar noticia completa?')">

                                                                <input type="hidden" name="accion" value="eliminar_noticia">
                                                                <input type="hidden" name="idnoticia" value="<?= $noticia['Idnoticia'] ?>">

                                                                <button class="btn-eliminar">
                                                                    🗑 Eliminar noticia
                                                                </button>

                                                            </form>
                                                <?php 
                            }
                        }                   

                                                                $resSub = mysqli_query($conn,"
                                                            SELECT s.*, f.foto
                                                            FROM subnoticias s
                                                            LEFT JOIN Subnoticiafoto f
                                                            ON s.Idsubnoticias = f.Idsubnoticias
                                                            WHERE s.Idnoticia='$noticiaSeleccionada'
                                                            ORDER BY s.orden ASC
                                                        ");

                                                        if(mysqli_num_rows($resSub) == 0){
                                                               echo "<p class='sin-sub'>No hay subnoticias aún.</p>";
                                                                }
                                                ?>
        </div>
        <div class="tab-content" id="tab-sub">
            <h3>Subnoticias</h3>

            <?php echo '<div class="sub-grid" id="sortable-subnoticias">'; 
                     while($sub = mysqli_fetch_assoc($resSub)): ?>

                                  <form class="sub-box"
                                                        data-id="<?= $sub['Idsubnoticias'] ?>"
                                                        method="POST"
                                                        enctype="multipart/form-data">
                                    <input type="hidden" name="accion" value="actualizar_sub">
                                    <input type="hidden" name="idsub" value="<?= $sub['Idsubnoticias'] ?>">

                                    <label>Título</label>
                                    <input type="text" name="titulo_sub"
                                        value="<?= htmlspecialchars($sub['subtitulo']) ?>">

                                    <label>Texto</label>
                                    <textarea name="texto_sub"><?= htmlspecialchars($sub['descripcion']) ?></textarea>

                                    <label>Nuevo YouTube (ID o link)</label>
                                    <input type="text" name="youtube_sub" data-error="error_<?= $sub['Idsubnoticias'] ?>">

                                    <?php
                                                    $resMedia = mysqli_query($conn,"
                                                        SELECT foto FROM Subnoticiafoto
                                                        WHERE Idsubnoticias='".$sub['Idsubnoticias']."'
                                                    ");
                                                    $media = mysqli_fetch_assoc($resMedia);
                                                            echo '<div class="preview-media">';

                                                            if($media){

                                                                $item = $media['foto'];

                                                                // YouTube ID
                                                                if(preg_match('/^[a-zA-Z0-9_-]{11}$/',$item)){

                                                                    echo "<iframe 
                                                                            src='https://www.youtube.com/embed/$item' 
                                                                            frameborder='0'
                                                                            allowfullscreen>
                                                                        </iframe>";

                                                                }

                                                                // Imagen local
                                                                 elseif(file_exists(ROOT_PATH.$item)){

                                                                    echo "<img src='".BASE_URL.$item."'>";

                                                                }

                                                                else{

                                                                    echo '<div class="media-placeholder">
                                                                            <span>🚫</span>
                                                                            Media no encontrada
                                                                        </div>';
                                                                }

                                                            }else{

                                                                echo '<div class="media-placeholder">
                                                                        <span>🎬</span>
                                                                        Sin imagen ni video
                                                                    </div>';
                                                            }

                                                            echo '</div>';
                                    ?>

                                    <label>O subir nueva imagen/video</label>
                                    <input type="file" name="media_sub">

                                    <button type="submit">Guardar subnoticia</button>

                                    <button type="submit"
                                            name="accion"
                                            value="eliminar_sub"
                                            onclick="return confirm('¿Eliminar subnoticia?')">
                                    Eliminar
                                    </button>

                                    </form>

                 <?php endwhile;
                 echo '</div>'; ?>

                 <h3>Agregar nueva subnoticia</h3>

                                            <form method="POST" enctype="multipart/form-data">

                                                <input type="hidden" name="accion" value="crear_sub">
                                                <input type="hidden" name="idnoticia" value="<?= $noticiaSeleccionada ?>">

                                                <label>Título</label>
                                                <input type="text" name="titulo_sub" required>

                                                <label>Texto</label>
                                                <textarea name="texto_sub" required></textarea>

                                                <label>Tipo</label>
                                                <div>
                                                    <label><input type="radio" name="tipo_sub" value="imagen" required> Imagen</label>
                                                    <label><input type="radio" name="tipo_sub" value="youtube"> YouTube</label>
                                                </div>

                                                <label>Imagen</label>
                                                <input type="file" name="media_sub">

                                                <label>YouTube</label>
                                                <input type="text" name="youtube_sub">

                                                <button type="submit">Agregar subnoticia</button>

                                            </form>
       </div>
       <div class="tab-content" id="tab-com">             
                                        <h3>Comentarios</h3>

                                        <?php
                                        $resCom = mysqli_query($conn,"
                                            SELECT * FROM comentarios_noticias
                                            WHERE Idnoticia='$noticiaSeleccionada'
                                            AND parent_id IS NULL
                                            ORDER BY fecha DESC
                                        ");

                                        while($com = mysqli_fetch_assoc($resCom)){
                                        ?>

                                        <div style="border:1px solid #ccc;padding:10px;margin:10px 0;">

                                        <strong><?= $com['nombre'] ?></strong>
                                        <p><?= $com['comentario'] ?></p>

                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="accion" value="eliminar_completo">
                                            <input type="hidden" name="idcom" value="<?= $com['id'] ?>">
                                            <button onclick="return confirm('¿Eliminar comentario y todas sus respuestas?')">
                                                Eliminar comentario completo
                                            </button>
                                        </form>

                                        <?php
                                            // Cargar respuestas
                                            $resResp = mysqli_query($conn,"
                                                SELECT * FROM comentarios_noticias
                                                WHERE parent_id='".$com['id']."'
                                            ");

                                            while($resp = mysqli_fetch_assoc($resResp)){
                                        ?>
                                                <div style="margin-left:40px;border-left:3px solid #ddd;padding-left:10px;">

                                                    <strong><?= $resp['nombre'] ?></strong>
                                                    <p><?= $resp['comentario'] ?></p>

                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="accion" value="eliminar_respuesta">
                                                        <input type="hidden" name="idcom" value="<?= $resp['id'] ?>">
                                                        <button onclick="return confirm('¿Eliminar esta respuesta?')">
                                                            Eliminar respuesta
                                                        </button>
                                                    </form>

                                                </div>
                                        <?php
                                            }
                                        ?>
                                        </div>

                                        <?php } ?>
            </div>

                                                 
                       <?php
                                                   
                        mysqli_close($conn);
                        include ROOT_PATH . 'app/vistas/layout_privado_pie.php';
                        ?>
            </div>  
        </body>
                            <script>

                            window.APP = {
                                baseUrl: "<?= BASE_URL ?>",
                                usuario: <?= json_encode($_SESSION['id'] ?? null) ?>,
                                noticia: <?= json_encode($noticiaSeleccionada ?? null) ?>,
                                categoria: <?= json_encode($categoria ?? null) ?>
                            };

                            <?php
                            require_once ROOT_PATH.'js/modificar.js';
                            ?>
                            </script>
    </html>

