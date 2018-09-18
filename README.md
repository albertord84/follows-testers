# follows-testers

Testers do sistema follows da DUMBU

# Directorios excluidos

## var

aqui se guardan los mensajes compuestos desde la interfaz web para ser procesados por el robot que corre desde el cron disparando a todos los seguidores de un perfil de referencia. por tanto, es un directorio que tiene que ser creado una vez que se haga un pull en el servidor de cara a la internet. debe crearse con permisos **777**.

## vendor

directorio que contiene las dependencias del codeigniter. no es necesario compartirlo, ni subirlo una y otra vez a github. basta con copiar el **composer.phar** para este directorio raiz, y luego desde aqui mismo ejecutar esto despues del primer pull:

*php composer.phar install*

## build y node_modules

estos dos señoritos contienen lo siguiente. **build** tiene toda la chatarra que genera una y otra vez el compilador de nodejs cuando procesa el codigo del cliente web. **node_modules** ni soñar compartirlo. son cientos de megas de dependencias de nodejs. este solo haria falta a menos que se requiera un cambio urgente en el servidor de cara a internet. entonces habria que crear este directorio ejecutando lo siguiente en la raiz:

*npm install*

para esto tiene que estar instalado el **npm**. entonces el comando anterior creara y llenara de cosas **node_modules**. luego que termine la 

## etc

este directorio contiene algunos archivos sensibles con nombres de cuentas y contraseñas. por eso lo excluimos del repositorio. no obstante, hay que crearlo con permisos **777**.

## log

contiene los diferentes logs de las cosas que iran ocurriendo en el sistema. crearlo despues del primer pull y ponerle permisos **777**.

# Directorios a proteger

+ application
+ src
+ public
+ vendor

¿como hacerlo? poniendo en la raiz de cada uno un punto **htaccess** con lo siguiente:

```html

<IfModule authz_core_module>
	Require all denied
</IfModule>
<IfModule !authz_core_module>
	Deny from all
</IfModule>

```
estos directorios son protegidos porque no puede verse desde la web publica el contenido de los mismos.

# Directorio del cliente web (frontend)

es el directorio **client**. este contiene el resultado de la compilacion del codigo javascript, html y css que esta en el directorio **src**. ademas, este codigo esta enlazado con el contenido de **public**. o sea, cuando nodejs compila lo que hay en **src**, toma tambien lo que hay en **public**, y emsambla todo para luego ponerlo dentro de **client**. por tanto, este direcorio **client** es el que siempre debe estar de cara a la web, aparte del **index.php** que es el punto de entrada del CodeIgniter que es nuestro backend.

asi que esto pone de relieve que solo hacen falta de cara a la web el directorio **client** y el archivo **index.php**. todo lo demas hay que protegerlo de los ojos extraños usando todas las medidas y restricciones que se pueda.