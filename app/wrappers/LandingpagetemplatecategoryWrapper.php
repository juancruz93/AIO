<?php

namespace Sigmamovil\Wrapper;

/**
 * Description of LandingpagetemplatecategoryWrapper
 *
 * @author juan.pinzon
 */
class LandingpagetemplatecategoryWrapper extends \BaseWrapper {

  public function getAllLPTW() {
    $idAccount = ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ((isset($this->user->Usertype->Subaccount->Account->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : NULL));
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);
    $conditions = ((isset($idAccount)) ? "AND idAccount = {$idAccount}" : "AND idAllied = {$idAllied}");

    $lptc = \LandingPageTemplateCategory::find(array(//Landing Page Template Category
                "columns" => "idLandingPageTemplateCategory, name",
                "conditions" => "deleted = ?0 {$conditions}",
                "bind" => array(0)
    ));

    $data = [];
    if ($lptc->count() > 0) {
      foreach ($lptc as $key => $value) {
        $data[$key] = array(
            "idLandingPageTemplateCategory" => $value->idLandingPageTemplateCategory,
            "name" => $value->name
        );
      }
    }

    return $data;
  }

  public function saveSimple($data) {
    if (!isset($data->name) || empty($data->name)) {
      throw new \InvalidArgumentException("Debe ingresar un nombre para la categoría");
    }
    $lptcategory = new \LandingPageTemplateCategory();
    $lptcategory->setName($data->name);
    $idAccount = ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ((isset($this->user->Usertype->Subaccount->Account->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : NULL));
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);
    if (isset($idAccount)) {
      $lptcategory->setIdAccount($idAccount);
    }else {
      $lptcategory->setIdAllied($idAllied);
    }
    
    if (!$lptcategory->save()) {
      foreach ($lptcategory->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    
    return ["idLandingPageTemplateCategory" => $lptcategory->idLandingPageTemplateCategory, "message" => "La categoría se ha creado correctamente"];
  }

}
