<?php


class Categoria {
    protected $id;
    protected $nombreCat;
    
    function getId() {
        return $this->id;
    }

    function getNombreCat() {
        return $this->nombreCat;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombreCat($nombreCat) {
        $this->nombreCat = $nombreCat;
    }

    function __construct($row) {
        $this->id = $row['id'];
        $this->nombreCat = $row['nombreCat'];
    }


}
