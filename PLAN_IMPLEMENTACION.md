# PLAN DE IMPLEMENTACIÓN — StreetWear CR

> **Estado 10/08/2026 (post-fusión de Darío):** parte de la FASE 1 ya fue implementada por Darío e integrada en `main` (historial de pedidos, seeders de pedidos/pagos, reportes PDF y pruebas automáticas). En esta misma fecha se completaron las FASE 1, 2, 3, 4, 5, 6, 7 y 8 (ver "[Avance y replanificación](#avance-y-replanificación)"): detalle de pedido, imágenes en seeder, README, documentación, diagrama, deployment y defensa, errores funcionales, seguridad e idempotencia, pulido UX/UI y 41 tests en verde.

Este plan organiza los hallazgos de `AUDITORIA_PROYECTO.md` en fases. **No se ha implementado nada todavía.** Cada fase se ejecutará solo con la aprobación explícita ("APROBADO, IMPLEMENTAR FASE X"), y al terminar se mostrarán cambios, pruebas y resultados antes de avanzar.

Responsables disponibles: **NÉSTOR**, **ANDRIY**, **DARÍO**, **COMPARTIDA**.

---

## FASE 0 — BLOQUEADORES

**Objetivo:** que el proyecto arranque, pruebe e instale sin fricción.

**Prioridad:** CRÍTICA — impide pruebas y demostración.

**Problemas que corrige:**
- E-01: `public/build/manifest.json` no existe → el sitio no carga sin `npm run dev` y 2 de 3 pruebas fallan.
- Instalación limpia: `composer install` sobre un clon no crea `database/database.sqlite`.
- Instalación limpia: tras `migrate:fresh --seed` las tablas de permisos quedan vacías (falta `php artisan shield:install` o un `PermissionSeeder`).

**Archivos involucrados:**
- `composer.json` (script `setup`).
- `README.md` (pasos de instalación).
- Opcional: `PermissionSeeder.php` nuevo.

**Dependencias:** ninguna nueva.

**Riesgos:** ninguno relevante (solo build y documentación).

**Pruebas necesarias:** `php artisan test` debe dar todo verde.

**Responsable recomendado:** NÉSTOR.

**Criterio de aceptación:**
1. `npm run build` genera `public/build/manifest.json`.
2. `php artisan test` pasa sin fallos.
3. Un compañero puede clonar, `composer install`, `npm install`, crear `.env`, `key:generate`, crear `database.sqlite`, `migrate:fresh --seed`, `storage:link`, `npm run build` y ver la tienda.

---

## FASE 1 — REQUISITOS OBLIGATORIOS FALTANTES (rúbrica)

**Objetivo:** cumplir los criterios de la rúbrica que hoy son `NO CUMPLE`.

**Prioridad:** ALTA.

**Problemas que corrige:**
- Criterio 7/31: historial y detalle de pedidos del cliente.
- Criterio 12: imágenes de producto en el seeder.
- Criterio 35/36: reportes PDF (ventas por mes y por cliente).
- Criterio 57/58/59: README completo, documentación técnica/manual y diagrama de caso de uso de compra.
- Seeders de pedidos y pagos (demostrar datos en historial, panel y reportes).

**Archivos involucrados:**
- `AccountController.php`, `account/orders.blade.php`, nueva vista de detalle de pedido (`account/orders/show.blade.php` o similar), ruta nueva en `routes/web.php`.
- `ProductSeeder.php` (imágenes).
- Nueva dependencia `barryvdh/laravel-dompdf` (instalación = `composer require` — se hará en esta fase con aprobación).
- Nuevo `ReportController.php`, rutas de reportes, vistas PDF, botón en Filament o navbar admin.
- Nuevos `OrderSeeder.php` y `PaymentSeeder.php` (+ `OrderFactory`, `PaymentFactory` si se usan).
- `README.md`, `docs/` (manual, documentación técnica), diagrama (archivo de imagen o fuente editable).

**Dependencias:** `barryvdh/laravel-dompdf` (la única librería nueva aprobada).

**Riesgos:** DomPDF + Filament pueden aumentar ligeramente la memoria; en XAMPP es habitual. Validar con reportes de pocas filas.

**Pruebas necesarias:** tests de historial (autorización), de reportes (respuesta 200 + PDF) y manual.

**Responsable recomendado:** DARÍO (reportes, seeders, pruebas) + NÉSTOR (integración, historial, rutas) + COMPARTIDA (documentación).

**Criterio de aceptación:** un cliente ve su historial y el detalle de un pedido; el admin descarga reportes PDF de ventas por mes y por cliente; la instalación limpia muestra imágenes y datos de ejemplo.

