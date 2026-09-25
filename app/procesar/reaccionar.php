<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../php/config.php';


$idcomentario = $_POST['id'] ?? '';
$tipo = $_POST['tipo'] ?? '';

if(!$idcomentario || !$tipo){
    echo json_encode(['status'=>'error']);
    exit;
}

$idcomentario = (int) $idcomentario;
$tipo = mysqli_real_escape_string($conn, $tipo);
$ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);

if(!isset($_COOKIE['usuario_unico'])){
    $cookie_id = uniqid('user_', true);
    setcookie('usuario_unico',$cookie_id,time()+(86400*30),"/");
}else{
    $cookie_id = $_COOKIE['usuario_unico'];
}
$cookie_id = mysqli_real_escape_string($conn, $cookie_id);

/* Verificar si ya reaccionó */
$check = mysqli_query($conn,"
    SELECT id FROM reacciones_comentarios
    WHERE Idcomentario='$idcomentario'
    AND (ip='$ip' OR cookie_id='$cookie_id')
");

if(mysqli_num_rows($check) > 0){
    echo json_encode(['status'=>'ya_reacciono']);
    exit;
}


/* Insertar reacción */
mysqli_query($conn,"
    INSERT INTO reacciones_comentarios
    (Idcomentario,ip,cookie_id,tipo)
    VALUES
    ('$idcomentario','$ip','$cookie_id','$tipo')
");

/* Obtener nuevos totales */
$res = mysqli_query($conn,"
    SELECT tipo, COUNT(*) as total
    FROM reacciones_comentarios
    WHERE Idcomentario='$idcomentario'
    GROUP BY tipo
");

$contador = [];
while($row = mysqli_fetch_assoc($res)){
    $contador[$row['tipo']] = $row['total'];
}

echo json_encode([
    'status'=>'ok',
    'contador'=>$contador
]);