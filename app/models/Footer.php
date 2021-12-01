<?php

class Footer extends Modelbase {

  public $idFooter,
      $idAllied,
      $name,
      $description,
      $content,
      $deleted;

  public function initialize() {
    $this->belongsTo('idAllied', 'Allied', 'idAllied');
    $this->hasMany("idFooter", "AccountConfig", "idFooter");
  }

}
