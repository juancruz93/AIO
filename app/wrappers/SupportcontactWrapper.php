<?php

namespace Sigmamovil\Wrapper;

/**
 * Description of SupportcontactWrapper
 *
 * @author desarrollo3
 */
class SupportcontactWrapper {

  public $limit,
          $offset,
          $idAllied,
          $idMasteraccount,
          $supportcontact,
          $modelsManager,
          $idSupportContact;

  function __construct() {
    $this->limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
  }

  public function getAllTecnichalContact() {
    $this->data = $this->modelsManager->createBuilder()
            ->from("SupportContact")
            ->limit($this->limit)
            ->offset($this->offset)
            ->where("idAllied = :id: AND deleted = 0", array("id" => $this->idAllied))
            ->getQuery()
            ->execute();
    $this->totals = $this->modelsManager->createBuilder()
            ->from("SupportContact")
            ->where("idAllied = :id: AND deleted = 0", array("id" => $this->idAllied))
            ->getQuery()
            ->execute();
    $this->modelData();
  }

  public function getFirstTecnichalContact() {
    $this->data = \SupportContact::findFirst([
                "conditions" => "idSupportContact = ?0",
                "bind" => [0 => $this->idSupportContact]]);
    $this->modelDataFirst();
  }

  public function modelDataFirst() {
    $su = new \stdClass();
    foreach ($this->data as $key => $value) {
      $su->$key = $value;
    }
    $this->supportcontact[] = $su;
  }

  public function modelData() {
    $this->supportcontact = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / $this->limit));
    $arr = array();
    foreach ($this->data as $value) {
      $su = new \stdClass();
      $su->idSupportContact = $value->idSupportContact;
      $su->name = $value->name;
      $su->lastname = $value->lastname;
      $su->phone = $value->phone;
      $su->email = $value->email;
      $su->type = $value->type;
      $su->created = $value->created;
      $su->updated = $value->updated;
      $arr[] = $su;
    }
    $this->supportcontact['items'] = $arr;
  }

  public function addTecnichalContact() {
    $obj = new \SupportContact();
    if (!isset($this->supportcontact["type"])) {
      throw new \InvalidArgumentException("Debes seleccionar un tipo de contacto");
    }
    foreach ($this->supportcontact as $key => $value) {
      $obj->$key = $value;
    }
    $obj->idAllied = $this->idAllied;
    $this->saveObj($obj);
  }

  public function editTecnichalContact() {
    $obj = new \SupportContact();
    foreach ($this->supportcontact as $key => $value) {
      $obj->$key = $value;
    }
    $this->saveObj($obj);
  }

  public function saveObj($obj) {
    if (!$obj->save()) {
      foreach ($obj->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  function setIdAllied($idAllied) {
    $this->idAllied = $idAllied;
  }

  function setOffset($offset) {
    $this->offset = $offset;
  }

  function setIdMasteraccount($idMasteraccount) {
    $this->idMasteraccount = $idMasteraccount;
  }

  function getSupportcontact() {
    return $this->supportcontact;
  }

  function setSupportcontact($supportcontact) {
    $this->supportcontact = $supportcontact;
  }

  function setIdSupportContact($idSupportContact) {
    $this->idSupportContact = $idSupportContact;
  }

}
