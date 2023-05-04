##  Ejecute los siguientes pasos para ejecutar el ejemplo de manera local

1. Clone el repositorio
```
git clone  https://url_repo.git
```
2. Dentro de la carpeta del proyecto:
```
composer install
```
3. Cree el archivo .env
```
cp .env.example .env
```
Dentro de .env configurar la base de datos para el caso de sqlite
```
DB_CONNECTION=sqlite
DB_DATABASE=/ruta_archivo_base_datos.sqlite
```
**Las demás configuraciones de la base de datos se deben comentar**

Establecer el tiempo máximo de juego en segundos
```
MAX_GAME_TIME = número_deseado
```
Establecer configuración para el uso de caché. En este caso se usó Redis.
```
CACHE_DRIVER=redis
```
**Se debe tener instalado la extensión phpredis si se usa redis**

4. Genere la llave de la app
```
php artisan key:generate
```
5. Ejecute las migraciones
```
php artisan migrate
```
6. Inicie la app
```
php artisan serve
```
7. Consulte el api ejecutando:
http://ip_serv:puerto/api/documentation

