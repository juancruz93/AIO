<?php

/**
 * @RoutePrefix("/api/language")
 */
class ApilanguageController extends ControllerBase {

  /**
   * 
   * @Post("/getlanguage/{page:[0-9]+}")
   */
  public function getlanguageAction($page) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\LanguageWrapper();
      $wrapper->findLanguages($page);
      return $this->set_json_response($wrapper->getLanguages(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding languages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getlanguagefirst/{page:[0-9]+}")
   */
  public function getlanguagefirstAction($id) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\LanguageWrapper();
      $wrapper->findLanguageFirst($id);
      return $this->set_json_response($wrapper->getLanguages(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding languages... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/delete/{idLanguage:[0-9]+}", methods="DELETE")
   */
  public function deleteAction($idLanguage) {
    try {
      $language = Language::findFirst(array('conditions' => "idLanguage= ?0", 'bind' => array($idLanguage)));
      if (!$language) {
        throw new InvalidArgumentException("No se ha encontrado el idioma, por favor intenta de nuevo");
      }

      $wrapper = new \Sigmamovil\Wrapper\LanguageWrapper();
      $wrapper->setLanguage($language);
      $wrapper->deleteLanguage();
      $this->trace("success", "Se ha eliminado el idioma");

      return $this->set_json_response(array("message" => "Se ha eliminado el idioma exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding language... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * 
   * @Put("/edit/{idLanguage:[0-9]+}")
   */
  public function editAction($idLanguage) {
    try {
      $language = Language::findFirst(array('conditions' => "idLanguage = ?0", 'bind' => array($idLanguage)));
      if (!$language) {
        throw new InvalidArgumentException("No se ha encontrado el idioma, por favor intenta de nuevo");
      }

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);

      $wrapper = new \Sigmamovil\Wrapper\LanguageWrapper();
      $wrapper->setLanguage($language);
      $wrapper->setData($data);
      $wrapper->editLanguage();
      $this->trace("success", "Se ha editado el idioma");

      return $this->set_json_response(array("message" => "Se ha editado el idioma exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding language... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
}
