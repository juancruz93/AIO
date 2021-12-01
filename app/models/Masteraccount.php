<?php

use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class Masteraccount extends Modelbase {

  public $idMasteraccount,
          $name,
          $description,
          $nit,
          $address,
          $phone,
          $idCity,
          $status,
          $idPaymentPlan,
          $idAccountCategory,
          $createdBy,
          $updatedBy;

  public function initialize() {
    $this->hasMany("idMasteraccount", "Allied", "idMasteraccount");
    $this->hasMany("idMasteraccount", "Maxmta", "idMasteraccount");
    $this->hasMany("idMasteraccount", "Maxurldomain", "idMasteraccount");
    $this->hasMany("idMasteraccount", "Maxmailclass", "idMasteraccount");
    $this->hasMany("idMasteraccount", "Maxadapter", "idMasteraccount");
    $this->hasOne("idCity", "City", "idCity");
    $this->hasOne("idMasteraccount", "MasterConfig", "idMasteraccount");
    $this->hasOne("idMasteraccount", "Usertype", "idMasteraccount");
    $this->hasMany("idMasteraccount", "Mxs", "idMasteraccount");
    $this->hasMany("idMasteraccount", "PriceList", "idMasteraccount");
    $this->hasMany("idMasteraccount", "Tax", "idMasteraccount");
    $this->belongsTo('idPaymentPlan', 'PaymentPlan', 'idPaymentPlan');
    $this->hasMany("idMasteraccount", "AccountCategory", "idMasteraccount");
    $this->hasOne('idAccountCategory', 'AccountCategory', 'idAccountCategory');
    $this->belongsTo("idSmsSendingRule", "SmsSendingRule", "idSmsSendingRule");
    $this->hasMany("idMasteraccount", "Mxssr", "idMasteraccount");
  }

  public function validation() {

    $this->validate(new Uniqueness(array(
        'field' => 'name',
        'message' => 'Ya existe una Cuenta Maestra con el nombre ingresado, por favor valide la información'
    )));
    $this->validate(new Uniqueness(array(
        'field' => 'nit',
        'message' => 'Ya existe una Cuenta Maestra con el nit ingresado, por favor valide la informaci&oacute;n'
    )));

    if ($this->validationHasFailed() == true) {
      return false;
    }
  }

}
