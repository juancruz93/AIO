<?php


class Trace extends \Modelbase {

  public $idTrace,
          $idUserOriginal,
          $idUserEffective,
          $userDescription,
          $result,
          $operation,
          $description,
          $date,
          $created,
          $updated,
          $createdBy,
          $updatedBy,
          $idMasteraccount,
          $idAllied,
          $idAccount,
          $idSubaccount;

  public function initialize() {
  }

}
