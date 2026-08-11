# Manual de Usuario — StreetWear CR

Guía de uso de la tienda virtual para clientes y administradores.

---

## Accesos de ejemplo

| Rol | Correo | Contraseña |
|---|---|---|
| Cliente | `cliente@streetwearcr.test` | `Cliente12345` |
| Administrador | `admin@streetwearcr.test` | `Admin12345` |

Tienda: `http://localhost/StreetWear%20CR/public` (XAMPP) o la URL del
proyecto. Panel administrativo: añade `/admin` a la URL.

---

## Parte 1 — Cliente

### 1. Registrarse

1. Abre la tienda y haz clic en **Cuenta → Crear cuenta**.
2. Escribe tu nombre completo, correo electrónico y contraseña
   (mínimo 8 caracteres), y confírmala.
3. Pulsa **Crear cuenta** y quedas con sesión iniciada.

### 2. Iniciar y cerrar sesión

- **Iniciar sesión:** Cuenta → Iniciar sesión. Ingresa tu correo y
  contraseña.
- **Cerrar sesión:** en la esquina superior derecha, abre tu menú
  (tu nombre) y pulsa **Cerrar sesión**.

### 3. Editar perfil

1. En tu menú, elige **Mi perfil**.
2. Modifica tu nombre o correo y pulsa **Guardar cambios**.

### 4. Consultar productos

- El catálogo se muestra en la página principal.
- Usa la caja **Buscar productos** para buscar por nombre, filtrar por
  categoría o por rango de precio, y pulsa **Buscar**.
- Pulsa **Limpiar filtros** para volver a ver todo.

### 5. Ver detalle de producto

- Haz clic en **Ver producto** en cualquier tarjeta.
- Verás la imagen, precio, descripción, disponibilidad y el botón
  **Agregar al carrito**.
- Debajo aparecen tus **productos vistos recientemente** (máximo 5).

### 6. Usar el carrito

1. Pulsa **Agregar al carrito** en un producto.
2. Entra al carrito desde el icono del carrito (el contador rojo muestra la
   cantidad de artículos).
3. Puedes **cambiar la cantidad** (pulsa Actualizar) o **Eliminar** un
   producto.
4. El resumen muestra **Subtotal, IVA (13 %), Envío y Total**.
5. Pulsa **Continuar con la compra** para ir al checkout.

### 7. Realizar el checkout

1. Inicia sesión (es obligatorio).
2. Revisa tus datos de cliente.
3. Escribe la **dirección de envío** completa
   (provincia, cantón, distrito y señas).
4. Selecciona el **método de pago**: Tarjeta de crédito/débito o PayPal.
   > El pago está en **modo demostración**: no introduzcas números reales
   > de tarjeta.
5. Revisa el resumen del pedido y pulsa **Confirmar pedido**.

### 8. Confirmación y número de seguimiento

- Al confirmar verás la pantalla **"¡Pedido recibido!"** con el
  **número de seguimiento** (formato alfanumérico único).
- Guárdalo: lo usarás para consultar tu pedido.

### 9. Consultar el historial de pedidos

1. En tu menú, elige **Mis pedidos**.
2. Verás cada pedido con su número, fecha, estado (badge de color) y total.
3. Pulsa **Ver detalle** para ver dirección, productos, resumen,
   estado del pago y seguimiento.

### 10. Productos vistos recientemente

- Al visitar productos, la tienda guarda en tu navegador una cookie con los
  últimos 5 vistos (durante 30 días) para mostrarlos en cada detalle.

---

## Parte 2 — Administrador

### 11. Acceder al panel

- Inicia sesión con la cuenta de administrador.
- Desde tu menú, pulsa **Panel administrativo**, o entra directamente a
  `/admin`.

### 12. Administrar productos y stock

1. En el menú lateral elige **Productos**.
2. Pulsa **Nuevo producto** para crear uno (nombre, categoría, precio,
   stock, imagen, descripción).
3. Puedes **editar** un producto o **desactivarlo**; desactivar lo quita
   del catálogo.
4. El **stock** se reduce automáticamente con cada venta. Si un producto
   llega a 0, se muestra como "Agotado".

### 13. Administrar categorías

- En **Categorías** puedes crear, editar o eliminar categorías para
  organizar el catálogo.

### 14. Administrar usuarios

- En **Usuarios** consulta los clientes registrados, edita sus datos y
  asigna el rol (`customer` o `super_admin`).
- Los **roles y permisos** se gestionan en el recurso de **Roles**
  (integrado con Filament Shield).

### 15. Consultar y actualizar pedidos

1. En **Pedidos** verás todos los pedidos con su estado, cliente, total y
   seguimiento.
2. Abre un pedido y cambia su **estado** en el formulario:
   - Pendiente → En preparación → Enviado → Entregado
   - Cancelado (si aplica).
3. El cliente verá el nuevo estado en su historial.

### 16. Consultar pagos

- En **Pagos** consulta cada pago: método, estado, monto y referencia.
- Estados posibles: Pendiente, Pagado, Fallido, Reembolsado.
- No verás datos de tarjeta: el sistema no los almacena.

### 17. Generar reportes PDF

1. En el panel, abre la página **Reportes**.
2. Filtra por rango de fechas, estado o cliente (opcional).
3. Pulsa uno de los botones:
   - **Descargar reporte de pedidos**
   - **Descargar reporte de ventas** (totales, por mes y por cliente)
   - **Descargar reporte de productos vendidos**
4. Se descarga un archivo PDF con la información.

---

## Solución de problemas frecuentes

| Problema | Solución |
|---|---|
| La página no carga estilos | Ejecuta `npm install` y `npm run build` en la raíz del proyecto. |
| No se ven las imágenes de productos | Ejecuta `php artisan storage:link`. |
| "La sesión de compra expiró" al confirmar | El formulario de checkout se reenvió dos veces o pasó demasiado tiempo; abre el carrito e inicia el checkout de nuevo. |
| No puedo ver un pedido | Solo el propietario del pedido puede verlo; revisa que hayas iniciado sesión con la cuenta correcta. |
| Login con demasiados intentos | Hay un límite de 5 intentos por minuto; espera un momento e inténtalo de nuevo. |
