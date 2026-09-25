
<?php
//require_once 'php/fotoredimensionar.php';
//h
if (mysqli_num_rows($result)>=1){
    $row=mysqli_fetch_array($result);

    $consultafotos='select * from FotosP where Idnoticia='. $row['Idnoticia'];
    $resultadofotos=mysqli_query($conn, $consultafotos);
    $arrayfotos=mysqli_fetch_array($resultadofotos);
    $evaluar= $row['Visualizar'];
    $Numerop=$row['Nomegusta'];
    $notas='select (sum(Nota)/'. $Numerop .') as Notax from Notas where Idnoticia='. $row['Idnoticia'];
    $resultadonotas=mysqli_query($conn, $notas);

          echo "<div class='table'>
                <img class='Primerafoto' src='". $arrayfotos['Foton'] ."'>
                <p class='Titulo1'>". $row['Titulo']."</p>";


                if ($evaluar>1){
                  $id=base64_encode($id);
                  echo"<form action='mostrarnoticia.php' method='post'>
                  <div class='table'>
                        <p>
                        <input type='submit' name='boton' id='Seleccion' value='Selecciona'>

                          <label class='container'>1
                                                  <input type='radio' name='radio' value='1'>
                          <span class='checkmark'></span></label>
                          <label class='container'>2
                                                  <input type='radio' name='radio' value='2'>
                          <span class='checkmark'></span></label>
                          <label class='container'>3
                                                  <input type='radio' name='radio' value='3'>
                          <span class='checkmark'></span></label>
                          <label class='container'>4
                                                  <input type='radio' name='radio' value='4'>
                          <span class='checkmark'></span></label>
                          <label class='container'>5
                                                  <input type='radio' name='radio' value='5'>
                          <span class='checkmark'></span></label>
                          <label class='container'>6
                                                  <input type='radio' name='radio' value='6'>
                          <span class='checkmark'></span></label>
                          <label class='container'>7
                                                  <input type='radio' name='radio' value='7'>
                          <span class='checkmark'></span></label>
                          <label class='container'>8
                                                  <input type='radio' name='radio' value='8'>
                          <span class='checkmark'></span></label>
                          <label class='container'>9
                                                  <input type='radio' name='radio' value='9'>
                          <span class='checkmark'></span></label>
                          <label class='container'>10
                                                  <input type='radio' name='radio' value='10'>
                          <span class='checkmark'></span></label>


                        </p>
                        </div>
                        <input name='npersonas' type='hidden' value='". $Numerop ."'>";
                        $renota=mysqli_fetch_array($resultadonotas);
                        $renota=$renota['Notax'];
                        echo "<p class='Textop' >Numero de Evaluaciones: ". $Numerop ."<br>
                        Promedio de Notas:". $renota ."</p>
                        <input name='id' type='hidden' value='". $id ."'>
                        </form>";

                }else {

                }

          echo "
                <p class='subtitulo'>". $row['Descrititulo']."</p>
                <p class='Textor'>". _JS($row['Texto']) ."</p>
                </div>";


                echo "<div class='table'>";
                  echo "<div class='CabecerayPie1'>";
                  $consultas= "select * from subnoticias where Idnoticia=" . $row['Idnoticia'] ;
                  $resultados= mysqli_query($conn,$consultas);
                   while ($arraysubtitulo = mysqli_fetch_array($resultados)){
                     $consultafotonew="select * from subnoticias
                      JOIN Subnoticiafoto on subnoticias.Idsubnoticias = Subnoticiafoto.Idsubnoticias
                      where subnoticias.Idsubnoticias=". $arraysubtitulo['Idsubnoticias'];
                        $fotosnew= mysqli_query($conn,$consultafotonew);
                          if (mysqli_num_rows($fotosnew)>=1){
                                $fotossub=mysqli_fetch_array($fotosnew);
                                $fotosub=$fotossub['foto'];
                                echo "<div class='Celda'>
                                           <p class='subtitulo'>". $arraysubtitulo['subtitulo'] ."</p>";
                                           if (strlen(strstr($fotosub,'imagen/fotoserieypelis/'))>0) {
                                echo "<p class='Textor'><span class='tamanosub'><img src='". $fotosub ."'></span>". $arraysubtitulo['descripcion'] ."</p>";
                              }else{
                                echo "<div class='youtube-player' data-id='". $fotosub ."'></div><p class='Textor'>". $arraysubtitulo['descripcion']."</p>";

                            }
                                 echo "</div>";
                          }else {
                            echo "<div class='Celda'>
                                       <p class='subtitulo'>". $arraysubtitulo['subtitulo'] ."</p>
                                       <p class='Textor'>". $arraysubtitulo['descripcion'] ."</p>";
                             echo "</div>";
                          }


                         echo "</div>";
                  }
                echo "</div>";
                mysqli_free_result($result);
                $row="";


      }else{
    echo "No hay noticia disponible";
}
$row="";
require_once 'script/script.php';
?>
