<?php

namespace Sigmamovil\Wrapper;

/**
 * Description of PubliclandingpageWrapper
 *
 * @author juan.pinzon
 */
class PubliclandingpageWrapper extends \BaseWrapper {

  private $landingpage;
  private $dirPublic;
  private $serv;

  public function __construct() {
    parent::__construct();
    $this->dirPublic = getcwd();
    $this->ipServer = \Phalcon\DI::getDefault()->get('ipServer');
  }

  public function publicationLandingPage($idLandingPage, $ip) {
    $lp = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));

    $this->setLandingpage($lp);

    $this->validationsLandingPage();
    $this->discountVisit($ip);
    $content = $this->getContentLandingPage();

    return $content->pages->index->blocks;
  }

  function getLandingpage() {
    return $this->landingpage;
  }

  function setLandingpage($landingpage) {
    $this->landingpage = $landingpage;
  }

  function getServ() {
    return $this->serv;
  }

  function setServ($serv) {
    $this->serv = $serv;
  }

  private function validationsLandingPage() {
    if (!$this->getLandingpage()) {
      throw new \InvalidArgumentException("La Landing Pages a la que intenta ingresar no existe");
    }

    $this->getLandingpage()->status = "published";
    $this->getLandingpage()->save();

    if ($this->getLandingpage()->status == "draft") {//Ese draft debe cambiarse por un valor en el config.ini
      throw new \InvalidArgumentException("Esta LandingPage está en construcción ;)");
    }

    foreach ($this->getLandingpage()->Subaccount->Saxs as $service) {
      if ($service->idServices == 8) {
        $this->setServ($service);
        break;
      }
    }

    if ($this->getServ()->amount == 0) {
      throw new \InvalidArgumentException("Ha llegado al límite de la cantidad de vistas permitidas en esta Landing Page");
    }

    if ($this->getLandingpage()->countview != 0) {
      if ($this->getLandingpage()->countview == $this->countViewsToLandingPage($this->getLandingpage()->idLandingPage)) {
        throw new \InvalidArgumentException("Ha llegado al límite de la cantidad de vistas permitidas en esta Landing Page");
      }
    }

    $now = new \DateTime('now', new \DateTimeZone('America/Bogota'));

    if ($this->getLandingpage()->startDate > $now->format("Y-m-d H:i:s")) {
      throw new \InvalidArgumentException("La LandingPage aún no está disponible");
    }
    if ($now->format("Y-m-d H:i:s") > $this->getLandingpage()->endDate) {
      throw new \InvalidArgumentException("La LandingPage ha expirado");
    }
  }

  private function countViewsToLandingPage($idLandingPage) {
    $lpv = \Landingpageviews::count(array(
                "conditions" => array(
                    "idLandingPage" => (int) $idLandingPage
                )
    ));

    return $lpv;
  }

  private function discountVisit($ip) {
    $lpviews = \Landingpageviews::findFirst(array(
                "conditions" => array(
                    "idLandingPage" => (int) $this->getLandingpage()->idLandingPage,
                    "IpAddress" => (string) $ip
                )
    ));

    if (!$lpviews) {
      $lpnewview = new \Landingpageviews();
      $contactManager = new \Sigmamovil\General\Misc\ContactManager();
      $nextIdAnswer = $contactManager->autoIncrementCollection("idLandingPageViews");
      $lpnewview->idPageLandingViews = $nextIdAnswer;
      $lpnewview->idLandingPage = (int) $this->getLandingpage()->idLandingPage;
      $lpnewview->IpAddress = (string) $ip;
      $lpnewview->createdBy = "desarrollo@sigmamovil.com";
      $lpnewview->updatedBy = "desarrollo@sigmamovil.com";

      if (!$lpnewview->save()) {
        foreach ($lpnewview->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      $idSubaccount = (int) $this->getLandingpage()->Subaccount->idSubaccount;
      $sql = "CALL updateCountersLandingPageSaxs({$idSubaccount})";
      $this->db->execute($sql);
    }
  }

  private function getContentLandingPage() {
    $obSub = $this->getLandingpage()->Subaccount; //Objeto subaccount
    $path = "{$this->dirPublic}{$this->asset->assets}{$obSub->Account->idAccount}/landing-pages/"
            . "{$this->getLandingpage()->idLandingPage}/site{$this->getLandingpage()->idLandingPage}.json";

    if (!file_exists($path)) {
      throw new \InvalidArgumentException("No se ha encontrado el archivo base de la Landing Page");
    }

    $json = file_get_contents($path);

    $urlAbsolute = "..\/library\/htmlbuilder\/elements\/bundles\/";
    $html = str_replace(["bundles\\", "..\{$urlAbsolute}"], [$urlAbsolute, $urlAbsolute], $json);

    return json_decode($html);
  }

  public function getTitlePage() {
    $footerInfo = json_decode($this->getLandingpage()->footerInfo);

    return $footerInfo->website;
  }

  public function getFBcontent($idLandingPage) {

    if (!isset($idLandingPage)) {
      throw new \InvalidArgumentException("Dato de landingPage inválido");
    }

    $landingPage = \LandingPage::findFirst(array(
                "conditions" => "idLandingPage = ?0",
                "bind" => array($idLandingPage)
    ));

    // generamos link de visualizacion
    $footerInfo = json_decode($landingPage->footerInfo);

    $titleLP = strtolower(str_replace(" ", "", $footerInfo->website));

    //$url = "{$this->urlManager->get_base_uri(true)}lp/{$titleLP}/{$landingPage->idLandingPage}";    
    $url = "{$this->ipServer->ip}/lp/{$titleLP}/{$landingPage->idLandingPage}";

    // generamos imagen de visualizacion    
    $filePath = "{$this->dirPublic}/assets/{$landingPage->Subaccount->Account->idAccount}/landing-pages/{$landingPage->idLandingPage}/site{$landingPage->idLandingPage}.json";
    
    if (!file_exists($filePath)) {
      throw new \InvalidArgumentException("El archivo base de la LandingPage no existe");
    }

    $json = file_get_contents($filePath);
    $content = json_decode($json);

    if (count($content->pages->index->blocks) > 1) {
      $path = "{$this->asset->assets}{$landingPage->Subaccount->Account->idAccount}/landing-pages/{$landingPage->idLandingPage}/{$landingPage->idLandingPage}_thumbnail.png";
         
      $thumbnail = "{$this->dirPublic}{$path}";
      $thumb = "{$this->ipServer->ip}{$path}";
      if (!file_exists($thumbnail)) {
        $thumb = "{$this->ipServer->ip}images/circle/plantillas.jpg";
      }
      
      
      return [
          "hasContent" => true,
          "thumbnail" => $thumb,
          "url" => $url
      ];
    } 


  }

}
