# Documentación Técnica — StreetWear CR

> Proyecto universitario de tienda virtual. Este documento describe la
> arquitectura, los modelos, las rutas, la seguridad y las decisiones de
> diseño para que cualquier integrante pueda defender el sistema.

---

## 1. Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 (PHP 8.2) |
| Base de datos | SQLite |
| Frontend | Bootstrap 5, JavaScript, Vite |
| Panel administrativo | Filament 5 |
| Roles y permisos | Spatie Laravel Permission + Filament Shield |
| Reportes PDF | barryvdh/laravel-dompdf |
| Autenticación | Sesiones de Laravel (sin paquete externo) |

---

## 2. Arquitectura general

El sistema sigue la arquitectura MVC clásica de Laravel:

```
Navegador → rutas/web.php → Controlador → Modelo → BD
                    └──────── Vista (Blade) ←────────┘
```

Flujo principal de una compra:

1. El cliente navega el catálogo (`ProductController`).
2. Agrega productos al carrito, que vive en **sesión** (`CartController`).
3. En el checkout se validan los datos de envío y el método de pago
   (`CheckoutController`).
4. Dentro de una **transacción de BD** se crean `Order`, `OrderItem` y
   `Payment`, se descuenta stock y se vacía el carrito.
5. El cliente ve la confirmación con el **número de seguimiento**.

---

## 3. Modelos y relaciones

```
User 1───* Order 1───* OrderItem
              │
              └───1 Payment
```

| Modelo | Tabla | Relaciones |
|---|---|---|
| `User` | `users` | `hasMany(Order)`; roles Spatie |
| `Product` | `products` | `belongsTo(Category)` |
| `Category` | `categories` | `hasMany(Product)` |
| `Order` | `orders` | `belongsTo(User)`, `hasMany(OrderItem)`, `hasOne(Payment)` |
| `OrderItem` | `order_items` | `belongsTo(Order)`, `belongsTo(Product)` (nullable, `nullOnDelete`) |
| `Payment` | `payments` | `belongsTo(Order)` (uno a uno) |

### Campos importantes

- **`orders`**: `tracking_number` (único, generado automáticamente),
  `status`, `subtotal`, `tax`, `shipping`, `total`, `shipping_address`.
- **`order_items`**: guarda `product_name` y `price` **copiados** en el
  momento de la compra. Así el historial es estable aunque cambie el
  catálogo después.
- **`payments`**: `method` (`card` | `paypal`), `status`,
  `transaction_id` (único), `amount`, `paid_at`.
  **No se almacenan números de tarjeta ni credenciales** (solo el método).

### Estados permitidos (whitelist)

Definidos como constantes en los modelos:

- `Order::STATUSES` → `pending`, `processing`, `shipped`, `delivered`,
  `cancelled`.
- `Payment::STATUSES` → `pending`, `paid`, `failed`, `refunded`.

---

## 4. Rutas principales (`routes/web.php`)

| Método | URL | Nombre | Acceso |
|---|---|---|---|
| GET | `/` | — | Redirige a `/productos` |
| GET | `/productos` | `products.index` | Público |
| GET | `/productos/{product}` | `products.show` | Público |
| GET | `/carrito` | `cart.index` | Público |
| POST | `/carrito/agregar/{product}` | `cart.add` | Público |
| PUT | `/carrito/actualizar/{product}` | `cart.update` | Público |
| DELETE | `/carrito/eliminar/{product}` | `cart.remove` | Público |
| GET/POST | `/registro` | `register`, `register.store` | Invitados |
| GET/POST | `/login` | `login`, `login.store` | Invitados (POST con `throttle:5,1`) |
| POST | `/logout` | `logout` | Autenticado |
| GET | `/mi-cuenta` | `account.dashboard` | Autenticado |
| GET/PUT | `/mi-cuenta/perfil` | `account.profile`, `account.profile.update` | Autenticado |
| GET | `/mi-cuenta/pedidos` | `account.orders` | Autenticado |
| GET | `/mi-cuenta/pedidos/{order}` | `account.orders.show` | Autenticado (solo propietario) |
| GET/POST | `/checkout` | `checkout.index`, `checkout.store` | Autenticado |
| GET | `/checkout/confirmacion/{order}` | `checkout.success` | Autenticado |
| GET | `/reportes*` | `reports.*` | Solo `super_admin` |

El panel administrativo Filament vive en `/admin`.

---

## 5. Carrito (sesión)

- El carrito se guarda en `session('cart')` como un arreglo de ítems con
  `id`, `name`, `price`, `image` y `quantity`.
- `CartController::index` agrupa y calcula subtotal, IVA (13 %), envío fijo
  y total con la clase `CartCalculator`.
- Al agregar, si el producto ya está en el carrito, se **incrementa** la
  cantidad; el límite es el **stock disponible** de la BD.
- `cart.update` valida que el producto siga activo y que la cantidad no
  supere el stock.
