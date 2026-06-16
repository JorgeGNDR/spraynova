# Spray Nova: estructura recomendada para NBQ y DOPE

## Productos

Crea solo estos productos principales:

- `NBQ 800ml`
- `DOPE Classic 400ml`

Ambos deben ser **Producto variable** y estar en la categoría WooCommerce `Sprays` con slug `sprays`.

## Atributos

Crea un atributo global:

- Nombre: `Color`
- Slug recomendado: `color`

Cada color real debe ser un término del atributo `Color`.

Ejemplos:

- `Negro Mate`
- `Blanco`
- `Rojo Vivo`
- `Azul Cielo`
- `Plata`

## Variaciones

Cada color es una variación del producto.

Para cada variación rellena:

- Color
- Precio
- SKU
- Peso
- Imagen si la tienes
- Opcional: `Color visual HEX`
- Opcional: `Código de color`
- Opcional: `Familia de color`

El tema usa esos campos opcionales para pintar la carta de colores. Si no los rellenas, intenta ordenarlo automáticamente por nombre y SKU.

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

## Experiencia del cliente

En la ficha de `DOPE Classic 400ml` o `NBQ 800ml`, el cliente verá:

- Buscador por nombre o código.
- Filtros por familia: negros, blancos, rojos, azules, etc.
- Parrilla de colores.
- Cantidades por color.
- Botón único: `Añadir selección al carrito`.

Así puede comprar 1, 6, 12 o 30 latas combinando colores sin entrar y salir de 60 páginas.