---

## FASE 2 — ERRORES FUNCIONALES

**Objetivo:** corregir funciones existentes que trabajan incorrectamente.

**Prioridad:** ALTA.

**Problemas que corrige:**
- E-03: subtotal/total/pago calculados con precios de sesión vs precios de BD → recalcular todo desde BD en `CheckoutController::store`, o advertir/rechazar si el precio cambió.
- E-04: `cart.update` no verifica que el producto siga activo.
- E-02 ya cubierto en FASE 1 (historial).
- E-14: producto duplicado en BD (limpieza de datos, sin borrar migraciones).
- E-10: `Str::limit` con descripción NULL.

**Archivos involucrados:**
- `CheckoutController.php`, `CartController.php`, `products/index.blade.php`, `CartCalculator.php`.

**Dependencias:** ninguna.

**Riesgos:** modificar el cálculo de precios puede cambiar montos en pedidos existentes; la BD actual tiene 0 pedidos, así que el riesgo es mínimo.

**Pruebas necesarias:** tests de checkout con cambio de precio entre carrito y compra; tests de carrito con producto desactivado.

**Responsable recomendado:** DARÍO.

**Criterio de aceptación:** `order.subtotal` == suma de `order_items.subtotal`, `payment.amount` == `order.total`, siempre; carrito rechaza productos inactivos.

---

## FASE 3 — SEGURIDAD

**Objetivo:** endurecer autenticación, stock, pedidos y pagos.

**Prioridad:** ALTA.

**Problemas que corrige:**
- E-06: rate-limit de login (`throttle`).
- E-05: bloqueo atómico de stock (`lockForUpdate` / decremento condicional).
- Doble envío de checkout (token de idempotencia o validación de sesión).
- E-07: validación de filtros (numérico, exists, max length).
- E-09: registro de `RoleResource` de Shield para gestionar roles/permisos por UI.
- Validar estados de `Order`/`Payment` (whitelist en controlador, no solo en Filament).
- Pasar `APP_DEBUG=false`/`APP_ENV=production` cuando exista despliegue (FASE 7).

**Archivos involucrados:**
- `routes/web.php` (throttle en login y checkout).
- `AuthController.php`, `CheckoutController.php`, `CartController.php`, `ProductController.php`.
- `AdminPanelProvider.php` (registrar RoleResource).
- `ProductForm.php`/`UserForm.php` (password solo en create; validaciones de imagen).

**Dependencias:** ninguna.

**Riesgos:** `lockForUpdate` requiere transacción iniciada en la misma conexión (ya está dentro de `DB::transaction`); verificar en SQLite. El throttle puede molestar en demos (usar límite alto, p. ej. 30/min).

**Pruebas necesarias:** tests de intentos de login excesivos, de doble POST de checkout, de stock insuficiente, de acceso a pedido ajeno (403).

**Responsable recomendado:** DARÍO.

**Criterio de aceptación:** login limitado, no se vende stock inexistente, doble POST no duplica pedidos, filtros validados, roles editables desde el panel.

---

## FASE 4 — UX / UI

**Objetivo:** presentar la tienda a nivel de demostración profesional.

**Prioridad:** MEDIA-ALTA.

**Problemas que corrige:**
- Imágenes de producto (ya en FASE 1, aquí se hace el pulido visual de tarjetas).
- Historial de pedidos con estado visual (badges) y detalle.
- Estados vacíos y mensajes de error/éxito consistentes.
- Mejoras responsive (tablet y móvil) y de accesibilidad (focus, contraste).
- Página 404/error personalizada.
- Detalle visual del carrito, checkout y confirmación.

**Archivos involucrados:**
- `resources/views/**` (layouts, products, cart, checkout, account).
- `resources/css/app.css`.
- `resources/js/app.js` (interacciones: contador, toasts, confirmaciones).
- Opcional: `resources/views/errors/*`.

**Dependencias:** ninguna nueva.

**Riesgos:** Andriy debe conservar rutas, controladores, modelos y lógica del checkout (regla AGENTS.md).

**Pruebas necesarias:** prueba manual en escritorio, laptop, tablet y móvil; test visual del flujo 1-23 de AGENTS.md.

**Responsable recomendado:** ANDRIY.

**Criterio de aceptación:** la tienda se ve completa en los 4 tamaños, con imágenes, historial y estados claros, sin tocar la lógica de negocio.

---

## FASE 5 — PRUEBAS

**Objetivo:** pruebas automáticas verdes y cobertura mínima de la rúbrica.

