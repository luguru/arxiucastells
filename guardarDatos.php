<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnect.php");
//require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/DB.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Libros.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/functions.php");


//Recibimos los datos de la imagen de portada
$nombre_portada = $_FILES['portada']['name'];
$tipo_portada = $_FILES['portada']['type'];
$tamano_portada = $_FILES['portada']['size'];

//Ruta de la carpeta destino del servidor
$carpeta_portadas = __DIR__ . '/portadas/';

//Movemos la imagen del directorio temporal al directorio elegido
if ($nombre_portada == '') {
    $nombre_portada = "nofotoportada.jpg";
    move_uploaded_file($_FILES['portada']['tmp_name'], $carpeta_portadas . $nombre_portada);
} else {
    $array = explode('.', $nombre_portada);
    $fileName = $array[0];
    $fileName = eliminar_tildes($fileName);
    $fileExt = $array[1];
    $nombre_portada = $fileName . "_" . intval(time()) . "." . $fileExt;
    move_uploaded_file($_FILES['portada']['tmp_name'], $carpeta_portadas . $nombre_portada);


    $source = $carpeta_portadas . $nombre_portada;
    //$quality = 70;

    //reducir tamaño imagen ficha
    $destination = $carpeta_portadas . $nombre_portada;
    //$wide = 300;
    compress($source, $destination, 300, 70);

    //crear miniatura para listado
    $destination_thumb = $carpeta_portadas . 'thumbs/' . $nombre_portada;
    //$wide_thumb = 50;
    compress($source, $destination_thumb, 50, 70);


}

$titulo = $_REQUEST['titulo'];
$autor1 = $_REQUEST['autor1'];
$autor2 = $_REQUEST['autor2'];
$autor3 = $_REQUEST['autor3'];
$editorial = $_REQUEST['editorial'];
$edicion = $_REQUEST['edicion'];
$genero = $_REQUEST['genero'];
$categoria = $_REQUEST['categoria'];
$isbn = $_REQUEST['isbn'];
$comentario = $_REQUEST['comentario'];

if ($genero == 0) {
    $genero = 1;
}
if ($categoria == 0) {
    $categoria = 1;
}

if (!$mysqli->query("INSERT INTO libro (titulo,autor1,autor2,autor3,editorial,edicion,genero,categoria,isbn,comentario,portada) VALUES ('$titulo','$autor1','$autor2','$autor3','$editorial','$edicion',$genero,$categoria,'$isbn','$comentario','$nombre_portada');")) {
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
    <a class='btn btn-success' href='formulario.php'>Volver</a>
</div>
</body>
</html>