- `cart.remove` elimina el ítem. Si el carrito queda vacío, se limpia.

---

## 6. Checkout y transacción

`CheckoutController::store` ejecuta este flujo **dentro de una
transacción** (`DB::transaction`):

1. Si el carrito está vacío, redirige al carrito (sin tocar BD).
2. Valida `shipping_address` (obligatoria) y `payment_method`
   (`card` o `paypal`).
3. Valida el **token de idempotencia** (`checkout_token`) con
   `session()->pull()` + `hash_equals`. Evita pedidos duplicados por doble
   envío del formulario (F5 o doble clic).
4. Recorre el carrito y **recalcula precios desde la BD**
   (`Product::lockForUpdate()`), no desde la sesión.
5. Verifica stock suficiente; si falta, revierte y rechaza con mensaje.
6. Crea `Order` (con `tracking_number` único), sus `OrderItem` y el
   `Payment`.
7. Descuenta el stock de cada producto.
8. Vacía el carrito y redirige a la confirmación.

### Cálculos

- Subtotal = suma de `price × quantity` (precios de BD).
- IVA = 13 % del subtotal.
- Envío = valor fijo (constante).
- Total = subtotal + IVA + envío.
- `OrderItem.subtotal` = `price × quantity`.
- `Payment.amount` = total del pedido.

---

## 7. Seguridad aplicada

| Medida | Dónde |
|---|---|
| CSRF en todos los formularios | `@csrf` en Blade |
| Escapado de salida | Blade `{{ }}` (evita XSS) |
| Validación de formularios | Reglas en los controladores |
| Rate limit de login | `throttle:5,1` en `POST /login` |
| Idempotencia de checkout | `checkout_token` en sesión |
| Bloqueo de stock | `lockForUpdate()` dentro de la transacción |
| Acceso a pedidos propios | `abort_unless($order->user_id === auth()->id(), 403)` |
| Filtros del catálogo saneados | Categoría con `exists`, precios numéricos ≥ 0 |
| Roles | Spatie + Shield (`super_admin`, `customer`) |
| Contraseñas | Hash de Laravel (bcrypt por defecto) |
| Estados restringidos | Whitelist de constantes en modelos |
| Sin datos de tarjeta | Solo método de pago en BD |

---

## 8. Productos vistos recientemente (cookies)

- Al ver un producto, `ProductController::show` registra su id en una
  cookie llamada `recent_products` (JSON).
- La cookie guarda hasta **5 productos** y expira a los **30 días**.
- Laravel encripta las cookies automáticamente
  (`EncryptCookies` middleware); el controlador y los tests descifran con
  `Crypt::decrypt` + `CookieValuePrefix::remove`.

---

## 9. Reportes PDF

`ReportController` (protegido con `abort_unless` rol `super_admin`):

- `/reportes/pedidos` → lista de pedidos con filtros (fechas, estado,
  cliente).
- `/reportes/ventas` → totales, ventas por mes y por cliente.
- `/reportes/productos` → cantidad vendida y total generado por producto.

Los PDFs se generan con DomPDF desde vistas Blade de `resources/views/reports/`.

---

## 10. Panel administrativo (Filament)

- `/admin` → panel Filament.
- Recursos: productos, categorías, usuarios, pedidos, pagos y roles
  (`RoleResource` de Shield en `AdminPanelProvider`).
- Pedidos y pagos tienen formularios con **selects de estado** que usan las
  constantes `Order::STATUSES` / `Payment::STATUSES`.
- Página personalizada "Reportes" para descargar PDFs.

---

## 11. Seeders y datos de ejemplo

- `UserSeeder`: crea `admin@streetwearcr.test` (`super_admin`) y
  `cliente@streetwearcr.test` (`customer`). Contraseñas: `Admin12345` y
  `Cliente12345`.
- `CategorySeeder` y `ProductSeeder`: 5 productos con imágenes SVG de
  ejemplo en `storage/app/public/products/seed/`.
- `OrderSeeder`: 3 pedidos de ejemplo con sus items y pagos. Es
  **idempotente** (no duplica pedidos si ya existen).

---

## 12. Decisiones de diseño

1. **Carrito en sesión**: simple, sin tablas, adecuado para el alcance del
   proyecto. El checkout recalcula contra la BD para evitar inconsistencias.
2. **`order_items` con copia de nombre/precio**: el historial no cambia si
   el catálogo se modifica después.
3. **Envío fijo**: regla de negocio simple y explicable.
4. **Estados en whitelist**: evita valores inválidos desde el panel o el
   código.
5. **Cookie de 5 productos / 30 días**: muestra recientes sin requerir
   cuenta ni BD extra.
6. **Pago en modo demostración**: no se integra pasarela real ni se
   almacenan datos de tarjeta.
7. **SQLite**: cero configuración de servidor, ideal para desarrollo,
   pruebas y demo portátil.
