import { post } from './api.js?v=2';

export function initRespuestas(){

    /* BOTÓN RESPONDER */
    document.addEventListener("click", function(e){

        const boton = e.target.closest(".btn-responder");
        if(!boton) return;

        const comentario = boton.closest(".comentario-item");
        const respuestas = comentario.querySelector(".respuestas");
        const parentId = comentario.dataset.id;

        if(respuestas.querySelector(".form-respuesta")) return;

        const form = document.createElement("form");
        form.classList.add("form-respuesta");

        form.innerHTML = `
            <input type="hidden" name="parent_id" value="${parentId}">
            <input type="text" name="nombre" placeholder="Tu nombre" required>
            <textarea name="comentario" placeholder="Responder..." required></textarea>
            <button type="submit">Enviar</button>
        `;

        respuestas.appendChild(form);
    });


    /* ENVIAR RESPUESTA */
    document.addEventListener("submit", async function(e){

        if(!e.target.classList.contains("form-respuesta")) return;

        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const idNoticia = document.getElementById("id-noticia").value;
        formData.append("id", idNoticia);

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

            const parentContainer = form.closest(".respuestas");

            // 🔥 Insertamos HTML generado por PHP
            parentContainer.insertAdjacentHTML("beforeend", data.html);

            form.remove();
        }

    });

}