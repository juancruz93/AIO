<?php

namespace Sigmamovil\Wrapper;

class LanguageWrapper extends \BaseWrapper {

  private $languages;
  private $language;

  /**
   * @param \Language $language
   */
  public function setLanguage(\Language $language) {
    $this->language = $language;
  }

  public function findLanguages($page) {
    
    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");

  
//$prueba = \Language:: 
    $this->data = $this->modelsManager->createBuilder()
            ->from('Language')
            ->where("Language.deleted = 0")
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)
            ->offset($page)
            ->getQuery()
            ->execute();
    $this->totals = $this->modelsManager->createBuilder()
            ->from('Language')
            ->where("Language.deleted = 0")
            ->getQuery()
            ->execute();
    $this->modelData();
  }

  public function findLanguageFirst($id) {

    $this->data = $this->modelsManager->createBuilder()
            ->from('Language')
            ->where("Language.deleted = 0 and Language.idLanguage = " . $id)
            ->getQuery()
            ->execute();

    foreach ($this->data as $data) {
      $language = new \stdClass();
      $language->idLanguage = $data->idLanguage;
      $language->name = $data->name;
      $language->created = date("d/m/Y  H:ia", $data->created);
      $language->updated = date("d/m/Y  H:ia", $data->updated);
      $language->shortName = $data->shortName;
      $language->deleted = $data->deleted;
      $language->createdBy = $data->createdBy;
      $language->updatedBy = $data->updatedBy;
    }
    $this->languages = json_encode($language);
//    var_dump($obj);
//    exit;
  }

  public function modelData() {
    $this->languages = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $data) {
      $language = new \stdClass();
      $language->idLanguage = $data->idLanguage;
      $language->name = $data->name;
      $language->created = date("d/m/Y  H:ia", $data->created);
      $language->updated = date("d/m/Y  H:ia", $data->updated);
      $language->shortName = $data->shortName;
      $language->deleted = $data->deleted;
      $language->createdBy = $data->createdBy;
      $language->updatedBy = $data->updatedBy;

      $arr[] = $language;
    }
    $this->languages['items'] = $arr;
  }

  public function editLanguage() {

    $this->language->name = $this->data->name;
    $this->language->shortName = $this->data->shortName;
    $name = trim($this->data->name);
    $shortName = trim($this->data->shortName);

    if ($name=="") {
      throw new \InvalidArgumentException("El campo nombre es de caracter obligatorio");
    }
    if (strlen($name) > 60) {
      throw new \InvalidArgumentException("El campo nombre no puede tener mas de 60 caracteres");
    }
    if (strlen($name) < 2) {
      throw new \InvalidArgumentException("El campo nombre no puede tener menos de 2 caracteres");
    }
 
    if ($shortName=="") {
      throw new \InvalidArgumentException("El campo nombre corto es de caracter obligatorio");
    }

    if (strlen($shortName) > 6) {
      throw new \InvalidArgumentException("El campo nombre corto no puede tener mas de 6 caracteres");
    }

    if (strlen($shortName) < 2) {
      throw new \InvalidArgumentException("El campo nombre corto no puede tener menos de 2 caracteres");
    }
    if (!$this->language->save()) {
      foreach ($this->language->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function deleteLanguage() {
    $this->language->deleted = time();
    if (!$this->language->update()) {
      foreach ($this->language->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException("No se pudo eliminar el idioma, contacta al administrador para solicitar más información");
      }
    }
  }

  function getLanguages() {
    return $this->languages;
  }

}
