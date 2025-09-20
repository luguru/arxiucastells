<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Libros.php");

if (isset($_POST["guardar"])) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/guardarDatos.php");
}
if (isset($_POST["consultar"])) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/consultarDatos.php");
}
if (isset($_POST["salir"])) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/logoff.php");
}
if (isset($_POST["enviar"])) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/mostrarLibro.php");
}
if (isset($_POST["modificar"])) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/modificarFoto.php");
}
 
