<?php

class Role extends \Phalcon\Mvc\Model {

  public $idRole,
          $created,
          $updated,
          $name,
          $nameForView;

  public function initialize() {
    $this->hasMany('idRole', 'Allowed', 'idRole');
    $this->hasOne("idRole", "User", "idRole");
  }

}
