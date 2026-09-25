import { post } from './api.js';

export function initVotos(){

    document.addEventListener("click", async function(e){

        const boton = e.target.closest(".votos-form button");
        if(!boton) return;

        const data = await fetch('/ov1/app/procesar/votar.php',{
            method:'POST',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },
            body:`id=${boton.dataset.id}&tipo=${boton.dataset.tipo}`
        }).then(res=>res.json());

        if(data.status==='ok'){

            document.getElementById('likes-count').innerText = data.likes;
            document.getElementById('dislikes-count').innerText = data.dislikes;

            document.querySelectorAll('.votos-form button')
                .forEach(b=>b.disabled=true);
        }

        if(data.status==='ya_voto'){
            alert("Ya has votado 😉");
        }

    });

}