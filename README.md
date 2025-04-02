# CREDENCIALES




# AR MINISTRIES - GRUPOS PEQUEÑOS

Requiere previamente instalados.

- PHP v7.4
- Node v16.16.0
- Composer v2.3.10
- Laravel v8.x

## Instucciones de Instalación

### Instalación de dependencias

Instalacion de dependencias de Composer v2

```
composer install
```

Instalacion de dependencias NPM

```
npm install
```

### Docker (Opcional)

Permisos para Docker
```
sudo chown $USER:docker /var/run/docker.sock
```

Levantar contenedores
```
docker compose up
```

Ingresar al contenedor
```
docker exec -it ar-gp-v2 /bin/bash
```

### Generar .env

Crear archivo en la raíz con nombre ".env" copiar plantilla y rellenar campos necesarios de la DB.

```
APP_NAME=GRUPOS_PEQUENOS
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# MAIL_DRIVER=smtp
# MAIL_HOST=mail.arministriesgp.com
# MAIL_PORT=465
# MAIL_USERNAME=gp@arministriesgp.com
# MAIL_PASSWORD=superusuario2020
# MAIL_ENCRYPTION=ssl

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## Preparación de datos

### Insertar Data

Con el script de la data de SQL ejecutar

```
mysql -u <usuario> -p <data_base> < <archivo_con_data>.sql

```

### Actualización de contraseñas

El siguiente comando cambiara las contraseñas de todos los usuarios del sistema a "123456789" y generará como administrador al correo "admin@admin.cl"

```
php artisan reset:passwords
```

## Ejecución del proyecto

Para levantar el servidor de laravel

```
php artisan serve
```

Para ver los cambios en vivo de los elementos Vue

```
npm run watch
```

mantener ambos al mismo tiempo.

### AHORA A PROBAR...
