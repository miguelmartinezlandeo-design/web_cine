<?php
ob_start();
include __DIR__ . '/index.php';
$html = ob_get_clean();
echo $html;
?>
<script>
document.addEventListener("DOMContentLoaded", function(){
setTimeout(function(){
  var sel = [".cabecera-app",".logo-app img",".hamburger",".barra-superior",".btn-usuario",
             ".filtro-botones",".filtro-botones a",".btn-paginacion",".noticia-principal-v2",
             ".caja-foto-grande",".texto-titular h2",".grilla-noticias",".tarjeta-noticia",
             ".crop-foto",".tarjeta-info h3",".seccion-archivo",".titulo-seccion",
             ".contenedor-dos-columnas",".item-lista-dos",".mini-foto",".pie-img"];
  var out = {};
  out.viewport = innerWidth + "x" + innerHeight;
  out.stylesheets = Array.prototype.map.call(document.styleSheets, function(s){
      return (s.href||"inline").split("/").pop() + " rules=" + (function(){try{return s.cssRules.length}catch(e){return "BLOCKED"}})();
  });
  sel.forEach(function(s){
    var el = document.querySelector(s);
    if(!el){ out[s]="MISSING"; return; }
    var r = el.getBoundingClientRect(), c = getComputedStyle(el);
    out[s] = "box=" + Math.round(r.width) + "x" + Math.round(r.height) + "@" + Math.round(r.top)
      + " | disp=" + c.display + " gtc=" + c.gridTemplateColumns.slice(0,60)
      + " rad=" + c.borderRadius + " fs=" + c.fontSize + " fw=" + c.fontWeight
      + " ff=" + c.fontFamily.split(",")[0] + " sh=" + (c.boxShadow||"none").slice(0,34);
  });
  out.counts = {
    tarjetas: document.querySelectorAll(".tarjeta-noticia").length,
    items2col: document.querySelectorAll(".item-lista-dos").length
  };
  out.overflowX = document.documentElement.scrollWidth > innerWidth
      ? "OVERFLOW " + document.documentElement.scrollWidth + ">" + innerWidth : "ok";
  out.bodyClass = document.body.className;
  out.imagesNoAlt = Array.prototype.filter.call(document.images,function(i){return i.getAttribute("alt")===null}).length;
  out.brokenImgs = Array.prototype.filter.call(document.images,function(i){return !i.complete || i.naturalWidth===0}).map(function(i){return i.getAttribute("src")});
  var pre = document.createElement("pre");
  pre.id = "AUDIT";
  pre.textContent = JSON.stringify(out, null, 1);
  document.body.appendChild(pre);
}, 2500);
});
</script>