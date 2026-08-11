# Diagrama de Caso de Uso — Proceso de Compra (StreetWear CR)

Fuente editable (Mermaid). Puedes renderizarla en GitHub (vista previa),
mermaid.live o VSCode con la extensión de Mermaid.

## Diagrama

```mermaid
graph LR
    subgraph Cliente
        U["Cliente (usuario registrado o invitado)"]
    end

    subgraph Sistema ["Tienda StreetWear CR"]
        UC1["Consultar catálogo"]
        UC2["Buscar / filtrar productos"]
        UC3["Ver detalle de producto"]
        UC4["Agregar producto al carrito"]
        UC5["Modificar cantidades del carrito"]
        UC6["Ver resumen (subtotal, IVA, envío, total)"]
        UC7["Realizar checkout"]
        UC8["Ingresar dirección de envío"]
        UC9["Seleccionar método de pago"]
        UC10["Confirmar pedido"]
        UC11["Obtener número de seguimiento"]
        UC12["Consultar historial de pedidos"]
        UC13["Ver detalle de un pedido propio"]
        UC14["Iniciar sesión"]
        UC15["Registrarse"]
    end

    U --> UC1
    U --> UC2
    U --> UC1
    U --> UC3
    U --> UC4
    U --> UC5
    U --> UC6
    UC4 --> UC6
    UC7 --> UC14
    UC15 --> UC14
    UC14 --> UC7
    UC7 --> UC8
    UC8 --> UC9
    UC9 --> UC10
    UC10 --> UC11
    UC11 --> UC12
    UC12 --> UC13

    UC6 --> UC7
```

## Descripción breve del flujo

1. El cliente consulta el catálogo y puede **buscar o filtrar** productos
   por nombre, categoría y rango de precio.
2. Abre el **detalle** de un producto y lo **agrega al carrito**.
3. En el carrito **modifica cantidades** y revisa el **resumen** (subtotal,
   IVA 13 %, envío y total).
4. Para **realizar el checkout** debe estar autenticado (registrarse o
   iniciar sesión).
5. Ingresa la **dirección de envío** y selecciona el **método de pago**
   (tarjeta o PayPal, modo demostración).
6. Al **confirmar el pedido** se genera el **número de seguimiento** y el
   carrito queda vacío.
7. Desde su cuenta puede **consultar el historial** y **ver el detalle** de
   cada pedido propio (estado, productos, pago y seguimiento).

## Actores

| Actor | Descripción |
|---|---|
| **Cliente** | Cualquier persona que navega, compra y consulta sus pedidos. |
| **Sistema** | Ejecuta la compra, valida stock, genera tracking y registra el pago. |
| **Administrador** (relacionado, ver MANUAL.md) | Consulta y cambia estados de pedidos, consulta pagos y descarga reportes PDF. |
