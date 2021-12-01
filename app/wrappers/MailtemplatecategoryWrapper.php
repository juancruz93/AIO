<?php

namespace Sigmamovil\Wrapper;

class MailtemplatecategoryWrapper extends \BaseWrapper {

  /**
   * @description consulta todos los datos para hacer el paginador primer parametro es la pagina y el segundo son los filtros 
   * @param Integer $page
   * @param Array $data
   * @return Array 
   * @throws \InvalidArgumentException
   */
  public function listmailtemplatecategory($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : Null));
    $name = (isset($filter->name) ? "AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');

    
    if ((isset($filter->dateinitial) && !empty($filter->dateinitial)) && (isset($filter->dateend) && !empty($filter->dateend))) {
      
      $startDate = strtotime($filter->dateinitial);

      $finalDate = strtotime($filter->dateend);
      
      if ($startDate > $finalDate) {
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a final');
      }
      if ($filter->dateinitial > date('Y-m-d')) {
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la actual.');
      }

      $where .= " AND created  BETWEEN '{$startDate}' AND '{$finalDate}'";
    }

    $conditions = array(
        "conditions" => "deleted = ?0 AND idAccount = ?1 {$name} {$where}",
        "bind" => array(0, $idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $mailtemplatecategory = \MailTemplateCategory::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = count(\MailTemplateCategory::find($conditions));

    $data = array();
    if (count($mailtemplatecategory) > 0) {
      foreach ($mailtemplatecategory as $key => $value) {
        $data[$key] = array(
            "idMailTemplateCategory" => $value->idMailTemplateCategory,
            "idAccount" => $value->idAccount,
            "name" => $value->name,
            "description" => $value->description,
            "created" => date("Y-m-d H:m", $value->created),
            "updated" => date("Y-m-d H:m", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "status" => $value->status
        );
      }
    }

    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );
  }

  /**
   * @descripcion consulta todas categoria de plantillas de correo
   * @return Array    
   */
  public function findMailTemplateCategory() {
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : ''));
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : '');
    $conditions = (($idAccount != '') ? "AND idAccount = {$idAccount}" : "AND idAllied = {$idAllied}");

    $mailtemplatecategoy = \MailTemplateCategory::find(array(
                "conditions" => "deleted = ?0 {$conditions}",
                "bind" => array(0)
    ));
    $array = array();
    if (count($mailtemplatecategoy)) {
      foreach ($mailtemplatecategoy as $key => $value) {
        $array[$key] = array(
            "idMailTemplateCategory" => $value->idMailTemplateCategory,
            "name" => $value->name
        );
      }
    }
    return $array;
  }

  /**
   * @descripcion consulta por filtro todas las categorias
   * @return Array 
   */
  public function findMailTemplateCategoryFilter() {
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : NULL));
    $idAllied = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAllied : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAllied : ((isset($this->user->Usertype->Allied)) ? $this->user->Usertype->Allied->idAllied : NULL)));
    $condaccount = ($idAccount != NULL) ? "AND idAccount = {$idAccount}" : "AND idAccount IS NULL";
    $condallied = ($idAllied != NULL) ? "AND idAllied = {$idAllied}" : "AND idAllied IS NULL";

    $conditionsAcc = array(
        "conditions" => "deleted = ?0 {$condaccount}",
        "bind" => array(0),
        "order" => "created DESC"
    );
    $conditionsAll = array(
        "conditions" => "deleted = ?0 {$condallied}",
        "bind" => array(0),
        "order" => "created DESC"
    );
    $conditionsGlo = array(
        "conditions" => "deleted = ?0 AND idAccount IS NULL AND idAllied IS NULL",
        "bind" => array(0),
        "order" => "created DESC"
    );

    $lists = array(
        "globalCategory" => \MailTemplateCategory::find($conditionsGlo),
    );

    if (!isset($this->user->Usertype->Allied->idAllied)) {
      $lists["accountCategory"] = \MailTemplateCategory::find($conditionsAcc);
      $lists["alliedCategory"] = \MailTemplateCategory::find($conditionsAll);
    } else {
      $lists["alliedCategory"] = \MailTemplateCategory::find($conditionsAll);
    }

    $dataCategory = [];
    foreach ($lists as $key => $list) {
      foreach ($list as $k => $li) {
        $dataCategory[$key][$k] = array(
            "id" => $li->idMailTemplateCategory,
            "text" => $li->name
        );
      }
    }

    return $dataCategory;
  }

  /**
   * @descripcion guarda una category
   * @param Array $data
   * @return Array id templatecategory   
   */
  public function saveMailTemplateCategory($data) {
    $mailtempcateg = new \MailTemplateCategory();
    $mailtempcateg->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : Null));
    $mailtempcateg->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : Null);

    $mailtempcateg->name = ucwords($data->name);

    if (isset($data->status)) {
      $mailtempcateg->status = $data->status;
    } else {
      $mailtempcateg->status = 0;
    }

    $mailtempcateg->description = $data->description;
    $mailtempcateg->deleted = 0;
    $mailtempcateg->createdBy = $this->user->email;
    $mailtempcateg->updatedBy = $this->user->email;

    $conditions = (($mailtempcateg->idAccount != null) ? "AND idAccount = {$mailtempcateg->idAccount}" : "AND idAllied = {$mailtempcateg->idAllied}");
    $mtc = \MailTemplateCategory::findFirst(array(
                "conditions" => "name = ?0 {$conditions} ",
                "bind" => array($mailtempcateg->name)
    ));

    if ($mtc) {
      return $mtc->idMailTemplateCategory;
    }

    if (!$mailtempcateg->save()) {
      foreach ($mailtempcateg->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $mailtempcateg->idMailTemplateCategory;
  }

  /**
   * @descripcion borra una categoria
   * @param Array $data
   * @return bolean   
   */
  public function deletemailtemplatecategory($data) {
    if (!isset($data->id)) {
      throw new \InvalidArgumentException("Dato de categoría de plantilla inválido");
    }

    $mailtemplatecate = \MailTemplateCategory::findFirst(array(
                "conditions" => "idMailTemplateCategory = ?0",
                "bind" => array($data->id)
    ));

    if (!$mailtemplatecate) {
      throw new \InvalidArgumentException("La categoría que intenta editar no existe");
    }

    $mailtemplatecate->deleted = 1;

    if (!$mailtemplatecate->update()) {
      foreach ($mailtemplatecate->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

  /**
   * @descripcion busca una categoria para editar
   * @param integer $id
   * @return Array $data   
   */
  public function getmailtemplate($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de categoría de plantilla inválido");
    }

    $mailtemplateca = \MailTemplateCategory::findFirst(array(
                "conditions" => "idMailTemplateCategory = ?0",
                "bind" => array($id)
    ));

    if (!$mailtemplateca) {
      throw new \InvalidArgumentException("La categoría que intenta editar no existe");
    }

    $data = array(
        "idMailTemplateCategory" => $mailtemplateca->idMailTemplateCategory,
        "name" => $mailtemplateca->name,
        "description" => $mailtemplateca->description,
        "status" => $mailtemplateca->status
    );

    return $data;
  }

  /**
   * @descripcion guarda la editcion de una categoria
   * @param array $data
   * @return bolean   
   */
  public function editmailtemplatecategory($data) {


    if (!isset($data["idMailTemplateCategory"])) {
      throw new \InvalidArgumentException("Dato de categoría de automatización de campaña inválido");
    }

    $mailtemplatecate = \MailTemplateCategory::findFirst(array(
                "conditions" => "idMailTemplateCategory = ?0",
                "bind" => array($data["idMailTemplateCategory"])
    ));

    if (!$mailtemplatecate) {
      throw new \InvalidArgumentException("La categoria de plantilla que intenta editar no existe");
    }

    $mailtemplatecate->name = ucwords($data['name']);
    $mailtemplatecate->description = (isset($data['description']) && $data['description'] != "") ? $data['description'] : "Sin descripción";
    if (isset($data['status'])) {
      $mailtemplatecate->status = $data['status'];
    } else {
      $mailtemplatecate->status = 0;
    }

    if (!$mailtemplatecate->update()) {
      foreach ($mailtemplatecate->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return true;
  }

}
