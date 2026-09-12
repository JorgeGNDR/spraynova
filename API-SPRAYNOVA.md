# Conexión local con WordPress y WooCommerce

El script `scripts/spraynova-api.mjs` permite consultar y editar productos de `dev.spraynova.es`, actualizar variaciones y subir imágenes mediante las API oficiales.

## 1. Crear las credenciales

En WordPress entra en **Usuarios → Perfil → Contraseñas de aplicación**, crea una llamada `Codex Spraynova dev` y copia la contraseña una sola vez.

Entra después en **WooCommerce → Ajustes → Avanzado → REST API → Añadir clave**. Usa la descripción `Codex Spraynova dev`, selecciona tu usuario y concede permisos de **Lectura/Escritura**. Copia la Consumer Key y la Consumer Secret.

## 2. Guardarlas solo en el ordenador

Desde la raíz del repositorio:

```bash
cp .env.spraynova.example .env.spraynova
```

Completa `.env.spraynova` con el usuario, la contraseña de aplicación y las claves de WooCommerce. Este archivo está excluido de Git. No pegues las claves en conversaciones ni las incluyas en commits.

## 3. Comprobar la conexión

```bash
node scripts/spraynova-api.mjs status
```

## Comandos disponibles

```bash
# Buscar productos.
node scripts/spraynova-api.mjs products "Dripper"

# Consultar un producto y sus variaciones.
node scripts/spraynova-api.mjs product 123
node scripts/spraynova-api.mjs variations 123

# Actualizar un producto con los campos incluidos en un JSON.
node scripts/spraynova-api.mjs update-product 123 cambios.json

# Actualizar el HEX de una variación.
node scripts/spraynova-api.mjs set-color 123 456 '#D4AF37' amarillos

# Subir una imagen a la biblioteca multimedia.
node scripts/spraynova-api.mjs upload-media ./foto.jpg "Descripción de la imagen"
```

Las credenciales pueden revocarse en cualquier momento desde las mismas pantallas donde se crearon.
