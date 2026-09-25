import { initVotos } from './votos.js?v=2';
import { initComentarios } from './comentarios.js?v=2';
import { initRespuestas } from './respuestas.js?v=2';
import { initReacciones } from './reacciones.js?v=2';


document.addEventListener("DOMContentLoaded", ()=>{

    initVotos();
    initComentarios();
    initRespuestas();
    initReacciones();

});
