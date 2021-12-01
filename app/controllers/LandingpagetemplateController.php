<?php

/**
 * Description of LandingpagetemplateController
 *
 * @author juan.pinzon
 */
class LandingpagetemplateController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Plantillas de Landing Page");
    parent::initialize();
  }

  public function indexAction() {
    
  }

  public function listAction() {
    
  }

  public function createAction($idlpt) {
    if (!isset($idlpt)) {
      $idlpt = 0;
    }
    $this->view->setVar("idLandingPageTemplate", $idlpt);
    $this->view->setVar("controllerName", "lptemplate");
  }

  public function previewAction($idlpt) {
    try {
      if (!isset($idlpt)) {
        throw new InvalidArgumentException("No hay argumento para mostrar la vista previa");
      }

      $lpt = LandingPageTemplate::findFirst(array(
                  "conditions" => "idLandingPageTemplate = ?0",
                  "bind" => array($idlpt)
      ));

      $path = getcwd() . $this->asset->assets . "{$lpt->idAccount}/landing-pages-templates/{$lpt->idLandingPageTemplate}/site{$lpt->idLandingPageTemplate}.json";

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

  public function selecttemplateAction() {
    
  }

}
