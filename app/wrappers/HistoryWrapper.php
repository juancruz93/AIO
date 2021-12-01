<?php

namespace Sigmamovil\Wrapper;

class HistoryWrapper extends \BaseWrapper {

  private $history;

  public function findHistory($page, $data) {
    $this->nameRoles = \Phalcon\DI::getDefault()->get('nameRoles');

    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");


    $where = "";
    if (isset($data["string"]) and $data["string"] != "") {
      $where .= "(Trace.operation LIKE '%" . $data["string"] . "%' OR Trace.description LIKE '%" . $data["string"] . "%' OR Trace.userDescription LIKE '%" . $data["string"] . "%') ";

      if ($data["inidate"] != "" and $data["findate"] != "") {
        $where .= " AND ";
      }
    }
    if ($data["inidate"] != "" and $data["findate"] != "") {
      $inidate = strtotime($data["inidate"]);
      $findate = strtotime($data["findate"]);
      $where .= "(Trace.created BETWEEN " . $inidate . " AND " . $findate . ")";
    }
    if ($this->user->Role->name == $this->nameRoles->master) {
      if ($where != "") {
        $where .= " AND ";
      }
      $where .= "(Trace.idMasteraccount = " . $this->user->userType->idMasteraccount . ")";
    }
    if ($this->user->Role->name == $this->nameRoles->allied) {
      if ($where != "") {
        $where .= " AND ";
      }
      $where .= "(Trace.idAllied = " . $this->user->userType->idAllied . ")";
    }


    if ($data["idMasteraccount"] != "" AND $data["idMasteraccount"] != "0") {
      if ($where != "") {
        $where .= " AND ";
      }
      $where .= "(Trace.idMasteraccount = " . $data["idMasteraccount"] . ")";
    }
    if ($data["idAllied"] != "" AND $data["idAllied"] != "0") {
      if ($where != "") {
        $where .= " AND ";
      }
      $where .= "(Trace.idAllied = " . $data["idAllied"] . ")";
    }
    if ($data["idAccount"] != "") {
      if ($where != "") {
        $where .= " AND ";
      }
      $where .= "(Trace.idAccount = " . $data["idAccount"] . ")";
    }
 
    if ($data["idSubaccount"] != "") {
      if ($where != "") {
        $where .= " AND ";
      }
      $where .= "(Trace.idSubaccount = " . $data["idSubaccount"] . ")";
    }

//    if ($this->user->Role->name == $this->nameRoles->master) {
//      if ($where != "") {
//        $where .= " AND ";
//      }
//      $where .= "(Trace.idMasteraccount = " . $this->user->userType->idMasteraccount . ")";
//    }
//
//    if ($this->user->Role->name == $this->nameRoles->allied) {
//      if ($where != "") {
//        $where .= " AND ";
//      }
//      $where .= "(Trace.idAllied = " . $this->user->userType->idAllied . ")";
//    }
    $this->data = $this->modelsManager->createBuilder()
            ->from('Trace')
            ->where($where)
            ->orderBy("Trace.created DESC")
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)
            ->offset($page)
            ->getQuery()
            ->execute();
    $this->totals = $this->modelsManager->createBuilder()
            ->from('Trace')
            ->where($where)
            ->orderBy("Trace.created DESC")
            ->getQuery()
            ->execute();
    $this->modelData();
  }

  public function findMasteraccounts() {

    $this->dataMasteraccount = \Masteraccount::find([
                "conditions" => "deleted = 0"]);

    $this->modelMasteraccount();
  }

  public function findAllieds($idMasteraccount) {
    $this->nameRoles = \Phalcon\DI::getDefault()->get('nameRoles');
    if ($this->user->Role->name == $this->nameRoles->master) {
      $idMasteraccount = $this->user->userType->idMasteraccount;
    }

    $this->dataAllied = \Allied::find([
                "conditions" => "deleted = 0 AND idMasteraccount = ?0",
                "bind" => array(0 => $idMasteraccount)]);
    $this->modelAllied();
  }

  public function findAccounts($idAllied) {
    $this->nameRoles = \Phalcon\DI::getDefault()->get('nameRoles');
    if ($this->user->Role->name == $this->nameRoles->allied) {
      $idAllied = $this->user->userType->idAllied;
    }

    $this->dataAccount = \Account::find([
                "conditions" => "deleted = 0 AND idAllied = ?0",
                "bind" => array(0 => $idAllied)]);

    $this->modelAccounts();
  }

  public function findSubaccounts($idAccount) {

    $this->dataSubaccount = \Subaccount::find([
                "conditions" => "deleted = 0 AND idAccount = ?0",
                "bind" => array(0 => $idAccount)]);
    $this->modelSubaccounts();
  }

  public function modelData() {
    $this->history = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $data) {
      $trace = new \stdClass();
      $trace->idTrace = $data->idTrace;
      $trace->idUserOriginal = $data->idUserOriginal;
      $trace->idUserEffective = $data->idUserEffective;
      $trace->result = $data->result;
      $trace->operation = $data->operation;
      $trace->description = $data->description;
      $trace->userDescription = $data->userDescription;
      $trace->date = $data->date;
      $trace->created = date("d/m/Y  H:ia", $data->created);
      $trace->updated = date("d/m/Y  H:ia", $data->updated);
      $trace->createdBy = $data->createdBy;
      $trace->updatedBy = $data->updatedBy;

      $arr[] = $trace;
    }
    $this->history['items'] = $arr;
  }

  public function modelMasteraccount() {
    $this->masteraccount = array();

    foreach ($this->dataMasteraccount as $data) {
      $item = new \stdClass();
      $item->idMasteraccount = $data->idMasteraccount;
      $item->idAccountCategory = $data->idMasteraccount;
      $item->idCity = $data->idCity;
      $item->idPaymentPlan = $data->idPaymentPlan;
      $item->name = $data->name;
      $item->description = $data->description;
      $item->nit = $data->nit;
      $item->address = $data->address;
      $item->phone = $data->phone;
      $item->status = $data->status;
      $item->deleted = $data->deleted;
      $item->created = date("d/m/Y  H:ia", $data->created);
      $item->updated = date("d/m/Y  H:ia", $data->updated);
      $item->createdBy = $data->createdBy;
      $item->updatedBy = $data->updatedBy;

      $this->masteraccount[] = $item;
    }
  }

  public function modelAllied() {
    $this->allied = array();

    foreach ($this->dataAllied as $data) {
      $item = new \stdClass();
      $item->idAllied = $data->idAllied;
      $item->idMasteraccount = $data->idMasteraccount;
      $item->idAccountCategory = $data->idAccountCategory;
      $item->idCity = $data->idCity;
      $item->idPaymentPlan = $data->idPaymentPlan;
      $item->name = $data->name;
      $item->zipcode = $data->zipcode;
      $item->nit = $data->nit;
      $item->email = $data->email;
      $item->address = $data->address;
      $item->phone = $data->phone;
      $item->status = $data->status;
      $item->deleted = $data->deleted;
      $item->created = date("d/m/Y  H:ia", $data->created);
      $item->updated = date("d/m/Y  H:ia", $data->updated);
      $item->createdBy = $data->createdBy;
      $item->updatedBy = $data->updatedBy;

      $this->allied[] = $item;
    }
  }

  public function modelAccounts() {
    $this->account = array();

    foreach ($this->dataAccount as $data) {
      $item = new \stdClass();
      $item->idAllied = $data->idAllied;
      $item->idAccount = $data->idAccount;
      $item->idAccountCategory = $data->idAccountCategory;
      $item->idCity = $data->idCity;
      $item->idPaymentPlan = $data->idPaymentPlan;
      $item->name = $data->name;
      $item->nit = $data->nit;
      $item->email = $data->email;
      $item->address = $data->address;
      $item->phone = $data->phone;
      $item->status = $data->status;
      $item->deleted = $data->deleted;
      $item->attachments = $data->attachments;
      $item->created = date("d/m/Y  H:ia", $data->created);
      $item->updated = date("d/m/Y  H:ia", $data->updated);
      $item->createdBy = $data->createdBy;
      $item->updatedBy = $data->updatedBy;

      $this->account[] = $item;
    }
// 
//    $arr = array();
//    foreach ($this->dataSubaccount as $data) {
//      $item = new \stdClass();
//      $item->idSubaccount = $data->idSubaccount;
//      $item->idAccount = $data->idAccount;
//      $item->idCity = $data->idCity;
//      $item->name = $data->name;
//      $item->description= $data->description;
//      $item->status = $data->status;
//      $item->deleted = $data->deleted;
//      $item->attachments= $data->attachments;
//      $item->created = date("d/m/Y  H:ia", $data->created);
//      $item->updated = date("d/m/Y  H:ia", $data->updated);
//      $item->createdBy = $data->createdBy;
//      $item->updatedBy = $data->updatedBy;
//
//      $arr[] = $item;
//    }
//    $this->filterAccount['subaccounts'] = $arr;
  }

  public function modelSubaccounts() {
    $this->subaccount = array();

    foreach ($this->dataSubaccount as $data) {
      $item = new \stdClass();
      $item->idSubaccount = $data->idSubaccount;
      $item->idAccount = $data->idAccount;
      $item->idCity = $data->idCity;
      $item->name = $data->name;
      $item->description = $data->description;
      $item->status = $data->status;
      $item->deleted = $data->deleted;
      $item->attachments = $data->attachments;
      $item->created = date("d/m/Y  H:ia", $data->created);
      $item->updated = date("d/m/Y  H:ia", $data->updated);
      $item->createdBy = $data->createdBy;
      $item->updatedBy = $data->updatedBy;

      $this->subaccount[] = $item;
    }
  }

  function getHistory() {
    return $this->history;
  }

  function getMasteraccounts() {
    return $this->masteraccount;
  }

  function getAllieds() {
    return $this->allied;
  }

  function getAccounts() {
    return $this->account;
  }

  function getSubaccounts() {
    return $this->subaccount;
  }

}
