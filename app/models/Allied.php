<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class Allied extends Modelbase {

  public $idMasteraccount,
          $idAllied,
          $idPaymentPlan,
          $idAccountCategory,
          $name,
          $idCity,
          $email,
          $address,
          $zipcode,
          $created,
          $updated,
          $nit,
          $status,
          $phone;

  public function initialize() {
    $this->belongsTo("idMasteraccount", "Masteraccount", "idMasteraccount");
    $this->belongsTo("idPaymentPlan", "PaymentPlan", "idPaymentPlan");
    $this->hasMany("idAllied", "Account", "idAllied");
    $this->belongsTo("idCity", "City", "idCity");
    $this->hasOne("idAllied", "Alliedconfig", "idAllied");
    $this->hasMany("idAllied", "Alxs", "idAllied");
    $this->hasOne("idAllied", "Usertype", "idAllied");
    $this->hasMany("idAllied", "Admincontact", "idAllied");
    $this->hasMany("idAllied", "Technicalcontact", "idAllied");
    $this->hasMany('idAllied', 'Footer', 'idAllied');
    $this->hasMany('idAllied', 'MailTemplateCategory', 'idAllied');
    $this->hasMany("idAllied", "PriceList", "idAllied");
    $this->hasMany("idAllied", "Tax", "idAllied");
    $this->hasMany("idAllied", "SupportContact", "idAllied");
    $this->hasMany("idAllied", "AccountCategory", "idAllied");
    $this->hasOne('idAccountCategory', 'AccountCategory', 'idAccountCategory');
    $this->hasMany("idAllied", "Rate", "idAllied");
    $this->hasMany("idAllied", "Range", "idAllied");
    $this->hasMany("idAllied", "LandingPageTemplate", "idAllied");
  }

  public function validation() {
    $this->validate(new PresenceOf(array(
        'field' => 'name',
        'message' => 'El nombre del Aliado es obligatorio, por favor valide la información'
    )));

    $this->validate(new Uniqueness(array(
        'field' => 'nit',
        'message' => 'Ya existe un Aliado con el nit ingresado, por favor valide la información'
    )));

    if ($this->validationHasFailed() == true) {
      return false;
    }
  }

}
