<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mi sitio</title>
<link rel="stylesheet" href="<?= BASE_URL ?>estilo/estilo1.css">
<link rel="stylesheet" href="<?= BASE_URL ?>estilo/botonvuelta.css">
</head>

<body>

<div class="wrapper">
<header class="cabecera">
        <img src="<?= BASE_URL ?>imagen/plantilla1.svg" class="cabecera-img">
        <a href="<?=
    defined('EN_ADMIN')
        ? BASE_URL.'usuario/usuarioadmin.php'
        : BASE_URL.'index.php'
?>" class="btn-ver-sitio">
            🌍 Ver sitio
        </a>
                                <?php
                        $archivoActual = basename($_SERVER['PHP_SELF']);
                        if($archivoActual == 'modificar.php' || $archivoActual == 'grabar.php' || $archivoActual == 'subnoticiagregar.php'):
                        ?>
                            <a href="<?= BASE_URL ?>logout/logout.php"
                            class="btn-logout"
                            onclick="return confirm('¿Cerrar sesión?')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                             Cerrar sesión
                            </a>
                        <?php endif; ?>
</header>

