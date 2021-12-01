<?php

/**
 * @RoutePrefix("/api/country")
 */
class ApicountryController extends ControllerBase {

  /**
   * 
   * @Get("/getcountries/{page:[0-9]+}")
   */
  public function getcountriesAction($page) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\CountryWrapper();
      $wrapper->findCountries($page);
      return $this->set_json_response($wrapper->getCountries(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding countries... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getonecountry/{idCountry:[0-9]+}")
   */
  public function getonecountryAction($idCountry) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\CountryWrapper();
      $wrapper->findCountry($idCountry);
      return $this->set_json_response($wrapper->getCountry(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding countries... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/edit")
   */
  public function editAction() {
    try {

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);

      $wrapper = new \Sigmamovil\Wrapper\CountryWrapper();
      $wrapper->editCountry($data);
      $this->trace("success", "Se ha editado el país");

      return $this->set_json_response(array("message" => "Se ha editado el país exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while editing country... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/indicatives")
   */
  public function getallindicativesAction() {
    try {
      $wrapper = new \Sigmamovil\Wrapper\CountryWrapper();
      return $this->set_json_response($wrapper->getAllIndicatives());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get indicatives... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
