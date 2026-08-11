# AUDITORÍA TÉCNICA COMPLETA — StreetWear CR

Fecha de auditoría: 10/08/2026
Modo: solo lectura. No se modificó código, base de datos ni dependencias.

---

# 1. Resumen ejecutivo

Estimación de completitud **con evidencia** (no se inventaron porcentajes):

| Área | % | Justificación |
|---|---|---|
| Funcionalidad | 55 % | El núcleo (catálogo, filtros, carrito, auth, checkout, pedidos, admin) existe y funciona, pero faltan historial de pedidos, reportes PDF, pasarela de pago y seeders de pedidos/pagos. |
| Seguridad | 65 % | Buenos fundamentos (CSRF, sesiones, hashing, autorización de pedidos, roles), pero no hay rate-limit de login, validaciones débiles en filtros, no hay manejo de producción/HTTPS y no hay pasarela real. |
| UX | 55 % | UI consistente con Bootstrap, responsive básico correcto, pero los productos del seeder no tienen imagen, la vista "Mis pedidos" está vacía/estática y faltan estados/pulido para demostración. |
| Pruebas | 15 % | Existen 3 pruebas; **2 fallan** hoy (Vite manifest no encontrado) y no cubren registro, login, carrito, checkout, stock, autorización, reportes ni cookies. |
| Documentación | 25 % | README muy corto, sin pasos de instalación, sin credenciales, sin manual, sin diagramas. |
| Preparación para entrega | 40 % | Falta hosting, SSL, build de frontend, ZIP, diagrama de casos de uso, defensa oral y reportes. |

**Conclusión ejecutiva:** el proyecto tiene una base sólida y bien estructurada para una tienda virtual universitaria. Las rutas, modelos y controladores son simples y defendibles. Sin embargo, **no está listo para presentación** porque: (1) las pruebas automáticas fallan, (2) no existe pasarela de pago real, (3) no existen reportes PDF, (4) no existe historial de pedidos, (5) no hay build de assets (el sitio no carga sin `npm run dev`), y (6) el README no permite una instalación limpia.

---

# 2. Stack detectado

Versiones reales obtenidas con `composer show`, `php --version`, `npm -v` y `php artisan about`:

| Tecnología | Versión detectada |
|---|---|
| PHP | 8.2.12 (XAMPP, ZTS, VC2019) |
| Composer | 2.10.2 |
| Laravel Framework | 12.64.0 |
| Filament | 5.7.3 |
| Spatie Laravel Permission | 6.25.0 |
| Filament Shield | 4.3.1 |
| PHPUnit | 11.5.56 |
| Faker | 1.24.1 |
| Node.js | v24.18.0 |
| npm | 11.16.0 |
| Bootstrap | 5.3.8 |
| Vite | 7.0.7 |
| Tailwind CSS | 4.0.0 (instalado como plugin de Vite pero **no se usa**: el CSS importa Bootstrap) |
| SQLite | base en `database/database.sqlite` |
| Mail | log (sin SMTP) |

**NO están instalados:** `dompdf` / `barryvdh/laravel-dompdf` (reportes PDF), `stripe/stripe-php`, `paypal/paypal-checkout-sdk` ni ningún SDK de pasarela de pago. Esto se verificó en `composer.json`, `composer.lock` y `vendor`.

Stack declarado en AGENTS.md/README **no coincide** con lo instalado en dos puntos:
- AGENTS.md dice "DomPDF" y "reportes PDF" → **no existe**. No hay controlador de reportes, ni rutas, ni vistas PDF.
- AGENTS.md dice "Seeders de pedidos y pagos" y "pruebas automáticas" de Darío → no existen `OrderSeeder`/`PaymentSeeder` ni pruebas de checkout/pagos.

---

# 3. Arquitectura actual

Arquitectura Laravel estándar (MVC) sin complejidad innecesaria:

- **Rutas web** en `routes/web.php` (35 rutas). Sin API.
- **Controladores** (5 activos): `ProductController`, `CartController`, `AuthController`, `AccountController`, `CheckoutController`. Controladores pequeños y con una sola responsabilidad clara.
- **Servicio** `app/Services/CartCalculator.php`: cálculos de subtotal, IVA (13 %), envío fijo (₡3000) y total.
- **Modelos** (6): `User`, `Category`, `Product`, `Order`, `OrderItem`, `Payment` con relaciones Eloquent correctas.
- **Políticas** (2): `CategoryPolicy`, `ProductPolicy` generadas por Filament Shield.
- **Filament** panel `admin`: recursos Categorías, Productos, Pedidos, Pagos, Usuarios (schemas/tablas separados en `Schemas/` y `Tables/`).
- **Permisos**: Spatie Permission + Shield. `super_admin` tiene acceso total por interceptación del Gate (`filament-shield.php`), `customer` solo a la tienda. `User::canAccessPanel()` exige rol `super_admin`.
- **Session/cache/queue**: driver `database` (tablas `sessions`, `cache`, `jobs` existen).

**Código muerto detectado:**
- `app/Filament/Resources/Orders/Pages/CreateOrder.php` y `app/Filament/Resources/Payments/Pages/CreatePayment.php` → no están registrados en `getPages()` de sus recursos.
- `resources/views/welcome.blade.php` (82 KB, plantilla por defecto de Laravel, nunca se renderiza).
- 2 imágenes huérfanas en `storage/app/public/products/` (ningún producto las referencia; `products.image` es `NULL` en los 8 registros).
- Producto duplicado en BD: id 4 "Camiseta Overzide Negra" (typo) y id 5 "Camiseta Oversize Negra" (del seeder).

**Observaciones de diseño (correctas):**
- `CheckoutController` reutiliza `CartCalculator`; el checkout vuelve a leer el precio desde la BD para los `OrderItem` y vuelve a validar stock dentro de una transacción.
- Validación inline en controladores (sin FormRequests). Aceptable para el nivel del proyecto, pero repetida en varios puntos.

