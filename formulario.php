<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/DB.php");
//require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnect.php");


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/head.php"); ?>
</head>
<body>
<div class="container">
    <h1>Arxiu Castells</h1>
    <form id='datosLibros' class="row" action='controlador.php' method='POST' enctype="multipart/form-data" onsubmit="return validacion()">
        <div id="datosLibro" class="col-12">
            <div class="form-group">
                <label class="titulo"><h3>Título: </h3></label>
                <input type='text' name='titulo' class='titulo form-control' placeholder="Título" maxlength="250"/><br/>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label><h3>Categoría: </h3></label>
                    <select name="categoria" id="categoria" class="form-control" required>
                        <option value="">Seleccione una categoría:</option>
                        <?php
                        $categorias = DB::cargarCategorias();
                        foreach ($categorias as $categoria) { ?>
                            <option value="<?= $categoria['id'] ?>"><?= $categoria['nombreCategoria'] ?></option>
                            <?php
                        }
                        ?>
                        <!-- $query = $mysqli->query("SELECT * FROM categoria ORDER BY nombreCategoria");
                        while ($valores = mysqli_fetch_array($query)) {
                            echo '<option value="' . $valores['id'] . '">' . $valores['nombreCategoria'] . '</option>';
                        }
                        ?> -->
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label class="autor"><h3>Autor: </h3></label>
                    <input type='text' name='autor1' class='form-control autor' maxlength="250" placeholder="Autor"/><br/>
                </div>
                <div class="form-group col-md-3">
                    <label class="autor"><h3>Autor: </h3></label>
                    <input type='text' name='autor2' class='form-control autor' maxlength="250" placeholder="Autor"/><br/>
                </div>
                <div class="form-group col-md-3">
                    <label class="autor"><h3>Autor: </h3></label>
                    <input type='text' name='autor3' class='form-control autor' maxlength="250" placeholder="Autor"/><br/>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label><h3>Editorial: </h3></label>
                    <input type='text' name='editorial' class='form-control editorial' maxlength="250" placeholder="Editorial"/><br/>
                </div>
                <div class="form-group col-md-3">
                    <label><h3>Edición: </h3></label>
                    <input type='text' name='edicion' class="form-control" id='edicion' maxlength="250" placeholder="Edición"/><br/>
                </div>
                <div class="form-group col-md-3">
                    <label><h3>Género: </h3></label>
                    <select name="genero" id="genero" class="form-control">
                        <option value="0">Seleccione un genero:</option>
                        <?php
                        $generos = DB::cargarGeneros();
                        foreach ($generos as $genero) { ?>
                            <option value="<?= $genero['id'] ?>"><?= $genero['nombreGenero'] ?></option>
                            <?php
                        }
                        ?>

                        <?php
                        /* $query = $mysqli->query("SELECT * FROM genero ORDER BY nombreGenero");
                        while ($valores = mysqli_fetch_array($query)) {
                            echo '<option value="' . $valores['id'] . '">' . $valores['nombreGenero'] . '</option>';
                        } */
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label><h3>ISBN: </h3></label>
                    <input type='text' name='isbn' class="form-control" id='isbn' maxlength="150" placeholder="ISBN"/><br/>
                </div>
            </div>
            <div class="form-row">
                <label class="comentario"><h3>Comentario: </h3></label>
                <textarea name='comentario' class='form-control comentario' maxlength="600" placeholder="Escribe un comentario..."></textarea><br/>
            </div>
            <div class="form-row mt-3">
                <label class="subirPortada"><h3>Subir portada: </h3></label>
                <input type="file" name="portada" class="form-control" size="20"/>
            </div>
            <div class="form-row d-flex align-items-center justify-content-between">
                <div>
                    <input type="submit" name="guardar" value="Guardar" id="BotonGuardar">
                </div>
                <div>
                    <input type="submit" name="consultar" value="Consultar" id="BotonConsultar">
                    <input type="submit" name="salir" value="Salir" id="BotonSalir">
                </div>
            </div>
    </form>
</div>
</body>
</html>