**Prioridad:** ALTA.

**Problemas que corrige:**
- Corregir `ExampleTest` (espera 302 o usa `followingRedirects`).
- Corregir la causa de fallo de `ProductCatalogTest` (build de assets en FASE 0 o mock de Vite en tests).
- Añadir pruebas para: registro, login, logout, perfil, catálogo, filtros, carrito (agregar/actualizar/eliminar), checkout, stock, autorización de pedidos (403), cookies, roles.
- Si existe, cobertura de reportes.

**Archivos involucrados:**
- `tests/Feature/**` (nuevos archivos de tests).
- `tests/TestCase.php` (helpers si se necesitan).
- Factories: `ProductFactory`, `CategoryFactory`, `OrderFactory`, `PaymentFactory`, `OrderItemFactory`.

**Dependencias:** ninguna (PHPUnit ya instalado).

**Riesgos:** los tests que usan sesión/cookies con `SESSION_DRIVER=array` deben configurarse correctamente.

**Pruebas necesarias:** `php artisan test` completo en verde.

**Responsable recomendado:** DARÍO.

**Criterio de aceptación:** `php artisan test` → todo PASS con cobertura de los flujos críticos.

---

## FASE 6 — DOCUMENTACIÓN

**Objetivo:** documentación de instalación, uso y técnica.

**Prioridad:** MEDIA.

**Problemas que corrige:**
- README completo (tecnologías, requisitos, instalación paso a paso, credenciales, flujo de prueba, estructura).
- Manual de usuario (cliente y administrador).
- Documentación técnica (arquitectura, modelos, rutas, seguridad, decisiones de diseño).
- Diagrama de caso de uso del proceso de compra.

**Archivos involucrados:**
- `README.md`, `docs/` (o `docs/README.md`, `docs/MANUAL.md`, `docs/TECNICA.md`, `docs/diagrama-uso-compra.*`).

**Dependencias:** ninguna.

**Riesgos:** mantener documentación sincronizada con el código.

**Pruebas necesarias:** seguir el README en una máquina limpia de principio a fin.

**Responsable recomendado:** NÉSTOR + ANDRIY (manual visual).

**Criterio de aceptación:** un compañero (o la docente) puede instalar y usar el sistema solo con la documentación.

---

## FASE 7 — DEPLOYMENT

**Objetivo:** producción real accesible.

**Prioridad:** MEDIA (opcional si no se exige hosting).

