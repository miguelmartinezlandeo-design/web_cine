<?php

require_once '../../php/config.php';

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