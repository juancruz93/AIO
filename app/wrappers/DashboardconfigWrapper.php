<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\Wrapper;

class DashboardconfigWrapper extends \BaseWrapper {
  
  protected $path;
  
  public function __construct() {
    parent::__construct();
    $this->path = \Phalcon\DI::getDefault()->get('path');
  }

  public function saveImage($file, $idAccount) {
    $dashboardImage = new \DashboardImage();
    
    $dashboardImage->idAccount = $idAccount;
    
    $dashboardImage->contentType = $file['type'];
    $dashboardImage->size = $file['size'];
    $info = \getimagesize($file['tmp_name']);
    $dashboardImage->dimensions = $info[0] . ' x ' . $info[1];
    $dashboardImage->extensions = \pathinfo($file['name'], PATHINFO_EXTENSION);
//    var_dump($dashboardImage);
//    exit();
    if (!$dashboardImage->save()) {
      foreach ($dashboardImage->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $dashboardImage->name = $dashboardImage->idDashboardImage . "." . $dashboardImage->extensions;;
    if (!$dashboardImage->save()) {
      foreach ($dashboardImage->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $this->moveFile($dashboardImage,$file);
    return true;
  }
  
  public function getAllImage($idAccount,$page = 0){
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }
    
    $images = array();
    $imageDashboard = \DashboardImage::find(array("conditions"=>"idAccount = ?0",
        "bind"=>array($idAccount),
        "limit"=>\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"));
    $imageDashboardCount = \DashboardImage::count(array("conditions"=>"idAccount = ?0","bind"=>array($idAccount)));
    if($imageDashboard){
      foreach($imageDashboard as $key => $value){
        $images[$key] = array(
            "idDashboardImage" => $value->idDashboardImage,
            "name" => $value->name,
            "extesion" => $value->extensions
        );
      }
    }
    return array(
        "total" => $imageDashboardCount,
        "total_pages" => ceil($imageDashboardCount / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $images
    );
  }

  private function moveFile($dashboardImage,$file) {
    $dir = "{$this->path->path}/public/images/image_dashboard/{$this->user->Usertype->idAllied}/{$dashboardImage->idAccount}/";
    $nameImage = $dashboardImage->idDashboardImage . "." . $dashboardImage->extensions;
    if (!\file_exists($dir)) {
      \mkdir($dir, 0777, true);
    }
    if (!\move_uploaded_file($file['tmp_name'],$dir . $nameImage)) {
      throw new \InvalidArgumentException("hubo un problema moviendo el archivo al directorio DIR: ".$dir.$nameImage);
    }
  }
  
  public function getConfigDashboard($idAccount){
    $dashboardConfig = \Dashboard::findFirst(array("conditions"=>"idAccount = ?0","bind"=>array($idAccount)));
    if(!$dashboardConfig){
      return false;
    }
    $objreturn = [];
//    var_dump($dashboardConfig->idDashboard);
//    exit();
    $objreturn['idDashboard'] = $dashboardConfig->idDashboard;
    $objreturn['content'] = $dashboardConfig->content;
    return $objreturn;
  }
  
  public function getDefaultDashboard(){
    $dashboardConfig = \DashboardDefault::findFirstByIdDashboarddefault(1);
    
    
    return array(
        "idDashboarddefault" => $dashboardConfig->idDashboarddefault,
        "content" => $dashboardConfig->content
    );
  }
  
  public function getConfigDashboardClient($idAccount){
    $dashboardConfig = \Dashboard::findFirst(array("conditions"=>"idAccount = ?0","bind"=>array($idAccount)));
    if(!$dashboardConfig){
      $dashboardConfig = \DashboardDefault::findFirstByIdDashboarddefault(1);
    }
    
    return array(
        "idDashboarddefault" => $dashboardConfig->idDashboarddefault,
        "content" => $dashboardConfig->content
    );
  }
  
  public function saveConfig($idaccount, $arrayData){
    $configDashboard = \Dashboard::findFirst(array("conditions"=>" idAccount = ?0","bind"=>array($idaccount)));
    if(!$configDashboard){
      $configDashboard = new \Dashboard();
      $configDashboard->idAccount = $idaccount;
    }
    $configDashboard->content = json_encode($arrayData);
    if(!$configDashboard->save()){
      foreach ($configDashboard->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    return array("message"=>"Se actualizo correctamente la configuracion de dashboard");
  }

}
