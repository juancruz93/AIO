<?php

namespace Sigmamovil\General\Misc;

class CleanString {

  public function clear($string) {
    $string = str_replace(array('á', 'à', 'â', 'ã', 'ª', 'ä'), "a", $string);
    $string = str_replace(array('Á', 'À', 'Â', 'Ã', 'Ä'), "A", $string);
    $string = str_replace(array('Í', 'Ì', 'Î', 'Ï'), "I", $string);
    $string = str_replace(array('í', 'ì', 'î', 'ï'), "i", $string);
    $string = str_replace(array('é', 'è', 'ê', 'ë'), "e", $string);
    $string = str_replace(array('É', 'È', 'Ê', 'Ë'), "E", $string);
    $string = str_replace(array('ó', 'ò', 'ô', 'õ', 'ö', 'º'), "o", $string);
    $string = str_replace(array('Ó', 'Ò', 'Ô', 'Õ', 'Ö'), "O", $string);
    $string = str_replace(array('ú', 'ù', 'û', 'ü'), "u", $string);
    $string = str_replace(array('Ú', 'Ù', 'Û', 'Ü'), "U", $string);
    $string = str_replace(
            array("¨", "º", "~",
        "#", "@", "|", "!", "\"",
        "·", "$", "%", "&", "/",
        "(", ")", "?", "'", "¡",
        "¿", "[", "^", "<code>", "]",
        "+", "}", "{", "¨", "´",
        ">", "< ", ";", ",", ":"), '', $string
    );
    $string = str_replace("ç", "c", $string);
    $string = str_replace("Ç", "C", $string);
    $string = str_replace("ñ", "n", $string);
    $string = str_replace("Ñ", "N", $string);
    $string = str_replace("Ý", "Y", $string);
    $string = str_replace("ý", "y", $string);
    $string = str_replace("&aacute;", "a", $string);
    $string = str_replace("&Aacute;", "A", $string);
    $string = str_replace("&eacute;", "e", $string);
    $string = str_replace("&Eacute;", "E", $string);
    $string = str_replace("&iacute;", "i", $string);
    $string = str_replace("&Iacute;", "I", $string);
    $string = str_replace("&oacute;", "o", $string);
    $string = str_replace("&Oacute;", "O", $string);
    $string = str_replace("&uacute;", "u", $string);
    $string = str_replace("&Uacute;", "U", $string);
    return strtolower(str_replace(' ', '_', $string));
  }

}
