<?php

/**
 * @RoutePrefix("/api/gallery")
 */
class ApigalleryController extends ControllerBase {

  /**
   * @Get("/{page:[0-9]+}")
   */
  public function indexAction($page) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\GalleryWrapper();
      $wrapper->findAllGallery($page);
      return $this->set_json_response($wrapper->getGallery(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
