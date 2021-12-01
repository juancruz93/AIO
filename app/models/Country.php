<?php

class Country extends Modelbase {

  public $idCountry,
          $idCurrency,
          $minDigits,
          $maxDigits,
          $name,
          $phoneCode;

  public function initialize() {
    $this->hasMany("idCountry", "State", "idCountry");
    $this->hasMany("idCountry", "Tax", "idCountry");
    $this->hasMany("idCountry", "PriceList", "idCountry");
    $this->hasMany("idCountry", "SmsSendingRule", "idCountry");
    $this->belongsTo("idCurrency", "Currency", "idCurrency");
    $this->hasMany("idCountry", "Rate", "idCountry");
  }

}
