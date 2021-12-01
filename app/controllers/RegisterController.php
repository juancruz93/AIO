<?php

use Sigmamovil\General\Links\ParametersEncoder;

class RegisterController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Registro");
    parent::initialize();
  }

  public function indexAction() {
    $this->view->setVar("app_name", "register");
  }

  public function signupAction() {
    $this->view->setVar("form", new AccountForm());
  }

  public function paymentplanAction() {
    
  }

  public function paymentplandetailAction() {
    
  }

  public function paymentAction() {
    
  }

  public function payAction() {
    $this->view->setVar("form", new PaymentForm());
  }

  public function validatemailAction($id) {
    try {
      $this->db->begin();
      if (!isset($id)) {
        throw new InvalidArgumentException("Su correo no puede ser verificado");
      }

      $parametersencoder = new ParametersEncoder();
      $parametersencoder->setBaseUri($this->urlManager->get_base_uri(true));
      $params = $parametersencoder->decodeLink("register/validatemail", $id);

      $user = User::findFirst(array(
                  "conditions" => "idUser = ?0",
                  "bind" => array($params[0])
      ));

      if (!$user) {
        throw new InvalidArgumentException("El usuario a validar no existe");
      }

      $subaccount = Subaccount::findFirst(array(
                  "conditions" => "idSubaccount = ?0",
                  "bind" => array($user->Usertype->Subaccount->idSubaccount)
      ));

      $subaccount->status = 1;

      if (!$subaccount->save()) {
        foreach ($subaccount->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?0",
                  "bind" => array($subaccount->idAccount)
      ));

      if (!$subaccount || !$account) {
        throw new InvalidArgumentException("La cuenta no fue creada o validada correctamente, por favor contacte con soporte");
      }

      $account->status = 1;

      if (!$account->save()) {
        foreach ($account->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      $this->db->commit();
      $this->notification->success("Su cuenta fue verificada correctamente");
      return $this->response->redirect("session#/");
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->notification->error($ex->getMessage());
      $this->response->redirect("register#/");
    }
  }

  public function congratulationsAction($id) {
    try {
      if (!isset($id)) {
        throw new InvalidArgumentException("Ha ocurrido un error guardando la nueva cuenta");
      }

      $user = User::findFirst(array(
                  "columns" => "idUser, email",
                  "conditions" => "idUser = ?0",
                  "bind" => array($id)
      ));

      if (!$user) {
        throw new InvalidArgumentException("El usuario no se guardó correctamente");
      }

      $this->view->setVar("email", $user->email);
    } catch (InvalidArgumentException $ex) {
      $this->notification->error($ex->getMessage());
      $this->response->redirect("register#/");
    }
  }
  
  public function completeprofileAction(){
    
  }

  public function apiAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = (array) json_decode(base64_decode($contentsraw), true);

      $wrapper = new \Sigmamovil\Wrapper\RegisterWrapper();
      return $this->set_json_response($wrapper->register($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while validate user ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  

  public function continueregisterAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = (object) json_decode(base64_decode($contentsraw), true);

      $wrapper = new \Sigmamovil\Wrapper\RegisterWrapper();
      return $this->set_json_response($wrapper->continueRegister($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
    
  }

  public function rangespricesAction($idRangesPrices, $idAccount){
    try {
      $wrapper = new \Sigmamovil\Wrapper\RegisterWrapper();
      return $this->set_json_response($wrapper->rangesPrices($idRangesPrices, $idAccount), 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }    
  }

}