<?php
require_once 'reacciones.php';
    
function renderComentario($conn, $comentario){

    $esRespuesta = !empty($comentario['parent_id']);

    $clase = $esRespuesta 
        ? "comentario-item respuesta-item"
        : "comentario-item";

    echo "<div class='$clase' data-id='".$comentario['id']."'>";

    echo "<strong>".$comentario['nombre']."</strong>";
    echo "<p>".$comentario['comentario']."</p>";

    // Mostrar reacciones SIEMPRE
    mostrarReacciones($conn, $comentario['id']);

    if(!$esRespuesta){

        echo "<button class='btn-responder'>Responder</button>";
        echo "<div class='respuestas'>";

        $respuestas = mysqli_query($conn,"
            SELECT * FROM comentarios_noticias
            WHERE parent_id='".$comentario['id']."'
            AND estado='aprobado'
            ORDER BY fecha ASC
        ");

        while($resp = mysqli_fetch_assoc($respuestas)){
            renderComentario($conn, $resp);
        }

        echo "</div>";
    }

    echo "</div>";
}