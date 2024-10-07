# Proyecto con Docker Compose

Este proyecto utiliza Docker Compose para gestionar contenedores de servicios como una base de datos y una aplicación web. 
El contenedor cuenta con dos servicios: 
1) Apache - Servidor Web (api_verifarma_apache) - Puerto 80
2) Mysql - Base de datos (api_verifarma_mysql) - Puerto 3306

También cuenta con un network bridge (api_verifarma_network)

Sigue los pasos a continuación para instalar y ejecutar el proyecto en tu máquina local.

## Requisitos

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Estructura del Proyecto

```plaintext
.
├── docker-compose.yml   # Definición de los servicios de Docker Compose
├── config/              # Configuraciones de la aplicación como el archivo router.json y el directorio jwt donde se alojara la clave pública y privada para generar el token
├── docker/              # Directorio donde se aloja la configuración para levantar el docker
├── public/              # Directorio de archivos públicos de la aplicación
├── scripts/             # Aqui se aloja el archivo verifarma para correr por consola comandos de verifarma
├── src/                 # Código fuente de la aplicación
├── tests/               # Clases de Units Test
├── var/                 # Aqui se aloja los directorios de logs de la aplicación
└── README.md            # Instrucciones del proyecto
```
## Dependencias
El proyecto no está desarrollado con ningún framework. Hay algunas depencias aisladas de Symfony, asi como Firebase/JWT o Monolog/Logger, etc. 

Todo tipo de dependencia, (se explica en mas detalle en el próximo apartado) se instalan con composer install

## Instrucciones para levantar el ambiente Docker con docker-compose

Correr los siguientes comandos:

1) docker-compose up -d
2) docker exec -it api_verifarma_apache bash
3) composer install
4) cp .env.dist .env
5) Copiar los siguientes datos de conexión de base de datos, al archivo generado del paso anterior (.env):
```plaintext
DATABASE_DRIVER=pdo_mysql
DATABASE_HOST=api_verifarma_mysql
DATABASE_PORT=3306
DATABASE_NAME=api_verifarma
DATABASE_USER=api_verifarma
DATABASE_PASSWORD=api_verifarma
```
6) php scripts/verifarma run-migrations

## Comprobación de ambiente levantado

Para comprobar si el proyecto esta andando entrar desde un navedor a: http://localhost/api/status

Y el navegador debería responder un Json: 

{
  "status": "ok"
}

## Swagger 

Swagger es una interfaz gráfica que sirve para documentar y debugear la API.

Para poder entrar se debe ingresar a: http://localhost/swagger.html

## Uso de la Rest Api

Credenciales:
```plaintext
{
    "username": "admin",
    "password": "verifarmaApi"
}
```
Para poder consumir la APi se podrá usar cualquier cliente API como Postman/SoapUI o por consola con curl.

Para el uso general de la API se deberá acceder al recurso de la api "/api/auth" (POST), pasando por body las creenciales.
La API devolverá un token, para poder usarse en los demas recursos protegidos de la misma, como cabezeras "Authorization: Bearer" o sea "Authorization: Bearer {Token}"

Listado de recursos de la API:

1) GET - /api/status - Sin autenticación

Devuelve un status OK, para saber si el ambiente esta levantado

2) POST - /api/auth - Sin autenticación

Para autenticar contra la API. Se debera pasar las credenciales mencionadas arriba como body y como JSON.

Devolverá el Token.

3) GET - /api/farmacia/{id} - Autenticado

Devuelve la farmacia por ID. 

Se deberá pasar por header el token como "Authorization: Bearer {Token}"

4) POST - /api/farmacia - Autenticado

Guarda un nuevo registro farmacia. 

Se deberá pasar en el body el siguiente formato:

{
    "nombre": "nombre",
    "direccion": "dirección",
    "latitud": "-34.810896",
    "longitud": "-58.3702655"
}

Se deberá pasar por header el token como "Authorization: Bearer {Token}"

5) GET - /api/farmacia?lat={latitud}&lon={longitud} - Autenticado

Según los parametros dados de latitud y longitud, encuentra la farmacia mas cercana.

Se deberá pasar por header el token como "Authorization: Bearer {Token}"

## Comandos utiles
Para correr los test unitarios:
```plaintext
./vendor/bin/phpunit tests
```
Para correr el lint:
```plaintext
php vendor/bin/phplint src/
```
Para correr el lint a los tests
```plaintext
php vendor/bin/phplint tests/
```
Para correr los php cs fixer y que corriga los archivos automaticamente:
```plaintext
./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
```
Para entrar el contenedor:
```plaintext
docker exec -it api_verifarma_apache bash
```
Para correr las migrations:
```plaintext
php scripts/verifarma run-migrations
```
Para deshacer los cambios de las migrations:
```plaintext
php scripts/verifarma run-migrations --down
```
Empezar el contenedor
```plaintext
docker-compose start
```
Parar el contenedor
```plaintext
docker-compose stop
```
