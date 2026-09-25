
<?php
    if (session_status() === PHP_SESSION_NONE) {
            session_start();
    

            if(!isset($_SESSION['usuario'])){
                            
                            header("Location:". BASE_URL ."usuario/usuario.php");
                            exit;
            }
    }
    include ROOT_PATH . 'app/vistas/cabecera.php';
?>
