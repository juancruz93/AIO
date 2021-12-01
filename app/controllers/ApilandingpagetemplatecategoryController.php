<?php

/**
 * Description of ApilandingpagetemplatecategoryController
 *
 * @author juan.pinzon
 */
use Sigmamovil\Wrapper\LandingpagetemplatecategoryWrapper as lptcw;

/**
 * @RoutePrefix("/api/lptemplatecategory")
 */
class ApilandingpagetemplatecategoryController extends ControllerBase {

  /**
   * @Get("/getall")
   */
  public function getallAction() {
    try {
      $wrapper = new lptcw();
      return $this->set_json_response($wrapper->getAllLPTW());
    } catch (Exception $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while getall landing page template category ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/savesimple")
   */
  public function savesimplecategoryAction() {
    try {
      $json = $this->getRequestContent();
      $data = json_decode($json);

      if (!$data) {
        throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información");
      }

      $wrapper = new lptcw();
      return $this->set_json_response($wrapper->saveSimple($data));
    } catch (Exception $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while savesimple landing page template category ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
