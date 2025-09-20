<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/js/xajax_core/xajax.inc.php");

$xajax = new xajax('resources/valida.php');

$xajax->register(XAJAX_FUNCTION, "validarLogin");

$xajax->configure('javascript URI', 'resources/js/');
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/head.php"); ?>
        <?php $xajax->printJavascript(); ?>
    </head>
    <body class="container">
        <div id='login' class="d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
            <h2>Introduzca sus datos</h2>
            <form id='datos' style="background-color: #f2f2f2; padding: 2rem 4rem; border-radius: 1rem;" action='formulario.php' method='post' onsubmit='return enviarFormulario();'>
                    <div><span class='error'><?php if (isset($error)) echo $error; ?></span></div>
                    <div class='campo'>
                        <label class="mb-0" for='usuario' >Usuario:</label>
                        <input type='text' name='usuario' id='usuario' class="mt-0 form-control" maxlength="50" />
                    </div>
                    <div class='my-4 campo'>
                        <label class="mb-0" for='password' >Contraseña:</label>
                        <input type='password' name='password' id='password' class="mt-0 form-control" maxlength="50" />
                    </div>
                    <div class='campo'>
                        <input type='submit' class="btn btn-block btn-success" name='login' value='Enviar' />
                    </div>
            </form>
        </div>
    </body>
</html>