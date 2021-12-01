<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\Wrapper;

/**
 * Description of SurveycategoryWrapper
 *
 * @author juan.pinzon
 */
class SurveycategoryWrapper extends \BaseWrapper {

  private $form;

  public function listSurveyCategory($page, $name) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $filter = new \Phalcon\Filter;

    $filName = ((isset($name)) ? "AND name LIKE '%{$filter->sanitize($name, "string")}%'" : "");
    $idAccount = $this->user->Usertype->Subaccount->Account->idAccount;

    $conditions = array(
        "conditions" => "deleted = ?0 AND status = ?1 AND idAccount = ?2 {$filName}",
        "bind" => array(0, 1, $idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $surveycategory = \SurveyCategory::find($conditions);
    $conditions["columns"] = "idSurveyCategory";
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = count(\SurveyCategory::find($conditions));

    $data = [];
    if (count($surveycategory) > 0) {
      foreach ($surveycategory as $key => $value) {
        $data[$key] = array(
            "idSurveyCategory" => $value->idSurveyCategory,
            "name" => $value->name,
            "description" => $value->description,
            "status" => $value->status,
            "created" => date("Y-m-d H:i:s", $value->created),
            "updated" => date("Y-m-d H:i:s", $value->updated),
            "createdBY" => $value->createdBy,
            "updatedBY" => $value->updatedBy
        );
      }
    }

    return array(
        "total" => $total,
        "total_pages" => (ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT))),
        "items" => $data
    );
  }

  public function createSurveyCategory($data) {
    $surveycategory = new \SurveyCategory();
    $this->form = new \SurveycategoryForm();
    $this->form->bind($data, $surveycategory);

    $surveycategory->idAccount = $this->user->Usertype->Subaccount->Account->idAccount;

    $sc = \SurveyCategory::findFirst(array(//sc abreviado para surveycategory
                "columns" => "idSurveyCategory",
                "conditions" => "deleted = ?0 AND status = ?1 AND name = ?2 AND idAccount = ?3",
                "bind" => array(0, 1, $surveycategory->name, $surveycategory->idAccount)
    ));

    if ($sc) {
      throw new \InvalidArgumentException("El nombre de categoría que intenta crear ya existe en esta cuenta");
    }

    if (!$this->form->isValid() || !$surveycategory->save()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($surveycategory->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "La categoría <b>{$surveycategory->name}</b> ha sido guardada exitosamente", "idSurveyCategory" => $surveycategory->idSurveyCategory];
  }

  public function getOneSurveyCategory($idSurveyCategory) {
    if (!isset($idSurveyCategory)) {
      throw new \InvalidArgumentException("Dato de categoría de encuesta inválido");
    }

    $surveycategory = \SurveyCategory::findFirst(array(
                "columns" => "idSurveyCategory, name, description, status",
                "conditions" => "deleted = ?0 AND idSurveyCategory = ?1",
                "bind" => array(0, $idSurveyCategory)
    ));

    if (!$surveycategory) {
      throw new \InvalidArgumentException("La categoría de encuesta que intenta editar no existe o fue borrada");
    }

    $data = array(
        "idSurveyCategory" => $surveycategory->idSurveyCategory,
        "name" => $surveycategory->name,
        "description" => $surveycategory->description,
        "status" => (int) $surveycategory->status
    );

    return $data;
  }

  public function editSurveyCategory($data) {
    $surveycategory = \SurveyCategory::findFirst(array(
                "conditions" => "idSurveyCategory = ?0",
                "bind" => array($data["idSurveyCategory"])
    ));

    if (!$surveycategory) {
      throw new \InvalidArgumentException("La categoría de encuesta que intenta editar no existe");
    }

    $this->form = new \SurveycategoryForm();
    $this->form->bind($data, $surveycategory);

    $surcat = \SurveyCategory::findFirst(array(
                "conditions" => "deleted = ?0 AND idSurveyCategory != ?1 AND name = ?2",
                "bind" => array(0, $surveycategory->idSurveyCategory, $surveycategory->name)
    ));

    if ($surcat) {
      throw new \InvalidArgumentException("La categoría de encuesta que intenta guardar ya existe, debe validar el nombre");
    }

    if (!$this->form->isValid() || !$surveycategory->update()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($surveycategory->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "La categoría <b>{$surveycategory->name}</b> ha sido actualizada exitosamente"];
  }

  public function deleteSurveyCategory($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de de categoría de encuesta inválido");
    }

    $surveycategory = \SurveyCategory::findFirst(array(
                "conditions" => "deleted = ?0 AND idSurveyCategory = ?1",
                "bind" => array(0, $id)
    ));

    if (!$surveycategory) {
      throw new \InvalidArgumentException("La categoría de encuesta que intenta eliminar, no existe");
    }

    $surveycategory->deleted = time();
    
    if (!$surveycategory->update()) {
      foreach ($surveycategory->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    
    return ["message" => "La categoría <b>{$surveycategory->name}</b> fue eliminada exitosamente"];
  }

}
