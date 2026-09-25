<?php
// Usamos el segundo bloque de noticias ($phpnoticia1 definido en el index)
if ($result2 = mysqli_query($conn, $phpnoticia1)) {
    if (mysqli_num_rows($result2) >= 1) {
        echo "<div class='contenedor-dos-columnas'>";
        
        while ($row = mysqli_fetch_array($result2)) {
            $res_f = mysqli_query($conn, "SELECT * FROM FotosP WHERE Idnoticia=" . $row['Idnoticia']);
            $foto = mysqli_fetch_array($res_f);
            $id_e = urlencode(base64_encode($row['Idnoticia']));
            
            // Recortamos el texto para que no sea muy largo
            $resumen = substr(strip_tags($row['Texto']), 0, 100) . "...";

            echo "
            <div class='item-lista-dos'>
                <a href='mostrarnoticia.php?id=$id_e' class='enlace-noticia'>
                    <div class='mini-foto'>
                        <img src='".BASE_URL.$foto['Foton']."' alt='thumb'>
                    </div>
                    <div class='mini-texto'>
                        <h4>{$row['Titulo']}</h4>
                        <p>$resumen</p>
                    </div>
                </a>
            </div>";
        }
        
        echo "</div>";
    }
}
?>