<?php

/* 
 * Esta clase puede generar los thumbail que no se han registrado o guardado, esto puede ser de manera general o de manera individual
 */
require_once(__DIR__ . "/../bootstrap/index.php");

$generator = new ThumbnailGenerator();
$generator->generator();

class ThumbnailGenerator{
  
  protected $template,
            $idSelected,
            $offSet = 0,
            $limit  = 8000,
            $flag = true;
  
  public function __construct($template = true,$idSelected = false) {
    $this->template = $template;
    $this->idSelected = $idSelected;
  }
  
  public function generator(){
    if($this->template){
      if(!$this->idSelected){
       // while($this->flag){
          $templates = \MailTemplate::find(array("offset"=>$offSet,"limit"=>$limit));
          foreach ($templates as $key  => $value){
			//var_dump($value->idMailTemplate);exit();
            $this->save($value);
          }
        //}
      }
    }
  }
  
  private function save($template){
	var_dump("hola1");
    $dirAcc = getcwd() . "/public/assets/";
    $dir = "";
var_dump("hola2");
    if (!isset($template->idAccount)) {
      return true;
    } else {
      $dirAcc .= "{$template->idAccount}/images/templates/{$template->idMailTemplate}_thumbnail.png";
    }
	var_dump("hola3");
    $dir = $dirAcc;
    if (file_exists($dir)) {
      return true;
    }
	var_dump("hola4");
	var_dump("52.55.110.0/thumbnail/mailtemplateshow/{$template->idMailTemplate} {$dir}");
    $domain = $this->ipServer->ip."/";
    exec("wkhtmltoimage --quality 25 --zoom 0.2 --width 180 --height 180 52.55.110.0/thumbnail/mailtemplateshow/{$template->idMailTemplate} {$dir}");
	var_dump("Todo bien mani");
  }
}


