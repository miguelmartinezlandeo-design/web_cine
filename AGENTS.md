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

# Rediseño Obligatorio del Navbar de Filtros en index.php

- **Estructura HTML/CSS Unificada (Flexbox único):**
  - Todo el bloque debe vivir dentro de un ÚNICO contenedor `<div class="navbar-filtros">`.
  - Usar `display: flex; align-items: center; justify-content: space-between; wrap: nowrap; padding: 8px 16px;`.
  - Queda prohibido dividir la cuenta, las categorías y la paginación en dos bloques verticalmente separados.

- **Distribución de 3 elementos en la misma fila:**
  1. **Izquierda:** Botón/Icono Cuenta.
  2. **Centro:** Filtros (`Todas`, `Terror`, etc.) en un sub-contenedor con `overflow-x: auto; display: flex; gap: 8px;`.
  3. **Derecha:** Paginación (`Anterior` / `Siguiente`) en un sub-contenedor compacto (`display: flex; gap: 4px;`), usando botones pequeños de altura estándar (`padding: 6px 12px; font-size: 14px;`).

- **Vista Móvil (`@media (max-width: 768px)`):**
  - Mantener `flex-direction: row;` (o una grilla de máximo 2 filas súper compactas sin fondos dobles).

  # Carril de Categorías con Scroll Horizontal Flotante (index.php)

- **Estructura Fija vs. Desplazable (Flexbox de 3 bloques):**
  - **Extremo Izquierdo (Fijo):** El botón de Usuario/Cuenta permanece estático a la izquierda (`flex-shrink: 0`).
  - **Extremo Derecho (Fijo):** Los botones de paginación ('Anterior' / 'Siguiente') permanecen estáticos a la derecha (`flex-shrink: 0`).
  - **Centro (Carril Desplazable):** El contenedor de las categorías (`Todas`, `Terror`, `Comedia`, `Ficción`, etc.) debe tener `flex: 1`, `overflow-x: auto` y `white-space: nowrap`.

- **Experiencia Táctil y Cursor:**
  - Ocultar la barra de desplazamiento visual para un acabado estético (`::-webkit-scrollbar { display: none; }` y `scrollbar-width: none;`).
  - Activar el desplazamiento nativo por arrastre táctil con el dedo en móviles y mediante la rueda/cursor del ratón en escritorio (`-webkit-overflow-scrolling: touch;`).

## Implementación del Carril de Categorías (aplicado)

- **Contenedor único (`.filtro-botones`, que es el mismo `<nav>` que `.nav-filtros`):**
  - `flex: 1` + `min-width: 0` para que sea el único bloque que cede espacio frente a Cuenta y Paginación.
  - `overflow-x: auto` + `white-space: nowrap` en el **contenedor** (no solo en cada `<a>`), de modo que las categorías nunca se parten en dos líneas.
  - Barra de scroll oculta: `scrollbar-width: none` + `::-webkit-scrollbar { display: none; }`.
  - `-webkit-overflow-scrolling: touch;` va en la **regla base**, no dentro de `@media (max-width: 768px)`, para que el arrastre con el dedo sea fluido en todo momento y no dependa del breakpoint.

- **Extremos fijos:**
  - `.nav-cuenta` y `.nav-paginacion` llevan `flex: 0 0 auto` (equivalente a `flex-shrink: 0`): permanecen estáticos mientras el carril central se desplaza.

- **Rueda del ratón en escritorio (requiere JS):**
  - `overflow-x: auto` **no** reacciona a la rueda vertical: sin esto, al pasar el cursor por el carril la rueda seguía bajando la página.
  - Se resuelve con un `<script>` inline en `index.php` que traduce `deltaY` a `scrollLeft` sobre `.nav-filtros`, con listener `{ passive: false }` para poder llamar a `preventDefault()`.
  - Condiciones para no interceptar: el carril no tiene contenido desplazable, ya hay `deltaX` (trackpad), o se está en un borde desplazándose hacia fuera. En los bordes se devuelve el control a la página para no atrapar al usuario.
  - **Ubicación:** inline en `index.php`, acotado a `.nav-filtros`. **No** se añade a `script/script.php` porque es compartido por todas las páginas y en la portada ya falla (`document.getElementById("form-comentario")` devuelve `null`), lo que cortaría la ejecución del resto del script. `index.php` tampoco carga `js/core.js`.

- **Nota de aislamiento:** todo lo anterior vive en `estilo/index-modern.css` (que solo carga `index.php`) bajo el scope `.portada`, salvo el `<script>` del carril, que es el único punto de JS añadido y también vive exclusivamente en `index.php`. Ninguna página interna recibe estos cambios.