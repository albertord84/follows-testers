# follows-testers

Testers do sistema follows da DUMBU

### ¿Qué se publica?

los siguientes directorios y archivos son los que se deben desplegar exclusivamente, cuando la aplicacion este en modo de produccion.

```
- application/
- client/
- etc/
- index.php
- log/
- system/
- var/
- vendor/
```

### ¿Cómo publicar la aplicacion?

lo mas recomendable es hacerlo desde un equipo con Linux, porque en su ambiente existe el comando rsync.

ejecutando rsync de la siguiente forma, se puede publicar de forma segura hacia el servidor publico, la aplicacion despues que se realice cualquier cambio al codigo:

```
rsync -Phazv --progress --exclude-from='.rsync_exclude' . ip.remota.com:"/dir/destino/aplicacion"
```

obviamente se nota algo extraño en estos parametros. la referencia al archivo **.rsync_exclude**. ¿qué contiene este archivo? pues todos los nombres de directorios y de archivos que no quiero que suban hacia el servidor que hospeda la aplicacion de cara a la internet.

por ejemplo, el directorio **node_modules** es excluido de la aplicacion en su estado de produccion. para esto en **.rsync_exclude** existe una linea que contiene esto:

```
/node_modules
```

asi mismo hay otra para:

```
/var
/test
/build
```

pues son directorios cuyo contenido nada tiene que ver con el codigo que se ejecuta en produccion. he aqui otros ejemplos incluidos en nuestro **.rsync_exclude**:

```
/vendor/mgp25/instagram-php/sessions
.gitignore
webpack.config.js
composer.phar
composer.json
composer.lock
```

### Directorios a proteger

+ application
+ etc
+ log
+ system
+ var
+ vendor

¿como hacerlo? poniendo en la raiz de cada uno un **.htaccess** con lo siguiente:

```

<IfModule authz_core_module>
	Require all denied
</IfModule>
<IfModule !authz_core_module>
	Deny from all
</IfModule>

```
estos directorios son protegidos porque no puede verse desde la web publica el contenido de los mismos.

### Directorios con permisos especiales

se debe tener en cuenta que nuestra aplicacion hace escritura en disco. por lo que los directorios siguiente deben tener permisos **777**:

```
etc
log
var
```

en **etc** se guardan las configuraciones que sufren cambios cuando se interactua con la interfaz web. esto se cambiara por acceso a bases de datos mas adelante. pero por ahora se hace escribiendo un **.json** en el directorio **etc**.

en **log** esta de más decir por qué se requieren dichos permisos. alli estan las trazas de lo que va ocurriendo, por ende debe existir el permiso de escritura irrestricto.

en **var** se guardan los mensajes que luego cogera la tarea del cron para hacer los envios de mensajes a los seguidores de los perfiles de referencia.