---

# 4. Funcionalidades implementadas

| Funcionalidad | Estado | Evidencia | Archivos | Observación |
|---|---|---|---|---|
| Catálogo de productos | CUMPLE | Vista `/productos` con tarjetas | `ProductController.php`, `products/index.blade.php` | Solo muestra `active = true`. |
| Búsqueda por nombre | CUMPLE | `where('name', 'like', ...)` | `ProductController.php:18` | Sin validación del input (riesgo bajo). |
| Filtro por categoría | CUMPLE | `where('category_id', ...)` | `ProductController.php:27` | El select viene de la BD. |
| Filtro por precio | CUMPLE | `min_price` / `max_price` | `ProductController.php:35-50` | Sin validación numérica previa. |
| Detalle de producto | CUMPLE | `/productos/{id}` | `products/show.blade.php` | |
| Productos vistos recientemente | CUMPLE | Cookie `recent_products` | `ProductController.php:71-138` | Ver Fase 9. |
| Registro de usuarios | CUMPLE | Validación, hash, rol customer, login auto | `AuthController.php:18-46` | |
| Inicio de sesión | CUMPLE | `Auth::attempt` + `remember` + regeneración de sesión | `AuthController.php:53-76` | Sin límite de intentos. |
| Cierre de sesión | CUMPLE | Invalida y regenera token | `AuthController.php:78-88` | |
| Perfil / edición de datos | CUMPLE | nombre + email con `Rule::unique` | `AccountController.php:20-44` | No hay cambio de contraseña. |
| Carrito agregar | CUMPLE | Sesión `cart` + control de stock | `CartController.php:35-70` | |
| Carrito actualizar cantidades | CUMPLE | Validación min:1 max:stock | `CartController.php:72-94` | |
| Carrito eliminar | CUMPLE | `unset` de sesión | `CartController.php:96-109` | |
| Subtotal / IVA / envío / total | CUMPLE | `CartCalculator` | `CartCalculator.php` | IVA 13 %, envío fijo ₡3000. |
| Checkout (dirección + método) | CUMPLE | `/checkout` GET/POST con validación | `CheckoutController.php` | En modo demostración. |
| Creación de Order / OrderItem / Payment | CUMPLE | Transacción `DB::transaction` | `CheckoutController.php:78-156` | |
| Reducción de stock | CUMPLE | `Product::decrement` dentro de transacción | `CheckoutController.php:135-138` | Sin bloqueo/lock (riesgo de doble envío). |
| Número de seguimiento | CUMPLE | `SWCR-YYYYMMDD-XXXXXX` único | `CheckoutController.php:190-207` | |
| Vaciar carrito tras pedido | CUMPLE | `session()->forget('cart')` | `CheckoutController.php:165` | |
| Confirmación de pedido | CUMPLE | `/checkout/confirmacion/{order}` con verificación de dueño | `checkout/success.blade.php` | `abort(403)` si no es el dueño. |
| Panel Filament (categorías, productos, usuarios, pedidos, pagos) | CUMPLE | Recursos Filament 5 | `app/Filament/Resources/**` | |
| Cambiar estado de pedido | CUMPLE | Select de estados en `OrderForm` | `OrderForm.php`, `OrdersTable.php` | Estados: pending/processing/shipped/delivered/cancelled. |
| Cambiar estado de pago | CUMPLE | Select en `PaymentForm` | `PaymentForm.php` | Manual, no automático. |
| Cookies recientes | CUMPLE | Ver Fase 9 | `ProductController.php` | |

---

# 5. Funcionalidades incompletas

1. **Historial de pedidos del cliente** — `account/orders.blade.php` es una vista estática ("Todavía no tienes pedidos registrados") y `AccountController::orders()` no consulta la BD. Un cliente con pedidos verá siempre el mensaje de vacío. Falta consultar `$request->user()->orders()->with('items','payment')` y renderizar la tabla.
2. **Detalle de pedido desde el historial** — el único detalle visible es la pantalla de éxito inmediatamente después de comprar. No hay ruta de detalle por pedido en "Mi cuenta".
3. **Pagos** — se registra un `Payment` con `status=pending` y `transaction_id=null`. No existe cobro real ni simulado (ver Fase 5).
4. **Imágenes de productos** — el seeder pone `image => null`, por lo que una instalación limpia muestra "Sin imagen" en todas las tarjetas. La carga por Filament funciona y hay 2 archivos de prueba, pero no hay imagen en ningún producto.
5. **Gestión de roles en el panel** — Shield está instalado y generó 24 permisos, pero el recurso `shield/roles` **no está registrado** en el panel (no existe la ruta `/admin/shield/roles`). Roles solo se asignan por seeder/código.
6. **README** — incompleto (no hay instalación, credenciales, ni pruebas).
7. **Estado "paid" automático** — el pago nunca pasa a `paid` salvo edición manual en Filament.

---

# 6. Funcionalidades no implementadas

1. **Pasarela de pago (tarjeta y/o PayPal)** — requisito 28/29 de la rúbrica. Solo hay selección visual del método en el formulario de checkout y un registro local `pending`. No hay Stripe test, PayPal Sandbox, SDK, redirect seguro, webhook, callback, `transaction_id` real ni actualización automática de estados.
2. **Reportes PDF de ventas por mes** — requisito 35. No existe controlador, ruta, vista ni librería PDF.
3. **Reportes PDF de ventas por cliente** — requisito 36. Ídem.
4. **Seeders de pedidos y pagos** — no existen `OrderSeeder`/`PaymentSeeder`; la BD actual tiene 0 pedidos, lo que impide demostrar historial, reportes y administración de pedidos con datos.
5. **HTTPS/SSL** — no hay certificado configurado (entorno local XAMPP).
6. **Hosting** — no hay despliegue a producción.
7. **Diagrama de caso de uso del proceso de compra** — no existe ningún documento.
8. **Documentación técnica / manual de usuario** — no existe.
9. **Pruebas de pago/pasarela** — no existen.

