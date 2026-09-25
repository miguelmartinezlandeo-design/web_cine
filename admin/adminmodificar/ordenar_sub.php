<?php
session_start();

require_once '../../php/config.php';

if(empty($_SESSION['id'])){
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(["status"=>"error","msg"=>"No autorizado"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"),true);

foreach($data as $item){

    $stmt = $conn->prepare("
        UPDATE subnoticias
        SET orden=?
        WHERE Idsubnoticias=?
    ");

    $stmt->bind_param("ii",$item['orden'],$item['id']);
    $stmt->execute();

}

echo json_encode(["status"=>"ok"]);