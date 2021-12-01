<?php

/**
 * Description of PubliclandingpageController
 *
 * @author juan.pinzon
 */
use Sigmamovil\Wrapper\PubliclandingpageWrapper as plp;

/**
 * @RoutePrefix("/lp");
 */
class PubliclandingpageController extends ControllerBase {

  /**
   * @Get("/{namesite}/{idlp:[0-9]+}")
   */
  public function publicationAction($nameSite, $idLP) {
    try {
      $wrapper = new plp();
      $blocks = $wrapper->publicationLandingPage($idLP, $this->request->getClientAddress());
      $this->view->setVar("blocks", $blocks);
      $this->view->setVar("title", $wrapper->getTitlePage());
      $content = $wrapper->getFBcontent($idLP);

      $this->view->setVar("url", $content['url']);
      $this->view->setVar("image", $content['thumbnail']);
    } catch (InvalidArgumentException $ex) {
      $this->session->set('msj', $ex->getMessage());
      return $this->response->redirect("lp/errors");
    } catch (Exception $ex) {
      $this->cookies->set('msj', $ex->getMessage());
      return $this->response->redirect("lp/errors");
    }
  }

  /**
   * @Get("/errors")
   */
  public function errorsAction() {
    $this->view->setVar("msj", $this->session->get('msj'));
  }

}
