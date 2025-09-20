<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/DB.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Libros.php");

session_start();

if (!isset($_SESSION['usuario'])) {
    die("Error - debe <a class='btn btn-danger' href='index.php'>identificarse.</a><br />");
}

function mostrarLibro() {
    if (isset($_POST['enviar'])) {
        $codigo = $_POST['codigo'];
        $libros = db::muestraLibro($codigo);
        echo "<table id='tabla' class='table table-hover'>";
        echo "<thead>";
        echo "<th>TÍTULO</th><th>AUTOR</th><th>AUTOR</th><th>AUTOR</th><th>EDICIÓN</th><th>EDITORIAL</th>"
        . "<th>GENERO</th><th>CATEGORÍA</th><th>ISBN</th>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($libros as $p) {
            echo "<tr>";
            echo "<form id='uploadimage' action='controlador.php' method='POST' enctype='multipart/form-data' onsubmit='return validacion()'>";
            echo "<h4>ID <input id='num_id' type='text' name='id' value='" . $p->getId() . "' disabled/></h4>";
            echo "<td name='titulo'>" . $p->getTitulo() . "</td>";
            echo "<td name='autor1'>" . $p->getAutor1() . "</td>";
            echo "<td name='autor2'>" . $p->getAutor2() . "</td>";
            echo "<td name='autor3'>" . $p->getAutor3() . "</td>";
            echo "<td name='edicion'>" . $p->getEdicion() . "</td>";
            echo "<td name='editorial'>" . $p->getEditorial() . "</td>";
            echo "<td>" . $p->getGenero() . "</td>";
            echo "<td>" . $p->getCategoria() . "</td>";
            echo "<td name='isbn'>" . $p->getIsbn() . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td colspan='8' id='comentarioLibro' class='col-lg-12 col-md-12 col-12' name='comentario'><br>" . $p->getComentario() . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<p class='col-lg-5 col-md-5 col-12'><img id='imagenLibro' src='portadas/" . $p->getPortada() . "'/></p>";
            echo "<p class='col-lg-3 col-md-3 col-12'><input type='file' name='portada' size='20'/></br>";
            echo "<input type='hidden' name='id' value='" . $p->getId() ."'/>";
            echo "<input type='submit' value='Modificar portada' name='modificar' id='BotonModificar'/></p>";
            echo "</tr>";
            echo "</form>";
        }
        echo "</tbody></table>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/head.php"); ?>
        <script src="resources/js/mindmup-editabletable.js" type="text/javascript"></script>
        <script>
       $(document).ready(function () {
 

        $('table').editableTableWidget();
        $('table').editableTableWidget({editor: $('<textarea>')});
        $('table').editableTableWidget({
            cloneProperties: ['background', 'border', 'outline']
        });

        $('table td').on('validate', function (evt, newValue) {
     
            var nombre = $(this).attr('name');
            var nuevoValor = newValue;
            var id = $('#num_id').val();
            
             console.log(nombre);
              console.log(nuevoValor);
               console.log(id);

            $.ajax({
                url: "actualizarDatos.php",
                data: {nombre: nombre, nuevoValor: nuevoValor, id: id},
                type: "POST",
                success: function (respuesta) {

                },
                error: function () {

                }
            });

        });

});
        </script>
    </head>
    <body class=" container">
        <div id="contenedor">
            <div class="col-md-12 col-12">
                <?php mostrarLibro(); ?>
            </div>
            <div class="col-md-12">
                <a id="BotonVolver" href='formulario.php'>Volver</a>
                <a id="BotonSalir" href='logoff.php'>Salir</a>
            </div>
        </div>
    </body>
</html>