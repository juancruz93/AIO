<?php

/**
 * 
 *
 * @author desarrollo3
 */
class Domain extends Modelbasemongo {

  public $idDomain,
          $idAccount,
          $created,
          $deleted,
          $status,
          $domain,
          $createdBy;

  public function writeAttribute($attribute, $value) {
    return $this->{$attribute} = $value;
  }

  public function getSource() {
    return "domain";
  }

}
