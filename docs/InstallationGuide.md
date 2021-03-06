# Guía de instalación

## Requisitos mínimos
El sistema donde se vaya a realizar la instalación debe cumplir los siguientes requisitos:

- Docker

## Instalación

**Obtener código fuente**
En la carpeta donde se vaya a desarrollar el proyecto, instalamos todos los archivos y dependencias.

Existen dos métodos:

1. Clone Repository

    ``` bash
    git clone https://github.com/medaqueno/boilerwork.git <ruta-a-carpeta> # HTTPS
    ```

2. Composer create. 
Requiere las siguientes dependencias instaladas en el host:
    - PHP 8.1+
    - Composer

    ``` bash
    composer create-project --ignore-platform-reqs medaqueno/boilerwork <ruta-a-carpeta>
    ```
    > Ignoramos las dependencias en este momento ya que las extensiones necesarias serán instaladas posteriormente en el contenedor de Docker.


**Personalizar datos para el proyecto**

En el interior de la carpeta `docker` que se habrá creado encontraremos el archivo docker-compose.yml en el que están señalados por defecto los puertos **4000** para la **aplicación**, **5432** para **PostgreSQL** (Lectura y escritura, misma base de datos para desarrollo local) y  **6379** para **Redis** .

- Personalizar estos puertos para el proyecto elegido en caso de estar ya en uso si se requiere. 
- Personalizar los nombres de los contenedores para el proyecto. 
- **Actualizar el nombre de la aplicación** en el archivo .env generado.

``` bash
APP_NAME="MY_APP_NAME"
```

- Se recomienda para el desarrollo del proyecto eliminar **composer.lock del archivo .gitignore** en la raíz, para que se añada al repositorio del proyecto.

**Construir imagen e iniciar el proyecto**
Finalmente, construimos/arrancamos la imagen:

``` bash
cd <ruta-a-carpeta>/docker 

# Recomendado para ver la salida del contenedor directamente en el terminal.
docker compose up --build
```

Este proceso levantará dos contenedores docker:
1. Aplicación PHP/Swoole.
2. PostgreSQL.
    - Las credenciales para conectarse a la base de datos de desarrollo por defecto se encuentra en el archivo **.env** en la carpeta **src**. El host es 127.0.0.1
    - En la carpeta **src/migrations** encontramos:
        - 2022_01_01_12_00_postgresql_event_sourcing.dump Genera un esquema estandarizado orientado a Event Sourcing y las tablas correspondientes.
        - 2022_01_01_13_00_postgresql_read_projections.dump Genera un esquema en el que crear las tablas de vistas.
        - 2022_01_01_13_04_view_all_examples.sql Genera una tabla para una vista. Los campos creados son solo de ejemplo.
    - Modificar en el .env de la carpeta **src** los datos de conexión, tanto de lectura como escritura.
3. Existe una conexión por defecto al servidor online de desarrollo de Kafka en el archivo .env.local, con lo que se necesita conexión por VPN para un correcto funcionamiento.