---

# 7. Errores detectados

| ID | Severidad | Archivo | Problema | Impacto | Solución recomendada |
|---|---|---|---|---|---|
| E-01 | ALTO | `public/build` (falta) | No existe `public/build/manifest.json`. `@vite()` lanza excepción si no corre `npm run dev`. | El sitio no carga sin el servidor Vite; **2 de 3 pruebas fallan**. | `npm run build` y documentarlo en README. Añadir al script `composer setup`. |
| E-02 | ALTO | `AccountController.php:46` / `account/orders.blade.php` | Historial de pedidos no consulta la BD; vista estática. | Un cliente no puede consultar sus pedidos (requisito 7 y 31 de la rúbrica). | Consultar `user->orders()` y listar con detalle. |
| E-03 | ALTO | `CheckoutController.php:72-75` | El subtotal/IVA/total del pedido se calculan con **precios de sesión**, pero los `OrderItem` se recalculan con **precio actual de la BD**. Si el admin cambia el precio entre el carrito y el checkout, `order.subtotal` ≠ suma de `order_items.subtotal` y `payment.amount` no coincide. | Inconsistencia contable en factura. | Calcular subtotal/total siempre desde la BD en el `store`, o rechazar si el precio cambió. |
| E-04 | MEDIO | `CartController.php:72-94` | `cart.update` actualiza la cantidad con `$request->quantity` sin verificar que el producto siga activo o exista. | Cantidad válida para producto desactivado. | Re-validar producto activo y stock. |
| E-05 | MEDIO | `CheckoutController.php:78-156` | Sin `lockForUpdate` al decrementar stock. Dos pestañas/requests simultáneos pueden superar el stock (race condition). | Venta sobre stock. | `DB::transaction` + `lockForUpdate` o re-verificación con condición atómica en el `decrement`. |
| E-06 | MEDIO | `AuthController.php:53-76` | Login sin `throttle`. | Fuerza bruta de contraseñas. | Middleware `throttle` en rutas de login. |
| E-07 | MEDIO | `ProductController.php:18-50` | `search`, `min_price`, `max_price`, `category` sin validación (no se valida que `min_price` sea numérico ni que `category` exista). | Entradas malformadas / consultas sin tipo. | Validar con `Rule::numeric` y `exists`. |
| E-08 | MEDIO | `ProductSeeder.php` | `image => null` en todos los productos. | Catálogo sin imágenes en instalación limpia (requisito 12). | Incluir imágenes locales o URLs en el seeder. |
| E-09 | MEDIO | `AdminPanelProvider.php` | Shield `RoleResource` no registrado → sin gestión de roles en el panel. | No se pueden asignar permisos por UI. | `->resources([RoleResource::class])` o incluir en discovery. |
| E-10 | BAJO | `products/index.blade.php:230` | `Str::limit($product->description, 100)` con `description` nullable (migración permite NULL). | Deprecación de PHP 8.1+ si es NULL. | `Str::limit($product->description ?? '', 100)`. |
| E-11 | BAJO | `UserForm.php:22-24` | Campo `password` `required()` en edición; el admin debe reescribir una contraseña para guardar cualquier cambio. | UX confusa. | `required` solo en create; opcional en edit (hash automático vía cast). |
| E-12 | BAJO | `Orders/Pages/CreateOrder.php`, `Payments/Pages/CreatePayment.php` | Archivos muertos (no registrados en `getPages`). | Código muerto. | Eliminar. |
| E-13 | BAJO | `welcome.blade.php` | Plantilla por defecto sin uso. | Código muerto. | Eliminar. |
| E-14 | BAJO | BD `products` | Producto duplicado id 4 "Camiseta Overzide Negra" vs id 5 "Camiseta Oversize Negra". | Duplicidad de datos. | Limpiar en seeder de limpieza (no borrar migraciones). |
| E-15 | BAJO | `.env.example` | `APP_LOCALE=en`, pero la app usa `es`; falta `DB_DATABASE` explícito (funciona por default). | Confusión en instalación limpia. | Ajustar `.env.example` a `es` y documentar creación de `database/database.sqlite`. |
| E-16 | BAJO | `config/filament-shield.php` | Permisos solo para `Category` y `Product` (24); `Order`, `Payment`, `User` sin permisos/políticas Shield. | Menos granularidad de permisos. | Generar permisos para el resto o documentar que `super_admin` cubre el acceso. |

---

# 8. Seguridad

**Hallazgos:**

| Riesgo | Severidad | Detalle |
|---|---|---|
| Sin límite de intentos de login | MEDIO | `login.store` sin `throttle`. |
| Precios de sesión vs BD | MEDIO | `order.subtotal`/`payment.amount` desde sesión (ver E-03); un cliente podría comprar con precio viejo. |
| Race condition de stock | MEDIO | Decremento sin lock atómico (E-05). |
| Doble envío del formulario de checkout | MEDIO | No hay token de idempotencia; un doble POST crea 2 pedidos y decrementa stock 2 veces. |
| Inputs de filtros sin validar | BAJO/MEDIO | `search`, `min_price`, `max_price`. |
| APP_DEBUG=true | BAJO | Correcto en local, debe ser `false` en producción (no hay entorno de producción). |
| Campo password requerido en edición de usuario | BAJO | No es fuga (el cast `hashed` protege), pero obliga a escribir contraseña en cada edición. |
| Sin HTTPS | ALTO (para entrega) | Requisito 54 de la rúbrica. |

