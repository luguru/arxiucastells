<?php


class Genero {
    protected $id;
    protected $nombreGen;
    
    function getId() {
        return $this->id;
    }

    function getNombreGen() {
        return $this->nombreGen;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setNombreGen($nombreGen) {
        $this->nombreGen = $nombreGen;
    }

    function __construct($row) {
        $this->id = $row['id'];
        $this->nombreGen = $row['nombreGen'];
    }


}
