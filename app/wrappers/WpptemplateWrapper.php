<?php

namespace Sigmamovil\Wrapper;

class WpptemplateWrapper extends \BaseWrapper {

    public function listWppTemplate($page, $filter) {
        if ($page != 0) {
          $page = $page + 1;
        }
        if ($page > 1) {
          $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
        }
    
        $sanitize = new \Phalcon\Filter;
    
        $wpptempcat = (isset($filter->wpptempcat) ? "AND wppTemplateCategory like '%{$sanitize->sanitize($filter->wpptempcat, "string")}%'" : '');
        $namewpptemp = (isset($filter->namewpptemp) ? "AND name like '%{$sanitize->sanitize($filter->namewpptemp, "string")}%'" : '');
        $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));
    
        $conditions = array(
            "conditions" => "status= 1 AND deleted = ?0 AND idAccount = ?1 {$wpptempcat} {$namewpptemp}",
            "bind" => array(0, $idAccount),
            "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
            "offset" => $page,
            "order" => "created DESC"
        );
    
        $wpptemplate = \WppTemplate::find($conditions);
        unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
        $total = \WppTemplate::count($conditions);
        
        $data = array();
        if (count($wpptemplate) > 0) {
          foreach ($wpptemplate as $key => $value) {
            $data[$key] = array(
                "idWppTemplate" => $value->idWppTemplate,
                "wppTemplateCategory" => $value->wppTemplateCategory,
                "idAccount" => $value->idAccount,
                "name" => $value->name,
                "approved" => $value->approved,
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
    //esto retorna el listado de las categorias de las plantillas en la tabla wpp_templeate_category
    /*public function listWppTemplateCat() {
        $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : ''));
    
        $wpptemplatecategory = \WppTemplateCategory::find(array(
                    "conditions" => "deleted = ?0 AND idAccount = ?1",
                    "bind" => array(0, $idAccount),
                    "order" => "created DESC"
        ));
        $idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
        $array = array();
        if (count($wpptemplatecategory) > 0) {
          foreach ($wpptemplatecategory as $key => $value) {
            $array[$key] = array(
                "idWppTemplateCategory" => $value->idWppTemplateCategory,
                "name" => $value->name
            );
          }
        }
        return $array;
    }*/

    public function saveWppTemplate($data) {

      $wpptemplate = new \WppTemplate();
      $wpptemplate->idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : $this->user->Usertype->Subaccount->idAccount);
      if (empty($data->name)) {
        throw new \InvalidArgumentException("El campo nombre no puede estar vacío");
      }
      if (strlen(str_replace(" ", "", $data->name)) > 45) {
        throw new \InvalidArgumentException("El campo nombre debe tener máximo 45 caracteres");
      }
      $wpptemplate->name = $data->name;
  
      if (empty($data->categ)) {
        throw new \InvalidArgumentException("Debes seleccionar una categoría para la plantilla HSM");
      }
      $wpptemplate->wppTemplateCategory = $data->categ;
  
      if (empty($data->content)) {
        throw new \InvalidArgumentException("El Campo contenido no puede estar vacío");
      }
      if (mb_strlen(trim($data->content), 'UTF-8') > 300) {
          throw new \InvalidArgumentException("El campo contenido debe tener máximo 300 caracteres");
      } 
  
      $wpptemplate->content = $data->content;
      $wpptemplate->deleted = 0;
      $wpptemplate->approved = 0;
      $wpptemplate->status = 1;
      if($data->morecaracter == true){
       $wpptemplate->morecaracter = 1;
      }else{
       $wpptemplate->morecaracter = 0;   
      }
      
      if (!$wpptemplate->save()) {
        foreach ($wpptemplate->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      $this->sendMailNotificationHsmCreated($wpptemplate);
  
      return true;
    }

    public function sendMailNotificationHsmCreated($wpptemplate) {
      try {
        $subaccount = $this->user->Usertype->Subaccount;
        $account = $subaccount->Account;
        //Correo de soporte al cual llegan las notificaciones de falta de saldo
        $supportEmail = "desarrollo.tics@sigmamovil.com.co";
  
        //Objeto que guardara la informacion de envio de correo
        $data = new \stdClass();
  
        //Datos del correo
        $data->fromEmail = "desarrollo@sigmamovil.com";
        $data->fromName = "Creacion de plantilla HSM - AIO WPP";
        $data->from = array($data->fromEmail => $data->fromName);
        $data->subject = "Notificación de Creacion de Plantilla HSM";
  
        //Contenido del correo
        $content = '<table style="background-color: #E6E6E6; width: 100%;">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding: 20px;"><center>'
                . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding-left: 0px; padding-right: 0px;">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="word-break: break-word; padding: 15px 15px 0 15px; font-family: Helvetica, Arial, sans-serif;">'
                . '<h3><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
                . 'Estimado equipo de Soporte:'
                . '</span></h3>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '<tr>'
                . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                . '<tbody>'
                . '<tr>'
                . '<td style="padding-left: 0px; padding-right: 0px;">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
                . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
                . '<tbody>'
                . '<tr>'
                . '<td style="word-break: break-word; padding: 0 15px 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
                . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
                . 'Se informa que la subcuenta  <b>' . $subaccount->name . '</b>, de la cuenta <b>'. $account->name .'</b>, ha creado la plantilla con ID: <b>' . $wpptemplate->idWppTemplate .'</b>, con el siguiente contenido: '
                . '</span></p>'
                . '<table cellpadding="0" style="border-collapse: collapse;" width="100%"><thead><tr style="font-size: 15px;height: 35px;background-color: #e36c09;color: #ffffff;">'
                . '<th style="border: 1px solid #ddd;">Nombre Plantilla</th><th style="border: 1px solid #ddd;">Texto Plantilla</th><th style="border: 1px solid #ddd;">Categoria</th><th style="border: 1px solid #ddd;width: 80px;">Lenguaje</th>'
                . '</tr></thead><tbody><tr>'
                . '<td style="border: 1px solid #ddd;padding: 10px 2px;">'.$wpptemplate->name.'</td><td style="border: 1px solid #ddd;padding: 10px 2px;">'.$wpptemplate->content.'</td><td style="border: 1px solid #ddd;padding: 10px 2px;min-width: 90px;">'.$wpptemplate->wppTemplateCategory.'</td><td style="border: 1px solid #ddd;padding: 10px 2px;">Spanish</td>'
                . '</tr></tbody></table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table>'
                . '</td>'
                . '</tr>'
                . '</tbody>'
                . '</table></td></tr></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';
  
        $data->html = str_replace("tmp-url", "prueba", $content);
        $data->plainText = "Se ha enviado una notificacion de saldo de SMS.";
        $data->to = $supportEmail;
  
        $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48', 25);
        $mtaSender->setDataMessage($data);
        $mtaSender->sendMail();
      } catch (InvalidArgumentException $e) {
        $this->notification->error($e->getMessage());
      } catch (Exception $e) {
        \Phalcon\DI::getDefault()->get('logger')->log("Exception while sending email notification SMS balance: {$e->getMessage()}");
        \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
        $this->notification->error($e->getMessage());
      }
    }

    public function editWppTemplate($data) {

      if (empty($data->name)) {
        throw new \InvalidArgumentException("El campo nombre no puede estar vacío");
      }

      $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : ''));

      $wpptemplate = \WppTemplate::findFirst([
          "conditions" => "idWppTemplate = ?0 AND idAccount = ?1",
          "bind" => array($data->id, $idAccount)
      ]);

      if (!$wpptemplate) {
          throw new \InvalidArgumentException("No se encontró la plantilla HSM, por favor valida la información");
      }
      
      $userMail = \Phalcon\DI::getDefault()->get('user')->email;
      $wpptemplate->name = $data->name;
      $wpptemplate->updated = time();
      $wpptemplate->updatedBy = $userMail;

      if (!$wpptemplate->update()) {
        foreach ($wpptemplate->getMessages() as $msg) {
            $this->logger->log("Message: {$msg}");
            throw new \InvalidArgumentException("No se pudo eliminar la plantilla HSM, es posible que tenga una relacion activa, contacta al administrador para solicitar más información");
        }
      }

      return $arrFinish = array("message" => "Se actualizo la plantilla HSM ".$wpptemplate->name." correctamente");

    }

    public function deleteWpptemplate($idWppTemplate) {

        $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount)) ? $this->user->Usertype->Subaccount->Account->idAccount : ''));

        $wpptemplate = \WppTemplate::findFirst([
            "conditions" => "idWppTemplate = ?0 AND idAccount = ?1",
            "bind" => array($idWppTemplate, $idAccount)
        ]);

        if (!$wpptemplate) {
            throw new \InvalidArgumentException("No se encontró la plantilla HSM, por favor valida la información");
        }
        
        $userMail = \Phalcon\DI::getDefault()->get('user')->email;
        $wpptemplate->status = 0;
        $wpptemplate->deleted = time();
        $wpptemplate->updated = time();
        $wpptemplate->updatedBy = $userMail;


        if (!$wpptemplate->update()) {
            foreach ($wpptemplate->getMessages() as $msg) {
                $this->logger->log("Message: {$msg}");
                throw new \InvalidArgumentException("No se pudo eliminar la plantilla HSM, es posible que tenga una relacion activa, contacta al administrador para solicitar más información");
            }
        }
        
        return $arrFinish = array("message" => "Se eliminó la plantilla HSM ".$wpptemplate->name." correctamente");

    }
        

}