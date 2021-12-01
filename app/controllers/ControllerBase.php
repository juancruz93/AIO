<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ControllerBase
 *
 * @author Will
 */
class ControllerBase extends \Phalcon\Mvc\Controller {

  protected $_isJsonResponse = false;

  /**
   * Función para poner title
   */
  protected function initialize() {
    // Agregar el nombre de la aplicación al principio del título
    $this->tag->prependTitle('AIO | ');
  }

  /**
   * Llamar este metodo para enviar respuestas en modo JSON
   * @param string $content
   * @param int $status
   * @param string $message
   * @return \Phalcon\Http\ResponseInterface
   */
  public function set_json_response($content, $status = 200, $message = '') {
    $this->view->disable();

    $this->_isJsonResponse = true;
    $this->response->setContentType('application/json', 'UTF-8');
    $this->response->setHeader('Access-Control-Allow-Origin', '*');
    $this->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');  
    $this->response->sendHeaders();
    
    if ($status != 200) {
      $this->response->setStatusCode($status, $message);
    }

    if (is_array($content)) {
      $content = json_encode($content);
    }
    $this->response->setContent($content);

    return $this->response;
  }

  /**
   * Esta función recibe el tipo de Trace y el mensaje para guardar el registro en la base de datos
   * @param string $controller
   * @param string $action
   * @param int $date
   * @param int $ip
   */
  public function trace($status, $msg, $idUser = null) {
    try {
      $controller = $this->dispatcher->getControllerName();
      $action = $this->dispatcher->getActionName();
      $operation = $controller . '::' . $action;
      $date = time();
      $ip = $_SERVER['REMOTE_ADDR'];

      $idMasteraccount = null;
      $idAllied = null;
      $idAccount = null;
      $idSubaccount = null;
      if (count($this->session->get('arrUser')) > 0) {
        $idUsertype = current($this->session->get('arrUser'))->idUsertype;
        $userOriginal = User::findFirst(["conditions" => "idUsertype = ?0 ", "bind" => [0 => $idUsertype]]);
        $userEffective = $this->user;
        $userDescription = "La acción fue realizada por el super-usuario " . $userOriginal->name . " " . $userOriginal->lastname . " de rol '" . $userOriginal->role->name . "', idUser = " . $userOriginal->idUser . " y email " . $userOriginal->email . " bajo el usuario " . $userEffective->name . " " . $userEffective->lastname . " de rol '" . $userEffective->Role->name . "', de idUser = " . $userEffective->idUser . " y email " . $userEffective->email . ". ";
        $idUserOriginal = $userOriginal->idUser;
        $idUserEffective = $userEffective->idUser;
        $user = $userEffective;
      } else {

        $user = ((\Phalcon\DI::getDefault()->has('user')) ? Phalcon\DI::getDefault()->get('user') : "indefinido");
        if ($idUser != null) {
          $user = User::findFirst(["conditions" => "idUser = ?0 ", "bind" => [0 => $idUser]]);
        }
        
        if ($user != "indefinido") {
          $userDescription = "La acción fue realizada por el usuario " . $user->name . " " . $user->lastname . " de rol '" . $user->Role->name . "', de idUser = " . $user->idUser . " y email " . $user->email . ". ";
          $idUserOriginal = null;
          $idUserEffective = $user->idUser;
        } else {
          $userDescription = "La acción fue realizada mediante URL pública";
          $idUserOriginal = null;
          $idUserEffective = null;
        }
      }

      if ($user->Role->name == $this->nameRoles->master) {
        $idMasteraccount = $user->userType->idMasteraccount;
      } else if ($user->Role->name == $this->nameRoles->allied) {
        $idMasteraccount = $user->userType->Allied->idMasteraccount;
        $idAllied = $user->userType->idAllied;
      } else if ($user->Role->name == $this->nameRoles->account) {
        $idMasteraccount = $user->userType->Account->Allied->idMasteraccount;
        $idAllied = $user->userType->Account->idAllied;
        $idAccount = $user->userType->idAccount;
      } else if ($user->Role->name == $this->nameRoles->subaccount) {
        $idMasteraccount = $user->userType->Subaccount->Account->Allied->idMasteraccount;
        $idAllied = $user->userType->Subaccount->Account->idAllied;
        $idAccount = $user->userType->Subaccount->idAccount;
        $idSubaccount = $user->userType->idSubaccount;
      }


      $trace = new Trace();
      $trace->idUserOriginal = $idUserOriginal;
      $trace->idUserEffective = $idUserEffective;
      $trace->result = $status;
      $trace->operation = $operation;
      $trace->userDescription = $userDescription;
      $trace->description = $msg;
      $trace->date = $date;
      $trace->ip = ip2long($ip);
      $trace->idMasteraccount = $idMasteraccount;
      $trace->idAllied = $idAllied;
      $trace->idAccount = $idAccount;
      $trace->idSubaccount = $idSubaccount;

      if (!$trace->save()) {
        foreach ($trace->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while tracing...{$ex->getMessage()}-----{$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * Retorna el contenido (JSON) POST desde ember transformado en datos que pueda leer PHP
   */
  public function getRequestContent() {
//    if (isset($this->requestContent->content) && $this->requestContent) {
//      return $this->requestContent->content;
//    } else {
    return $this->request->getRawBody();
//    }
  }

  /**
   * Función que valida el prefijo de una cuenta, si este se encuentra vacio, toma las primeras
   * 4 letras del nombre de la cuenta
   * @param string $name
   * @param string $prefix
   * @return string $new_prefix
   */
  public function validatePrefix($name, $prefix) {
    $p = (empty($prefix) ? $name : $prefix);

    $prefix = (strlen($p) <= 4 ? strtolower($p) : $this->getPrefix($p));

    return $prefix;
  }

  /**
   * Toma una cadena y la recorta hasta dejarla del de tamaño de 4 caracteres
   * @param string $name
   * @return string $string
   */
  public function getPrefix($name) {
    $name = str_replace(' ', '', $name);
    $prefix = strtolower(substr($name, 0, 4));

    return $prefix;
  }

  /**
   * Toma el nombre de usuario y borra el prefijo
   * @param string $username
   * @return string
   */
  public function removePrefix($username) {
    $un = explode('_', $username);

    if (isset($un[1])) {
      return $un[1];
    }
    return $username;
  }

  /**
   * Valida que exista un modelo que se haya buscado
   * @param type $model
   * @param type $msg
   * @param type $redirect
   * @return type
   */
  protected function validateModel($model, $msg, $redirect) {
    if (!$model) {
      $this->notification->error($msg);
      return $this->response->redirect($redirect);
    }
  }

  public function getSpaceUsedInAccount($idAccount = null) {
    if ($this->user->UserType->idAccount) {
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->idAccount)
      ));
    }
    if ($this->user->UserType->idSubaccount) {
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $this->user->UserType->Subaccount->idAccount)
      ));
    }
    //$account = $this->user->account;
    $phql = "SELECT SUM(Asset.size) cnt FROM Asset WHERE Asset.idAccount = :idAccount:";
    $result = $this->modelsManager->executeQuery($phql, array('idAccount' => isset($account->idAccount) ? $account->idAccount : $idAccount));

