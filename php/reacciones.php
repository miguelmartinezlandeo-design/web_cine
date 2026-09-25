<?php

function mostrarReacciones($conn, $comentario_id){

    $reacciones = mysqli_query($conn,"
        SELECT tipo, COUNT(*) as total
        FROM reacciones_comentarios
        WHERE Idcomentario='$comentario_id'
        GROUP BY tipo
    ");

    $contador = [
        'love'=>0,
        'haha'=>0,
        'angry'=>0,
        'wow'=>0,
        'sad'=>0
    ];

    while($r = mysqli_fetch_assoc($reacciones)){
        $contador[$r['tipo']] = $r['total'];
    }

    echo "<div class='reacciones' data-comentario='$comentario_id'>";
    echo "<button data-tipo='love'>❤️ <span>".$contador['love']."</span></button>";
    echo "<button data-tipo='haha'>😂 <span>".$contador['haha']."</span></button>";
    echo "<button data-tipo='wow'>😮 <span>".$contador['wow']."</span></button>";
    echo "<button data-tipo='sad'>😢 <span>".$contador['sad']."</span></button>";
    echo "<button data-tipo='angry'>😡 <span>".$contador['angry']."</span></button>";
    echo "</div>";

}
?>