**Lo que SÍ está correcto:**
- **CSRF**: todos los formularios usan `@csrf`; middleware `VerifyCsrfToken` en web y en Filament.
- **SQL Injection**: todas las consultas usan Eloquent/Query Builder con parámetros; el `LIKE` usa bindings.
- **XSS**: Blade escapa por defecto (`{{ }}`) en todas las vistas; sin `{!! !!}` peligrosos encontrados.
- **Contraseñas**: `Hash::make` y cast `hashed`; sin contraseñas en texto plano.
- **Sesiones**: driver `database`, regeneración de sesión en login/registro/logout.
- **Mass assignment**: `$fillable` definido en todos los modelos.
- **Acceso horizontal**: `checkout.success` verifica `order->user_id === Auth::id()` (abort 403). No existe ruta para ver pedidos de otros.
- **Panel admin**: `canAccessPanel()` solo `super_admin`; `/admin` protegido por middleware Filament.
- **Datos de tarjeta**: no se almacenan números, CVV ni tokens.
- **Secretos**: `.env` en `.gitignore`; sin claves de pago en el repo (no hay ninguna en `.env`); `services.php` sin claves reales.
- **Cookies**: `recent_products` solo guarda IDs enteros; los productos se revalidan con `active=true` al mostrar.
- **Archivos**: FileUpload con `->image()` (restringe a imágenes); límite de tamaño por defecto de Filament.

---

# 9. Base de datos

**Migraciones aplicadas (9/9):** usuarios, cache, jobs, permisos Spatie, categorías, productos, pedidos, order_items, pagos.

**Integridad:**
- `products.category_id` → FK `cascadeOnDelete` (borrar categoría borra productos; riesgo aceptable para proyecto, se podría usar `restrict`).
- `orders.user_id` → FK `restrictOnDelete` (correcto, no se borra usuario con pedidos).
- `order_items.order_id` → `cascadeOnDelete`; `product_id` nullable `nullOnDelete` (correcto, conserva el nombre del producto).
- `payments.order_id` → `unique` (1 pago por pedido) + `cascadeOnDelete`.
- `orders.tracking_number` único; `payments.transaction_id` único nullable.
- Tipos `decimal(10/12, 2)` correctos para moneda; `quantity` `unsignedInteger`.
- **Sin check constraints** en `status` (orders/payments). Se valida solo por UI/controlador.

**Estado actual de datos:** users 4, categories 4, products 8, orders 0, order_items 0, payments 0, roles 2 (super_admin, customer), permissions 24, sessions 3. **Cero pedidos** → nada que mostrar en historial/reportes/panel.

**Seeders:** `RoleSeeder`, `UserSeeder` (admin@streetwearcr.test / Admin12345, cliente@streetwearcr.test / Cliente12345), `CategorySeeder` (4), `ProductSeeder` (5). Usan `updateOrCreate` (idempotentes). No hay seeder de pedidos/pagos. `RefreshDatabase` en pruebas funcionaría.

**Instalación limpia (`migrate:fresh --seed`):** el flujo funciona para migraciones, pero las tablas de permisos quedarían **vacías** (los 24 permisos actuales se generaron con `php artisan shield:install`/`generate`, no por un seeder). Para una instalación limpia se debe ejecutar `php artisan shield:install` (o `shield:generate`) después del seed, o crear un `PermissionSeeder`.

---

# 10. UX / UI

**Fortalezas:**
- Layout único coherente (`layouts/app.blade.php`): navbar oscuro fija, footer fijo, contenedor central.
- Bootstrap 5.3 responsive correcto (`col-12 col-md-6 col-lg-4`, etc.).
- Contador del carrito en la navbar (badge).
- Mensajes de éxito/error globales y por página.
- Estados vacíos en carrito y catálogo.
- Pantalla de confirmación de pedido clara con tracking.
- Advertencia visible de pago en demostración.

**Deficiencias:**
- **Productos sin imagen** en instalación limpia ("Sin imagen" en todo el catálogo) — es lo más visible para una demo.
- "Mis pedidos" vacío aunque haya pedidos (mensaje estático engañoso).
- Errores de validación solo como alerta superior, sin marcado por campo (excepto checkout que los lista).
- Sin `max` en el input de cantidad del carrito (la validación es server-side; UX aceptable).
- Sin página 404 personalizada ni manejo de errores custom.
- Sin favicon propio (usa `favicon.ico` por defecto).
- La descripción truncada con `Str::limit` puede romper con NULL (E-10).
- Accesibilidad básica aceptable (aria-labels en navbar), pero sin foco visible ni contraste verificado.

**Nivel para demostración empresarial:** funcional y ordenado, pero requiere al menos imágenes de producto, historial de pedidos y pulido de estados vacíos para verse profesional.

---

# 11. Pruebas

**Ejecución real (`php artisan test`, 10/08/2026):**

```
PASS  Tests\Unit\ExampleTest        (1)
FAIL  Tests\Feature\ExampleTest      -> Expected status [200] but received 302 (GET / redirige a /productos)
FAIL  Tests\Feature\ProductCatalogTest -> 500: Vite manifest not found
Tests: 2 failed, 1 passed (3 assertions)
```

**Inventario:**
- `tests/Unit/ExampleTest.php` — trivial (`true is true`).
- `tests/Feature/ExampleTest.php` — `GET /` espera 200 pero es un redirect → **mal diseñado**.
- `tests/Feature/ProductCatalogTest.php` — catálogo con productos activos → **falla por build de assets**.

**No existen pruebas para:** registro, login, logout, perfil, filtros, carrito (agregar/actualizar/eliminar), checkout, stock, autorización de pedidos, reportes, cookies, roles, Filament.

**Causa raíz de las fallas:** `@vite()` en el layout exige `public/build/manifest.json`; al no existir (nunca se ejecutó `npm run build`), toda página falla. Solución FASE 0: `npm run build` (o generar el manifest en pruebas).