**Problemas que corrige:**
- Criterios 54/55 (HTTPS y hosting).
- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` real.
- Build de frontend en producción (`npm run build`).
- `storage:link`, permisos de carpetas, `.env` de producción sin secretos.
- Certificado SSL y redirección HTTPS.

**Archivos involucrados:**
- `.env` (producción), servidor (Apache/Nginx), config de Vite, posible `.htaccess`.

**Dependencias:** host y dominio (decisión del equipo).

**Riesgos:** costos del hosting, configuración de HTTPS, URLs de assets con `APP_URL`.

**Pruebas necesarias:** flujo manual completo en producción + `php artisan config:cache`.

**Responsable recomendado:** NÉSTOR.

**Criterio de aceptación:** el sistema es accesible por HTTPS desde Internet y funciona el flujo completo.

---

## FASE 8 — PREPARACIÓN DE DEFENSA

**Objetivo:** que cada integrante pueda defender el proyecto.

**Prioridad:** ALTA (antes de la exposición).

**Problemas que corrige:** preparación oral, no técnica.

**Contenido:**
- **Partes difíciles de explicar:** checkout con transacción, cálculos de IVA/envío, cookies de recientes, permisos Spatie/Shield, políticas.
- **Decisiones de diseño:** sesión para carrito, precio desde BD en items, envío fijo, estados de pedido/pago, cookie de 5 productos/30 días.
- **Uso de IA:** identificar qué código fue asistido y saber explicarlo.
- **Errores encontrados y soluciones:** los de este informe (E-01 a E-16).
- **Código que cada integrante debe dominar:**
  - NÉSTOR: integración, checkout, Filament, README.
  - DARÍO: backend, pruebas, reportes, seguridad, pagos.
  - ANDRIY: todas las vistas y el flujo visual.
- **Posibles preguntas de la docente:** por qué SQLite, cómo se evita SQL Injection/XSS, cómo se protege un pedido ajeno, cómo funciona el IVA, por qué la cookie y cómo expira, qué pasa si se agota el stock, cómo se genera el tracking, qué es la pasarela sandbox.

**Archivos involucrados:** `docs/` (guion de defensa, preguntas-respuesta).

**Responsable recomendado:** COMPARTIDA.

**Criterio de aceptación:** cada integrante responde 5 preguntas técnicas sin consultar código.

---

## Orden recomendado de integración

1. **FASE 0** (Néstor) — desbloquea pruebas y demostración.
2. **FASE 5** en paralelo (Darío) — corrige y amplía pruebas sobre la base de la FASE 0.
3. **FASE 1 + 2** (Néstor/Darío) — historial, reportes, seeders, precios.
4. **FASE 3** (Darío) — seguridad.
5. **FASE 4** (Andriy) — UX/UI sobre funcionalidad estable.
6. **FASE 6** (Néstor/Andriy) — documentación.
7. **FASE 7** (Néstor) — deployment.
8. **FASE 8** (todos) — defensa.

---

## Avance y replanificación

Actualizado el 10/08/2026 tras integrar `db13d96`, `7699454` y `ad00b54` (Darío) y verificar el código real.

### FASE 0 — PARCIALMENTE COMPLETADA ✅

- `npm run build` ejecutado → `public/build/manifest.json` generado (E-01 resuelto localmente).
- `composer install` ejecutado → DomPDF instalado (estaba en `composer.lock` sin instalarse).
- `php artisan test` → 25/25 PASS (88 assertions).
- **Falta:** documentar el paso `npm run build` en README (porque `public/build` está en `.gitignore`) y aclarar la creación de `database/database.sqlite` en la instalación limpia.

### FASE 1 — COMPLETADA ✅ (10/08/2026)

- Historial de pedidos del cliente (criterio 7/31): `AccountController::orders()` consulta la BD y `account/orders.blade.php` lista pedidos.
- **Detalle de pedido (31):** nueva ruta `account.orders.show`, `AccountController::showOrder()` con `abort_unless` 403 (solo pedidos propios) y nueva vista `account/order-detail.blade.php` con badges de estado de pedido/pago, productos, resumen y tracking. Cada pedido del historial tiene botón "Ver detalle".
- Seeders de pedidos y pagos: `OrderSeeder` + factories (3 pedidos de ejemplo con estados/pagos).
- Reportes PDF (35/36): `ReportController` (orders/sales/products) + vistas + página Filament "Reportes" + rutas `/reportes/*`. Verificado: 4 vistas OK, 3 PDF generan.
- **Imágenes en seeder (12, E-08):** SVGs placeholder generados en `storage/app/public/products/seed/*.svg`, referenciados en `ProductSeeder`. Además se corrigió `public/storage` (junction apuntaba a otra carpeta) y se rehízo `php artisan storage:link`.
- **README completo (57):** instalación desde cero, credenciales demo, flujo cliente/admin, reportes, pruebas, seguridad y estructura.
- **Documentación técnica/manual (58):** `docs/MANUAL.md` y `docs/TECNICA.md`.
- **Diagrama de caso de uso de compra (59):** `docs/diagrama-uso-compra.md` (Mermaid).

### FASE 2 — COMPLETADA ✅ (10/08/2026)

- E-03: `CheckoutController::store` recalcula subtotal/impuesto/envío/total dentro de la transacción usando precios de BD (`$product->price`), no precios de sesión.
- E-04: `CartController::update` rechaza productos inactivos.
- E-14: eliminado el producto duplicado con typo ("Camiseta Overzide Negra") de la BD local (sin tocar migraciones).
- E-10: `Str::limit($product->description ?? '', 100)` en `products/index.blade.php`.

### FASE 3 — COMPLETADA ✅ (10/08/2026)

- E-06: `throttle:5,1` en `POST /login` (rutas) + test de 429 tras 5 intentos fallidos.
- E-05: `Product::query()->lockForUpdate()` dentro de la transacción de checkout (no-op en SQLite, protege MySQL/PostgreSQL).
- Doble POST checkout: token de idempotencia en sesión (`checkout_token`), campo oculto en la vista, `hash_equals` + `session()->pull` en `store`. Pruebas de doble POST (1 pedido) y token inválido (0 pedidos).
- E-07: filtros del catálogo saneados (`category` con `exists`, `min_price`/`max_price` numéricos y ≥ 0).
- E-09: `RoleResource` de Shield registrado en `AdminPanelProvider` → rutas `admin/shield/roles/*`.
- Whitelist estados: constantes `Order::STATUSES` y `Payment::STATUSES`; formularios Filament usan las constantes.
- `OrderSeeder` ahora es idempotente (E-17 resuelto).

### FASE 4 — COMPLETADA ✅ (10/08/2026)

Pulido UX/UI aplicado sin tocar lógica de negocio:

- Páginas de error personalizadas: `resources/views/errors/404.blade.php`, `403`, `500` (autocontenidas).
- Favicon propio (`public/favicon.svg`) enlazado en el layout.
- CSS mejorado (`resources/css/app.css`): cards de producto con hover y imagen uniforme (`product-image`, 260px, `object-fit: cover`), clases de empty state, foco visible para accesibilidad y badge de estado.
- Catálogo: overlay "Agotado" sobre la imagen, empty state con icono y CTA "Ver todos los productos".
- Carrito: imagen con `object-fit`, resumen ordenado (subtotal → IVA → envío → total → botón), empty state con CTA.
- Historial de pedidos: badges de color por estado (iguales a los del detalle), empty state con CTA.
- "Vistos recientemente": cards reutilizan las clases del catálogo.
- `npm run build` verificado y suite completa en verde (41/41).

### FASE 5 — COMPLETADA ✅ (10/08/2026)

- `php artisan test` → **41/41 PASS (129 assertions)** tras la instalación limpia (`migrate:fresh --seed`).
- Nuevos tests: `AccountTest` (detalle propio, 403 ajeno, invitado redirigido, perfil, email único), `RecentProductsTest` (cookie con ids, acumulación, máximo 5), `CheckoutIdempotencyTest` (doble POST, token inválido), `ReportTest` (403 no-admin, 200 PDF admin, invitado redirigido), throttle login (429), producto inactivo en carrito.
- Verificación real por HTTP: `/reportes`, `/reportes/pedidos|ventas|productos` (200 + `application/pdf`), `/mi-cuenta/pedidos` y detalle de pedido (200).

### FASE 6 — COMPLETADA ✅ (10/08/2026)

- README completo con instalación, credenciales, flujo funcional, reportes, pruebas, seguridad y estructura.
- `docs/MANUAL.md`: manual de usuario (cliente y administrador) con solución de problemas.
- `docs/TECNICA.md`: documentación técnica (arquitectura, modelos y relaciones, rutas, carrito, checkout/transacción, seguridad, cookies, reportes, Filament, seeders, decisiones de diseño).
- `docs/diagrama-uso-compra.md`: diagrama Mermaid del proceso de compra con descripción del flujo.
- Enlaces a `docs/` añadidos al README.

### FASE 7, 8 — COMPLETADAS ✅ (10/08/2026)

- FASE 7: `docs/DEPLOYMENT.md` — preparación del servidor, `.env` de producción (`APP_DEBUG=false`, `APP_URL`, `SESSION_SECURE_COOKIE`), migraciones/seeders, `storage:link`, cachés, Apache/Nginx, HTTPS con Certbot, checklist de producción y notas de seguridad.
- FASE 8: `docs/DEFENSA.md` — guion de demo cliente y admin, partes difíciles de explicar (transacción, idempotencia, precios desde BD, cookies, Shield), decisiones de diseño, errores E-01 a E-20, preguntas-respuesta de la docente y código que domina cada integrante.

### Nuevos hallazgos de la integración

- E-17: `OrderSeeder` no era idempotente (crea 3 pedidos por ejecución). **Resuelto**: verifica `orders()->exists()` antes de crear.
- E-18: página Filament "Reportes" visible para autenticados al panel; el controlador ya protege con 403 para no-admin. Opcional: ocultar por rol en el menú.
- E-19 (informativo): vistas de reportes usan `₡` (soportado por DejaVu Sans; verificado en la generación de PDF).
- E-20 (resuelto): `public/storage` era un junction hacia otra carpeta (`TiendaVirtual`); se reenlazó a `storage/app/public`.

### Limpieza final (10/08/2026, post FASE 8)

- **E-11:** `UserForm` — `password` solo `required` en create; en edit es opcional (`dehydrated(filled)` + helperText). No se sobreescribe la contraseña al guardar.
- **E-12/E-13:** eliminado código muerto — `welcome.blade.php`, `Orders/Pages/CreateOrder.php`, `Payments/Pages/CreatePayment.php` (no registrados en `getPages`).
- **E-15:** `.env.example` en español (`es`/`es_CR`) con instrucción de crear `database/database.sqlite`.
- **E-18:** `Reports::shouldRegisterNavigation()` muestra "Reportes" en el menú Filament solo a `super_admin`; el controlador mantiene el 403.
- Verificación: 41/41 PASS (129 assertions), `route:list --path=admin` OK, panel arranca.
