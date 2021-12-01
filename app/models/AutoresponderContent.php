<?php

class AutoresponderContent extends Modelbase
{
  public $idAutoreponderContent,
      $idAutoresponder,
      $type,
      $content;

  public function initialize()
  {
    $this->hasOne("idAutoresponder", "Autoresponder", "idAutoresponder");
  }

}