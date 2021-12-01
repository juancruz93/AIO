<?php

/**
 * @RoutePrefix("/api/apikey")
 */
class ApiapikeyController extends ControllerBase
{

  /**
   * @Get("/{page:[0-9]+}")
   */
  public function indexAction($page)
  {
    try {
      $subaccount = Subaccount::find(array(
          "conditions" => "idAccount = ?0",
          "bind" => array(0 => $this->user->Usertype->idAccount)
      ));

      if (!$subaccount) {
        throw new InvalidArgumentException("Para crear API KEYS, debe haber al menos una subcuenta y un usuario de subcuenta, por favor valida la información");
      }

      $wrapper = new \Sigmamovil\Wrapper\ApikeyWrapper();
      return $this->set_json_response($wrapper->findSubaccount($page), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist ... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/create/{idUser:[0-9]+}")
   */
  public function createAction($idUser)
  {
    $user = User::findFirst(array(
        "conditions" => "idUser = ?1 ",
        "bind" => array(1 => $idUser)
    ));

    if ($user->Usertype->Subaccount->idAccount == \Phalcon\DI::getDefault()->get('user')->Usertype->idAccount) {
      try {
        $obj = new ApiKeyManager();
        $obj->setUser($user);

        if (count($user->apikey) == 1) {
          throw new InvalidArgumentException('Ya existe una API Key para este usuario');
        } else {
          $key = $obj->createAPIKey();
        }

        return $this->set_json_response(array('APIKey' => $key, 'message' => 'Se ha creado la API Key exitosamente'), 200, '');

      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response(array('message' => $msg->getMessage()), 400);
      } catch (Exception $ex) {
        $this->logger->log("Exception while finding Apiapikey ... {$ex}");
        return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
      }
    } else {
      return $this->set_json_response(null, 500, 'No se pudo crear la API Key, por favor contacte al administrador');
    }
  }

  /**
   * @Put("/update/{idUser:[0-9]+}")
   */
  public function updateAction($idUser)
  {
    $user = User::findFirst(array(
        "conditions" => "idUser = ?1 ",
        "bind" => array(1 => $idUser)
    ));

    if ($user->Usertype->Subaccount->idAccount == \Phalcon\DI::getDefault()->get('user')->Usertype->idAccount) {
      try {
        $obj = new ApiKeyManager();
        $obj->setUser($user);

        if (count($user->apikey) == 0) {
          throw new InvalidArgumentException('No existe una API Key para este usuario');
        } else {
          $key = $obj->updateAPIKey();
        }

        return $this->set_json_response(array('APIKey' => $key, 'message' => 'Se ha actualizado la API Key exitosamente'), 200, '');

      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response(array('message' => $msg->getMessage()), 400);
      } catch (Exception $ex) {
        $this->logger->log("Exception while finding Apiapikey ... {$ex}");
        return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
      }
    } else {
      return $this->set_json_response(null, 500, 'No se pudo crear la API Key, por favor contacte al administrador');
    }
  }

  /**
   * @Put("/updatestatus/{idUser:[0-9]+}")
   */
  public function updatestatusAction($idUser)
  {
    $dataJson = $this->request->getRawBody();
    $arrayData = json_decode($dataJson, true);

    if (!empty($arrayData)) {

      $user = User::findFirst(array(
          "conditions" => "idUser = ?1 ",
          "bind" => array(1 => $idUser)
      ));

      if ($user->Usertype->Subaccount->idAccount == \Phalcon\DI::getDefault()->get('user')->Usertype->idAccount) {
        try {
          $obj = new ApiKeyManager();
          $obj->setUser($user);

          if (count($user->apikey) == 0) {
            throw new InvalidArgumentException('No existe una API Key para este usuario');
          } else {
            $key = $obj->updateAPIKeyStatus($arrayData['status']);
          }
          return $this->set_json_response(array('APIKey' => $key, 'message' => 'Se ha actualizado la API Key exitosamente'), 200, '');

        } catch (InvalidArgumentException $msg) {
          return $this->set_json_response(array('message' => $msg->getMessage()), 400);
        } catch (Exception $ex) {
          $this->logger->log("Exception while finding Apiapikey ... {$ex}");
          return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
      } else {
        return $this->set_json_response(null, 500, 'No se pudo crear la API Key, por favor contacte al administrador');
      }
    }
  }

  /**
   * @Route("/delete/{idUser:[0-9]+}")
   */
  public function deleteAction($idUser)
  {
    $user = User::findFirst(array(
        "conditions" => "idUser = ?1 ",
        "bind" => array(1 => $idUser)
    ));

    if ($user->Usertype->Subaccount->idAccount == \Phalcon\DI::getDefault()->get('user')->Usertype->idAccount) {
      try {
        $obj = new ApiKeyManager();
        $obj->setUser($user);

        if (count($user->apikey) == 0) {
          throw new InvalidArgumentException('No existe una API Key para este usuario');
        } else {
          $obj->deleteAPIKey();
        }
        return $this->set_json_response(array('message' => 'Se ha eliminado la API Key exitosamente'), 200, '');

      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response(array('message' => $msg->getMessage()), 400);
      } catch (Exception $ex) {
        $this->logger->log("Exception while finding Apiapikey ... {$ex}");
        return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
      }
    } else {
      return $this->set_json_response(null, 500, 'No se pudo crear la API Key, por favor contacte al administrador');
    }
  }
}