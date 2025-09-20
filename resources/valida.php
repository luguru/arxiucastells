<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/js/xajax_core/xajax.inc.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/DB.php");

$xajax = new xajax();
$xajax->register(XAJAX_FUNCTION, "validarLogin");
$xajax->processRequest();

function validarLogin ($usuario, $password) {
	if (($usuario == 'luis' && $password == 'luis') || ($usuario == 'jaume' && $password == 'jaume')) {
		$_SESSION['usuario'] = $usuario;
	}
    return $usuario;
}
