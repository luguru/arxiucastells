<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();

//require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/DB.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Libros.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Genero.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Categoria.php");

if (!isset($_SESSION['usuario'])) {
    die("Error - debe <a class='btn btn-danger' href='index.php'>identificarse.</a><br />");
}

function muestraLibros() {
    $titulo = $_REQUEST['titulo'];
    $autor1 = $_REQUEST['autor1'];
    $autor2 = $_REQUEST['autor2'];
    $autor3 = $_REQUEST['autor3'];
    $isbn = $_REQUEST['isbn'];
    $genero = $_REQUEST['genero'];
    $categoria = $_REQUEST['categoria'];

    $libros = DB::consultarDatos($titulo, $autor1, $autor2, $autor3, $isbn, $genero, $categoria);

    echo "<table class='table table-hover sortable-theme-bootstrap mt-3' id='tablaResultados'>"
    . "<thead>"
    . "<tr><th><b>ID</b></th>"
    . "<th><b>TITULO</b></th>"
    . "<th><b>AUTOR</b></th>"
    . "<th><b>AUTOR</b></th>"
    . "<th><b>AUTOR</b></th>"
    . "<th><b>EDICION</b></th>"
    . "<th><b>EDITORIAL</b></th>"
    . "<th><b>GENERO</b></th>"
    . "<th><b>CATEGORÍA</b></th>"
    . "<th><b>ISBN</b></th>"
    . "<th><b>COMENTARIO</b></th>"
    . "<th><b>PORTADA</b></th>"
    . "<th></th></tr>"
    . "</thead>";
    echo "<tbody>";
    foreach ($libros as $p) {
        echo "<tr><form id='" . $p->getId() . "' action='controlador.php' method='post'>";
        echo "<input type='hidden' name='codigo' value='" . $p->getId() . "'/>";
        echo "<td>" . $p->getId() . "</td>";
        echo "<td>" . $p->getTitulo() . "</td>";
        echo "<td>" . $p->getAutor1() . "</td>";
        echo "<td>" . $p->getAutor2() . "</td>";
        echo "<td>" . $p->getAutor3() . "</td>";
        echo "<td>" . $p->getEdicion() . "</td>";
        echo "<td>" . $p->getEditorial() . "</td>";
        echo "<td>" . $p->getGenero() . "</td>";
        echo "<td>" . $p->getCategoria() . "</td>";
        echo "<td>" . $p->getIsbn() . "</td>";
        echo "<td>" . $p->getComentario() . "</td>";
        echo "<td><img src='portadas/thumbs/" . $p->getPortada() . "'/></td>";
        echo "<td><input type='submit' name='enviar' value='Mostrar' id='BotonConsultar'/></td>";
        echo "</form></tr>";
    }
    echo "</tbody></table>";
}
?>

<html lang="es">
    <head>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/head.php"); ?>
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
        <script type="text/javascript" src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="resources/js/editabletable.js" type="text/javascript"></script>
        <link href="resources/css/editabletable.css" rel="stylesheet" type="text/css"/>
        <script>
            $(document).ready( function () {
                $('#tablaResultados').DataTable();
            } );
        </script>
    </head>
    <body>
        <?php muestraLibros() ?>
        <div class="col-md-12">
            <a id="BotonVolver" href='formulario.php'>Volver</a>
            <a id="BotonSalir" href='logoff.php'>Salir</a>
        </div>
    </body>
</html>