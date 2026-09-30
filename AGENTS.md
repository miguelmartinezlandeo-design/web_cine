- **Trabajo en Ramas:** Nunca hagas commit ni pushes directamente en la rama principal (`main` / `master`).
- **Rama Actual:** Antes de realizar cualquier cambio en el código o front-end, asegúrate de estar en una rama secundaria como `diseno-moderno` o crea una nueva con `git checkout -b <nombre-rama>`.
- **Commits:** Cuando completes una mejora, realiza un commit con un mensaje claro que describa los cambios visuales o de estilos.

# Objetivo de Front-end

- **Diseño:** Rediseñar la interfaz de `index.php` para que sea moderna, responsive y limpia.
- **Reglas:** 
  - Usar componentes visuales estructurados (tarjetas, sombras suaves, buena tipografía).
  - No alterar variables PHP, loops ni la conexión a MySQL (`$conn`,  etc.).
  - Guardar todos los avances en una rama secundaria de Git.

  # Ajustes Específicos de Cabecera (Header / Logo)

- **Regla exclusiva para `index.php`:**
  - La cabecera superior en `index.php` debe alinearse perfectamente con el ancho del contenedor principal (el contenedor de botones y contenido).
  - No debe verse como un banner rectangular rígido o encajonado. Debe ser transparente o integrarse orgánicamente con el fondo claro del sitio (`#f8f9fa` / blanco).
  - El logo/banner debe ser totalmente responsive (`width: 100%`, `max-width: 100%`, `height: auto`, `object-fit: contain`) tanto en versión de escritorio como en dispositivos móviles.

- **Respeto a páginas internas / publicaciones:**
  - Esta modificación de dimensiones de cabecera **solo se debe aplicar a `index.php`**.
  - En las páginas internas de publicaciones (detalles, noticias, etc.), se debe respetar el tamaño y proporciones originales del logo sin alterarlo.
  # Correcciones de Cabecera, Menú y Paginación en index.php

1. **Paginación en Categorías (Anterior / Siguiente):**
   - Asegura que los botones 'Anterior' y 'Siguiente' funcionen correctamente al filtrar por categorías ('Terror', 'Ficción', 'Comedia', etc.), conservando los parámetros `sigue` y `menubar` en la URL para no perder el filtro activo.

2. **Logo y Menú Hamburguesa:**
   - Aumenta ligeramente el tamaño del logo en la cabecera de `index.php`.
   - Ajusta los márgenes y alineación (`display: flex; align-items: center; gap: 10px;`) para que el logo **nunca monte ni tape** el botón del menú hamburguesa. El menú debe permanecer siempre accesible y visible al lado del logo.

3. **Despliegue del Menú Hamburguesa (Overlay Flotante):**
   - El menú desplegable **no debe empujar el contenido de la página hacia abajo** cuando se abre.
   - Aplica un posicionamiento flotante (`position: absolute;` o `position: fixed; z-index: 1000;`).
   - El fondo del menú desplegable debe tener un tono semi-transparente (por ejemplo: `background: rgba(255, 255, 255, 0.95);` o con `backdrop-filter: blur(8px);`) para flotar sobre las publicaciones sin desplazar el layout.

   # Rediseño Moderno del Menú Desplegable (Glassmorphism / Modo Oscuro)

# Estándar Visual del Menú Desplegable (index.php)

- **Forma y Estructura:**
  - El menú desplegable en `index.php` debe ser un **panel flotante** con márgenes (`top/right/bottom: 12px`) y bordes redondeados en sus 4 esquinas (`border-radius: 16px`).
  - No debe ser un cajón pegado al borde del viewport, sino una tarjeta flotante estilizada sobre el contenido.

- **Estilo Glassmorphism (Modo Oscuro):**
  - Fondo semi-transparente oscuro: `background: rgba(18, 18, 20, 0.85)` con desenfoque `backdrop-filter: blur(12px)`[cite: 6].
  - Borde sutil brillante perimetral de 1px (`border: 1px solid rgba(255, 255, 255, 0.1)`)[cite: 6].

- **Tipografía y Colores de Contraste:**
  - Texto e ítems en color claro legibles sobre el fondo oscuro[cite: 6].
  - El botón/enlace de cerrar sesión (`.link-salir`) debe usar el color de alto contraste `#ff8a80` para garantizar perfecta legibilidad sobre el fondo oscuro[cite: 6].

- **Estados Interactivos (hover / focus):**
  - Los enlaces del menú deben marked con una **pastilla sutil** en `hover` y `focus-visible`: fondo `rgba(255, 255, 255, 0.1)` y `border-radius: 8px`. Mantiene la legibilidad sin recurrir a cambios de color agresivos sobre el fondo oscuro.
  - `.link-salir` mantiene su propio tono en estado activo (`background: rgba(255, 138, 128, 0.14)` + `color: #ffb4ac`) para no perder su semántica de salida.

- **Botón Hamburguesa (estado abierto):**
  - Cuando el menú desplegable está abierto, el botón hamburguesa debe mostrar un **anillo de contraste**: `box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.14)`.
  - Se aplica con `:has()` sobre `.wrapper` (`.portada .wrapper:has(.menu-movil.activo) .hamburger`) para no modificar el JS compartido de `app/vistas/cabecera.php`. En navegadores sin soporte de `:has()` el menú sigue funcionando, solo sin anillo.

- **Alcance:**
  - Todas estas reglas del menú oscuro flotante se aplican **únicamente dentro de `.portada` (`index.php`)**[cite: 6]. Las páginas internas mantendrán su diseño original[cite: 6].

  # Rediseño del Bloque de Filtros y Paginación (index.php)

- **Unificación Visual:**
  - Consolidar la barra superior (cuenta/paginación) y la barra inferior de categorías en un **único contenedor moderno**.
  - Eliminar los grandes espacios en blanco intermedios.

- **Estructura Interna:**
  - **Filtros de Categorías:** Centrados, con estilo de píldoras sutiles (`border-radius: 20px`), destacando la categoría activa con fondo oscuro/contraste.
  - **Paginación (Anterior / Siguiente):** Agrupar los botones de paginación a la derecha en un grupo compacto (`display: flex; gap: 8px`), reduciendo la altura excesiva del botón 'Siguiente'.
  - **Botón Cuenta:** Mantener el acceso a cuenta en el extremo izquierdo alineado verticalmente al centro.

- **Comportamiento en Móvil:**
  - Las categorías deben permitir desplazamiento horizontal suave (`overflow-x: auto`) sin romper la pantalla.

- **Ajustes Exclusivos del Móvil de `index.php` (no aplicar en escritorio ni en otras páginas):**
  - Los dos ajustes siguientes rigen **únicamente** en la vista móvil de la portada (`@media (max-width: 768px)` bajo `.portada`). En escritorio se conserva el diseño actual.
  - **Píldoras de categoría en móvil:** `border-radius: 20px` (radio recortado), sustituyendo la píldora completa (`999px`) que se usa en escritorio.
  - **Grupo de paginación en móvil:** `display: flex; align-items: center; gap: 8px;` sobre `.superior-der`, para mantener el grupo compacto a la derecha.
  - **Motivo:** en pantallas estrechas la píldora completa de escritorio ocupa demasiado ancho y deja fuera categorías; el radio de 20px y el `gap: 8px` densifican el bloque sin romper el carril con scroll horizontal.
  - **Aislamiento:** todas las reglas viven en `estilo/index-modern.css`, que solo carga `index.php`, y dentro del scope `.portada`. Ninguna página interna (detalles, noticias, usuario, registro, admin) recibe estos cambios.