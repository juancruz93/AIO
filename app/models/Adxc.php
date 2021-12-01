<?php

class Adxc extends Modelbase {

  public    $idadxc,
            $status,
            $idCountry,
            $idAdapter,
            $tollfreenumber;

  public function initialize() {
   $this->hasMany("idCountry", "Country", "idCountry");
   $this->belongsTo('idAdapter', 'Adapter', 'idAdapter');
  }

}
