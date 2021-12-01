<?php

/**
 * Description of NameSender
 *
 * @author desarrollo3
 */
class NameSender extends Modelbase {

  public $idNameSender,
          $idAccount,
          $name,
          $status,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $deleted;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->hasMany("idNameSender", "Mail", "idNameSender");
    $this->hasMany("idNameSender", "Autoresponder", "idNameSender");
  }

  public function beforeValidationOnCreate() {
    $this->created = time();
    $this->updated = time();
    $email = "Indefinido";
    if (isset($this->createdBy)) {
      $email = $this->createdBy;
    }
    if (\Phalcon\DI::getDefault()->has('user') && isset(\Phalcon\DI::getDefault()->get('user')->email)) {
      $email = \Phalcon\DI::getDefault()->get('user')->email;
    }
    $this->createdBy = $email;
    $this->updatedBy = $email;
  }

  public function beforeValidationOnUpdate() {
    $this->updated = time();
    $email = ((\Phalcon\DI::getDefault()->has('user') && isset(\Phalcon\DI::getDefault()->get('user')->email)) ? \Phalcon\DI::getDefault()->get('user')->email : "Indefinido");
    if (!isset($this->updatedBy) || $this->updatedBy == "Indefinido") {
      $this->updatedBy = $email;
    }
  }

}
