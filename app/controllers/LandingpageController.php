<?php

class LandingpageController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Landing Page");
    parent::initialize();
  }

  public function indexAction() {
    $flag = false;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      echo $key->idServices;
      if ($key->idServices == $this->services->landing_page) {
        $flag = true;
      }
    }
    if ($flag == false) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }
    $this->view->setVar('app_name', "landingpage");
  }

  public function listAction() {
    $this->view->setVar('app_name', "landingpage");
  }

  public function createAction() {
    $this->view->setVar("app_name", "landingpage");
  }

  public function basicinformationAction() {
    $landingpageForm = new LandingpageForm();
    $this->view->setVar("landingpageForm", $landingpageForm);
  }

  public function pagebuilderAction($idLandingPage) {
    if (!isset($idLandingPage)) {
      $this->notification->error("No se ha especificado el argumento de Landing Page");
      return $this->response->redirect("landing#/");
    }
    $this->tag->setTitle("AIO | Constructor de páginas");
    $this->view->setVar("app_name", "pagebuilder");
    $this->view->setVar("controllerName", "landingpage");
    $this->view->setVar("idLandingPage", $idLandingPage);
  }

  public function designerAction() {
    
  }

  public function contentAction() {
    
  }

  public function confirmationAction() {
    
  }

  public function shareAction() {
    $di = Phalcon\DI\FactoryDefault::getDefault();
    $this->view->setVar('idfb', $di->get('configFb')->idApp);
  }

  public function previewAction($idlp) {
    try {
      if (!isset($idlp)) {
        throw new InvalidArgumentException("No hay argumento para mostrar la vista previa");
      }

      $lp = LandingPage::findFirst(array(
                  "conditions" => "idLandingPage = ?0",
                  "bind" => array($idlp)
      ));

      $path = getcwd() . $this->asset->assets . "{$lp->Subaccount->idAccount}/landing-pages/{$lp->idLandingPage}/site{$lp->idLandingPage}.json";

      if (!file_exists($path)) {
        throw new InvalidArgumentException("El archivo base de la plantilla de LandingPage no existe");
      }

      $json = file_get_contents($path);
      $urlAbsolute = "..\/library\/htmlbuilder\/elements\/bundles\/";
      $html = str_replace(["bundles\\", "..\{$urlAbsolute}"], [$urlAbsolute, $urlAbsolute], $json);
      $content = json_decode($html);
      $this->view->setVar("blocks", $content->pages->index->blocks);
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
      $this->response->redirect("landingpage#/");
    } catch (Exception $ex) {
      $this->notification->error("Ha ocurrido un error, por favor comuniquese con soporte");
      $this->response->redirect("landingpage#/");
    }
  }

}
