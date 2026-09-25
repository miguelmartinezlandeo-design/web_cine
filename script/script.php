
<script>
document.addEventListener("DOMContentLoaded", function(){

    /* =========================
       BOTONES OPCIONALES
    ========================= */
    var btn = document.getElementById("submitBtn");
    if(btn){
        btn.addEventListener("click", function(){
            window.location.href="http://ov1.local?id=2";
        });
    }

    var btn1 = document.getElementById("submitBtn1");
    if(btn1){
        btn1.addEventListener("click", function(){
            window.location.href="http://ov1.local";
        });
    }

    /* =========================
       SELECT PERSONALIZADO
    ========================= */
    var selects = document.getElementsByClassName("custom-select");

    for (var i = 0; i < selects.length; i++) {

        var selElmnt = selects[i].getElementsByTagName("select")[0];
        if(!selElmnt) continue;

        var selected = document.createElement("DIV");
        selected.setAttribute("class", "select-selected");
        selected.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
        selects[i].appendChild(selected);

        var options = document.createElement("DIV");
        options.setAttribute("class", "select-items select-hide");

        for (var j = 1; j < selElmnt.length; j++) {

            var opt = document.createElement("DIV");
            opt.innerHTML = selElmnt.options[j].innerHTML;

            opt.addEventListener("click", function() {
                var s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                var h = this.parentNode.previousSibling;

                for (var i = 0; i < s.length; i++) {
                    if (s.options[i].innerHTML == this.innerHTML) {
                        s.selectedIndex = i;
                        h.innerHTML = this.innerHTML;
                        break;
                    }
                }
                h.click();
            });

            options.appendChild(opt);
        }

        selects[i].appendChild(options);

        selected.addEventListener("click", function(e) {
            e.stopPropagation();
            closeAllSelect(this);
            this.nextSibling.classList.toggle("select-hide");
        });
    }

    function closeAllSelect(elmnt) {
        var x = document.getElementsByClassName("select-items");
        for (var i = 0; i < x.length; i++) {
            x[i].classList.add("select-hide");
        }
    }

    document.addEventListener("click", closeAllSelect);

    /* =========================
       YOUTUBE EMBEDS
    ========================= */
    var videos = document.getElementsByClassName("youtube-player");

    for (var n = 0; n < videos.length; n++) {
        var div = document.createElement("div");
        div.setAttribute("data-id", videos[n].dataset.id);
        div.innerHTML = '<img src="https://i.ytimg.com/vi/'+videos[n].dataset.id+'/hqdefault.jpg"><div class="play"></div>';
        div.onclick = function(){
            var iframe = document.createElement("iframe");
            iframe.setAttribute("src","https://www.youtube.com/embed/"+this.dataset.id+"?autoplay=1");
            iframe.setAttribute("frameborder","0");
            iframe.setAttribute("allowfullscreen","1");
            this.parentNode.replaceChild(iframe,this);
        };
        videos[n].appendChild(div);
    }

});
/*
===============================================================
 a ver esto es para actualizar los votos de me gusta y no me gusta entonces con este JavaScript que está abajo hace esto mismo
============================================================== */

document.querySelectorAll('.votos-form button').forEach(btn => {

    btn.addEventListener('click', function(){

        const id = this.dataset.id;
        const tipo = this.dataset.tipo;

        fetch('<?= BASE_URL ?>app/procesar/votar.php', {
            method:'POST',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },
            body:'id='+id+'&tipo='+tipo
        })
        .then(res => res.json())
        .then(data => {

            if(data.status === 'ok'){
                document.getElementById('likes-count').innerText = data.likes;
                document.getElementById('dislikes-count').innerText = data.dislikes;

                document.querySelectorAll('.votos-form button')
                    .forEach(b => b.disabled = true);
            }

            if(data.status === 'ya_voto'){
                alert("Ya has votado en esta noticia 😉");
            }

        });

    });

});

// aquí hace una actualización sin refrescar la web anterior para poner Like o dislike

document.addEventListener("DOMContentLoaded", function(){

    const botones = document.querySelectorAll('.votos-form button');

    botones.forEach(btn => {

        btn.addEventListener('click', function(){

            const id = this.dataset.id;
            const tipo = this.dataset.tipo;

            fetch('<?= BASE_URL ?>app/procesar/votar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + id + '&tipo=' + tipo
            })
            .then(response => response.json())
            .then(data => {

                console.log(data); // 🔥 mira en consola

                if(data.status === 'ok'){
                    document.getElementById('likes-count').innerText = data.likes;
                    document.getElementById('dislikes-count').innerText = data.dislikes;
                    botones.forEach(b => b.disabled = true);
                }

                if(data.status === 'ya_voto'){
                    botones.forEach(b => b.disabled = true);
                }

            })
            .catch(error => {
                console.log("Error:", error);
            });

        });

    });

});

// aquí se añaden los comentarios pero éste ya va escribe tiene que ver algo con ellos

document.addEventListener("click", function(e){
    if(e.target.classList.contains("ver-mas")){
        const corto = e.target.previousElementSibling.previousElementSibling;
        const completo = e.target.previousElementSibling;

        corto.style.display = "none";
        completo.style.display = "inline";
        e.target.remove();
    }
});

// esto es la función para las reacciones a los comentarios y que no se haga un refrescar en la web
//============================================================

