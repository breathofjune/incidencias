Pasos para poder iniciar la aplicación:

1. Descargar e instalar Docker.
2. Descargar e instalar Git y clonar el repositorio. Alternativamente, se puede descargar el archivo ZIP del proyecto directamente desde GitHub y extraerlo.
3. Abrir una terminal en la carpeta raíz del proyecto y ejecutar los siguientes comandos:
    - docker-compose build
    - docker-compose up -d
   Alternativamente, también se puede usar Docker Desktop para levantar el contenedor.
4. Acceder a [http://localhost:8000/index.php?init_db](http://localhost:8000/index.php?init_db) para crear la base de datos. Este paso es necesario solo una vez, ya que la base de datos no se guarda en el repositorio.
5. Finalmente, accede a [http://localhost:8000](http://localhost:8000) para ver la aplicación en funcionamiento.

Si el puerto 8000 estuviera ocupado, se puede cambiar el docker-compose.yml
