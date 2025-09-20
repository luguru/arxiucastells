<?php
$bd_host = "localhost";
$bd_base = "arxiucastells";

//Datos para trabajar en producción
$bd_usuario = "arxiucastells";
$bd_password = "arxiu_4rx1uc4st3lls";

//Datos para trabajar en local
//$bd_usuario = "root";
//$bd_password = "";

$mysqli = new mysqli($bd_host, $bd_usuario, $bd_password, $bd_base);
$mysqli->query("SET NAMES 'utf8'");

if ($mysqli->connect_errno) {
    echo "Falló la conexión con MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$_SESSION['usuario'] = 'jaume';
if (!isset($_SESSION['usuario'])) {
    die("Error - debe <a class='btn btn-danger' href='index.php'>identificarse.</a><br />");
}
?>