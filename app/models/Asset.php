<?php

class Asset extends Modelbase {

  public $idAccount,
          $idAllied,
          $idAsset,
          $name,
          $size,
          $type,
          $contentType,
          $dimensions,
          $extension,
          $preview;

  public function initialize() {
    $this->belongsTo("idAccount", "Account", "idAccount");
    $this->belongsTo("idAllied", "Allied", "idAllied");
    $this->hasMany("idAsset", "Mailattachment", "idAsset");
    $this->hasMany("idAsset", "MailTemplateImage","idAsset");
  }

  static public function findAllAssetsInAccount(Account $account) {
    $assets = self::find(array(
                "conditions" => "idAccount = ?1",
                "order" => "created DESC",
                //"limit" => 20,
                "bind" => array(1 => $account->idAccount)
    ));
    return $assets;
  }
  
  static public function findAllAssetsInAccountPagination(Account $account,$page) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_ASSET_LIMIT;
    }
    
    $arrFinish = array();
    $assets = self::find(array(
                "conditions" => "idAccount = ?1",
                "order" => "created DESC",
                "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_ASSET_LIMIT,
                "offset" => $page,
                "bind" => array(1 => $account->idAccount)
    ));
    $countAssets = self::count(array("conditions" => "idAccount = ?1","bind" => array(1 => $account->idAccount)));
    
    $arrFinish = array("total" => $countAssets, "total_pages" => ceil($countAssets / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_ASSET_LIMIT), "items" => $assets);
    return $arrFinish;
  }

  static public function findAllAssetsInAllied(Allied $allied) {
    $assets = self::find(array(
                "conditions" => "idAllied = ?1",
                "order" => "created DESC",
                //"limit" => 20,
                "bind" => array(1 => $allied->idAllied)
    ));
    return $assets;
  }
  
  static public function findAllAssetsInAlliedPagination(Allied $allied, $page){
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_ASSET_LIMIT;
    }
    $arrFinish = array();
    
    
    
    $assets = self::find(array(
                "conditions" => "idAllied = ?1",
                "order" => "created DESC",
                "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_ASSET_LIMIT,
                "offset" => $page,
                "bind" => array(1 => $allied->idAllied)
    ));
    
    $countAssets = self::count(array("conditions" => "idAllied = ?1","bind" => array(1 => $allied->idAllied)));
    
    
    $arrFinish = array("total" => $countAssets, "total_pages" => ceil($countAssets / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_ASSET_LIMIT), "items" => $assets);
    return $arrFinish;
    
  }

}
