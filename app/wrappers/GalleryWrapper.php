<?php

namespace Sigmamovil\Wrapper;

class GalleryWrapper extends \BaseWrapper {

  private $gallery = array();
  private $totals;

  public function findAllGallery($page) {
    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
    $this->data = $this->modelsManager->createBuilder()
            ->from('Asset')
            ->where("Asset.idAccount = " . \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount)
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)
            ->offset($page)
            ->getQuery()
            ->execute();
    $this->totals = $this->modelsManager->createBuilder()
            ->from('Asset')
            ->where("Asset.idAccount = " . \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount)
            ->getQuery()
            ->execute();
    $this->modelData();
  }

  public function modelData() {
    $this->gallery = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $key => $value) {
      $obj = new \stdClass();
      $obj->$key = $value;
      array_push($arr, $value);
    }
    array_push($this->gallery, ["items" => $arr]);
  }

  function getGallery() {
    return $this->gallery;
  }

}
