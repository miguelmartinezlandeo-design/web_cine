import { initVotos } from './votos.js';
import { initComentarios } from './comentarios.js';
import { initRespuestas } from './respuestas.js';
import { initReacciones } from './reacciones.js';


document.addEventListener("DOMContentLoaded", ()=>{

    initVotos();
    initComentarios();
    initRespuestas();
    initReacciones();

});
