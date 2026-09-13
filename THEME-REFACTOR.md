# Spraynova 1.4.0 — revisión del tema

## Organización

- `functions.php`: arranque del tema, recursos, navegación y contacto.
- `inc/home.php`: valores validados del Personalizador para la portada.
- `inc/woocommerce.php`: integración de WooCommerce, metadatos, cartas de color y carrito.
- `template-parts/home/`: productos y categorías, renderizados en el orden seleccionado sin buffers HTML.
- `assets/css/home.css`: única fuente de reglas de portada, con base móvil y cambios en 721 y 1001 px.
- `assets/js/variations.js`: selección de variaciones y sincronización con WooCommerce; se carga solo en producto.

## Eliminado o sustituido

- Ilustración antigua de latas en CSS y tarjetas de producto ficticias.
- Reglas de portada duplicadas en media queries y estilos PHP.
- Confirmación falsa de suscripción; la newsletter necesita un shortcode de formulario conectado.
- Dependencia de `overflow-x:hidden` global para esconder problemas de ancho.
- Referencias a opciones de select que WooCommerce reemplaza y uso de listbox sin interacción de teclado.

Los nombres de los ajustes existentes se conservan. Los botones de variantes son botones nativos con `aria-pressed`, foco de teclado y opciones no disponibles desactivadas.

## Verificación

`tests/responsive.cjs` usa Playwright y consulta HTML público sin modificar la tienda. Intercepta los recursos del tema y carga los archivos locales. Comprueba 320, 375, 390, 768, 1024, 1440 y 1920 px, títulos, rejillas, formularios y ancho total. Prueba los seis colores del Dripper y su etiqueta de selección.

Las rutas de carrito y checkout se visitan con carrito vacío: no constituye una prueba de pago, portes ni checkout con productos. No se envían pedidos.

Además del análisis sintáctico de PHP, se ejecutan las plantillas en el WordPress aislado descrito abajo. La integración con las extensiones y configuración de la tienda debe revisarse en staging antes de desplegar en producción.

Para ejecutar: instalar `playwright` como dependencia de desarrollo y lanzar `node tests/responsive.cjs` desde la raíz. El script escribe resultados y capturas en `/tmp`.

## Ejecución en WordPress

La versión se ha instalado en WordPress Playground con PHP 8.3 y WooCommerce. Se ha comprobado el renderizado de las plantillas extraídas y el guardado del Personalizador (orden de secciones, proporción del vídeo y columnas de productos).

La prueba local añade una variación ficticia al carrito y comprueba que conserva el color seleccionado. Incluye selector por teclado, limpieza, menú móvil, búsqueda, carrito lateral y revisión de carrito/checkout con producto. Contacto y páginas legales se revisan también en los siete anchos. No se procesan pedidos, pagos ni mensajes.

Se corrigieron además los límites de ancho de cabecera, pie, contacto y carrito de bloques. La selección usa un único estado `aria-pressed` con fondo violeta, sin check. El select nativo se oculta únicamente cuando se ha construido su alternativa.

Instrucciones y límites: `tests/README.md`.
