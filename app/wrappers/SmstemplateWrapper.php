<?php

namespace Sigmamovil\Wrapper;

class SmstemplateWrapper extends \BaseWrapper {

  public function listSmsTemplate($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $smstempcat = (isset($filter->smstempcat) ? ($sanitize->sanitize($filter->smstempcat, "int") == 0 ? '' : "AND idSmsTemplateCategory = {$sanitize->sanitize($filter->smstempcat, "int")}") : '');
    $namesmstemp = (isset($filter->namesmstemp) ? "AND name like '%{$sanitize->sanitize($filter->namesmstemp, "string")}%'" : '');
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));

    $conditions = array(
        "conditions" => "deleted = ?0 AND idAccount = ?1 {$smstempcat} {$namesmstemp}",
        "bind" => array(0, $idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $smstemplate = \SmsTemplate::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \SmsTemplate::count($conditions);

    $data = array();
    if (count($smstemplate) > 0) {
      foreach ($smstemplate as $key => $value) {
        $data[$key] = array(
            "idSmsTemplate" => $value->idSmsTemplate,
            "idSmsTemplateCategory" => $value->idSmsTemplateCategory,
            "nameSmsTemplateCategory" => $value->SmsTemplateCategory->name,
            "idAccount" => $value->idAccount,
            "name" => $value->name,
            "content" => $value->content,
            "createdDate" => date("d-m-Y", $value->created),
            "updatedDate" => date("d-m-Y", $value->updated),
            "createdHour" => date("H:i", $value->created),
            "updatedHour" => date("H:i", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy
        );
      }
    }

    $array = array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );

    return $array;
  }

  public function saveSmsTemplate($data) {
    $smstemplate = new \SmsTemplate();
    $smstemplate->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : $this->user->Usertype->Subaccount->idAccount);
    if (empty($data->name)) {
      throw new \InvalidArgumentException("El campo nombre no puede estar vacío");
    }
    if (strlen(str_replace(" ", "", $data->name)) > 45) {
      throw new \InvalidArgumentException("El campo nombre debe tener máximo 45 caracteres");
    }
    $smstemplate->name = $data->name;

    if (empty($data->categ)) {
      throw new \InvalidArgumentException("Debes seleccionar o crear una categoría para la plantilla");
    }
    $smstemplate->idSmsTemplateCategory = $data->categ;

    if (empty($data->content)) {
      throw new \InvalidArgumentException("El Campo contenido no puede estar vacío");
    }
    if($data->morecaracter == true){
        if (mb_strlen(trim($data->content), 'UTF-8') > 300) {
            throw new \InvalidArgumentException("El campo contenido debe tener máximo 300 caracteres");
        } 
    }else{
        if (strlen($data->content) > 160) {
            throw new \InvalidArgumentException("El campo contenido debe tener máximo 160 caracteres");
        }
    }

    $smstemplate->content = $data->content;
    $smstemplate->deleted = 0;
    $smstemplate->status = 1;
    if($data->morecaracter == true){
     $smstemplate->morecaracter = 1;
    }else{
     $smstemplate->morecaracter = 0;   
    }
    
    if (!$smstemplate->save()) {
      foreach ($smstemplate->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

    return true;
  }

  public function getSmsTemplate($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de plantilla de SMS inválido");
    }

    $smstemplate = \SmsTemplate::findFirst(array(
                "conditions" => "idSmsTemplate = ?0",
                "bind" => array($id)
    ));

    if (!$smstemplate) {
      throw new \InvalidArgumentException("La plantilla que intenta editar no existe");
    }

    $more = false;    
    if($smstemplate->morecaracter == 1){
        $more = true;
    }else if ($smstemplate->morecaracter == 0){
        $more = false; 
    }
    
    $data = array(
        "idSmsTemplate" => $smstemplate->idSmsTemplate,
        "smstempcateg" => $smstemplate->idSmsTemplateCategory,
        "nametempsms" => $smstemplate->name,
        "contenttempsms" => $smstemplate->content,
        "morecaracter" =>$more
    );

    return $data;
  }

  public function editSmsTemplate($data) {
    if (!isset($data->idSmsTemplate)) {
      throw new \InvalidArgumentException("Dato de plantilla de SMS inválido");
    }

    $smstemplate = \SmsTemplate::findFirst(array(
                "conditions" => "idSmsTemplate = ?0",
                "bind" => array($data->idSmsTemplate)
    ));

    if (!$smstemplate) {
      throw new \InvalidArgumentException("La plantilla que intenta editar no existe");
    }


    $smstemplate->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : $this->user->Usertype->Subaccount->idAccount);
    if (empty($data->name)) {
      throw new \InvalidArgumentException("El campo nombre no puede estar vacío");
    }
    if (strlen(str_replace(" ", "", $data->name)) > 45) {
      throw new \InvalidArgumentException("El campo nombre debe tener máximo 45 caracteres");
    }
    $smstemplate->name = $data->name;

    if (empty($data->categ)) {
      throw new \InvalidArgumentException("Debe seleccionar un item del listado categorías");
    }
    $smstemplate->idSmsTemplateCategory = $data->categ;

    if (empty($data->content)) {
      throw new \InvalidArgumentException("El Campo contenido no puede estar vacío");
    }
    
    if($data->morecaracter == true){
        if (mb_strlen(trim($data->content), 'UTF-8') > 300) {
            throw new \InvalidArgumentException("El campo contenido debe tener máximo 300 caracteres");
        } 
    }else{
        if (strlen($data->content) > 160) {
            throw new \InvalidArgumentException("El campo contenido debe tener máximo 160 caracteres");
        }
    }
    
    $more = 0;    
    if($data->morecaracter == true){
        $more = 1;
    }else if ($data->morecaracter == false){
        $more = 0; 
    }
    $smstemplate->content = $data->content;
    $smstemplate->deleted = 0;
    $smstemplate->status = 1;
    $smstemplate->morecaracter = $more;

    if (!$smstemplate->update()) {
      foreach ($smstemplate->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }

    return true;
  }

  public function getsmstemplateautocomplete($filter) {

    $sanitize = new \Phalcon\Filter;
    $smstemplate = \SmsTemplate::find(array(
                "conditions" => "name like '%{$sanitize->sanitize($filter, "string")}%'"
    ));
    $data = array();
    if (count($smstemplate)) {
      foreach ($smstemplate as $key => $value) {
        $data["items"][$key] = array(
            "id" => $value->idSmsTemplate,
            "name" => $value->name,
        );
      }
    }

    return $data;
  }

  public function contentValidation($content) {
    $pattern = "/^[0-9A-Za-z_ \/\\'\-!#$%&()*+,.:;<=>?@]{1,160}$/i";

    if (!preg_match($pattern, $content)) {
      throw new \InvalidArgumentException("Hay campos no permitidos en el contenido");
    }

//    $allowedChar = array("!", "'", "#", "$", "%", "&", "(", ")", "*", "+", ",", "-", ".", "/", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", ":", ";", "<", "=", ">", "?", "@", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "_", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "x", "y", "z", " ", "");
//    $content=preg_split('//', trim($content), -1, PREG_SPLIT_NO_EMPTY);
//    foreach ($content as $cont) {
//      if (!in_array($cont, $allowedChar)) {
//        throw new \InvalidArgumentException("Hay campos no permitidos en el contenido ");
//      }
//    }
  }

  public function getallsmstemplate() {
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));

    $conditions = array(
        "conditions" => "deleted = ?0 AND idAccount = ?1",
        "bind" => array(0, $idAccount),
        "order" => "created DESC"
    
    );

    $smstemplate = \SmsTemplate::find($conditions);
    $data = array();
    if (count($smstemplate) > 0) {
      foreach ($smstemplate as $key => $value) {
        $data[$key] = array(
            "idSmsTemplate" => $value->idSmsTemplate,
            "idSmsTemplateCategory" => $value->idSmsTemplateCategory,
            "idAccount" => $value->idAccount,
            "name" => $value->name,
            "content" => $value->content,
            "created" => date("Y-m-d", $value->created),
            "updated" => date("Y-m-d", $value->updated),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy
        );
      }
    }
    return $data;
  }

  public function setSmstemplate(\SmsTemplate $smstemplate) {
    $this->smstemplate = $smstemplate;
  }

  public function deleteSmstemplate() {
    $this->smstemplate->deleted = time();
    if (!$this->smstemplate->update()) {
      foreach ($this->smstemplate->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException("No se pudo eliminar la plantilla, es posible que tenga una relacion activa, contacta al administrador para solicitar más información");
      }
    }
  }

  public function getalltags() {

    $this->tags[0]['name'] = 'Nombre';
    $this->tags[0]['tag'] = '%%NOMBRE%%';
    $this->tags[1]['name'] = 'Apellido';
    $this->tags[1]['tag'] = '%%APELLIDO%%';
    $this->tags[2]['name'] = 'Fecha de nacimiento';
    $this->tags[2]['tag'] = '%%FECHA_DE_NACIMIENTO%%';
    $this->tags[3]['name'] = 'Correo electrónico';
    $this->tags[3]['tag'] = '%%EMAIL%%';
    $this->tags[4]['name'] = 'Indicativo';
    $this->tags[4]['tag'] = '%%INDICATIVO%%';
    $this->tags[5]['name'] = 'Móvil';
    $this->tags[5]['tag'] = '%%TELEFONO%%';

    $this->user->usertype->idSubaccount;

    $sql = "SELECT idContactlist from contactlist where idSubaccount=" . $this->user->usertype->idSubaccount;
    $Contactlists = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);

    $ids = "";
    
    foreach ($Contactlists as $contactlist) {
      $ids .= $contactlist['idContactlist'] . ",";
    }
    $ids = substr($ids, 0, -1);
    if(count($Contactlists)==0){
      $ids = "null";
    }
    
    $sql = "SELECT name,alternativename from customfield "
            . "WHERE idContactlist in (" . $ids . ") AND deleted=0 "
            . "GROUP BY 1,2";
    $customfields = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
    $i = 6;
    foreach ($customfields as $cf) {
      $this->tags[$i]['name'] = $cf['name'];
      $this->tags[$i]['tag'] = '%%' . strtoupper($cf['alternativename']) . '%%';
      $i++;
    }
    return $this->tags;
  }

  public function listFullSmsTemplateByAccount() {
    $idAccount = ((isset($this->user->Usertype->Subaccount->Account->idAccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : NULL);

    $smstemplate = \SmsTemplate::find(array(
                "columns" => "idSmsTemplate, name, content",
                "conditions" => "deleted = ?0 AND status = ?1 AND idAccount = ?2",
                "bind" => array(0, 1, $idAccount),
                "order" => "created DESC"
    ));

    $data = [];
    if (count($smstemplate) > 0) {
      foreach ($smstemplate as $key => $value) {
        $data[$key] = array(
            "idSmsTemplate" => $value->idSmsTemplate,
            "name" => $value->name,
            "content" => $value->content
        );
      }
    }

    return $data;
  }

}
