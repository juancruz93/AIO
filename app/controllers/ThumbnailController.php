<?php

class ThumbnailController extends ControllerBase {

  public $detailConfig;

  public function initialize() {
    $this->tag->setTitle("Thumbnail");
    parent::initialize();
  }

  public function mailtemplateshowAction($idMailTemplate) {
    try {
      $mailtemplate = MailTemplate::findFirst(array(
                  "conditions" => "idMailTemplate = ?0",
                  "bind" => array($idMailTemplate)
      ));

      $allied = ((isset($mailtemplate->Allied->idAllied)) ? $mailtemplate->Allied : NULL);
      $account = ((isset($mailtemplate->Account->idAccount)) ? $mailtemplate->Account : NULL);

      if (!$mailtemplate) {
        throw new InvalidArgumentException("La plantilla de correo no existe, para crear el thumbnail");
      }

      //Se carga el dominio correspondiente
      $detconf = NULL;
      if (isset($allied)) {
        $detconf = $allied->Alliedconfig->DetailConfig;
      } elseif (isset($account)) {
        $detconf = $account->AccountConfig->DetailConfig;
      }

      foreach ($detconf as $value) {
        if ($value->idServices == $this->services->email_marketing) {
          $this->detailConfig = $value;
          break;
        }
      }
      $domain = $this->detailConfig->Dcxurldomain[0]->Urldomain;

      $editorobj = new Sigmamovil\Logic\Editor\HtmlObj();
      $editorobj->setAccount(NULL);
      $editorobj->assignContent(json_decode($mailtemplate->MailTemplateContent->content));
      $html = $editorobj->render();

      $this->view->setVar("html", $this->prepareContent($html, $domain));
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->notification->error("Ha ocurrido un error, por favor contacte con soporte");
      $this->logger->log($ex->getTraceAsString());
    }
  }

  public function mailshowAction($idMail) {
    try {
      $mail = Mail::findFirst(array(
                  "conditions" => "idMail = ?0",
                  "bind" => array($idMail)
      ));

      if (!$mail) {
        throw new InvalidArgumentException("El correo no existe para crear el thumbnail");
      }

      $detconf = $mail->Subaccount->Account->AccountConfig->DetailConfig;

      foreach ($detconf as $value) {
        if ($value->idServices == $this->services->email_marketing) {
          $this->detailConfig = $value;
          break;
        }
      }

      $domain = $this->detailConfig->Dcxurldomain[0]->Urldomain;

//      if (isset($mail->MailContent->typecontent)) {
//        if ($mail->MailContent->typecontent == "html") {
//          $content = $mail->MailContent->content;
//        } elseif ($mail->MailContent->typecontent == "Editor" || $mail->MailContent->typecontent == "url") {
//          $content = json_decode($mail->MailContent->content);
//        }
//      } else {
//        $content = "Hola";
//      }
//      $editorobj = new Sigmamovil\Logic\Editor\HtmlObj();
//      $editorobj->setAccount(NULL);
//      $editorobj->assignContent($content);
//      $html = $editorobj->render();
      if ($mail->MailContent->typecontent == "Editor") {
        $htmlObj = new \Sigmamovil\Logic\Editor\HtmlObj();
        $htmlObj->setAccount(NULL);
        $htmlObj->assignContent(json_decode($mail->MailContent->content));
        $html = utf8_decode($htmlObj->replacespecialchars($htmlObj->render()));
      } else if ($mail->MailContent->typecontent == "html" || $mail->MailContent->typecontent == "url") {
        $footerObj = new \Sigmamovil\General\Misc\FooterObj();
        $footerObj->setAccount(NULL);
        $html = utf8_decode($footerObj->addFooterInHtml(html_entity_decode($mail->MailContent->content, ENT_QUOTES)));
      }

      $this->view->setVar("html", $this->prepareContent($html, $domain));
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
    } catch (Exception $ex) {
      $this->notification->error("Ha ocurrido un error, por favor contacte con soporte");
      $this->logger->log($ex->getTraceAsString());
    }
  }

  public function validateSrcImage($imageSrc) {
    if (preg_match("/asset/i", $imageSrc)) {
      $idAsset = filter_var($imageSrc, FILTER_SANITIZE_NUMBER_INT);
    } elseif (preg_match("asset/showalliedassets/i", $imageSrc)) {
      $idAsset = filter_var($imageSrc, FILTER_SANITIZE_NUMBER_INT);
    }

    return $idAsset;
  }

  public function getRealImageSrc($idAsset, $domain) {
    $asset = Asset::findFirst(array(
                "conditions" => "idAsset = ?0",
                "bind" => array($idAsset)
    ));

    if ($asset) {
      $urlasset = "";
      $idOwner = null;
      if (isset($asset->Allied->idAllied)) {
        $urlasset = "allied-assets";
        $idOwner = $asset->Allied->idAllied;
      } elseif (isset($asset->Account->idAccount)) {
        $urlasset = "assets";
        $idOwner = $asset->Account->idAccount;
      } elseif (!isset($asset->Allied->idAllied) && !isset($asset->Account->idAccount)) {
        $urlasset = "root-assets";
        $idOwner = "";
      }

      $ext = pathinfo($asset->name, PATHINFO_EXTENSION);
      $img = "{$domain->name}{$urlasset}/{$idOwner}/images/{$asset->idAsset}.{$ext}";

      return $img;
    }
  }

  public function prepareContent($html, $domain) {
    if (trim($html) === '') {
      throw new InvalidArgumentException("El contenido está vacío");
    }

    $htmlObj = new DOMDocument();
    @$htmlObj->loadHTML($html);

    $images = $htmlObj->getElementsByTagName("img");

    if ($images->length != 0) {
      foreach ($images as $image) {
        $imageSrc = $image->getAttribute("src");
        $idAsset = $this->validateSrcImage($imageSrc);
        $newSrc = $this->getRealImageSrc($idAsset, $domain);

        if ($newSrc) {
          $image->setAttribute("src", $newSrc);
        }
      }
    }

    $htmlFinal = $htmlObj->saveHTML();

    return $htmlFinal;
  }

}
