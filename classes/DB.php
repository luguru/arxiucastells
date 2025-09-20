<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/Libros.php");

class DB {

    public static function conexion () {
        $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
        try {
            //Conexion produccion
            $conexion = new PDO("mysql:host=localhost;dbname=arxiucastells","arxiucastells","arxiu_4rx1uc4st3lls", $opciones);
            //Conexion local
            //$conexion = new PDO("mysql:host=localhost;dbname=arxiucastells;charset=utf8","root","", $opciones);
        } catch (Exception $e) {
            echo $e->getMessage(), "\n";
        }
        return $conexion;
    }

    public function cerrar($conexion)
    {
        $conexion = null;
    }

    protected static function ejecutaConsulta($sql) {
        /*$opc = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
        $dsn = "mysql:host=localhost;dbname=arxiucastells";
        $usuario = 'arxiucastells';
        $contrasena = 'arxiu_4rx1uc4st3lls';

        //$conexion = new PDO($dsn, $usuario, $contrasena, $opc);*/
        $conexion = self::conexion();
        $resultado = null;
        if (isset($conexion)) {
            $resultado = $conexion->query($sql);
        }
        return $resultado;
    }

    public static function consultarDatos($titulo, $autor1, $autor2, $autor3, $isbn, $genero, $categoria) {
        if ($categoria == 1 || $categoria == "") {
            $sql = "SELECT libro.id, libro.titulo, libro.autor1, libro.autor2, libro.autor3, libro.editorial, libro.edicion, genero.nombreGenero, categoria.nombreCategoria, libro.isbn, libro.comentario, libro.portada
            FROM libro
            LEFT JOIN genero ON libro.genero = genero.id
            LEFT JOIN categoria ON libro.categoria = categoria.id
            WHERE titulo LIKE '%$titulo%'
            AND autor1 LIKE '%$autor1%'
            AND autor2 LIKE '%$autor2%'
            AND autor3 LIKE '%$autor3%'
            AND isbn LIKE '%$isbn%'";
        } else {
            $sql = "SELECT libro.id, libro.titulo, libro.autor1, libro.autor2, libro.autor3, libro.editorial, libro.edicion, genero.nombreGenero, categoria.nombreCategoria, libro.isbn, libro.comentario, libro.portada
            FROM libro
            LEFT JOIN genero ON libro.genero = genero.id
            LEFT JOIN categoria ON libro.categoria = categoria.id
            WHERE titulo LIKE '%$titulo%'
            AND autor1 LIKE '%$autor1%'
            AND autor2 LIKE '%$autor2%'
            AND autor3 LIKE '%$autor3%'
            AND categoria LIKE '%$categoria%'
            AND isbn LIKE '%$isbn%'";
        }

        $resultado = self::ejecutaConsulta($sql);
        $libros = array();

        if ($resultado) {
            $row = $resultado->fetch();
            while ($row != null) {
                $libros[] = new Libros($row);
                $row = $resultado->fetch();
            }
        }
        return $libros;
    }

    public static function muestraLibro($codigo) {
        $sql = "SELECT libro.id, libro.titulo, libro.autor1, libro.autor2, libro.autor3, libro.editorial, libro.edicion, genero.nombreGenero, categoria.nombreCategoria, libro.isbn, libro.comentario, libro.portada
            FROM libro
            LEFT JOIN genero ON libro.genero = genero.id
            LEFT JOIN categoria ON libro.categoria = categoria.id
            WHERE libro.id='$codigo'";
        $resultado = self::ejecutaConsulta($sql);
        $libros = array();

        if ($resultado) {
            $row = $resultado->fetch();
            while ($row != null) {
                $libros[] = new Libros($row);
                $row = $resultado->fetch();
            }
        }
        return $libros;
    }

    public static function cargarCategorias() {
        $sql = "SELECT * FROM categoria ORDER BY id";
        $resultado = self::ejecutaConsulta($sql);
        return $resultado;
    }

    public static function cargarGeneros() {
        $sql = "SELECT * FROM genero ORDER BY nombreGenero";
        $resultado = self::ejecutaConsulta($sql);
        return $resultado;
    }

}
