<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../php/config.php';

$conn = mysqli_connect($servername, $username, $password, $database);

if(!$conn){
    echo json_encode(['status'=>'error','msg'=>'conexion']);
    exit;
}

$id = $_POST['id'] ?? '';
$tipo = $_POST['tipo'] ?? '';

if(!$id || !$tipo){
    echo json_encode(['status'=>'error','msg'=>'datos']);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];

/* Crear cookie si no existe */
if(!isset($_COOKIE['usuario_unico'])){
    $cookie_id = uniqid('user_', true);
    setcookie('usuario_unico', $cookie_id, time() + (86400 * 30), "/");
} else {
    $cookie_id = $_COOKIE['usuario_unico'];
}

/* Verificar si ya votó */
$check = mysqli_query($conn,"
    SELECT id FROM votos_noticias
    WHERE Idnoticia='$id'
    AND (ip='$ip' OR cookie_id='$cookie_id')
");

if(mysqli_num_rows($check) > 0){
    echo json_encode(['status'=>'ya_voto']);
    exit;
}

/* Insertar voto */
mysqli_query($conn,"
    INSERT INTO votos_noticias (Idnoticia, ip, cookie_id, tipo)
    VALUES ('$id','$ip','$cookie_id','$tipo')
");

/* Actualizar contador */
if($tipo == 'like'){
    mysqli_query($conn,"
        UPDATE Noticias SET me_gusta = me_gusta + 1
        WHERE Idnoticia='$id'
    ");
}else{
    mysqli_query($conn,"
        UPDATE Noticias SET no_me_gusta = no_me_gusta + 1
        WHERE Idnoticia='$id'
    ");
}

/* Obtener nuevos totales */
$res = mysqli_query($conn,"
    SELECT me_gusta, no_me_gusta
    FROM Noticias
    WHERE Idnoticia='$id'
");

$data = mysqli_fetch_assoc($res);

echo json_encode([
    'status'=>'ok',
    'likes'=>$data['me_gusta'],
    'dislikes'=>$data['no_me_gusta']
]);
