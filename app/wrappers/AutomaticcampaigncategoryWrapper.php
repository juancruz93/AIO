<?php

namespace Sigmamovil\Wrapper;

class AutomaticcampaigncategoryWrapper extends \BaseWrapper {

  private $form;

  public function __construct() {
    $this->form = new \AutomaticcampaigncategoryForm();
    parent::__construct();
  }

  public function listautomaticcampaigncategory($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : Null));
    $name = (isset($filter->name) ? "AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');

    $conditions = array(
        "conditions" => "deleted = ?0 AND idAccount = ?1 {$name}",
        "bind" => array(0, $idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $autocampcateg = \AutomaticCampaignCategory::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = count(\AutomaticCampaignCategory::find($conditions));

    $data = array();
    if (count($autocampcateg) > 0) {
      foreach ($autocampcateg as $key => $value) {
        $data[$key] = array(
            "idAutomaticCampaignCategory" => $value->idAutomaticCampaignCategory,
            "idAccount" => $value->idAccount,
            "name" => $value->name,
            "description" => $value->description,
            "created" => date("Y-m-d", $value->created),
            "updated" => date("Y-m-d", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy
        );
      }
    }

    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );
  }

  public function saveautomaticcampaigncategory($data) {
    $autocampcateg = new \AutomaticCampaignCategory();
    $autocampcateg->idAccount = ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : Null);
    $autocampcateg->deleted = 0;
    $autocampcateg->status = 1;
    $autocampcateg->description = (isset($data->description) && $data->description != "") ? $data->description : "Sin descripción";

    $this->form->bind($data, $autocampcateg);

    $acc = \AutomaticCampaignCategory::findFirst(array(
                "conditions" => "deleted = ?0 AND name = ?1 AND idAccount = ?2",
                "bind" => array(0, ucwords($autocampcateg->name), $autocampcateg->idAccount)
    ));

    if ($acc) {
      throw new \InvalidArgumentException("La categoría que intenta guardar ya existe");
    }

    if (!$this->form->isValid() || !$autocampcateg->save()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($autocampcateg->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  public function getautomaticcampaigncategory($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de categoría de automatización de campaña inválido");
    }

    $autocampcateg = \AutomaticCampaignCategory::findFirst(array(
                "conditions" => "idAutomaticCampaignCategory = ?0",
                "bind" => array($id)
    ));

    if (!$autocampcateg) {
      throw new \InvalidArgumentException("La categoría que intenta editar no existe");
    }

    $data = array(
        "idAutomaticCampaignCategory" => $autocampcateg->idAutomaticCampaignCategory,
        "name" => $autocampcateg->name,
        "description" => $autocampcateg->description
    );

    return $data;
  }

  public function editautomaticcampaigncategory($data) {
    if (!isset($data["idAutomaticCampaignCategory"])) {
      throw new \InvalidArgumentException("Dato de categoría de automatización de campaña inválido");
    }

    $autocampcateg = \AutomaticCampaignCategory::findFirst(array(
                "conditions" => "idAutomaticCampaignCategory = ?0",
                "bind" => array($data["idAutomaticCampaignCategory"])
    ));

    if (!$autocampcateg) {
      throw new \InvalidArgumentException("La categoria de automatización de campaña que intenta editar no existe");
    }
    $autocampcateg->description = (isset($data->description) && $data->description != "") ? $data->description : "Sin descripción";
    $this->form->bind($data, $autocampcateg);
    $autocampcateg->status = 1;

    if (!$this->form->isValid() || !$autocampcateg->update()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($autocampcateg->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  public function deleteautomaticcampaigncategory($data) {
    if (!isset($data->id)) {
      throw new \InvalidArgumentException("Dato de categoría de automatización de campaña inválido");
    }

    $autocampcateg = \AutomaticCampaignCategory::findFirst(array(
                "conditions" => "idAutomaticCampaignCategory = ?0",
                "bind" => array($data->id)
    ));

    if (!$autocampcateg) {
      throw new \InvalidArgumentException("La categoría que intenta editar no existe");
    }

    $autocampcateg->deleted = 1;

    if (!$autocampcateg->update()) {
      foreach ($autocampcateg->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  public function getcategoryautocomplete($filter) {
    $sanitize = new \Phalcon\Filter;
    $category = \AutomaticCampaignCategory::find(array(
                "conditions" => "name like '%{$sanitize->sanitize($filter, "string")}%'"
    ));
    $data = array();
    if (count($category)) {
      foreach ($category as $key => $value) {
        $data["items"][$key] = array(
            "id" => $value->idAutomaticCampaignCategory,
            "name" => $value->name,
        );
      }
    }

    return $data;
  }
  
  public function getallcategory(){
    $category = \AutomaticCampaignCategory::find(array("conditions"=>" deleted = ?0 and status = ?1 and idAccount = ?2","bind"=>array(0,1,$this->user->Usertype->Subaccount->idAccount)));
    $data = array();
    if (count($category)) {
      foreach ($category as $key => $value) {
        $data[$key] = array(
            "id" => $value->idAutomaticCampaignCategory,
            "name" => $value->name,
        );
      }
    }

    return $data;
  }

}
