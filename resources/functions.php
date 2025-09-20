<?php

function eliminar_tildes($cadena)
{
    $cadena_mod = array('á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ª' => 'a', 'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ä' => 'A',
        'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e', 'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i', 'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
        'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O',
        'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u', 'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ñ' => 'n', 'Ñ' => 'N', 'ç' => 'c', 'Ç' => 'C',
        ' '=> '_');

    $cadena = strtr($cadena, $cadena_mod);

    return $cadena;
}

function compress ($source, $destination, $wide, $quality) {
    $info = getimagesize($source);
    list($width, $height) = getimagesize($source);

    $proporcion = $width / $wide;

    $altura1 = $height / $proporcion;

    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);

    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);

    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    }

    $thumb = imagecreatetruecolor($wide, $altura1);

    imagecopyresampled($thumb, $image, 0, 0, 0, 0, $wide, $altura1, $width, $height);
    imagejpeg($thumb, $destination, $quality);
}