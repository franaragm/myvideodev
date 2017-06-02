# Symfony 3.1 API REST Red social de compartir videos

## Video Demostración

[Ver video Demo][14]

## Instrucciones

### Comandos de instalación

* Instalar dependencias:

 `$ composer install`

* Crear base de datos y configurar parametros en parameters.yml
* Crear estructura de tablas

`$ php bin/console doctrine:schema:create`

* Importar datos sql de prueba de app/Resources

### Datos de acceso JSON

`http://myvideo.dev/app_dev.php/login`

POST - json
`{"email":"fran@aragon.com", "password":"fran123", "getHash":"true"}`

### Comandos útiles

* Limpiar cache y logs

`$ php bin/console cache:clear --env=prod --no-debug`

[14]: 