---

# 12. Git

- Rama única: `main` (seguimiento de `origin/main`). Sin ramas de integrantes (`andriy-ux`, `dario-pagos-seguridad`).
- Historial (6 commits):
  1. `6b68270` Inicio del proyecto
  2. `bfe1f80` Identidad y documentación
  3. `3ce2980` Búsqueda y filtros
  4. `e134dd5` Auth, checkout, pedidos, pagos, administración
  5. `354b4cc` Datos iniciales para desarrollo
  6. `2991367` Cookies recientes
- `git status` limpio excepto `AGENTS.md` **sin commitear**.
- No hay evidencia en el historial de los trabajos de Darío descritos en AGENTS.md (seeders de pedidos/pagos, pruebas, reportes PDF). Pueden existir en ramas no fusionadas o simplemente no existir aún.
- `.env`, `database.sqlite`, imágenes y `node_modules` correctamente ignorados.

**Riesgos de colaboración:**
- Todo el trabajo vive en `main` → alto riesgo de conflictos al crear ramas.
- `AGENTS.md` sin versionar (debería subirse para que el equipo lo lea).
- Sin Pull Requests ni revisión (reglas de AGENTS.md no aplicadas todavía).

---

# 13. Auditoría contra la rúbrica

| Nº | Criterio | Estado | Evidencia | Archivos | Qué falta | Prioridad |
|---|---|---|---|---|---|---|
| 1 | Autenticación y gestión de usuarios | CUMPLE | Login/registro/logout; Filament gestiona usuarios | `AuthController`, `UserResource` | Gestión de roles por UI | — |
| 2 | Registro de usuarios nuevos | CUMPLE | Validación, hash, rol customer | `AuthController.php:18-46` | — | — |
| 3 | Inicio de sesión | CUMPLE | `Auth::attempt` + remember | `AuthController.php:53-76` | Rate-limit | Media |
| 4 | Cierre de sesión | CUMPLE | invalidate + regenerateToken | `AuthController.php:78-88` | — | — |
| 5 | Perfil de usuario | CUMPLE | Vista de perfil | `account/profile.blade.php` | Cambio de contraseña | Baja |
| 6 | Modificación de datos personales | CUMPLE | nombre/email con unique | `AccountController.php:20-44` | — | — |
| 7 | Historial de pedidos | NO CUMPLE | Vista estática, sin consulta | `account/orders.blade.php` | Implementar listado + detalle | **Alta** |
| 8 | Categorías de productos | CUMPLE | Filament CategoryResource | `app/Filament/Resources/Categories` | — | — |
| 9 | Lista de productos | CUMPLE | `/productos` | `products/index.blade.php` | — | — |
| 10 | Descripción de productos | CUMPLE | Campo y render | `products/show.blade.php` | Manejar NULL (E-10) | Baja |
| 11 | Precio | CUMPLE | decimal, formato ₡ | `ProductForm.php`, vistas | — | — |
| 12 | Imágenes | FUNCIONA CON DEFICIENCIAS | Upload OK, seeder sin imágenes | `ProductForm.php`, `ProductSeeder.php` | Imágenes en seeder | **Alta** |
| 13 | Búsqueda por nombre | CUMPLE | `LIKE %search%` | `ProductController.php:18` | Validar input | Baja |
| 14 | Filtro por categoría | CUMPLE | `category_id` | `ProductController.php:27` | — | — |
| 15 | Filtro por precio | CUMPLE | min/max | `ProductController.php:35-50` | Validar numérico | Baja |
| 16 | Agregar productos al carrito | CUMPLE | Sesión + control stock | `CartController.php:35-70` | — | — |
| 17 | Eliminar del carrito | CUMPLE | `unset` | `CartController.php:96-109` | — | — |
| 18 | Actualizar cantidades | CUMPLE | Validación min/max stock | `CartController.php:72-94` | Re-validar activo | Media |
| 19 | Cálculo automático de subtotal | CUMPLE | `CartCalculator::subtotal` | `CartCalculator.php` | — | — |
| 20 | Cálculo de impuestos | CUMPLE | 13 % | `CartCalculator.php:18` | — | — |
| 21 | Costos de envío | CUMPLE | fijo ₡3000 | `CartCalculator.php:23` | — | — |
| 22 | Total de la compra | CUMPLE | subtotal+IVA+envío | `CartCalculator.php:28` | — | — |
| 23 | Tabla de compra/factura | FUNCIONA CON DEFICIENCIAS | Resumen en carrito/checkout/éxito | vistas de carrito/checkout | Factura imprimible no existe | Media |
| 24 | Identificación del usuario en la compra | CUMPLE | `user_id` en Order | `CheckoutController.php:110` | — | — |
| 25 | Fecha de compra | CUMPLE | timestamps | `orders`, vistas | — | — |
| 26 | Monto de compra | CUMPLE | `total` decimal | `Order.php` | — | — |
| 27 | Proceso de checkout | CUMPLE | GET/POST validado, transacción | `CheckoutController.php` | Recalcular con precios BD | **Alta** |
| 28 | Opciones de pago tarjeta y/o PayPal | FUNCIONA CON DEFICIENCIAS | Radio card/paypal en checkout | `checkout/index.blade.php` | Solo selección, no cobro | **Alta** |
| 29 | Pasarela de pago segura | NO CUMPLE | Pago local `pending`, `transaction_id=null` | `CheckoutController.php:146-153` | Integrar Stripe/PayPal sandbox | **Alta** |
| 30 | Confirmación del pedido | CUMPLE | Pantalla de éxito | `checkout/success.blade.php` | — | — |
| 31 | Detalles de la compra | FUNCIONA CON DEFICIENCIAS | Solo en éxito inmediato | `checkout/success.blade.php` | Detalle desde historial | **Alta** |
| 32 | Número de seguimiento | CUMPLE | `SWCR-...` único | `CheckoutController.php:190` | — | — |
| 33 | Cookies productos vistos | CUMPLE | cookie `recent_products` | `ProductController.php:71-138` | — | — |
| 34 | Mostrar productos vistos recientemente | CUMPLE | Sección en detalle | `products/show.blade.php:139` | — | — |
| 35 | Reportes ventas por mes PDF | NO CUMPLE | Sin librería ni código | — | Instalar DomPDF + reportes | **Alta** |
| 36 | Reportes ventas por cliente PDF | NO CUMPLE | Ídem | — | Ídem | **Alta** |
| 37 | Validación de entradas | FUNCIONA CON DEFICIENCIAS | Validaciones en auth/checkout/carrito | varios | Filtros sin validar (E-07) | Media |
| 38 | Protección contra SQL Injection | CUMPLE | Eloquent/bindings | todo el código | — | — |
| 39 | Protección contra XSS | CUMPLE | Escapado Blade | vistas | — | — |
| 40 | Protección CSRF | CUMPLE | `@csrf` + VerifyCsrfToken | formularios | — | — |
| 41 | Manejo seguro de sesiones | CUMPLE | regeneración, driver database | `AuthController`, `.env` | — | — |
| 42 | Hashing de contraseñas | CUMPLE | `Hash::make` + cast `hashed` | `AuthController`, `User` | — | — |
| 43 | Datos sensibles | CUMPLE | No se guardan tarjetas/CVV/secretos | — | — | — |
| 44 | Roles y permisos | CUMPLE | Spatie + Shield, 24 permisos | `RoleSeeder`, policies | Gestión por UI (E-09) | Media |
| 45 | Protección del panel admin | CUMPLE | `canAccessPanel` super_admin | `User.php:58-61` | — | — |
| 46 | Evitar acceso a pedidos ajenos | CUMPLE | `abort(403)` en success | `CheckoutController.php:175` | — | — |
| 47 | Validación de stock en compra | CUMPLE | Re-chequeo en transacción | `CheckoutController.php:100-105` | Lock atómico (E-05) | Media |
| 48 | Uso de Laravel | CUMPLE | Framework completo | — | — | — |
| 49 | Uso de SQLite | CUMPLE | driver sqlite | `.env`, `config/database.php` | — | — |
| 50 | Bootstrap | CUMPLE | Bootstrap 5.3 vía Vite | `app.css`, `app.js` | — | — |
| 51 | Diseño responsive | CUMPLE | Grids responsive | layouts/vistas | Probar tablet | Baja |
| 52 | UX intuitiva | FUNCIONA CON DEFICIENCIAS | UI coherente | vistas | Imágenes, historial, estados | **Alta** |
| 53 | Uso de GitHub | CUMPLE | repo + remoto | git | Seguir flujo de ramas/PR | Media |
| 54 | SSL/HTTPS | NO CUMPLE | Sin producción | — | Certificado + APP_URL https | Media |
| 55 | Hosting | NO CUMPLE | Sin despliegue | — | Subir a host | Media |
| 56 | Código fuente completo | CUMPLE | Todo en repo | git | — | — |
| 57 | README / instrucciones | NO CUMPLE | README mínimo, sin pasos | `README.md` | Instalación completa | **Alta** |
| 58 | Documentación detallada | NO CUMPLE | No existe | — | Manual + documentación técnica | **Alta** |
| 59 | Diagrama caso de uso de compra | NO CUMPLE | No existe | — | Crear diagrama | **Alta** |
| 60 | Pruebas automáticas | NO CUMPLE | 3 pruebas, 2 fallan | `tests/` | Corregir y ampliar | **Alta** |
| 61 | Calidad del código | CUMPLE | Controllers/modelos limpios, sin duplicación crítica | `app/` | Eliminar código muerto | Baja |
| 62 | Buenas prácticas | FUNCIONA CON DEFICIENCIAS | MVC claro, validación inline, sin FormRequests | `app/` | FormRequests si se desea | Baja |

