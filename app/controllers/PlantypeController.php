<?php

class PlantypeController extends ControllerBase {

  public function listplanttypeAction() {
    try {
      $plantype = PlanType::find(array(
                  "conditions" => "deleted = ?0",
                  "bind" => array(0)
      ));
      $data = [];
      if (count($plantype) > 0) {
        foreach ($plantype as $key => $value) {
          $data[$key] = array(
              "idPlanType" => $value->idPlanType,
              "name" => $value->name
          );
        }
      }

      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding plantype ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
