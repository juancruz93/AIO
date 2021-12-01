<?php

namespace Sigmamovil\Wrapper;

class MailcategoryWrapper extends \BaseWrapper {

  private $mailcategory = array();

  public function findMailCategory($page, $filter) {
      
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }
  
    $where = "";
    if (isset($filter->name) && $filter->name != ""){
        $where = "AND name LIKE '%".$filter->name."%'";
    }
    
    if ((isset($filter->dateinitial) && !empty($filter->dateinitial)) && (isset($filter->dateend) && !empty($filter->dateend))){
      if($filter->dateinitial > $filter->dateend){
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la final');
      }
      
      if ($filter->dateinitial > date('Y-m-d')) {
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la actual.');
      }
      
      $startDate = strtotime($filter->dateinitial);
      $finalDate = strtotime($filter->dateend);
      
      $where .= " AND created  BETWEEN '{$startDate}' AND '{$finalDate}'";
    }
    
    $conditions = array(
        "conditions" => "deleted = 0 AND idAccount = ?0 ".$where, 
        "bind" => array(0 => $this->user->Usertype->Subaccount->idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "order" => "idMailCategory DESC",
        "offset" => $page,  
    );
    
    $mailCategory = \MailCategory::find($conditions);
    
    $total = \MailCategory::count(array("conditions" => "deleted = 0 AND idAccount = ?0".$where, "bind" => array(0 => $this->user->Usertype->Subaccount->idAccount)));

    $consult = array();
    if (count($mailCategory)) {
      foreach ($mailCategory as $key => $value) {
        $consult[$key] = array(
            "name" => $value->name,
            "idMailCategory" => $value->idMailCategory,
            "description" => $value->description,
            "deleted" => $value->deleted,
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "updatedDate" => date('d/m/Y', $value->updated),
            "createdDate" => date('d/m/Y', $value->created),
            "createdHour" => date('g:i a', $value->created),
            "updatedHour" => date('g:i a', $value->created),
        );
      }
    }

    $arrFinish = array("total" => $total, "total_pages" => ceil($total / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT), "items" => $consult);
    return $arrFinish;
    //$this->mailcategory = array("total"=>count($total),"total_pages"=>ceil(count($total) / 2),"items"=>$consult);
  }

  public function getMailcategory() {
    return $this->mailcategory;
  }

  public function validateNameMailCategory($name) {
    $validateMailCategory = \MailCategory::findFirst(array("conditions" => "name = ?0 AND deleted=0 AND idAccount = ?1", "bind" => array(0 => $name, 1 => $this->user->Usertype->Subaccount->idAccount)));
    if ($validateMailCategory) {
      throw new \InvalidArgumentException('El nombre de la categoría ya existe');
    }
  }

  public function saveMailCategory($arrMailCategory) {


    $mailcategory = new \MailCategory();
    $form = new \MailcategoryForm();

    /* $mailcategory->name = $arrMailCategory['name'];
      if (strlen($objMailCategory->name) > 80) {
      throw new \InvalidArgumentException("El campo nombre no puede tener mas de 80 caracteres");
      }
      if(!empty($arrMailCategory['description'])){
      if(strlen($arrMailCategory['description']) > 400){
      throw new \InvalidArgumentException("El campo descipcion no puede tener mas de 400 caracteres");
      }
      $mailcategory->description = $arrMailCategory['description'];
      } */

    $form->bind($arrMailCategory, $mailcategory);
    $mailcategory->idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
    if (!$form->isValid() || !$mailcategory->save()) {

      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
      foreach ($mailcategory->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  public function saveMailCategoryInMail($arrMailCategory) {

    $mailcategory = new \MailCategory();
    $form = new \MailcategoryForm();
    $form->bind($arrMailCategory, $mailcategory);
    $mailcategory->idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
    if (!$form->isValid() || !$mailcategory->save()) {

      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
      foreach ($mailcategory->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $mailcategory;
  }

  public function editMailCategory($arrMailCategory) {

    $mailcategory = \MailCategory::findFirst(array("conditions" => "idMailCategory = ?0", "bind" => array((Int) $arrMailCategory['idMailCategory'])));
    $form = new \MailcategoryForm();

    /* if (strlen($objMailCategory->name) > 80) {
      throw new \InvalidArgumentException("El campo nombre no puede tener mas de 80 caracteres");
      }
      if($objMailCategory->description){
      if(strlen($objMailCategory->name) > 400){
      throw new \InvalidArgumentException("El campo descipcion no puede tener mas de 400 caracteres");
      }
      $mailcategory->description = $objMailCategory->description;
      } */
    $form->bind($arrMailCategory, $mailcategory);
    if (!$form->isValid() || !$mailcategory->update()) {
      foreach ($mailcategory->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  public function deleteMailCategory($objMailCategory) {
    $mailcategory = \MailCategory::findFirst(array("conditions" => "idMailCategory = ?0", "bind" => array((Int) $objMailCategory->idmailcategory)));
    $mailcategory->deleted = time();
    $mxmc->deleted = time();

    if (!$mailcategory->update()) {

      foreach ($mxmc->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }

      foreach ($mailcategory->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

  public function getAllMailCategory() {
    $this->data = \MailCategory::find(array(
                "conditions" => "idAccount = ?0 and deleted = 0",
                "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount),
                "order" => "created DESC"
    ));
    $this->modelData();
  }

  public function getMailCategoryByidMail($idMail) {
    $sql = "SELECT idMailCategory FROM mxmc WHERE idMail = " . $idMail;
    $mxmc = \Phalcon\DI::getDefault()->get('db')->fetchall($sql);
    return $mxmc;
  }

  public function modelData() {
//$arr = array();
    foreach ($this->data as $key => $value) {
      $this->mailcategory[$key] = array(
          "name" => $value->name,
          "idMailCategory" => $value->idMailCategory,
          "description" => $value->description,
          "status" => $value->status,
          "deleted" => $value->deleted,
      );
    }

//    var_dump($arr);
//    exit();
  }

  public function getautomaticcampaignautocomplete($filter) {

    $sanitize = new \Phalcon\Filter;
    $smstemplate = \MailCategory::find(array(
                "conditions" => "name like '%{$sanitize->sanitize($filter, "string")}%'"
    ));
    $data = array();
    if (count($smstemplate)) {
      foreach ($smstemplate as $key => $value) {
        $data["items"][$key] = array(
            "id" => $value->idMailCategory,
            "name" => $value->name,
        );
      }
    }

    return $data;
  }

  public function getallmailcategorys() {
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    $mailCategory = \MailCategory::find(array("conditions" => "deleted = 0 AND idAccount = ?0", "bind" => array(0 => $idAccount)));
    $consult = array();
    if (count($mailCategory)) {
      foreach ($mailCategory as $key => $value) {
        $consult[$key] = array(
            "name" => $value->name,
            "idMailCategory" => $value->idMailCategory,
            "description" => $value->description,
            "deleted" => $value->deleted,
        );
      }
    }

    return $consult;
  }

}