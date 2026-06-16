=== Spray Nova ===

Tema WordPress + WooCommerce basado en el diseño original de Spray Nova.

== Instalación ==

1. En WordPress ve a Apariencia > Temas > Añadir tema > Subir tema.
2. Selecciona spray-nova-theme.zip, instala y activa.
3. Instala y activa WooCommerce.
4. Completa el asistente de WooCommerce para crear Tienda, Carrito, Finalizar compra y Mi cuenta.
5. Ve a Ajustes > Enlaces permanentes y selecciona "Nombre de la entrada".

== Categorías y filtros ==

Crea estas categorías de producto con estos slugs exactos:

* Sprays: sprays
* Rotuladores: rotuladores
* Ceras: ceras

Los filtros de la portada utilizan esos slugs. Marca productos como "Destacados" para controlar los que aparecen en la sección Productos destacados. Si no hay destacados, se muestran los productos más recientes.

== Menús ==

En Apariencia > Menús asigna:

* Menú principal
* Menú del pie

Si no asignas menús, el tema muestra enlaces predeterminados.

== Personalización ==

En Apariencia > Personalizar > Spray Nova puedes editar:

* Barra superior de envío.
* Textos de portada.
* Historia de marca.
* Enlaces de Instagram y TikTok.

En Apariencia > Personalizar > Identidad del sitio puedes sustituir el isotipo por otro logo.

== Imágenes de producto ==

Para mantener el acabado del diseño, se recomiendan imágenes cuadradas o verticales, con el producto centrado y fondo transparente o claro. Tamaño recomendado: 1200 x 1200 px.

== WooCommerce ==

El tema utiliza las funciones nativas de WooCommerce para:

* Productos simples y variables.
* Precios, ofertas y cupones.
* Stock y SKU.
* Carrito AJAX.
* Checkout y pasarelas de pago.
* Zonas y tarifas de envío.
* Cuenta del cliente y pedidos.

== Sprays con carta de colores ==

Para latas de spray, usa productos variables:

* NBQ 800ml
* DOPE Classic 400ml

Ambos deben estar dentro de la categoría de producto "Sprays" con slug "sprays". En esos productos el tema sustituye el selector estándar de variaciones por una carta visual de colores con búsqueda, filtros por familia y selector de cantidades.

Configuración recomendada:

1. Crea un atributo global llamado "Color".
2. Añade un término por color real.
3. Crea una variación por color.
4. En cada variación rellena precio y SKU.
5. Opcionalmente rellena "Color visual HEX", "Código de color" y "Familia de color".

Si todavía no controlas stock, no actives "Gestionar inventario" en las variaciones. WooCommerce aceptará pedidos y podrás revisar cada pedido antes de prepararlo y enviarlo.

Si más adelante quieres controlar stock, activa "Gestionar inventario" por variación. Cada color descontará sus unidades de forma independiente.

Version: 1.1.0
