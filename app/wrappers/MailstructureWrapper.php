<?php

namespace Sigmamovil\Wrapper;

class MailstructureWrapper {

  private $totals;
  private $asset;
  private $user;
  private $mailstructure = array();

  public function __construct() {
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
    $this->user = \Phalcon\DI::getDefault()->get('user');
    $this->asset = \Phalcon\DI::getDefault()->get('asset');
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
  }

  public function findAll($page, $name) {
    $where = "";
    (($page > 0) ? $page = ($page * 4) : "");
    if ($name['name'] != "") {
      $where .= " AND mailstructure.name LIKE '%{$name['name']}%' ";
    }
    $this->data = $this->modelsManager->createBuilder()
            ->from('Mailstructure')
            ->where("Mailstructure.deleted = 0 {$where}  LIMIT " . 4 . " OFFSET {$page}")
            ->getQuery()
            ->execute();
    $this->totals = $this->modelsManager->createBuilder()
            ->from('Mailstructure')
            ->where("Mailstructure.deleted = 0  {$where}")
            ->getQuery()
            ->execute();
    $this->modelData();
  }

  public function modelData() {
    $this->mailstructure = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / 4));
    $arr = array();
    foreach ($this->data as $key => $value) {
      $obj = new \stdClass();
      $obj->$key = $value;
      array_push($arr, $value);
    }
    array_push($this->mailstructure, ["items" => $arr]);
  }

  public function deletestructure($idStructure) {
    $template = \Mailstructure::findFirst(["conditions" => "idMailStructure = ?0", "bind" => [0 => $idStructure]]);
    $template->deleted = time();
    if (!$template->update()) {
      foreach ($template->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function editstructure($data, $files) {
    $name = $files['name'];
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    if (!isset($data['name'])) {
      throw new \InvalidArgumentException("El nombre es de caracter obligatorio");
    }
    if ($ext == "jpg" || $ext == "JPG") {
      $tmp_dir = $files['tmp_name'];
      $template = \Mailstructure::findFirst(["conditions" => "idMailStructure = ?0", "bind" => [0 => $data['idMailstructure']]]);

      $template->name = $data['name'];
      $template->content = $data['editor'];
      $template->description = "Sin descripción";
      if ($data['category'] != "undefined") {
        $template->description = $data['category'];
      }

//    ((isset($data["description"])) ? $template->description = $data["description"] : "" );
      if (!$template->update()) {
        foreach ($template->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      $this->moveImg($template->idMailStructure, $tmp_dir, $name);
    } else {
      throw new \InvalidArgumentException("Solo se permite imagenes de formato JPG");
    }
  }

  public function createMailStructure($arrayData, $files) {
    $name = $files['name'];
    if (!isset($arrayData['name'])) {
      throw new \InvalidArgumentException("El nombre es de caracter obligatorio");
    }
    $ext = pathinfo($name, PATHINFO_EXTENSION);

    if ($ext == "jpg" || $ext == "JPG") {
      $tmp_dir = $files['tmp_name'];

      $template = new \Mailstructure();
      $template->name = $arrayData['name'];
      $template->content = $arrayData['editor'];
      $template->description = "Sin descripción";
      if ($arrayData['category'] != "undefined") {
        $template->description = $arrayData['category'];
      }


      if (!$template->save()) {
        foreach ($template->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      $this->moveImg($template->idMailStructure, $tmp_dir, $name);
    } else {
      throw new \InvalidArgumentException("Solo se permite imagenes de formato JPG");
    }
  }

  public function moveImg($idMailStructure, $tmp_dir, $name) {
    $dir = $this->asset->dirmailstructure . "/" . $this->user->usertype->idAllied . '/';
    if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }
    $dir1 = $dir . $idMailStructure . ".png";
    $dir2 = $dir . $idMailStructure . "_thumb.png";
    $imageObj = new \Sigmamovil\General\Misc\ImageObject();
    $imageObj->moveImageFromSiteToAnother($tmp_dir, $dir1);
    $imageObj->createImageFromFile($dir1, $name);
    $imageObj->resizeImage(238, 260, "#ffffff");
    $imageObj->saveImage('png', $dir2);
  }

  function getMailstructure() {
    return $this->mailstructure;
  }

}
