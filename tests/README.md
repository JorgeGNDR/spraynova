# Comprobaciones del tema

Estas pruebas necesitan Node.js y Chromium de Playwright. Ejecutar desde la raíz del repositorio.

## WordPress aislado

Instalar las herramientas fuera del repositorio:

```sh
npm install --prefix /tmp/spraynova-tests @wp-playground/cli@3.1.53 playwright@1.63.0
/tmp/spraynova-tests/node_modules/.bin/playwright install chromium
/tmp/spraynova-tests/node_modules/.bin/wp-playground-cli server \
  --mount "$PWD/spray-nova-theme:/wordpress/wp-content/themes/spray-nova-theme" \
  --blueprint tests/wordpress-blueprint.json --port 9400 --workers 2 --login
```

El blueprint activa el tema, instala WooCommerce y crea un producto ficticio con seis variaciones. No utiliza datos ni credenciales de Spraynova. La instalación temporal tiene las credenciales de desarrollo de Playground (`admin` / `password`); no se debe publicar ni utilizar en producción.

En otra terminal:

```sh
NODE_PATH=/tmp/spraynova-tests/node_modules node tests/wordpress-local.cjs
NODE_PATH=/tmp/spraynova-tests/node_modules node tests/content-local.cjs
```

Las pruebas se conectan exclusivamente a `127.0.0.1:9400`. Añaden una variación al carrito local, prueban menú, búsqueda, teclado, limpieza de selección y guardado de ajustes del Personalizador. Visitan carrito y checkout con producto, contacto y página legal, y revisan siete anchos entre 320 y 1920 px. Guardan capturas y resultados en `/tmp`.

No envían formularios de contacto, pedidos ni pagos. El entorno no reproduce las extensiones de pagos, transportistas, caché ni configuración de Hostinger/Cloudflare de producción.

## Catálogo real, solo lectura

```sh
NODE_PATH=/tmp/spraynova-tests/node_modules node tests/responsive.cjs
```

Consulta seis rutas públicas de Spraynova e intercepta los recursos CSS/JS para probar los archivos locales contra su HTML actual. Prueba selección de colores, sin añadir al carrito ni modificar productos. Este método no ejecuta las plantillas PHP locales; se complementa con la prueba anterior.
