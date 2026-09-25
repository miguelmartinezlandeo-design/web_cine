<script>
function comprobar(){

                  var titulo = document.formulario.titulo.value.trim();
                  var subtitulo = document.formulario.subtitulo.value.trim();
                  var texto = document.formulario.textoarea.value.trim();
                  var imagen = document.formulario.imagen;
                  var categoria = document.formulario.categoria.value;
                  //var presentacion = document.querySelector('input[name="presentacion"]:checked');

                  var aviso = document.getElementById("aviso");
                  aviso.innerHTML = "";

                  var errores = "";

                        if(titulo === ""){
                           errores += "Es obligatorio introducir el título.<br>";
                        }

                        if(subtitulo === ""){
                           errores += "Es obligatorio introducir el subtítulo.<br>";
                        }

                        if(texto === ""){
                           errores += "Es obligatorio introducir el contenido.<br>";
                        }

                        if(imagen.value === ""){
                           errores += "Debe seleccionar una imagen.<br>";
                        }else{
                           var ext = imagen.value.split('.').pop().toLowerCase();
                           if(ext !== "jpg" && ext !== "jpeg" && ext !== "png"){
                                 errores += "Formato de imagen no válido (jpg, jpeg, png).<br>";
                           }
                        }

                        if(categoria === "0"){
                           errores += "Categoría inválida.<br>";
                        }

                       
                        if(errores !== ""){
                           aviso.innerHTML = errores;
                           aviso.style.color = "red";
                           return false;
                        }

                        return true;
                     }

document.addEventListener("DOMContentLoaded", function(){

                const form = document.getElementById("formSubnoticia");

                form.addEventListener("submit", function(e){

                    let subtitulo = document.querySelector("[name='subnoticiax']").value.trim();
                    let texto = document.querySelector("[name='textogrande']").value.trim();
                    let tipo = document.querySelector("[name='rad']:checked");
                    let aviso = document.getElementById("avisoSub");

                    aviso.innerHTML = "";

                    if(subtitulo === ""){
                        aviso.innerHTML = "Debe ingresar el subtítulo";
                        e.preventDefault();
                        return;
                    }

                    if(!tipo){
                        aviso.innerHTML = "Seleccione Imagen o Youtube";
                        e.preventDefault();
                        return;
                    }

                    if(tipo.value=="1"){
                        let yt = document.querySelector("[name='youtubev']").value.trim();

                        let patron = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/;

                        if(!patron.test(yt)){
                            aviso.innerHTML="El enlace de Youtube no es válido";
                            e.preventDefault();
                            return;
                        }
                    }

                    if(tipo.value=="0"){
                        let img = document.querySelector("[name='imagen']").value;
                        if(img===""){
                            aviso.innerHTML="Debe seleccionar una imagen";
                            e.preventDefault();
                            return;
                        }
                    }

                    if(texto===""){
                        aviso.innerHTML="Debe ingresar el texto";
                        e.preventDefault();
                        return;
                    }

                });

});
</script>
