Gestor de Biblioteca con PHP y MySQL
Esta es una aplicación web para gestionar un catálogo de una biblioteca personal. Permite añadir, buscar, editar y eliminar libros, almacenando toda la información en una base de datos MySQL.

Características
CRUD Completo: Funcionalidad para Crear, Leer, Actualizar y Borrar registros.

Backend en PHP: La lógica del servidor está escrita en PHP para una conexión segura con la base de datos.

Base de Datos MySQL: Utiliza MySQL para el almacenamiento persistente de datos.

Paginación: Muestra los resultados en páginas para un rendimiento óptimo, con opción de ver 10, 25, 50 o 100 registros.

Ordenación de Columnas: Permite ordenar el listado de libros por diferentes campos.

Procesamiento de Imágenes: Redimensiona las portadas subidas y crea miniaturas automáticamente, guardándolas en el servidor.

Búsqueda por ISBN: Integra la API de Google Books para autocompletar datos de libros a partir de su ISBN.

Sistema de Login: Acceso seguro para usuarios registrados en la tabla usuarios.

Estructura Profesional: Organización de carpetas que separa la lógica del servidor de los archivos de acceso público.

Estructura de Carpetas Final
Es muy importante que organices los archivos de esta manera para que las rutas funcionen correctamente.

/tu-proyecto/
|
|-- public/                <-- El "Document Root" de tu servidor debe apuntar aquí.
|   |-- images/
|   |   |-- portadas/
|   |   `-- miniaturas/
|   |-- css/
|   |   `-- style.css
|   |-- js/
|   |   `-- script.js
|   |-- index.php         (El gestor principal)
|   |-- login.php         (La página de acceso)
|   `-- logout.php        (Script para cerrar sesión)
|
|-- src/
|   |-- api/
|   |   `-- index.php     (El backend con toda la lógica)
|   `-- db/
|       `-- database.php  (Configuración de la conexión a la BBDD)
|
`-- README.md           (Este archivo)

Requisitos
Un servidor web local como XAMPP, WAMP o Laragon.

PHP (versión 7.4 o superior con la extensión gd habilitada para el procesamiento de imágenes).

MySQL o MariaDB.

Pasos para la Instalación
Configurar el Servidor Web:

Instala y ejecuta tu servidor web local (por ejemplo, Laragon).

Crea una carpeta para tu proyecto (ej: arxiucastells_new).

Dentro de esa carpeta, crea la estructura de directorios (public, src, etc.) y coloca cada archivo en su lugar correspondiente, como se muestra en el esquema de arriba.

Importante: Configura tu servidor web para que el "Document Root" (la carpeta raíz pública) apunte al directorio public/. En Laragon, esto se hace fácilmente al crear un nuevo proyecto.

Crear la Base de Datos:

Abre la herramienta de gestión de bases de datos de tu servidor (HeidiSQL, phpMyAdmin).

Crea una nueva base de datos llamada arxiucastells.

Selecciona la base de datos arxiucastells.

Importa el archivo sql/arxiucastells_db.sql para crear las tablas (libro, categoria, genero, usuarios) y cargar todos los datos.

Configurar la Conexión a la Base de Datos:

Abre el archivo src/db/database.php.

Verifica que los datos de conexión coincidan con los de tu servidor. La configuración por defecto debería funcionar para la mayoría de instalaciones locales.

$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Contraseña vacía por defecto
$db_name = 'arxiucastells';

Si usas una contraseña para root, añádela en $db_pass.

Acceder a la Aplicación:

Abre tu navegador web y ve a la URL de tu proyecto local (ej: http://arxiucastells_new.test/login.php o http://localhost/arxiucastells_new/public/login.php dependiendo de tu configuración).

Serás recibido por la pantalla de login.

Usa uno de los usuarios predeterminados de tu base de datos para acceder:

Usuario: jaume, Contraseña: jaume

Usuario: luis, Contraseña: luis

¡Y listo! La aplicación está completa y debería funcionar perfectamente.