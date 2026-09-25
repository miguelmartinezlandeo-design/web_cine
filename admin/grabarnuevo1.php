<?php
session_start();
require_once '../php/config.php';

if(!isset($_SESSION['usuario'])){
    header("Location: ".BASE_URL."usuario.php");
    exit;
}


if($_SERVER["REQUEST_METHOD"]=="POST"){

                $categoria = $_POST['categoria'] ?? 0;
                $titulo = $_POST['titulo'] ?? "";
                $subtitulo = $_POST['subtitulo'] ?? "";
                $texto = $_POST['textoarea'] ?? "";
               // $visualizar1 = $_POST['presentacion'] ?? 0;

                $nombre_img = $_FILES['imagen']['name'];
                $tamano_img = $_FILES['imagen']['size'];

                if($categoria!=0 && $titulo!="" && $subtitulo!="" && $texto!="" && $nombre_img!=""){

                    /* Buscar categoría visualización 
                    $consulta="SELECT * FROM Visualizacion
                            WHERE Visualizar=".$visualizar1."
                            AND Idcategoria1=".$categoria;

                    $resultado=mysqli_query($conn,$consulta);
                    $fila=mysqli_fetch_array($resultado);

                    $idvisual=$fila['Idcategoria'];*/

                            if($categoria!=""){

                                /* Insertar noticia */
                                $insertar="INSERT INTO Noticias
                                        (Idcategoria,Titulo,Descrititulo,Texto,Idusuario)
                                        VALUES
                                        ($categoria,'$titulo','$subtitulo','$texto',".$_SESSION['id'].")";

                                mysqli_query($conn,$insertar);

                                /* Obtener ID noticia */
                                $idnoticia=mysqli_insert_id($conn);

                                /* Guardar imagen */
                                $directorio = $_SERVER['DOCUMENT_ROOT'].'/ov1/imagen/fotoserieypelis/';
                                $nombre_final = time().$nombre_img;
                                

                                $ruta_fisica = $directorio.$nombre_final;
                                $ruta_bd = 'imagen/fotoserieypelis/'.$nombre_final;

                                move_uploaded_file($_FILES['imagen']['tmp_name'],$ruta_fisica);

                                /* Insertar foto principal */
                                $insertarfoto = "INSERT INTO FotosP(Idnoticia,Foton)
                                                VALUES ($idnoticia,'$ruta_bd')";
                                mysqli_query($conn,$insertarfoto);

                                /* Redirigir a subnoticias */
                                $id_encode = base64_encode($idnoticia);
                                header("Location: subnoticiagregar.php?id=".$id_encode);
                                exit;
                            }

                }else{
                    echo "Faltan datos obligatorios";
                }
}
?>
