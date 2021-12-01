<?php

namespace Sigmamovil\Wrapper;

class PricelistWrapper extends \BaseWrapper {

  private $form;

  public function __construct() {
    $this->form = new \PricelistForm();
    parent::__construct();
  }

  public function listPriceList($page, $name) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $filter = new \Phalcon\Filter;

    $filterName = $filter->sanitize(((isset($name)) ? $name : ""), "string");
    $idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : '');
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : '');
    $cond1 = ((!empty($idMasteraccount)) ? "AND idMasteraccount = {$filter->sanitize($idMasteraccount, "int")}" : "AND idMasteraccount IS NULL");
    $cond2 = ((!empty($idAllied)) ? "AND idAllied = {$filter->sanitize($idAllied, "int")}" : "AND idAllied IS NULL");

    $conditions = array(
        "conditions" => "deleted = ?0 {$cond1} {$cond2} AND name like '%{$filterName}%'",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "order" => "idPriceList DESC",
        "offset" => $page
    );

    $pricelist = \PriceList::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \PriceList::count($conditions);

    $data = [];
    if (count($pricelist) > 0) {
      foreach ($pricelist as $key => $value) {
        $data[$key] = array(
            "idPriceList" => $value->idPriceList,
            "idMasteraccount" => $value->idMasteraccount,
            "idAllied" => $value->idAllied,
            "service" => $value->Services->name,
            "country" => $value->Country->name,
            "name" => $value->name,
            "description" => $value->description,
            "accountingMode" => $value->accountingMode,
            "minValue" => $value->minValue,
            "maxValue" => $value->maxValue,
            "price" => $value->price,
            "status" => $value->status,
            "created" => date("Y-m-d", $value->created),
            "updated" => date("Y-m-d", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy
        );
      }
    }

    $array = array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );

    return $array;
  }

  public function listfullPriceList($idServices, $name) {
    $filter = new \Phalcon\Filter;

    $filterName = (isset($name) ? "AND name LIKE '%{$filter->sanitize($name, "string")}%'" : "");
    $idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : '');
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : '');
    $cond1 = ((!empty($idMasteraccount)) ? "AND idMasteraccount = {$filter->sanitize($idMasteraccount, "int")}" : "AND idMasteraccount IS NULL");
    $cond2 = ((!empty($idAllied)) ? "AND idAllied = {$filter->sanitize($idAllied, "int")}" : "AND idAllied IS NULL");

    $conditions = array(
        "conditions" => "deleted = ?0 {$cond1} {$cond2} AND idServices = ?1 {$filterName}",
        "bind" => array(0, $idServices),
    );

    $pricelist = \PriceList::find($conditions);

    $data = [];
    if (count($pricelist) > 0) {
      foreach ($pricelist as $key => $value) {
        $data[$key] = array(
            "idPriceList" => $value->idPriceList,
            "name" => $value->name,
        );
      }
    }

    return $data;
  }

  public function validateNewPriceListName($pricelist) {
    $conditions = array(
        "conditions" => "name = ?0 AND deleted = 0",
        "bind" => array($pricelist->name)
    );

    if (isset($this->user->Usertype->Masteraccount->idMasteraccount)) {
      $conditions["conditions"] = "name = ?0 AND deleted = 0 AND idMasteraccount = ?1";
      $conditions["bind"] = array($pricelist->name, $this->user->Usertype->Masteraccount->idMasteraccount);
    } else if (isset($this->user->Usertype->Allied->idAllied)) {
      $conditions["conditions"] = "name = ?0 AND deleted = 0 AND idAllied = ?1";
      $conditions["bind"] = array($pricelist->name, $this->user->Usertype->Allied->idAllied);
    }

    $pl = \PriceList::findFirst($conditions);

    if ($pl) {
      throw new \InvalidArgumentException("Ya existe una lista de precios con el nombre ingresado, por favor valida la información");
    }

    if ($pl && $pl->idPriceList != $pricelist->idPriceList) {
      throw new \InvalidArgumentException("Ya existe una lista de precios con el nombre ingresado, por favor valida la información");
    }
  }

  public function getPriceList($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de lista de precios inválido");
    }

    $pricelist = \PriceList::findFirst(array(
                "conditions" => "idPriceList = ?0",
                "bind" => array($id)
    ));



    if (!$pricelist) {
      throw new \InvalidArgumentException("La lista de precios que intenta editar no existe");
    }

    $data = array(
        "idPriceList" => $pricelist->idPriceList,
        "idMasteraccount" => $pricelist->idMasteraccount,
        "idAllied" => $pricelist->idAllied,
        "idServices" => $pricelist->idServices,
        "idCountry" => $pricelist->idCountry,
        "name" => $pricelist->name,
        "description" => $pricelist->description,
        "accountingMode" => $pricelist->accountingMode,
        "minValue" => (int) $pricelist->minValue,
        "maxValue" => (int) $pricelist->maxValue,
        "price" => (double) $pricelist->price,
        "status" => (int) $pricelist->status
    );

    return $data;
  }

  public function validatePriceListName($name, $pricelist) {
    $conditions = array(
        "conditions" => "name = ?0 AND deleted = 0",
        "bind" => array($name)
    );

    if (isset($this->user->Usertype->Masteraccount->idMasteraccount)) {
      $conditions["conditions"] = "name = ?0 AND deleted = 0 AND idMasteraccount = ?1";
      $conditions["bind"] = array($pricelist->name, $this->user->Usertype->Masteraccount->idMasteraccount);
    } else if (isset($this->user->Usertype->Allied->idAllied)) {
      $conditions["conditions"] = "name = ?0 AND deleted = 0 AND idAllied = ?1";
      $conditions["bind"] = array($pricelist->name, $this->user->Usertype->Allied->idAllied);
    }
  }

  public function createPriceList($data) {
    $pricelist = new \PriceList();
    $this->form->bind($data, $pricelist);

    $conditions = array(
        "conditions" => "name = ?0",
        "bind" => array($pricelist->name)
    );

    if (isset($this->user->Usertype->Masteraccount->idMasteraccount)) {
      $conditions["conditions"] = "name = ?0 AND idMasteraccount = ?1";
      $conditions["bind"] = array($pricelist->name, $this->user->Usertype->Masteraccount->idMasteraccount);
    } else if (isset($this->user->Usertype->Allied->idAllied)) {
      $conditions["conditions"] = "name = ?0 AND idAllied = ?1";
      $conditions["bind"] = array($pricelist->name, $this->user->Usertype->Allied->idAllied);
    }

    $pl = \PriceList::findFirst($conditions);

    if ($pl) {
      throw new \InvalidArgumentException("Ya existe una lista de precios con el nombre ingresado, por favor valida la información");
    }

    $pricelist->minValue = ((empty($data["minValue"])) ? "0" : $data["minValue"]);
    $pricelist->maxValue = ((empty($data["maxValue"])) ? "0" : $data["maxValue"]);
    $pricelist->name = substr($pricelist->name, 0, 69);
    $pricelist->description = substr($pricelist->description, 0, 99);

    $pricelist->price = trim($pricelist->price);
    $pricelist->price = (empty($pricelist->price) ? 0 : $pricelist->price);

    $pricelist->idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : NULL);
    $pricelist->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);

    if (!$this->form->isValid() || !$pricelist->save()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($pricelist->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "La lista de precios se ha guardado exitosamente"];
  }
  
  public function editPriceList($data) {
    $pricelist = \PriceList::findFirst(array(
                "conditions" => "idPriceList = ?0",
                "bind" => array($data["idPriceList"])
    ));

    if (!$pricelist) {
      throw new \InvalidArgumentException("La lista de precios que intenta editar no existe");
    }

    $this->form->bind($data, $pricelist);
    $pricelist->minValue = ((empty($data["minValue"])) ? "0" : $data["minValue"]);
    $pricelist->maxValue = ((empty($data["maxValue"])) ? "0" : $data["maxValue"]);
    $pricelist->idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : NULL);
    $pricelist->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);

    if (!$this->form->isValid() || !$pricelist->update()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($pricelist->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "La lista de precios se ha actualizado exitosamente"];
  }

  public function deletePriceList($id) {
    if (!$id) {
      throw new \InvalidArgumentException("Dato de lista de precio inválido");
    }

    $pricelist = \PriceList::findFirst(array(
                "conditions" => "idPriceList = ?0",
                "bind" => array($id)
    ));

    if (!$pricelist) {
      throw new \InvalidArgumentException("El impuesto que intenta eliminar no existe");
    }

    $pricelist->deleted = time();
    if (!$pricelist->update()) {
      foreach ($pricelist->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "La lista de precios se ha eliminado exitosamente"];
  }

}
