<?php

namespace Sigmamovil\Wrapper;

class CurrencyWrapper extends \BaseWrapper {

  private $form;

  public function __construct() {
    $this->form = new \CurrencyForm();
    parent::__construct();
  }

  public function listCurrency($page, $name) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $filterName = $sanitize->sanitize(((isset($name)) ? $name : ''), "string");

    $conditions = array(
        "conditions" => "deleted = ?0 AND name LIKE '%{$filterName}%'",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "order" => "idCurrency DESC",
        "offset" => $page,
    );

    $currency = \Currency::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \Currency::count($conditions);

    $data = [];
    if (count($currency) > 0) {
      foreach ($currency as $key => $value) {
        $data[$key] = array(
            "idCurrency" => $value->idCurrency,
            "name" => $value->name,
            "shortName" => $value->shortName,
            "symbol" => $value->symbol,
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

  public function createCurrency($data) {
    $currency = new \Currency();
    $this->form->bind($data, $currency);

    $cur = \Currency::findFirst(array(
                "conditions" => "name = ?0 AND shortName = ?1 AND deleted = ?2",
                "bind" => array($currency->name, $currency->shortName, 0)
    ));

    if ($cur) {
      throw new \InvalidArgumentException("El tipo de moneda que intenta guardar ya existe, debe validar el nombre o la abreviatura");
    }
    
    $currency->symbol = $currency->symbol[0];
    $currency->shortName = strtoupper($currency->shortName);
    if (!$this->form->isValid() || !$currency->save()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($currency->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "El tipo de moneda se ha guardado exitosamente"];
  }

  public function getCurrency($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de moneda inválido");
    }

    $currency = \Currency::findFirst(array(
                "conditions" => "idCurrency = ?0",
                "bind" => array($id)
    ));

    if (!$currency) {
      throw new \InvalidArgumentException("El tipo de moneda que intenta editar no existe");
    }

    $data = array(
        "idCurrency" => $currency->idCurrency,
        "name" => $currency->name,
        "shortName" => $currency->shortName,
        "symbol" => $currency->symbol,
        "status" => $currency->status
    );

    return $data;
  }

  public function editCurrency($data) {
    $currency = \Currency::findFirst(array(
                "conditions" => "idCurrency = ?0",
                "bind" => array($data["idCurrency"])
    ));

    if (!$currency) {
      throw new \InvalidArgumentException("El tipo de moneda que intenta editar no existe");
    }

    $this->form->bind($data, $currency);

    $cur = \Currency::findFirst(array(
                "conditions" => "deleted = ?0 AND ((idCurrency != ?1 AND name = ?2) OR (idCurrency != ?1 AND shortName = ?3))",
                "bind" => array(0,$currency->idCurrency, $currency->name, $currency->shortName)
    ));

    if ($cur) {
      throw new \InvalidArgumentException("El tipo de moneda que intenta guardar ya existe, debe validar el nombre o la abreviatura");
    }

    $currency->shortName = strtoupper($currency->shortName);
    if (!$this->form->isValid() || !$currency->update()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($currency->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "El tipo de moneda se ha actualizado exitosamente"];
  }

  public function deleteCurrency($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de moneda inválido");
    }

    $currency = \Currency::findFirst(array(
                "conditions" => "idCurrency = ?0",
                "bind" => array($id)
    ));

    if (!$currency) {
      throw new \InvalidArgumentException("El tipo de moneda que intenta eliminar no existe");
    }

    $currency->deleted = time();

    if (!$currency->update()) {
      foreach ($currency->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "El tipo de moneda se ha eliminado exitosamente"];
  }

}
