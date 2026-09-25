<?php
session_start();
require_once '../php/config.php';

if(!isset($_SESSION['usuario'])){
    header("Location: ".BASE_URL."usuario.php");
    exit;
}


if($_SERVER["REQUEST_METHOD"]=="POST"){

                $categoria = (int) ($_POST['categoria'] ?? 0);
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
                                $tituloEsc = mysqli_real_escape_string($conn, $titulo);
                                $subtituloEsc = mysqli_real_escape_string($conn, $subtitulo);
                                $textoEsc = mysqli_real_escape_string($conn, $texto);

                                $insertar="INSERT INTO Noticias
                                        (Idcategoria,Titulo,Descrititulo,Texto,Idusuario)
                                        VALUES
                                        ($categoria,'$tituloEsc','$subtituloEsc','$textoEsc',".$_SESSION['id'].")";

                                mysqli_query($conn,$insertar);

                                /* Obtener ID noticia */
                                $idnoticia=mysqli_insert_id($conn);

                                /* Guardar imagen */
                                $extPermitidas = ['jpg','jpeg','png','gif','webp','avif','jfif'];
                                $ext = strtolower(pathinfo($nombre_img, PATHINFO_EXTENSION));

                                if(!in_array($ext, $extPermitidas)){
                                    echo "Formato de imagen no permitido";
                                    exit;
                                }

                                $directorio = $_SERVER['DOCUMENT_ROOT'].'/ov1/imagen/fotoserieypelis/';
                                $nombre_final = time().'_'.basename($nombre_img);
                                

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
