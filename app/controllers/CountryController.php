<?php

class CountryController extends ControllerBase {

  public function countryAction() {
    try {
      $modelManager = Phalcon\DI::getDefault()->get('modelsManager');
      $array = array();
      $sql = "SELECT * FROM Country ORDER BY name";
      $country = $modelManager->executeQuery($sql);
      foreach ($country as $value => $key) {
        $array[$value] = $key;
      }
      return $this->set_json_response($array, 200, "OK");
    } catch (Exception $ex) {
      $this->notification->error($ex->getMessage());
    }
  }

  public function stateAction($idCountry) {
    try {
      $modelManager = Phalcon\DI::getDefault()->get('modelsManager');
      $array = array();
      if ($idCountry == -1) {
        $sql = "SELECT * FROM State  ORDER BY name";
        $state = $modelManager->executeQuery($sql);
      } else {
        $sql = "SELECT * FROM State WHERE idCountry = :idCountry: ORDER BY name";
        $state = $modelManager->executeQuery($sql, array('idCountry' => $idCountry));
      }
      foreach ($state as $value => $key) {
        $array[$value] = $key;
      }
      return $this->set_json_response($array, 200, "OK");
    } catch (Exception $ex) {
      $this->notification->error($ex->getMessage());
    }
  }

  public function citiesAction($idState) {
    try {
      $modelManager = Phalcon\DI::getDefault()->get('modelsManager');
      $array = array();
      $sql = "SELECT * FROM City WHERE idState = :idState: ";
      $city = $modelManager->executeQuery($sql, array("idState" => $idState));
      $array = $city->toArray();
      /*foreach ($city as $value => $key) {
        $key->name = $key->name;
        $array[$value] = $key;
      }*/
      return $this->set_json_response($array, 200, "OK");
    } catch (Exception $ex) {
      $this->notification->error($ex->getMessage());
    }
  }

  public function indexAction() {

    $this->view->setVar("app_name", "country");
  }

  public function listAction() {
    
  }
  public function editAction() {
    
  }
  
  public function getallcountryAction(){
    try {
      $contry = \Country::find();
      $data = array();
      if (count($contry) > 0) {
        foreach ($contry as $key => $value) {
          $data[$key] = array(
            "idCountry"=>$value->idCountry,
            "name"=>$value->name,  
          );
        }
      }
      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding listfullservices ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
