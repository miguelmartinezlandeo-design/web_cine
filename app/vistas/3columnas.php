<?php
if(mysqli_num_rows($result) >= 1){
    // 1. PRIMERA NOTICIA (GRANDE)
    $row = mysqli_fetch_array($result);
    $res_f = mysqli_query($conn, "SELECT * FROM FotosP WHERE Idnoticia=" . $row['Idnoticia']);
    $foto = mysqli_fetch_array($res_f);
    $id_e = urlencode(base64_encode($row['Idnoticia']));

    echo "<div class='noticia-principal-v2'>
            <a href='mostrarnoticia.php?id=$id_e'>
                <div class='caja-foto-grande'>
                    <img src='".BASE_URL."/".$foto['Foton']."'>
                    <div class='texto-titular'>
                        <h2>{$row['Titulo']}</h2>
                    </div>
                </div>
            </a>
          </div>";

    // 2. LAS SIGUIENTES 6 NOTICIAS (GRILLA)
    echo "<div class='grilla-noticias'>"; // <-- CLAVE: Esta clase activa el Grid
    while($row = mysqli_fetch_array($result)){
        $res_f = mysqli_query($conn, "SELECT * FROM FotosP WHERE Idnoticia=" . $row['Idnoticia']);
        $foto = mysqli_fetch_array($res_f);
        $id_e = urlencode(base64_encode($row['Idnoticia']));
        
        echo "<div class='tarjeta-noticia'>
                <a href='mostrarnoticia.php?id=$id_e'>
                    <div class='crop-foto'>
                        <img src='".BASE_URL.$foto['Foton']."'>
                    </div>
                    <div class='tarjeta-info'>
                        <h3>{$row['Titulo']}</h3>
                    </div>
                </a>
              </div>";
    }
    echo "</div>"; 
}
?>