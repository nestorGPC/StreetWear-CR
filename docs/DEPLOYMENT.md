# Guía de Despliegue (Deployment) — StreetWear CR

> Documento de referencia para llevar el proyecto a producción con HTTPS.
> La decisión de host/dominio la toma el equipo (criterios 54 y 55 de la
> rúbrica).

---

## 1. Requisitos en el servidor

- PHP >= 8.2 con extensiones: `pdo_sqlite`, `sqlite3`, `gd`, `mbstring`,
  `xml`, `curl`, `openssl`, `zip` (DomPDF requiere `mbstring` y `xml`).
- Composer.
- Node.js + npm (solo para compilar el frontend; luego no hacen falta).
- Apache o Nginx con reescritura hacia la carpeta `public/`.

---

## 2. Preparar los archivos

Subir el proyecto al servidor (por ejemplo en `/var/www/streetwear` o la
raíz del host):

1. **Copiar el código** (o `git clone`).
2. **Variables de entorno:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Editar `.env` con valores de **producción**:

   ```ini
   APP_NAME="StreetWear CR"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://midominio.com

   SESSION_SECURE_COOKIE=true
   ```

   > `APP_DEBUG=false` **siempre** en producción: nunca expongas errores o
   > credenciales a los visitantes.

3. **Dependencias:**

   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

   (`npm` y `node_modules` no son necesarios después del build).

4. **Base de datos:**

   ```bash
   touch database/database.sqlite
   php artisan migrate --force
   php artisan db:seed --force
   ```

   > Los seeders crean las cuentas demo y los datos de ejemplo. Si no
   > quieres datos demo en producción, omite `db:seed` y crea los usuarios
   > manualmente desde el panel.

5. **Enlace de almacenamiento** (imágenes):

   ```bash
   php artisan storage:link
   ```

6. **Permisos:**

   ```bash
   chmod -R 775 storage bootstrap/cache
   chmod -R 775 database          # SQLite necesita escribir
   ```

7. **Cachés de producción:**

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

   > Después de estos comandos, cada cambio en configuración/rutas requiere
   > repetir `php artisan config:clear` + volver a cachear.

---

## 3. Apuntar el servidor web a `public/`

### Apache

El `.htaccess` ya incluido en `public/` redirige las peticiones al
`index.php`. El `DocumentRoot` debe apuntar a la carpeta `public/` del
proyecto, no a la raíz (por seguridad).

```apache
DocumentRoot "/var/www/streetwear/public"
<Directory "/var/www/streetwear/public">
    AllowOverride All
    Require all granted
</Directory>
```

### Nginx

```nginx
server {
    listen 80;
    server_name midominio.com;
    root /var/www/streetwear/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. HTTPS (criterio 54)

**Opción recomendada: Let's Encrypt con Certbot** (gratis):

```bash
sudo apt install certbot python3-certbot-apache   # o -nginx
sudo certbot --apache -d midominio.com -d www.midominio.com
```

Certbot configura la renovación automática y el redireccionamiento
HTTP → HTTPS.

Verificar que redirige:

```bash
curl -I http://midominio.com   # debe responder 301/308 a https
```

Con HTTPS activo:

- `APP_URL` usa `https://`.
- `SESSION_SECURE_COOKIE=true` para que la cookie de sesión solo viaje por
  HTTPS.
- Los assets (`asset()`) se generan con `https` porque `APP_URL` es https.

---

## 5. Checklist de producción

- [ ] `APP_ENV=production` y `APP_DEBUG=false`.
- [ ] `APP_URL` apunta al dominio real.
- [ ] `php artisan config:cache`, `route:cache`, `view:cache`.
- [ ] `php artisan storage:link` ejecutado (imágenes visibles).
- [ ] Certificado SSL activo y redirección HTTP→HTTPS.
- [ ] `SESSION_SECURE_COOKIE=true`.
- [ ] Migraciones y seeders ejecutados; BD con permisos de escritura.
- [ ] `.env` de producción **no** está en el repositorio (está en
  `.gitignore`).
- [ ] Sin tokens, claves ni credenciales reales de pago en el código o la BD.
- [ ] `php artisan test` en verde en un entorno de prueba antes del deploy.

---

## 6. Notas de seguridad en producción

- No uses el correo `admin@streetwearcr.test` con la contraseña demo en un
  despliegue real; cambia las credenciales del administrador.
- El panel Filament (`/admin`) debe protegerse con contraseña fuerte.
- El pago sigue en **modo demostración**; si se integra una pasarela
  sandbox/real, las llaves van en `.env` (nunca en el repositorio).
- Mantén los logs (`storage/logs`) protegidos y revisa periódicamente.