---

# 14. Qué falta para presentar frente a la empresa

**OBLIGATORIO**
1. Corregir build de assets y pruebas (E-01).
2. Implementar historial y detalle de pedidos del cliente (7, 31).
3. Recalcular checkout con precios de BD (E-03).
4. Imágenes de producto visibles en catálogo (12).
5. Pasarela de pago en modo sandbox (29) — o al menos simulación clara de "pago aprobado".
6. Reportes PDF por mes y por cliente (35, 36).
7. README completo con instalación y credenciales (57).
8. Seeders de pedidos/pagos para demostrar datos reales.

**RECOMENDADO**
9. Gestión de roles en el panel (E-09).
10. Rate-limit de login y bloqueo atómico de stock (E-06, E-05).
11. Validación de filtros (E-07).
12. Estados vacíos y mensajes pulidos; 404 personalizado.
13. Factura visible/imprimible tras la compra.

**OPCIONAL**
14. Cambio de contraseña en perfil.
15. Página 404/error personalizada.
16. Notificaciones por correo (mailer log).
17. Dashboard Filament con métricas (ventas, pedidos, top productos).

---

# 15. Qué falta para entregar a la universidad

- **Código**: limpiar código muerto (E-11/12/13), corregir E-03/E-04/E-07, añadir form requests si se desea.
- **Documentación**: README completo (instalación, credenciales, flujo, tecnologías), manual de usuario, documentación técnica (arquitectura, modelos, rutas, seguridad) y diagrama de caso de uso del proceso de compra.
- **ZIP**: empaquetar el proyecto (excluyendo `vendor`, `node_modules`, `.env`, `database.sqlite`, imágenes locales).
- **Pruebas**: arreglar las 2 fallidas y añadir pruebas de registro, login, logout, catálogo, filtros, carrito, checkout, stock, autorización de pedidos, cookies, roles y (si se implementan) reportes.
- **GitHub**: mantener `main` estable, usar ramas por integrante, Pull Requests, subir `AGENTS.md`.
- **Diagrama**: caso de uso de compra (actor cliente/admin + escenarios).
- **Exposición**: preparar demo con datos (pedidos con diferentes estados y pagos), guion de recorrido y respuestas a preguntas.
- **Hosting**: desplegar a producción con `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` real, HTTPS (certificado), storage enlazado y `npm run build`.
- **SSL**: certificado válido y redirección a HTTPS.

