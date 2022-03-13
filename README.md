# Docker: PHP & MySQL & Laravel
El proyecto tiene como objetivo instalar rápidamente un ambiente de desarrollo local que pueda ser utilizado como base para proyectos. A continuación, se detalla el Stack.

* Servidor Web: Apache y PHP 8 de la imagen de Docker php:8-apache (por defecto)
* Node Versión 14.16.0
* Composer
* [Laravel 8](https://laravel.com/docs/8.x/)

Base de Datos: [MySQL 8.0](https://www.mysql.com/)

## Requerimientos
* [Docker Desktop](https://www.docker.com/products/docker-desktop)

## Configurar el ambiente de desarrollo

A continuación, se explica brevemente la configuración a realizar en el archivo docker/.env dependiendo del proyecto:

* `COMPOSE_PROJECT_NAME`: El nombre al stack de contenedores que se generarán.

* `WEB_WERVER_NAME`: El nombre que le daremos al contenedor del servidor web

* `PHP_VERSION` versión de PHP ([Versiones disponibles de PHP](https://github.com/docker-library/docs/blob/master/php/README.md#supported-tags-and-respective-dockerfile-links)).

* `DB_SERVER_NAME`: El nombre que tendrá el contenedor del Mysql

* `MYSQL_DATABASE`: El Nombre de la Base de Datos a Crear.


Adicionalmente se pueden modificar otros parámetros, como ser:
* `PHP_PORT` puerto para servidor web.
* `MYSQL_VERSION` versión de MySQL([Versiones disponibles de MySQL](https://hub.docker.com/_/mysql)).
* `MYSQL_USER` nombre de usuario para conectarse a MySQL.
* `MYSQL_PASSWORD` clave de acceso para conectarse a MySQL.

## Estructura de Archivos

* `/docker/mysql` contiene el docker file de MySQL y la carpeta Data.
* `/docker/web-server` contiene el docker file de PHP.
* `/docker/web-server/config` contiene los directorios de configuración de apache y php. Estan los conf de apache y el .ini de php. Estos archivos se copian al contenedor al momento de generarlos. Si se modifican, será necesario regenerar los contenedores.
* `/www/` carpeta para los archivos Laravel del proyecto.

## Instalar el ambiente de desarrollo

### Levantar los contenedores
Ingresar a la carpeta docker y ejecutar desde la línea de comandos:
`docker-compose up -d --build`

### Configurar el .env
* Copiar el archivo www/.env.example a un archivo /www/.env.
* Configurar el .env. Al menos las opciones APP_ y DB_.
* Como nos encontramos dentro del stack de docker, en DB_HOST se puede ingresar el _nombre del contenedor y el puerto real_ (no el publicado en el servidor).
  Ej:
  `DB_CONNECTION=mysql`
  `DB_HOST=db`
  `DB_PORT=3306`
  `DB_DATABASE=laravel`
  `DB_USERNAME=user`
  `DB_PASSWORD=1234`

### Ejecutar Comandos desde el contenedor.
Una vez instalado y levantado los contenedores, ingresar al conenedor y ejecutar los siguientes comandos:
* `composer install`: Descarga las dependencias de la carpeta vendor que no forman parte del repositorio. Ignora la versión de PHP
* `php artisan key:generate`: Genera la App Key en el .env
* `php artisan storage:link`: Genera el Link Simbólico
* `php artisan migrate`: Crea las Tablas Básicas del Sistema

#### Permisos
Si se ejecutaron los comandos desde la consola del contenedor, es probable que tengamos problemas de permisos. Otorgar acceso al www-data
Desde el terminal de la máquina ejecutar desde el www:
* sudo chown [owner]:www-data storage -R
* sudo chmod 775 storage -R
* sudo chown www-data:www-data bootstrap/cache -R