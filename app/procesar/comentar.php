<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/config.php';
require_once ROOT_PATH . 'php/filtros.php';
require_once ROOT_PATH . 'php/comentario.php';

$id = $_POST['id'] ?? '';
$nombre = trim($_POST['nombre'] ?? '');
$comentario = trim($_POST['comentario'] ?? '');

if(!$id || !$nombre || !$comentario){
    echo json_encode(['status'=>'error','msg'=>'Datos incompletos']);
    exit;
}
//=================== filtrar palabras
$comentario = filtrarPalabras($comentario);
$ip = $_SERVER['REMOTE_ADDR'];

if(!isset($_COOKIE['usuario_unico'])){
    $cookie_id = uniqid('user_', true);
    setcookie('usuario_unico',$cookie_id,time()+(86400*30),"/");
}else{
    $cookie_id = $_COOKIE['usuario_unico'];
}

/* ---- LIMITE 3 POR IP ---- */
$limite = mysqli_query($conn,"
    SELECT COUNT(*) as total
    FROM comentarios_noticias
    WHERE Idnoticia='$id'
    AND ip='$ip'
");
$data = mysqli_fetch_assoc($limite);

if($data['total'] >= 3){
    echo json_encode(['status'=>'error','msg'=>'Máximo 3 comentarios']);
    exit;
}

/* ---- REPETIDO ---- */
$repetido = mysqli_query($conn,"
    SELECT id FROM comentarios_noticias
    WHERE Idnoticia='$id'
    AND comentario='$comentario'
    AND ip='$ip'
");

if(mysqli_num_rows($repetido) > 0){
    echo json_encode(['status'=>'error','msg'=>'Comentario repetido']);
    exit;
}
/* inserta noticias a la vez que hoy hemos ingresado una columna para enlazar con sus comentarios */
            $parent_id = $_POST['parent_id'] ?? NULL;

            mysqli_query($conn,"
            INSERT INTO comentarios_noticias
            (Idnoticia,nombre,comentario,ip,cookie_id,estado,parent_id)
            VALUES
            ('$id','$nombre','$comentario','$ip','$cookie_id','aprobado',".($parent_id ? "'$parent_id'" : "NULL").")
            ");

/* Obtener comentario recién insertado */
$ultimo = mysqli_insert_id($conn);

$res = mysqli_query($conn,"
    SELECT * FROM comentarios_noticias
    WHERE id='$ultimo'
");

$comentario_nuevo = mysqli_fetch_assoc($res);

ob_start();
renderComentario($conn, $comentario_nuevo);
$html = ob_get_clean();

echo json_encode([
    'status' => 'ok',
    'html'   => $html,
    'parent_id' => $parent_id ?? null
]);