document.addEventListener("click", function(e){

    if(e.target.closest(".reacciones button")){

        const boton = e.target.closest("button");
        const contenedor = boton.closest(".reacciones");

        const id = contenedor.dataset.comentario;
        const tipo = boton.dataset.tipo;

        fetch("<?= BASE_URL ?>app/procesar/reaccionar.php",{
            method:"POST",
            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },
            body:"id="+id+"&tipo="+tipo
        })
        .then(res=>res.json())
        .then(data=>{

            if(data.status==="ok"){

                Object.keys(data.contador).forEach(tipo=>{
                    const btn = contenedor.querySelector("button[data-tipo='"+tipo+"']");
                    if(btn){
                        btn.querySelector("span").innerText = data.contador[tipo];
                    }
                });

                contenedor.querySelectorAll("button")
                    .forEach(b=>b.disabled=true);
            }

            if(data.status==="ya_reacciono"){
                alert("Ya reaccionaste a este comentario 😉");
            }

        });

    }

});
// hola aquí ponemos los comentarios sin recargar la web
// ===========================================================
document.getElementById("form-comentario")
.addEventListener("submit", function(e){

    e.preventDefault();

    const formData = new FormData(this);

    fetch("<?= BASE_URL ?>app/procesar/comentar.php",{
        method:"POST",
        body:formData
    })
    .then(res=>res.json())
    .then(data=>{

        if(data.status === "error"){
            document.getElementById("mensaje-comentario").innerHTML =
                "<div class='error-comentario'>"+data.msg+"</div>";
            return;
        }

        if(data.status === "ok"){

            document.getElementById("mensaje-comentario").innerHTML =
                "<div class='ok-comentario'>Comentario publicado</div>";

            /* Insertar comentario arriba e inserta las reacciones*/
            const contenedor = document.querySelector(".lista-comentarios");

                    const nuevo = document.createElement("div");
                    nuevo.classList.add("comentario-item");
                    nuevo.setAttribute("data-id", data.comentario.id);

                    nuevo.innerHTML = `
                        <strong>${data.comentario.nombre}</strong>
                        <p>${data.comentario.comentario}</p>
                          <button class="btn-responder">Responder</button>
                            <div class="respuestas"></div>

                        <div class="reacciones" data-comentario="${data.comentario.id}">
                            <button data-tipo="love">❤️ <span>0</span></button>
                            <button data-tipo="haha">😂 <span>0</span></button>
                            <button data-tipo="wow">😮 <span>0</span></button>
                            <button data-tipo="sad">😢 <span>0</span></button>
                            <button data-tipo="angry">😡 <span>0</span></button>
                        </div>
                    `;
            contenedor.prepend(nuevo);

            document.getElementById("form-comentario").reset();
        }

    });

});

// esto es para que aparezca el comentario de los comentarios las respuestas con su ex área
document.addEventListener("click", function(e){

    if(e.target.classList.contains("btn-responder")){

        const comentario = e.target.closest(".comentario-item");
        const respuestas = comentario.querySelector(".respuestas");
        const parentId = comentario.dataset.id;

        // Evitar duplicar formulario
        if(respuestas.querySelector(".form-respuesta")){
            return;
        }

        const form = document.createElement("form");
        form.classList.add("form-respuesta");

        form.innerHTML = `
            <input type="hidden" name="parent_id" value="${parentId}">
            <input type="text" name="nombre" placeholder="Tu nombre" required>
            <textarea name="comentario" placeholder="Responder..." required></textarea>
            <button type="submit">Enviar</button>
        `;

        respuestas.appendChild(form);
    }

});


	document.addEventListener("submit", function(e){

    if(e.target.classList.contains("form-respuesta")){
	//	alert("Submit detectado");
        e.preventDefault();

        console.log("Submit respuesta capturado");
		console.log("Enviando datos...");
        const form = e.target;
        const formData = new FormData(form);
        const idNoticia = document.getElementById("id-noticia").value;
    	formData.append("id", idNoticia);
        fetch("<?= BASE_URL ?>app/procesar/comentar.php",{
            method:"POST",
            body:formData
        })
        .then(res => res.json())
						.then(data => {

						if(data.status === "ok"){

							const nueva = document.createElement("div");
							nueva.classList.add("comentario-item");
							nueva.setAttribute("data-id", data.comentario.id);

							nueva.innerHTML = `
								<strong>${data.comentario.nombre}</strong>
								<p>${data.comentario.comentario}</p>

								<div class="reacciones" data-comentario="${data.comentario.id}">
									<button data-tipo="love">❤️ <span>0</span></button>
									<button data-tipo="haha">😂 <span>0</span></button>
									<button data-tipo="wow">😮 <span>0</span></button>
									<button data-tipo="sad">😢 <span>0</span></button>
									<button data-tipo="angry">😡 <span>0</span></button>
								</div>
							`;

							form.parentElement.appendChild(nueva);
							form.remove();

						} else {

							alert(data.msg); // 🔥 muestra el mensaje real

						}

					})
					.catch(error => {
						alert("Error de conexión");
					});

    }

});
//=========================================Mensaje para Modificar


setTimeout(()=>{
    const msg = document.querySelector('.mensaje-ok');
    if(msg) msg.style.display='none';
},3000);

</script>