<?php

use Phalcon\Mvc\Collection;

class Modelbasemongo extends Collection {

  public function beforeValidationOnUpdate() {
//    $this->description = ((isset($this->description)) ? $this->description : "Sin descripción");
    $this->updated = time();
    $email = ((\Phalcon\DI::getDefault()->has('user') && isset(\Phalcon\DI::getDefault()->get('user')->email)) ? \Phalcon\DI::getDefault()->get('user')->email : "Indefinido");
    if (!isset($this->updatedBy) || $this->updatedBy == "Indefinido") {
      $this->updatedBy = $email;
    }
  }

  public function beforeValidationOnCreate() {
//    $this->description = ((isset($this->description)) ? $this->description : "Sin descripción");
    $this->created = time();
    $this->updated = time();
    $deleted = ((isset($this->deleted)) ? $this->deleted : 0);
    $this->deleted = $deleted;
    $email = ((\Phalcon\DI::getDefault()->has('user') && isset(\Phalcon\DI::getDefault()->get('user')->email)) ? \Phalcon\DI::getDefault()->get('user')->email : "Indefinido");
    $this->createdBy = $email;
    $this->updatedBy = $email;
    if (isset($this->email)) {
      $this->email = strtolower($this->email);
    }
    if (!isset($this->updatedBy) || $this->updatedBy == "Indefinido") {
      $this->updatedBy = $email;
    }
    if (!isset($this->createdBy) || $this->createdBy == "Indefinido") {
      $this->createdBy = $email;
    }
  }

}
