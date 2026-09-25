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
                    <img src='".BASE_URL."/".$foto['Foton']."' style='width:100%; height:100%; object-fit:cover;'>
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
                    <div style='padding:10px;'>
                        <h3 style='margin:0; font-size:14px; color:#333;'>{$row['Titulo']}</h3>
                    </div>
                </a>
              </div>";
    }
    echo "</div>"; 
}
?>