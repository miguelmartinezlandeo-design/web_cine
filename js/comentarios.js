
import { post } from './api.js';

export function initComentarios(){

    const form = document.getElementById("form-comentario");
    if(!form) return;

    form.addEventListener("submit", async (e)=>{

        e.preventDefault();

        const formData = new FormData(form);

        const res = await fetch("/ov1/app/procesar/comentar.php",{
            method:"POST",
            body:formData
        });

        
        const data = await res.json();

        if(data.status === "error"){
            alert(data.msg);
            return;
        }

        if(data.status === "ok"){

            const contenedor = document.querySelector(".lista-comentarios");

            // 🔥 Insertamos HTML generado por PHP
            contenedor.insertAdjacentHTML("afterbegin", data.html);

            form.reset();
        }

    });

}