import { post } from './api.js';

export function initReacciones(){

    document.addEventListener("click", async (e)=>{

        const boton = e.target.closest(".reacciones button");
        if(!boton) return;

        const contenedor = boton.closest(".reacciones");
        //alert("ID: " + contenedor.dataset.comentario);
        const data = await post("/ov1/app/procesar/reaccionar.php",{
            id: contenedor.dataset.comentario,
            tipo: boton.dataset.tipo
        });

        if(data.status==="ok"){

            Object.keys(data.contador).forEach(tipo=>{
                const btn = contenedor.querySelector(`button[data-tipo='${tipo}']`);
                if(btn){
                    btn.querySelector("span").innerText = data.contador[tipo];
                }
            });

            contenedor.querySelectorAll("button")
                .forEach(b=>b.disabled=true);
        }

    });
}