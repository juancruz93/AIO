<?php

/**
 * Description of newPHPClass
 *
 * @author desarrollo3
 */
class WebversionController extends ControllerBase {

  public $detailConfig;

  public function initialize() {
    $this->tag->setTitle("Versión web");
    parent::initialize();
  }

  public function showAction($parameters) {

    $idenfifiers = explode("-", $parameters);
    list($idLink, $idMail, $idContact, $md5) = $idenfifiers;
    $src = $this->urlManager->get_base_uri(true) . '1-' . $idMail . '-' . $idContact;
    $md5_2 = md5($src . '-Sigmamovil_Rules');
//    if ($md5 == $md5_2) {
      $html = $this->startWebVersionProcess($idLink, $idMail, $idContact, FALSE);
      if (!$html) {
        return $this->response->redirect('error/link');
      }
 
      $this->view->setVar('html', $html);
//    } else { 
//      return $this->response->redirect('error/link');
//    }

  }

  protected function startWebVersionProcess($idLink, $idMail, $idContact, $social) {

    $mail = Mail::findFirst(array(
                'conditions' => 'idMail = ?1',
                'bind' => array(1 => $idMail)
    ));

    $mailContent = MailContent::findFirst(array(
                'conditions' => 'idMail = ?1',
                'bind' => array(1 => $idMail)
    ));
    $idcontact =null;
    if ($idContact == "public") {
      $idcontact ="";
    } else {
    $idcontact =$idContact;  
    }
    $contact = Contact::findFirst([["idContact" => (int) $idcontact]]);
    $subAccount = Subaccount::findFirst(["conditions" => "idSubaccount = ?0", "bind" => [0 => $mail->idSubaccount]]);

    if ($mail && $mailContent) {
      $urlManager = Phalcon\DI\FactoryDefault::getDefault()->get("urlManager");
      $account = Account::findFirstByIdAccount($subAccount->idAccount);

      /* Cargamos el dominio correspondiente de la cuenta para el envio */
      foreach ($account->AccountConfig->DetailConfig as $key) {
        if ($key->idServices == $this->services->email_marketing) {
          $this->setDetailConfig($key);
        }
      }
      $domain = $this->detailConfig->Dcxurldomain[0]->Urldomain;
//      $domain = Urldomain::findFirst(["conditions" => "idUrldomain = ?0", "bind" => [0 => $account->Accountclassification->idUrldomain]]);

      if ($mailContent->typecontent == "Editor") {
        $editor = new Sigmamovil\Logic\Editor\HtmlObj();
        $editor->setAccount($account);
        $editor->assignContent(json_decode($mailContent->content));
        $html = $editor->render();
      } else if ($mailContent->typecontent == "html") {
        $footerObj = new Sigmamovil\General\Misc\FooterObj();
        $footerObj->setAccount($account);
        $html = $footerObj->addFooterInHtml(html_entity_decode($mailContent->content));
      }

      $imageService = new \Sigmamovil\General\Misc\ImageService($account, $domain, $urlManager);
      $linkService = new Sigmamovil\General\Misc\LinkService($account, $mail);
      $prepareMail = new \Sigmamovil\General\Misc\PrepareMailContent($linkService, $imageService);
      list($contens, $links) = $prepareMail->processContent($html);

      $trackingObj = new \Sigmamovil\General\Misc\TrackingUrlObject();

      $customfieldManager = new Sigmamovil\General\Misc\CustomfieldManager($mailContent, $urlManager);
      $content = $customfieldManager->prepareUpdatingForms($contens);
      $field = $customfieldManager->searchCustomFields($content);
     
     
      $customFields = false;
      if ($field == false) {
        $fields = false;
      } else {
        $customFields = $field;
      }
     
      $arr = array();
      $arr2 = array();
      $cxc = Cxc::findFirst([["idContact" => (int)$idContact]]);
      if(is_array($cxc) || is_object($cxc)){
        foreach ($cxc->idContactlist as $value) {
          $arr[] = $value;
        }
      }
      foreach ($arr as $key => $value) {  
          foreach ($value as $key1 => $value2) {
          $arr2[$key1] = $value2;
          }
      }
      $contact->customfield[0] = $arr2;
      
      $contentss = $customfieldManager->processCustomFields($contact, $customFields, $content);
      
//      return $contentss["html"];
      $html = $trackingObj->getTrackingUrl($contentss["html"], $idMail, $idContact, $links);

      return $html;
    } else {
      return FALSE;
    }
  }

  function setDetailConfig($detailConfig) {
    $this->detailConfig = $detailConfig;
  }

}