---

# 16. Actualización post-fusión (10/08/2026, integración de Darío)

Esta sección corrige el estado del documento anterior, que se escribió **antes** de integrar los commits de Darío (`db13d96`, `7699454`, `ad00b54`). Ahora `main` está sincronizada con `origin/main` en `ad00b54`.

## Cambios integrados

| Área | Antes (sección anterior) | Ahora (verificado en esta sesión) |
|---|---|---|
| Historial de pedidos (E-02) | No consultaba BD (vista estática) | **CORREGIDO**: `AccountController::orders()` usa `$request->user()->orders()` y la vista `account/orders.blade.php` lista pedidos con tracking, fecha, estado y total. |
| Reportes PDF (35/36) | No existían | **IMPLEMENTADO**: `ReportController` con 3 reportes (`orders`, `sales`, `products`) que descargan PDF con DomPDF; rutas `/reportes/*`; página Filament "Reportes"; `reports/index.blade.php` con filtros (desde/hasta/estado/cliente). |
| DomPDF (35/36) | No instalado | **INSTALADO** (`composer install`): `barryvdh/laravel-dompdf` v3.1.2 + `dompdf/dompdf` v3.1.6 en `composer.lock`. |
| Seeders de pedidos/pagos | No existían | **IMPLEMENTADOS**: `OrderSeeder.php` + `OrderFactory`/`PaymentFactory`/`OrderItemFactory`; 3 pedidos de ejemplo (pending/processing/shipped) con pagos (pending/paid). |
| Pruebas automáticas (60) | 3 pruebas, 2 fallaban | **AMPLIADAS y VERDES**: 25 pruebas, 88 assertions, `php artisan test` → **25 passed**. Nuevos: `AuthTest`, `CartTest`, `CheckoutTest`, `OrderTest`, `PaymentTest`; `ExampleTest` corregido; `ProductCatalogTest` funciona tras build. |
| Build de assets (E-01) | Manifest faltante | **CORREGIDO localmente** con `npm run build`. OJO: `public/build` está en `.gitignore`, así que un clon limpio necesita `npm run build` antes de `php artisan test`. |

## Verificaciones ejecutadas en esta sesión

- `git status` limpio excepto `AGENTS.md`, `AUDITORIA_PROYECTO.md`, `PLAN_IMPLEMENTACION.md` (sin commitear).
- `composer install` → instaló DomPDF (declarado en `composer.lock` por Darío pero ausente de `vendor`).
- `npm run build` → generó `public/build/manifest.json` y los assets.
- `php artisan test` → 25/25 PASS (88 assertions).
- Las 4 vistas de reportes se renderizaron sin errores con los datos reales del controlador y los 3 PDFs se generaron correctamente (verificación con script de arranque Laravel).

## Nuevos hallazgos tras revisar el código integrado

| ID | Severidad | Archivo | Problema | Impacto |
|---|---|---|---|---|
| E-17 | BAJO | `database/seeders/OrderSeeder.php` | No es idempotente: cada `db:seed` (o `migrate:fresh --seed`) crea 3 pedidos nuevos vía `Order::factory()->create()` sin `updateOrCreate`. | En una reinstalación limpia es aceptable (empieza de cero); solo molesta si se corre `db:seed` repetido. |
| E-18 | BAJO | `app/Filament/Pages/Reports.php` | La página Filament "Reportes" solo enlaza a las rutas web `/reportes/*`. No valida que el usuario tenga rol `super_admin` por UI, aunque el controlador sí aborta 403 con `ensureIsAdmin()`. | Correcto por controlador; la página es visible en el menú de Filament para cualquier usuario autenticado al panel. |
| E-19 | INFORMATIVO | `resources/views/reports/*.blade.php` | Las vistas usan `₡` (colón, U+20A1). DejaVu Sans lo soporta; verificado que el PDF se genera sin error. | Ninguno. |

## Estado de la rúbrica tras la integración (solo filas que cambiaron)

| Nº | Criterio | Estado actualizado | Evidencia |
|---|---|---|---|
| 7 | Historial de pedidos | CUMPLE | `AccountController::orders()`, `account/orders.blade.php` |
| 35 | Reportes ventas por mes PDF | CUMPLE | `ReportController::sales()` → `reporte-ventas.pdf` con agrupación por mes |
| 36 | Reportes ventas por cliente PDF | CUMPLE | Mismo PDF con agrupación por cliente |
| 60 | Pruebas automáticas | CUMPLE | 25 tests / 88 assertions en verde |

**Sigue pendiente** (sin cambios respecto a la sección anterior): pasarela de pago real (28/29), imágenes en seeder (12), detalles de compra desde el historial (31), README completo (57), documentación (58), diagrama (59), hosting/HTTPS (54/55), gestión de roles por UI (E-09), y los hallazgos E-03 a E-08, E-10 a E-16.

---

# 17. Auditoría final (10/08/2026)

Tras completar las FASE 1-8 del `PLAN_IMPLEMENTACION.md`, se re-ejecutaron las
verificaciones de la auditoría con datos reales. Estado actual:

