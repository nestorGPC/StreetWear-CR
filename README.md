# StreetWear CR

StreetWear CR es una tienda virtual desarrollada como proyecto final del curso
Tecnologías y Sistemas Web II.

La aplicación está orientada a la venta de ropa, tenis, gorras y accesorios,
permitiendo administrar productos, categorías, usuarios, carrito de compras,
pedidos y pagos.

## Tecnologías utilizadas

- PHP 8.2
- Laravel 12
- SQLite
- Filament 5
- Spatie Laravel Permission
- Filament Shield
- DomPDF
- Bootstrap 5
- JavaScript
- Vite
- Git
- GitHub

## Requisitos

- PHP 8.2 o superior con extensiones `pdo_sqlite`, `mbstring`, `openssl`.
- Composer.
- Node.js y npm.
- Git.

## Instalación desde cero

```bash
git clone https://github.com/nestorGPC/StreetWear-CR.git
cd "StreetWear CR"
```

### 1. Instalar dependencias

```bash
composer install
npm install
```

### 2. Configurar el entorno

```bash
cp .env.example .env
```

En Windows:

```cmd
copy .env.example .env
```

Configurar la base de datos en `.env`:

```env
DB_CONNECTION=sqlite
```

Crear el archivo de la base de datos:

```bash
php -r "touch database/database.sqlite"
```

En Windows PowerShell:

```powershell
New-Item -ItemType File -Path database\database.sqlite
```

Generar la clave de la aplicación:

```bash
php artisan key:generate
```

### 3. Migrar y sembrar la base de datos

```bash
php artisan migrate
php artisan db:seed
```

### 4. Compilar los recursos

```bash
npm run build
```

### 5. Enlazar el almacenamiento público

```bash
php artisan storage:link
```

### 6. Iniciar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`.

## Documentación

- `docs/MANUAL.md` — manual de usuario (cliente y administrador).
- `docs/TECNICA.md` — documentación técnica (arquitectura, modelos, rutas, seguridad, decisiones).
- `docs/diagrama-uso-compra.md` — diagrama del proceso de compra.
- `docs/DEPLOYMENT.md` — guía de despliegue y HTTPS.
- `docs/DEFENSA.md` — preparación de la defensa oral.

## Usuarios de demostración

El seeder `UserSeeder` crea los siguientes usuarios:

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@streetwearcr.test` | `Admin12345` |
| Cliente | `cliente@streetwearcr.test` | `Cliente12345` |

## Flujo funcional

### Cliente

- Registrarse e iniciar sesión.
- Consultar el catálogo.
- Buscar productos por nombre.
- Filtrar por categoría y rango de precio.
- Ver el detalle de cada producto.
- Ver productos visitados recientemente mediante cookies.
- Agregar productos al carrito.
- Cambiar cantidades.
- Ver subtotal, impuestos, envío y total.
- Realizar el checkout ingresando una dirección.
- Seleccionar método de pago (tarjeta o PayPal, modo demo).
- Obtener número de seguimiento.
- Consultar el historial de pedidos.
- Ver el detalle de cada pedido propio.
- Editar el perfil.

### Administrador

- Panel en `/admin`.
- Administrar categorías.
- Administrar productos y stock.
- Administrar usuarios.
- Consultar pedidos y cambiar sus estados.
- Consultar pagos.
- Administrar roles con Filament Shield.
- Generar reportes PDF de pedidos, ventas y productos.

## Reportes PDF

Los reportes se generan en `/reportes` (solo super_admin):

- `reporte-pedidos.pdf`: pedidos filtrables por rango de fecha, estado y cliente.
- `reporte-ventas.pdf`: total vendido, ventas por mes y por cliente.
- `reporte-productos.pdf`: cantidad vendida y total generado por producto.

## Pruebas automáticas

```bash
php artisan test
```

La suite cubre autenticación, catálogo, carrito, checkout, pedidos, pagos,
perfil, cookies de productos recientes, idempotencia del checkout, límite de
intentos de inicio de sesión y reportes.

## Seguridad

- Autenticación de sesión con contraseñas hasheadas (bcrypt).
- Tokens CSRF en todos los formularios.
- Validación de entradas en los controladores.
- Rate limit en el inicio de sesión (5 intentos por minuto).
- Token de idempotencia en el checkout para evitar doble pedido.
- Los pedidos solo son visibles por su propietario.
- Los reportes solo están disponibles para el rol `super_admin`.
- Los estados de pedidos y pagos están restringidos a una lista fija.
- Los productos inactivos no se pueden comprar ni agregar al carrito.
- Las contraseñas y datos de tarjeta no se almacenan en la base de datos.
- El pago se encuentra en modo demostración.

## Estructura del proyecto

```text
app/
  Filament/          Recursos y esquemas del panel administrativo
  Http/Controllers/  Controladores de la aplicación
  Models/            Modelos Eloquent
database/
  factories/         Factories para pruebas
  migrations/        Migraciones de la base de datos
  seeders/           Datos de demostración
resources/views/     Vistas Blade
routes/web.php       Definición de rutas
tests/Feature/       Pruebas automáticas
```

## Panel administrativo

El panel administrativo se encuentra en:

```text
/admin
```

## Notas para el equipo

- La rama `main` debe mantenerse estable.
- Cada integrante trabaja en su propia rama y sube un Pull Request.
- Antes de cada commit ejecutar `php artisan test` y revisar `git status`.
- No subir `.env`, claves API, tokens ni credenciales reales.
- No borrar migraciones que ya fueron ejecutadas.
