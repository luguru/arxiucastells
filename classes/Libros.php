<?php

class Libros {

    protected $id;
    protected $titulo;
    protected $autor1;
    protected $autor2;
    protected $autor3;
    protected $editorial;
    protected $edicion;
    protected $genero;
    protected $categoria;
    protected $isbn;
    protected $comentario;
    protected $portada;

    function getId() {
        return $this->id;
    }

    function getTitulo() {
        return $this->titulo;
    }

    function getAutor1() {
        return $this->autor1;
    }
    
    function getAutor2() {
        return $this->autor2;
    }
    
    function getAutor3() {
        return $this->autor3;
    }

    function getEditorial() {
        return $this->editorial;
    }

    function getEdicion() {
        return $this->edicion;
    }

    function getGenero() {
        return $this->genero;
    }

    function getCategoria() {
        return $this->categoria;
    }

    function getIsbn() {
        return $this->isbn;
    }

    function getComentario() {
        return $this->comentario;
    }

    function getPortada() {
        return $this->portada;
    }

    function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    function setAutor1($autor1) {
        $this->autor1 = $autor1;
    }
    
    function setAutor2($autor2) {
        $this->autor2 = $autor2;
    }
    
    function setAutor3($autor3) {
        $this->autor3 = $autor3;
    }

    function setEditorial($editorial) {
        $this->editorial = $editorial;
    }

    function setEdicion($edicion) {
        $this->edicion = $edicion;
    }

    function setGenero($genero) {
        $this->genero = $genero;
    }

    function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

    function setIsbn($isbn) {
        $this->isbn = $isbn;
    }

    function setComentario($comentario) {
        $this->comentario = $comentario;
    }

    function setPortada() {
        return $this->portada;
    }

    public function __construct($row) {
        $this->id = $row['id'];
        $this->titulo = $row['titulo'];
        $this->autor1 = $row['autor1'];
        $this->autor2 = $row['autor2'];
        $this->autor3 = $row['autor3'];
        $this->editorial = $row['editorial'];
        $this->edicion = $row['edicion'];
        $this->genero = $row['nombreGenero'];
        $this->categoria = $row['nombreCategoria'];
        $this->isbn = $row['isbn'];
        $this->comentario = $row['comentario'];
        $this->portada = $row['portada'];
    }

}
