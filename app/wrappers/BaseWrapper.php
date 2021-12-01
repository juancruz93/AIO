<?php

class BaseWrapper {

  public $logger;
  public $message;
  public $modelsManager;
  public $db;
  public $data;
  public $user;
  public $limit;
  public $urlManager;
  public $asset;
  public $idAllied;
  private $nameRoles;

  public function __construct() {
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
    $this->db = \Phalcon\DI::getDefault()->get('db');
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
    $this->user = ((\Phalcon\DI::getDefault()->has('user')) ? Phalcon\DI::getDefault()->get('user') : "indefinido");
    $this->limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    $this->asset = \Phalcon\DI::getDefault()->get('asset');
    $this->urlManager = \Phalcon\DI::getDefault()->get('urlManager');
    $this->services = \Phalcon\DI::getDefault()->get('services');
    $this->idAllied = \Phalcon\DI::getDefault()->get('idAllied');
    $this->nameRoles = \Phalcon\DI::getDefault()->get('nameRoles');
  }

  public function setData($data = null) {
    if ($data == null || !is_object($data)) {
      throw new Exception("Invalid data..");
    }

    $this->data = $data;
  }

  public function setMessage($message) {
    $this->message = $message;
  }

  public function getMessage() {
    return $this->message;
  }

  public function validateDate($date) {
    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)){
      return true;
    }
    return false;
  }

  public function getConditionsForValidateNameInAllLevels($name) {
    $conditions = array(
        "conditions" => "name = ?0 AND deleted = 0",
        "bind" => array($name)
    );

    if (isset($this->user->Usertype->Masteraccount->idMasteraccount)) {
      $conditions["conditions"] = "name = ?0 AND deleted = 0 AND idMasteraccount = ?1";
      $conditions["bind"] = array($name, $this->user->Usertype->Masteraccount->idMasteraccount);
    } else if (isset($this->user->Usertype->Allied->idAllied)) {
      $conditions["conditions"] = "name = ?0 AND deleted = 0 AND idAllied = ?1";
      $conditions["bind"] = array($name, $this->user->Usertype->Allied->idAllied);
    }

    return $conditions;
  }
  
  public function calculateTotalPages($totalPages){
    return array("total" => count($totalPages), "total_pages" => ceil(count($totalPages) / $this->limit));
  }

}