    $space = ($result->getFirst()->cnt / 1048576);

    return $space;
  }

  public function getSpaceUsedInAllied($idAllied = null) {
    if (isset($idAllied)) {
      $sql = "SELECT idAccount FROM account WHERE idAllied = " . $idAllied;
      $accounts = $this->db->fetchall($sql);
      $idAccounts = "";
      foreach ($accounts as $value) {
        $idAccounts .= $value['idAccount'] . ",";
      }
    }
    $idAccounts = trim($idAccounts, ",");

    $phql = "SELECT SUM(Asset.size) cnt FROM Asset WHERE Asset.idAccount IN (:idAccounts:)";
    $result = $this->modelsManager->executeQuery($phql, array('idAccounts' => $idAccounts));

    $space = ($result->getFirst()->cnt / 1048576);

    return $space;
  }

  public function createRechageHistory($idAccountConfig, $rechargeAmount, $initialTotal, $idServices, $idAlliedconfig, $idMasterConfig,$initialAmount) {
    $rechargeHistory = new RechargeHistory();

    $rechargeHistory->idAccountConfig = $idAccountConfig;
    $rechargeHistory->idAlliedconfig = $idAlliedconfig;
    $rechargeHistory->idMasterConfig = $idMasterConfig;
    $rechargeHistory->rechargeAmount = $rechargeAmount;
    $rechargeHistory->idServices = $idServices;
    $rechargeHistory->initialAmount = $initialAmount;
    $rechargeHistory->initialTotal = $initialTotal;

    if (!$rechargeHistory->save()) {
      foreach ($rechargeHistory->getMessages() as $msg) {
        throw new Exception($msg);
      }
    }
  }

}