| Área | % anterior | % actual | Justificación |
|---|---|---|---|
| Funcionalidad | 55 % | 90 % | Historial y detalle de pedidos, reportes PDF, seeders de pedidos/pagos y checkout idempotente implementados. Pendiente: pasarela de pago sandbox real. |
| Seguridad | 65 % | 90 % | Rate-limit de login, token de idempotencia, `lockForUpdate`, filtros validados, estados en whitelist, roles por UI, pedidos protegidos. Pendiente: HTTPS en producción (requiere hosting). |
| UX | 55 % | 90 % | Imágenes en seeder, badges de estado en pedidos, páginas 404/403/500, favicon, empty states, foco visible, cards uniformes. |
| Pruebas | 15 % | 90 % | 41 tests / 129 assertions en verde, cubriendo auth, catálogo, carrito, checkout, idempotencia, pedidos, cookies, throttle y reportes PDF. |
| Documentación | 25 % | 95 % | README completo, manual de usuario, documentación técnica, diagrama de compra, guía de deployment y guion de defensa. |
| Preparación para entrega | 40 % | 80 % | Todo documentado y probado. Falta solo: hosting real, SSL configurado y pasarela sandbox. |
## Verificaciones finales ejecutadas

- `php artisan test` → **41 passed (129 assertions)**.
- `migrate:fresh --seed` → instalación limpia OK: 5 productos (ids 1-5),
  2 usuarios, 3 pedidos, 3 pagos, 5 order_items.
- `db:seed --class=OrderSeeder` repetido → sigue en 3 pedidos (idempotente).
- `npm run build` → manifest y assets generados; páginas cargan.
- Verificación HTTP real: `/productos` 200, `/mi-cuenta/pedidos/{id}` 200,
  `/reportes/*` 200 + `application/pdf`.
- `public/storage` reenlazado correctamente a `storage/app/public`
  (estaba apuntando a otra carpeta).

## Estado de la rúbrica (criterios que cambiaron en esta auditoría final)

| Nº | Criterio | Estado actualizado | Evidencia |
|---|---|---|---|
| 12 | Imágenes | CUMPLE | SVGs en `storage/app/public/products/seed/*.svg`, referenciados por `ProductSeeder`. |
| 28 | Opciones de pago tarjeta/PayPal | FUNCIONA CON DEFICIENCIAS | Selección visual funcional en checkout (modo demo). Falta cobro sandbox. |
| 31 | Detalles de la compra | CUMPLE | Ruta `account.orders.show` + `account/order-detail.blade.php` (badges, tracking, productos, pago, resumen). |
| 44 | Roles y permisos | CUMPLE | `RoleResource` de Shield registrado → rutas `admin/shield/roles/*`. |
| 52 | UX intuitiva | CUMPLE | FASE 4 completa: badges, empty states, 404/403/500, favicon, foco visible. |
| 57 | README / instrucciones | CUMPLE | Instalación desde cero, credenciales, flujo, pruebas, estructura, enlaces a `docs/`. |
| 58 | Documentación detallada | CUMPLE | `docs/MANUAL.md`, `docs/TECNICA.md`, `docs/DEPLOYMENT.md`, `docs/DEFENSA.md`. |
| 59 | Diagrama caso de uso de compra | CUMPLE | `docs/diagrama-uso-compra.md` (Mermaid + descripción del flujo). |

## Resueltos en esta auditoría final

- **E-03** (precios sesión vs BD): checkout recalcula todo desde BD en transacción.
- **E-04** (carrito con producto inactivo): `cart.update` lo rechaza.
- **E-05** (stock race condition): `lockForUpdate()` dentro de `DB::transaction`.
- **E-06** (login sin throttle): `throttle:5,1` + test 429.
- **E-07** (filtros sin validar): categoría con `exists`, precios numéricos ≥ 0.
- **E-08** (imágenes en seeder): SVGs placeholder.
- **E-09** (roles por UI): `RoleResource` registrado.
- **E-10** (`Str::limit` NULL): `?? ''`.
- **E-14** (producto duplicado): limpieza de datos.
- **E-17** (`OrderSeeder` no idempotente): verifica `orders()->exists()`.
- **E-20** (`public/storage` a otra carpeta): reenlazado con `storage:link`.

## Pendientes reales (para entrega final)

1. **Pasarela de pago sandbox** (criterio 29): el pago local `pending` debe
   sustituirse por la respuesta de Stripe/PayPal en modo prueba.
   **Nunca** almacenar números de tarjeta ni credenciales.
2. **Hosting + HTTPS** (criterios 54/55): desplegar a producción siguiendo
   `docs/DEPLOYMENT.md` (Certbot, `APP_ENV=production`, `APP_DEBUG=false`).
3. **Factura imprimible** (opcional, criterio 23): vista PDF de factura por pedido.

## Resueltos después de la auditoría final (E-11 a E-18)

- **E-11** (`password` obligatorio al editar usuario): `UserForm` usa
  `dehydrated(fn ($state) => filled($state))` + `required` solo en `create`
  + helperText en `edit` ("Leave blank to keep the current password").
- **E-12/E-13** (código muerto): eliminados `welcome.blade.php` y
  `Orders/Pages/CreateOrder.php`, `Payments/Pages/CreatePayment.php`
  (no estaban registrados en `getPages`).
- **E-15** (`.env.example`): `APP_LOCALE=es`, `APP_FALLBACK_LOCALE=es`,
  `APP_FAKER_LOCALE=es_CR`, y comentario con el paso de crear
  `database/database.sqlite` (PowerShell y Linux).
- **E-18** (página "Reportes" visible para no-admin): `Reports::shouldRegisterNavigation()`
  devuelve true solo si el usuario tiene rol `super_admin`; la ruta sigue
  protegida por el controlador con 403.

Verificación tras estos cambios: `php artisan test` → **41 passed (129 assertions)**;
`php artisan route:list --path=admin` OK (reports/users/orders/payments); panel
Filament arranca (302 → `/admin/login` sin sesión, esperado).
