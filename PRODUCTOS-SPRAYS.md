# Spray Nova: catálogo de sprays

## Idea base

La tienda debe mostrar pocas líneas claras y dejar que el cliente elija colores dentro de cada línea.

No conviene crear un producto por color. Lo correcto es:

- Un producto por gama.
- Una variación por color.
- Una carta visual de colores en la ficha del producto.

El tema Spray Nova ya hace esto automáticamente con cualquier **producto variable** que esté en la categoría WooCommerce `Sprays` con slug `sprays`.

## Productos principales

Estos son los productos que deberías crear en WooCommerce:

| Producto | Marca | Formato | Colores | Presión | Acabado | Tipo |
| --- | --- | --- | ---: | --- | --- | --- |
| DOPE Classic 400ml | DOPE | 400ml | 62 | Media | Mate | Variable |
| NBQ Eternal 400ml | NBQ | 400ml | 4 | Alta | Nitro | Variable |
| NBQ Fast | NBQ | 400ml | 46 | Alta | Mate | Variable |
| Plata o Plomo 500ml | NBQ | 500ml | 1 o varios | Alta | Superbrillante | Simple o variable |
| NBQ Eternal 800 | NBQ | 600ml reales / lata 800 | 15 | Alta | Nitro | Variable |
| DOPE Action 2.0 | DOPE | 600ml | 10 | Alta | Mate | Variable |

## Cómo lo verá el cliente

En la página de `Sprays`, el cliente verá tarjetas por gama:

- DOPE Classic 400ml
- NBQ Eternal 400ml
- NBQ Fast
- Plata o Plomo 500ml
- NBQ Eternal 800
- DOPE Action 2.0

Al entrar en una gama, verá:

- Foto principal de la lata.
- Presión, acabado y formato.
- Buscador por nombre o código.
- Filtros por familia de color.
- Swatches de color.
- Cantidad por color.
- Botón único: `Añadir selección al carrito`.

Esto escala bien: DOPE Classic con 62 colores y NBQ Eternal 400 con 4 colores usan la misma interfaz.

## Atributos recomendados

El único atributo que debe usarse como variación en los sprays es:

- `Color`

`Marca`, `Formato`, `Presión` y `Acabado` no deben ser opciones elegibles en la ficha del spray. En los CSV actuales van como metadatos internos para que el tema los muestre como información fija.

## Campos de cada variación

Cada color debe ser una variación del producto.

Para cada variación rellena:

- Color
- Precio
- SKU
- Peso
- Imagen si la tienes
- Opcional: `Color visual HEX`
- Opcional: `Código de color`
- Opcional: `Familia de color`

El tema usa esos campos opcionales para pintar la carta de colores.

## Cómo se muestran los colores en la web

El selector visual toma los colores de cada variación:

- `Color visual HEX`: pinta el círculo de color.
- `Código de color`: aparece debajo del nombre.
- `Familia de color`: decide el filtro: negros, blancos, rojos, azules, etc.
- El atributo `Color`: da el nombre visible de la variación.

Ejemplo:

- Variación: `D031 Bomba Red`
- `Color visual HEX`: `#DA1715`
- `Código de color`: `D031`
- `Familia de color`: `rojos`

En la ficha del producto el cliente verá el swatch rojo, el nombre `D031 Bomba Red`, su precio y los botones de cantidad.

## DOPE Classic 400ml

Ya hay dos CSV preparados:

- `data/dope-classic-colors.csv`: carta limpia de colores, útil como referencia.
- `data/woocommerce-dope-classic-400ml-import.csv`: importación WooCommerce con el producto `DOPE Classic 400ml` y sus 62 variaciones.

El CSV de WooCommerce usa precio `3.95` en todas las variaciones de 400ml.

## NBQ Eternal 400ml

Este producto tiene pocos colores, así que también debe ser variable, pero será muy rápido de gestionar.

Variaciones iniciales:

- Negro
- Blanco
- Plata
- Oro

He dejado preparado:

- `data/nbq-eternal-400-colors.csv`
- `data/woocommerce-nbq-eternal-400-import.csv`

Los HEX son aproximados para visualizar la web. Si NBQ tiene códigos oficiales, los sustituimos después.

## Resto de gamas

Para estas gamas falta la carta exacta de colores:

- NBQ Fast: 46 colores.
- NBQ Eternal 800: 15 colores.
- DOPE Action 2.0: 10 colores.
- Plata o Plomo: confirmar si es solo plata/plomo o más variantes.

He dejado un archivo maestro con la estructura:

- `data/spray-product-lines.csv`

Cuando tengas las cartas de color, se añaden igual que DOPE Classic.

## Stock

Como ahora mismo no llevas stock exacto, lo más cómodo es:

- No activar `Gestionar inventario` en las variaciones.
- Dejar estado de inventario como `Hay existencias`.
- Revisar manualmente cada pedido antes de prepararlo.

Esto permite vender sin bloquearte por un inventario que todavía no está medido.

Cuando empieces a controlar stock:

- Activa `Gestionar inventario` por variación.
- Cada color tendrá su cantidad independiente.
- WooCommerce descontará stock al recibir pedidos.

## Revisión de pedidos

Sí, puedes revisar cada pedido antes de prepararlo y enviarlo.

Con pagos por tarjeta, el pedido puede quedar pagado automáticamente y tú lo revisas antes de marcarlo como completado o enviarlo.

Si quieres revisar antes de aceptar dinero, usa temporalmente métodos manuales como transferencia bancaria. El pedido queda pendiente/en espera y tú decides cómo gestionarlo.

Para una tienda real con tarjeta, mi recomendación es:

- Aceptar pago automático.
- Revisar pedido.
- Preparar o contactar al cliente si falta algún color.
- Enviar.

Más adelante, cuando tengas stock fino, WooCommerce evitará vender colores agotados.

## Importación

Para importar un CSV:

1. Ve a `Productos > Todos los productos`.
2. Pulsa `Importar`.
3. Sube el CSV correspondiente.
4. En el mapeo, comprueba que las columnas `Meta: _spray_nova_color_hex`, `Meta: _spray_nova_color_code` y `Meta: _spray_nova_color_family` se importan como metadatos.
5. Ejecuta la importación.
6. Abre el producto y confirma que está en la categoría `Sprays`.
7. Revisa precios, imagen principal, descripción y peso antes de vender.

Importante: si ya habías importado una versión antigua donde `Marca`, `Formato`, `Presión` o `Acabado` salían como desplegables, lo más limpio es borrar esos productos y sus variaciones y volver a importar el CSV corregido. Reimportar encima no siempre elimina atributos antiguos en WooCommerce.

Para que el selector visual aparezca, el producto variable debe estar en la categoría `Sprays` y el slug de esa categoría debe ser exactamente `sprays`.

## Excel maestro

He creado un Excel maestro:

- `spray-nova-catalogo-productos.xlsx`

Incluye:

- Hoja `Gamas` con toda la estructura comercial.
- Hoja `DOPE Classic 400` con 62 colores reales.
- Hoja `NBQ Eternal 400` con 4 colores base.
- Plantillas para `NBQ Fast 400`, `NBQ Eternal 800`, `DOPE Action 2` y `Plata o Plomo 500`.
- Hoja `Ceras` con 47 productos extraídos del proveedor con permiso.
- Hoja `WooCommerce Import` con el formato de importación.

También hay un CSV maestro generado desde ese Excel:

- `data/woocommerce-spray-master-import.csv`

Ese CSV ya contiene los productos/variaciones que tienen colores rellenados.

Ahora incluye:

- 6 productos principales de sprays.
- 68 variaciones de sprays ya rellenas.
- 47 productos simples de ceras.

## Ceras

Las ceras están organizadas como productos simples en la categoría `Ceras`.

Archivos creados:

- `data/ceras-productos.csv`: catálogo limpio con nombre, SKU, precio, imagen y referencia.
- `data/woocommerce-ceras-import.csv`: importador WooCommerce solo para ceras.
- `data/images/ceras/`: copias locales de las 47 imágenes autorizadas.

La importación WooCommerce usa la columna `Images` con copias públicas servidas desde `https://spray-nova.vercel.app/data/images/ceras/`. Es más estable que importar directamente desde la web del proveedor, porque algunos servidores bloquean descargas automáticas desde WooCommerce.

Los precios de ceras son los detectados en la página del proveedor. Revísalos antes de vender si quieres aplicar otro margen.

## Precios actuales

- Sprays de 400ml importados: `3.95`.
- Ceras: precio detectado del proveedor.
- Gamas pendientes sin colores completos: precio vacío hasta confirmar.

La moneda no la decide el CSV. Si ves dólares, cambia WooCommerce a euros en `WooCommerce > Ajustes > General > Opciones de moneda`.

Para regenerar el Excel desde los CSV:

```bash
NODE_PATH=C:\tmp\spray-nova-excel\node_modules node tools\generate-product-workbook.js
```
