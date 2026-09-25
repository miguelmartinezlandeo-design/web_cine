<?php
function renderMedia($item){

    $item_original = trim($item);
    $item_clean = explode('?', $item_original)[0];

    // ID directo de youtube
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $item_clean)) {

        echo "<div class='video-responsive'>
                <iframe src='https://www.youtube.com/embed/{$item_clean}' allowfullscreen></iframe>
              </div>";
        return;
    }

    // Link youtube completo
    if (strpos($item_original, 'youtube') !== false || strpos($item_original, 'youtu.be') !== false) {

        if (preg_match('%(?:youtube\.com.*[?&]v=|youtu\.be/)([^"&?/ ]{11})%i', $item_original, $match)) {

            echo "<div class='video-responsive'>
                    <iframe src='https://www.youtube.com/embed/{$match[1]}' allowfullscreen></iframe>
                  </div>";
            return;
        }
    }

    // Video local
    if (preg_match('/\.(mp4|webm|ogg)$/i', $item_clean)) {

        echo "<div class='video-responsive'>
                <video controls>
                    <source src='".BASE_URL.$item_clean."'>
                </video>
              </div>";
        return;
    }

    // Imagen
    echo "<div class='foto-subnoticia'>
            <img src='".BASE_URL.$item_clean."' alt='Imagen'>
          </div>";
}
