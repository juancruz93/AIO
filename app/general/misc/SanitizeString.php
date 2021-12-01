<?php

namespace Sigmamovil\General\Misc;

/**
 * Description of SanitizeString
 *
 * @author juan.pinzon
 */
class SanitizeString {

  private $string;

  function __construct($string) {
    $this->string = $string;
  }

  function getString() {
    return $this->string;
  }

  protected function setString($string) {
    $this->string = $string;
  }

  function strTrim() {
    $this->setString(trim($this->getString()));
  }

  function sanitizeAccents() {
    $str = $this->getString();

    $str = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'å', 'ã', 'Á', 'À', 'Â', 'Ä', 'Ã', 'Å'), array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'A'), $str
    );

    $str = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $str
    );

    $str = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $str
    );

    $str = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'õ', 'Ó', 'Ò', 'Ö', 'Ô', 'Õ'), array('o', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O'), $str
    );

    $str = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $str
    );

    $str = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç','Ð','Š','š','Ÿ','Ý','ý','ÿ'), array('n', 'N', 'c', 'C','D','S','s','Y','Y','y','y'), $str
    );
    
    // otros caracteres especiales con su reemplazo
    $str = str_replace(
            array('Ž','ž','-','–','µ','~','Æ','æ','Œ','œ','¼','½','¾'), array('Z','ž','-','-','m','','AE','ae','OE','oe','1/4','1/2','3/4'), $str
    );
    
    $str = str_replace(
            array('¹','²','³','×','÷','º','ª','°','ø','Ø','©','ƒ','™'), array('1','2','3','*','/','o','a','o','o','O','c','f','TM'), $str
    );
    
    $str = str_replace(
            array('®','‰','·','•','ß','‡','†','Þ','þ','ð','§','—'), array('R','%','.','.','B','','t','p','p','d','s','-'), $str
    );
    

    $this->setString($str);
  }

  function sanitizeSpecials() {
    $str = str_replace(
            array("¡","¦","¨","¯","´","¿","¢","£","¤","¥","±","«","»","€","„","…","ˆ","‹","‘","’","“","”","›","˜"), '', $this->getString()
    );
    /*$str = str_replace(
            array("¡","¦","¨","¯","´","¸","¿","¢","£","¤","¥","±","«","»","€",
      "‚",",","„","…","ˆ","‹","‘","’","“","”","›","˜"), '', $this->getString()
    );*/

    $this->setString($str);
  }

  function sanitizeSpecialsSms() {
    $str = str_replace(array(
        "\\", "¨", "º", "~", "|", "·", "♦", "�",
        "[", "]", "^", "<code>", "{", "}",
        "¨", "´", "€", "\""
            ), '', $this->getString());
    
    $this->setString($str);
  }

  function nonPrintable(){
    $str = preg_replace('/[\x00-\x1F\x7F-\xA0\xAD]/u', '', $this->getString());
    $this->setString($str);
  }

  function sanitizeBlanks($replace = "") {
    $str = str_replace(" ", $replace, $this->getString());
    $this->setString($str);
  }

  function toLowerCase() {
    $this->setString(strtolower($this->getString()));
  }

}