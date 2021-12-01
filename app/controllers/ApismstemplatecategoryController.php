<?php

use Sigmamovil\Wrapper\SmstemplatecategoryWrapper;

/**
 * @RoutePrefix("/api/smstemplatecategory")
 */
class ApismstemplatecategoryController extends ControllerBase {

  /**
   * 
   * @Get("/listsmstempcategory")
   */
  public function listsmstemplatecategoryAction() {
    try {
      $smstempcategory = new SmstemplatecategoryWrapper();
      return $this->set_json_response($smstempcategory->listSmsTemplateCategory());
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms template category ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savemailtempcategory")
   */
  public function savesmstemplatecategoryAction() {
    try {
      $dataJson = $this->request->getRawBody();
      $arrayData = json_decode($dataJson);

      if (!$arrayData) {
        throw new InvalidArgumentException("Varifique la información enviada");
      }

      $smstempcatwrapper = new SmstemplatecategoryWrapper();
      $idSmsTemplateCategory = $smstempcatwrapper->saveSmsTemplateCategory($arrayData);
      $data = array(
          "message" => "Se ha guardado exitosamente la categoría <b>{$arrayData->name}</b> para las plantillas prediseñadas de SMS",
          "idSmsTemplateCategory" => $idSmsTemplateCategory
      );
      $this->trace("success", "Se ha guardado exitosamente la categoría <b>{$arrayData->name}</b> para las plantillas prediseñadas de SMS");

      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while save smstemplatecategory ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
