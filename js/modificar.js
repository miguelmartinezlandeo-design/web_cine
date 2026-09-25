document.addEventListener("DOMContentLoaded", function(){

                                                                    setTimeout(()=>{
                                                                        const msg = document.querySelector('.mensaje-ok');
                                                                        if(msg) msg.style.display='none';
                                                                    },3000);
                                        
                                document.querySelectorAll('.admin-tab').forEach(tab=>{

                                    tab.addEventListener('click',()=>{

                                        document.querySelectorAll('.admin-tab')
                                        .forEach(t=>t.classList.remove('active'));

                                        document.querySelectorAll('.tab-content')
                                        .forEach(c=>c.classList.remove('active'));

                                        tab.classList.add('active');

                                        const idTab = 'tab-'+tab.dataset.tab;

                                        document.getElementById(idTab)
                                        .classList.add('active');

                                        // guardar tab en memoria del navegador
                                        localStorage.setItem("adminTab", tab.dataset.tab);

                                    });

                                });


                                        const tabGuardado = localStorage.getItem("adminTab");

                                        if(tabGuardado){

                                            const tab = document.querySelector(`[data-tab="${tabGuardado}"]`);

                                            if(tab){
                                                tab.click();
                                            }

                                        }



const lista = document.getElementById("sortable-subnoticias");

if(lista){

    new Sortable(lista,{

        animation:150,

        onEnd:function(){

            const orden = [];

            document.querySelectorAll(".sub-box").forEach((el,index)=>{

                orden.push({
                    id:el.dataset.id,
                    orden:index+1
                });

            });

            fetch(APP.baseUrl+"admin/adminmodificar/ordenar_sub.php",{

                method:"POST",

                headers:{
                    "Content-Type":"application/json"
                },

                body:JSON.stringify(orden)

            });

        }

    });

}




                                                                document.querySelectorAll("input[name='youtube_sub']").forEach(input=>{

                                                                input.addEventListener("input",function(){

                                                                    const valor = this.value.trim();
                                                                    const errorId = this.dataset.error;

                                                                    let errorBox = document.getElementById(errorId);

                                                                    if(!errorBox){
                                                                        errorBox = document.createElement("div");
                                                                        errorBox.id = errorId;
                                                                        errorBox.style.color="red";
                                                                        errorBox.style.fontSize="12px";
                                                                        this.after(errorBox);
                                                                    }

                                                                    if(valor === ""){
                                                                        errorBox.innerHTML="";
                                                                        return;
                                                                    }

                                                                    const regex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/;

                                                                    if(!regex.test(valor)){
                                                                        errorBox.innerHTML="⚠️ Enlace YouTube inválido";
                                                                    }else{
                                                                        errorBox.innerHTML="";
                                                                    }

                                                                });

                                                            });
                               
                                                        });