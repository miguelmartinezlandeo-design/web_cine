<?php
function controlarVisitas($conn,$id,$ip){

/* Crear cookie única si no existe */
if(!isset($_COOKIE['usuario_unico'])){
    $cookie_id = uniqid('user_', true);
    setcookie('usuario_unico', $cookie_id, time() + (86400 * 30), "/"); // 30 días
} else {
    $cookie_id = $_COOKIE['usuario_unico'];
}

/* Verificar si ya visitó esta noticia */
$check = mysqli_query($conn, "
    SELECT id FROM visitas_noticias 
    WHERE Idnoticia='$id' 
    AND (ip='$ip' OR cookie_id='$cookie_id')
");

// Control visitas
if(mysqli_num_rows($check) == 0){

    mysqli_query($conn, "
        INSERT INTO visitas_noticias (Idnoticia, ip, cookie_id)
        VALUES ('$id','$ip','$cookie_id')
    ");

    mysqli_query($conn, "
        UPDATE Noticias 
        SET visitas = visitas + 1 
        WHERE Idnoticia='$id'
    ");
}

    /* DEVOLVER visitas actualizadas */
    $result = mysqli_query($conn, "
        SELECT visitas FROM Noticias 
        WHERE Idnoticia='$id'
    ");
    $data = mysqli_fetch_assoc($result);
    return $data['visitas'];
}
