# Preparación de Defensa — StreetWear CR

> Guion para que cada integrante pueda defender el proyecto. Incluye las
> partes difíciles de explicar, las decisiones de diseño, los errores
> encontrados y posibles preguntas de la docente con respuestas.

---

## 1. Demo de 5 minutos (flujo cliente)

1. Abrir el catálogo (mencionar búsqueda y filtros por categoría/precio).
2. Abrir un producto y mostrar **"Vistos recientemente"** (cookies).
3. Agregar al carrito y cambiar cantidad. Señalar el **contador** del navbar
   y el **resumen** (subtotal, IVA 13 %, envío ₡3000, total).
4. Checkout: iniciar sesión como cliente, dirección de envío, método de
   pago (tarjeta/PayPal, modo demo), **Confirmar pedido**.
5. Mostrar la pantalla de confirmación con el **número de seguimiento**.
6. Verificar que el carrito quedó vacío y que el stock bajó.
7. Abrir **Mis pedidos → Ver detalle** (estado, productos, pago).

## 2. Demo de 3 minutos (flujo administrador)

1. Cerrar sesión e iniciar como administrador.
2. Entrar a `/admin` (Filament).
3. Abrir **Pedidos**, cambiar el estado de un pedido a "Enviado".
4. Mostrar **Pagos** (método, estado, monto; sin datos de tarjeta).
5. Abrir **Reportes** y descargar un PDF de ventas.
6. Mencionar que también gestiona productos, stock, categorías y usuarios.

---

## 3. Partes difíciles de explicar

### Checkout con transacción

En `CheckoutController::store` todo se envuelve en `DB::transaction`:

```php
$order = DB::transaction(function () use ($request, $data, $cart) {
    // validar productos con lockForUpdate, precios desde BD
    // crear Order + OrderItems + Payment
    // decrementar stock
});
```

**Por qué:** si algo falla a mitad (p. ej. stock insuficiente), Laravel
revierte todos los cambios: no queda un pedido sin pago ni stock descontado
sin pedido. Todo o nada.

### Token de idempotencia (evitar pedidos duplicados)

- `checkout.index` genera un token aleatorio y lo guarda en sesión.
- `checkout.store` lo recibe del formulario y lo valida con
  `session()->pull()` + `hash_equals`.
- Si el usuario hace doble clic en "Confirmar" o refresca, el token ya se
  consumió → se rechaza con "La sesión de compra expiró".
- `session()->pull` elimina el token al leerlo, para que **no se pueda
  reutilizar**.

### Precios desde BD, no desde sesión

- El carrito vive en sesión (rápido y sin BD), pero al confirmar la compra
  se **recalcula todo con el precio actual del producto en la BD**
  (`Product::lockForUpdate()->find(...)`).
- Así, si el admin subió un precio después de que el cliente agregara el
  producto, el pedido cobra el precio real de la BD.

### Cookies de vistos recientemente

- Al ver un producto se guarda su id en una cookie `recent_products`.
- Máximo 5 productos, expira a los 30 días.
- Las cookies de Laravel van **encriptadas** por el middleware
  `EncryptCookies`, por eso en los tests se descifran con
  `Crypt::decrypt` + `CookieValuePrefix::remove`.

### Permisos Spatie/Shield

- `UserSeeder` asigna roles `super_admin` y `customer` con Spatie.
- Filament Shield genera las políticas y permite administrar roles por UI
  (`RoleResource` registrado en `AdminPanelProvider`).
- Reportes y panel: `abort_unless(auth()->user()->hasRole('super_admin'), 403)`.

---

## 4. Decisiones de diseño

| Decisión | Motivo |
|---|---|
| Carrito en sesión | Simple, sin tablas; el checkout valida contra BD. |
| `order_items` guarda nombre/precio copiados | El historial no cambia aunque cambie el catálogo. |
| IVA fijo 13 % | Impuesto de ventas en Costa Rica, fácil de explicar. |
| Envío fijo ₡3000 | Regla de negocio simple (podría hacerse por zona después). |
| SQLite | Cero configuración, ideal para desarrollo/demo/defensa. |
| Pago en modo demo | Sin pasarela real, sin almacenar datos de tarjeta. |
| Cookie 5 productos / 30 días | Recientes sin necesidad de cuenta ni BD extra. |
| Estados en whitelist (constantes) | Evita valores inválidos en pedidos y pagos. |
| Tracking `SWCR-Ymd-XXXXXX` | Formato legible, único (se valida contra BD). |

