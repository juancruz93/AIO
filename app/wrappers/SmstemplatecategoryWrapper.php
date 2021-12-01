<?php

namespace Sigmamovil\Wrapper;

class SmstemplatecategoryWrapper extends \BaseWrapper {

  public function listSmsTemplateCategory() {
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : ''));

    $smstemplatecategory = \SmsTemplateCategory::find(array(
                "conditions" => "deleted = ?0 AND idAccount = ?1",
                "bind" => array(0, $idAccount),
                "order" => "created DESC"
    ));

    $array = array();
    if (count($smstemplatecategory) > 0) {
      foreach ($smstemplatecategory as $key => $value) {
        $array[$key] = array(
            "idSmsTemplateCategory" => $value->idSmsTemplateCategory,
            "name" => $value->name
        );
      }
    }

    return $array;
  }

  public function saveSmsTemplateCategory($data) {
    $smstempcateg = new \SmsTemplateCategory();
    $smstempcateg->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : Null));
    $smstempcateg->name = ucwords($data->name);
    $smstempcateg->deleted = 0;
    $smstempcateg->status = 1;

    $stc = \SmsTemplateCategory::findFirst(array(
                "conditions" => "name = ?0 AND idAccount = ?1",
                "bind" => array(0 => $smstempcateg->name, 1 => $smstempcateg->idAccount)
    ));
  
    if ($stc) {
      throw new \InvalidArgumentException("El nombre de esta categoría ya existe");
    }


    if (!$smstempcateg->save()) {
      foreach ($smstempcateg->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return $smstempcateg->idSmsTemplateCategory;
  }

}
