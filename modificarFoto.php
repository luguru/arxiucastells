<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnect.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Libros.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/functions.php");


//Recibimos los datos de la imagen de portada
$nombre_portada = $_FILES['portada']['name'];
$tipo_portada = $_FILES['portada']['type'];
$tamano_portada = $_FILES['portada']['size'];

//Ruta de la carpeta destino del servidor
$carpeta_portadas = __DIR__.'/portadas/';

$array = explode('.', $nombre_portada);
$fileName = $array[0];
$fileName = eliminar_tildes($fileName);
$fileExt = $array[1];
$nombre_portada = $fileName . "_" . intval(time()) . "." . $fileExt;
move_uploaded_file($_FILES['portada']['tmp_name'], $carpeta_portadas . $nombre_portada);


$source = $carpeta_portadas . $nombre_portada;
$quality = 70;

//reducir tamaño imagen ficha
$destination = $carpeta_portadas . $nombre_portada;
$wide = 300;
compress($source, $destination, $wide, $quality);

//crear miniatura para listado
$destination_thumb = $carpeta_portadas . 'thumbs/' . $nombre_portada;
$wide_thumb = 50;
compress($source, $destination_thumb, $wide_thumb, $quality);

$id = $_POST['id'];

if (!$mysqli->query("UPDATE libro SET `portada`='$nombre_portada' where id=$id")) {
    // En caso de error recogemos su código y lo reflejamos mediante echo
    echo "<h1>No se han guardado los datos correctamente: (" . $mysqli->errno . ") " . $mysqli->error . "</h1>";
} else {
    echo "<h1>Datos guardados correctamente</h1>";
}
?>
<html lang="es">
    <head>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/head.php"); ?>
    </head>
    <body class=" container">
        <div class="col-md-12">
            <a id="BotonVolver" href='formulario.php'>Volver</a>
        </div>
    </body>
</html>