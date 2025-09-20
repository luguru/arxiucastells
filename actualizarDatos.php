<?php

//require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/DB.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnect.php");

// Rescatamos las variables del POST y las almacenamos en otras variables locales
$nombre = $_POST['nombre'];
$nuevoValor = $_POST['nuevoValor'];
$id = $_POST['id'];


// Sentencia para hacer el update
if (!$mysqli->query("UPDATE libro SET `$nombre`='$nuevoValor' where id=$id")) {
    // En caso de error recogemos su código y lo reflejamos mediante echo
    echo "No se ha podido realizar la modificación";
} else {
    // Lanazamos el mensaje de éxito en caso de que no haya ningun problema. 
    echo "Cambio realizado con éxito";
}
?>