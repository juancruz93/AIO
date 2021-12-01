<?php

/**
 * @RoutePrefix("/api/statisallied")
 */
class ApistatisalliedController extends ControllerBase {

  /**
   * 
   * @Get("/getstatisallied/{page:[0-9]+}")
   */
  public function getstatisalliedAction($page) {
    try {

      $wrapper = new \Sigmamovil\Wrapper\StatisalliedWrapper();
      return $this->set_json_response($wrapper->findStatisallied($page), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  
   /**
   * 
   * @Post("/downloadreport")
   */
  public function downloadreportAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\StatisalliedWrapper();
      $wrapper->getDataStatisallied($data);
      $wrapper->generateReportExcel();
      return $this->set_json_response(array('message'=>'Reporte generado exitosamente'), 200);
    } catch (InvalidArgumentException $msg) {
      $this->logger->log("Exception while create mail... {$msg}");
      return $this->set_json_response(array('message' => $msg->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while create mail... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