---

## 5. Errores encontrados y soluciones (E-01 a E-20)

- **E-01** `public/build/manifest.json` faltaba → `npm run build`.
- **E-02/E-07** historial de pedidos inexistente/filtros sin validar →
  implementado en FASE 1 y saneado en FASE 3.
- **E-03** precios de sesión vs BD → recalculo desde BD en la transacción.
- **E-04** carrito aceptaba producto inactivo → `cart.update` lo rechaza.
- **E-05** stock sin bloqueo → `lockForUpdate()` dentro de la transacción.
- **E-06** login sin rate limit → `throttle:5,1`.
- **E-08** productos sin imagen → SVGs placeholder en el seeder.
- **E-09** roles no administrables por UI → `RoleResource` de Shield.
- **E-10** `Str::limit` con descripción NULL → `?? ''`.
- **E-14** producto duplicado con typo en BD → limpieza de datos.
- **E-17** `OrderSeeder` duplicaba pedidos → idempotente
  (verifica `orders()->exists()`).
- **E-18** página "Reportes" visible para no-admin → oculta del menú con `shouldRegisterNavigation()` (solo `super_admin`), y el controlador mantiene el 403.
  (opcional ocultar por rol en el menú).
- **E-19** `₡` en PDFs → soportado por la fuente DejaVu Sans de DomPDF.
- **E-20** `public/storage` apuntaba a otra carpeta → `php artisan storage:link` reejecutado.

---

## 6. Posibles preguntas de la docente (con respuesta corta)

**¿Por qué SQLite?**
Porque no requiere instalar ni configurar un servidor de BD; el archivo es
portátil y suficiente para el alcance académico. En producción se puede
cambiar a MySQL cambiando `DB_CONNECTION` en `.env`.

**¿Cómo se evita SQL Injection?**
Todo el acceso a datos usa el query builder/ORM de Eloquent con `where`
parametrizado; nunca se concatenan valores del usuario en SQL. Los filtros
se validan (`category` con `exists`, precios numéricos).

**¿Cómo se evita XSS?**
Blade escapa automáticamente con `{{ }}`; los datos de usuario nunca se
imprimen con `{!! !!}`.

**¿Cómo se protege un pedido ajeno?**
`AccountController::showOrder` y `CheckoutController::success` verifican
`$order->user_id === auth()->id()`, si no, `abort(403)`.

**¿Cómo funciona el IVA y el envío?**
Subtotal = Σ(precio × cantidad) desde BD; IVA = 13 %; envío = ₡3000 fijo
(solo si hay productos); total = subtotal + IVA + envío. Clase
`CartCalculator`.

**¿Por qué una cookie de recientes y cómo expira?**
Para personalizar la tienda sin requerir cuenta. Se guardan hasta 5 ids,
`->withMinutes(60 * 24 * 30)` → 30 días, y se maneja con cookie JSON
encriptada por Laravel.

**¿Qué pasa si se agota el stock durante la compra?**
Dentro de la transacción se compara `product->stock` con la cantidad
pedida; si falta, se lanza `RuntimeException`, se revierte la transacción y
se muestra el mensaje "No hay suficiente inventario...".

**¿Cómo se genera el tracking?**
`generateTrackingNumber()`: `'SWCR-' . fechaYmd . '-' . Str::random(6)`
en mayúsculas; se repite si ya existe en la BD (garantiza unicidad).

**¿Qué es la pasarela sandbox?**
Es el entorno de pruebas de un proveedor de pagos (Stripe/PayPal) que no
cobra dinero real. El plan es sustituir el pago local de demostración por
la respuesta de la pasarela sandbox. **No se almacenan números de tarjeta
ni credenciales** en la BD.

**¿Por qué el carrito está en sesión y no en BD?**
Por rapidez y simplicidad: el cliente puede armar el carrito sin estar
registrado. El riesgo de inconsistencia se mitiga recalculando todo desde
la BD al confirmar.

---

## 7. Código que cada integrante debe dominar

| Integrante | Debe saber explicar |
|---|---|
| **Néstor** | Integración general, checkout (`CheckoutController`), Filament, README/instalación. |
| **Darío** | Backend, pruebas (`tests/Feature`), reportes PDF (`ReportController`), seguridad, seeders, pagos. |
| **Andriy** | Todas las vistas (`resources/views`), navbar, cards, carrito, checkout visual, responsive, accesibilidad. |
