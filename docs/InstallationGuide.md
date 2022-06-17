# Guía de instalación

## Requisitos mínimos
El sistema donde se vaya a realizar la instalación debe cumplir los siguientes requisitos:
- PHP 8.1+
- Composer
- Docker

## Instalación

En la carpeta donde se vaya a desarrollar el proyecto, instalamos todos los archivos y dependencias.

``` bash
composer create-project medaqueno/boilerwork <ruta-a-carpeta>
```

Posteriormente en el interior de la carpeta `docker` que se habrá creado ejecutamos:

``` bash
# Recomendado para ver la salida del contenedor directamente en el terminal.
docker compose up
```

Este proceso levantará dos contenedores docker:
1. Aplicación php.
2. PostgreSQL.
    -Las credenciales para conectarse a la base de datos de desarrollo por defecto se encuentra en el archivo **.env** en la raíz del proyecto. El host es 127.0.0.1
