<?php

/*
|--------------------------------------------------------------------------
| Lista base de palabras prohibidas
|--------------------------------------------------------------------------
*/
function obtenerListaProhibidas(){
    return [
        "mierda",
        "puta",
        "idiota",
        "imbecil"
    ];
}

/*
|--------------------------------------------------------------------------
| Equivalencias tipo leetspeak
|--------------------------------------------------------------------------
| a = a4@
| i = i1!
| o = o0
| e = e3
| s = s5$
| t = t7
| u = uüv
|--------------------------------------------------------------------------
*/
function mapaEquivalencias(){

    return [
        'a' => '[a4@áàä]',
        'e' => '[e3éèë]',
        'i' => '[i1!íìï]',
        'o' => '[o0óòö]',
        'u' => '[uüvúù]',
        's' => '[s5$]',
        't' => '[t7]',
        'c' => '[cç]',
        'b' => '[b8]',
        'g' => '[g9]'
    ];
}

/*
|--------------------------------------------------------------------------
| Generar patrón flexible avanzado
|--------------------------------------------------------------------------
*/
function generarPatronAvanzado($palabra){

    $equivalencias = mapaEquivalencias();
    $letras = str_split($palabra);
    $patron = "";

    foreach($letras as $letra){

        $letra = strtolower($letra);

        if(isset($equivalencias[$letra])){
            $patron .= $equivalencias[$letra];
        } else {
            $patron .= preg_quote($letra,'/');
        }

        // Permitir separación con símbolos
        $patron .= '[\W_]*';
    }

    return $patron;
}

/*
|--------------------------------------------------------------------------
| Censura visual elegante
|--------------------------------------------------------------------------
*/
function censurarPalabra($texto){
    return substr($texto, 0, 1) . str_repeat('*', strlen($texto)-1);
}

/*
|--------------------------------------------------------------------------
| Función principal
|--------------------------------------------------------------------------
*/
function filtrarPalabras($texto){

    $lista = obtenerListaProhibidas();

    foreach($lista as $palabra){

        $patron = generarPatronAvanzado($palabra);

        $regex = "/\b".$patron."\b/i";

        $texto = preg_replace_callback(
            $regex,
            function($match){
                return censurarPalabra($match[0]);
            },
            $texto
        );
    }

    return $texto;
}