<?php

class AlliedController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Aliados");
    parent::initialize();
  }

  public function indexAction() {
    
  }

  public function listuserAction($idAllied) {
    if (!$idAllied) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('masteraccount');
    }
    $flag = false;
    $allied = Allied::findByIdAllied($idAllied);
    $currentPage = $this->request->getQuery('page', null, 1);
    //var_dump($this->user->Usertype->Masteraccount->Allied);
    if ($this->user->Role->idRole == -1) {
      $flag = true;
    } else {
      foreach ($this->user->Usertype->Masteraccount->Allied as $key) {
        if ($key->idAllied == $idAllied) {
          $flag = true;
        }
      }
    }
    if ($flag == true) {
      $builder = $this->modelsManager->createBuilder()
              ->from('User')
              ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
              ->where("Usertype.idAllied  = {$idAllied}")
              ->orderBy('User.created');
      $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
          "builder" => $builder,
          "limit" => 15,
          "page" => $currentPage
      ));

      $page = $paginator->getPaginate();

      $this->view->setVar("page", $page);
      $this->view->setVar("idAllied", $idAllied);
      $this->view->setVar("allied", $allied[0]);
    } else {
      $this->notification->error("No tiene permisos para entrar a este sitio");
      $this->response->redirect("masteraccount/aliaslist/" . $this->user->Usertype->idMasteraccount);
    }
  }

  public function createuserAction($idAllied) {
    if (!$idAllied) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('');
    }
    $allied = Allied::findFirst(array(
                'conditions' => 'idAllied = ?1',
                'bind' => array(1 => $idAllied)
    ));
    if (!$allied) {
      $this->notification->error("La cuenta enviada no existe, por favor verifique la información");
      return $this->response->redirect("");
    }
    $user = new User();
    $this->view->setVar("allied", $allied);
    $form = new UserForm($user);
    $this->view->UserForm = $form;
    if ($this->request->isPost()) {
      $userManager = new \Sigmamovil\General\Misc\UserManager();
      try {
        if ($this->request->getPost('pass1') != $this->request->getPost('pass2')) {
          throw new InvalidArgumentException("las contraseñas no son iguales");
        }
        if (!is_numeric($this->request->getPost("citySelectedUser"))) {
          throw new InvalidArgumentException("La ciudad es de caracter obligatorio");
        }

        $user = $userManager->creataAlliedUser($this->request->getPost(), $idAllied);
        $this->notification->success('Se ha creado el usuario exitosamente en la cuenta maestra <strong>' . $allied->name . '</strong>');
        $this->trace("success", "Se creo un usuario con ID: {$user->idUser}");
        return $this->response->redirect("allied/listuser/{$idAllied}");
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
      } catch (Exception $exc) {
        echo $exc->getTraceAsString();
      }
    }
  }
  

  

  public function edituserAction($id, $idAllied) {
    if (!$idAllied || !$id) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('');
    }
    $userE = User::findFirst(array(
                "conditions" => "idUser = ?1",
                "bind" => array(1 => $id)
    ));
    if (!$userE) {
      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("");
    }
    $city = City::findByIdCity($userE->idCity);
    $state = State::findByIdState($city[0]->idState);
    $idState = $city[0]->idState;
    $idcountry = $state[0]->idCountry;
    $this->view->setVar("userE", $userE);
    $form = new UserForm($userE);
    $this->view->UserForm = $form;
    $this->view->setVar("idAllied", $idAllied);
    $this->view->setVar("idCountry", $idcountry);
    $this->view->setVar("idState", $idState);
    if ($this->request->isPost()) {
      $form->bind($this->request->getPost(), $userE);
      try {
        $name = $form->getValue('name');
        $lastname = $form->getValue('lastname');
        $movil = $form->getValue('cellphone');
        $citySelected = $this->request->getPost("citySelectedUser");
        if (empty($name) or empty($lastname) or empty($movil)) {
          throw new \InvalidArgumentException("Todos los campos son obligatorios");
        }
        if (empty($citySelected)) {
          throw new InvalidArgumentException("La ciudad es de caracter obligatorio");
        }
        $userE->idCity = $this->request->getPost("citySelectedUser");

        if ($userE->save()) {
          $this->notification->success('Se ha editado exitosamente el usuario <strong>' . $userE->name . '</strong>');
          $this->trace("success", "Se edito un usuario con ID: {$userE->idUser}");
          return $this->response->redirect("allied/listuser/{$idAllied}");
        } else {
          foreach ($userE->getMessages() as $message) {
            $this->notification->error($message);
          }
          $this->trace("fail", "No se edito el usuario con ID: {$userE->idUser}");
        }
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
      } catch (Exception $ex) {
        echo $ex->getTraceAsString();
      }
    }
  }

  public function deleteuserAction($id, $idAllied) {
    try {
      $idUser = $this->session->get('idUser');

      $user = User::findFirst(array(
                  "conditions" => "idUser = ?1",
                  "bind" => array(1 => $id)
      ));

      if ($id == $idUser) {
        throw new Exception("No se puede eliminar el usuario que esta actualmente en sesión, por favor verifique la información");
        $this->trace('fail', "Se intento borrar un usuario en sesión: {$idUser}");
      }

      if (!$user) {
        throw new Exception("El usuario que ha intentado eliminar no existe, por favor verifique la información");
        $this->trace('fail', "El usuario no existe: {$idUser}");
      }

      if ($user->idAccount == $this->user->idAccount) {
        
      } else {
        throw new Exception("El usuario que ha intentado eliminar no existe, por favor verifique la información");
        $this->trace('fail', "El usuario no existe: {$idUser}");
      }

      if (!$user->delete()) {
        foreach ($user->getMessages() as $msg) {
          throw new Exception($msg);
        }
      } else {
        $this->trace('success', "Se ha eliminado el usuario <strong>{$user->username}</strong> exitosamente");
        return $this->response->redirect("allied/listuser/{$idAllied}");
      }
    } catch (Exception $e) {
      $this->notification->error($e->getMessage());
      return $this->response->redirect("allied/listuser/{$idAllied}");
    }
  }

  public function passedituserAction($id, $idAllied) {

    $editUser = User::findFirst(array(
                "conditions" => "idUser = ?1",
                "bind" => array(1 => $id)
    ));

    if (!$editUser) {
      $this->flashSession->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("allied/listuser/{$idAllied}");
    }
//    if ($editUser->idAccount != $this->user->idAccount) {
//      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
//      return $this->response->redirect("allied/listuser/{$idAllied}");
//    }
    $this->view->setVar("userE", $editUser);
    $this->view->setVar("idAllied", $idAllied);
    if ($this->request->isPost()) {
      try {
        $pass = $this->request->getPost('pass1');
        $pass2 = $this->request->getPost('pass2');
        if ((empty($pass) || empty($pass2))) {
          throw new InvalidArgumentException("El campo Contraseña esta vacío, por favor valide la información");
        }
        if (($pass != $pass2)) {
          throw new InvalidArgumentException("Las contraseñas no coinciden");
        }
        if (strlen($pass) < 8) {
          throw new InvalidArgumentException("La contraseña es muy corta, debe tener como minimo 8 caracteres");
        }
        $editUser->password = $this->security->hash($pass);
        if (!$editUser->save()) {
          foreach ($editUser->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se edito la contraseña del usuario con ID: {$editUser->idUser}");
        }
        $this->notification->info('Se ha editado la contraseña exitosamente del usuario <strong>' . $editUser->username . '</strong>');
        $this->trace("sucess", "Se edito la contraseña del usuario con ID: {$editUser->idUser}");
        return $this->response->redirect("allied/listuser/{$idAllied}");
      } catch (InvalidArgumentException $e) {
        $this->notification->error($e->getMessage());
      } catch (Exception $e) {
        $this->trace("fail", $e->getTraceAsString());
        $this->logger->log("Exception while creating masteraccount: {$e->getMessage()}");
        $this->logger->log($e->getTraceAsString());
        $this->notification->error("Ocurrió un error, por favor contacte al administrador");
        return $this->response->redirect('/');
      }
    }
  }

  public function showAction($idAllied) {

    if (!$idAllied) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('');
    }
    $allied = Allied::findfirst([
                'idAllied = ?0',
                'bind' => [$idAllied]
    ]);
    $config = Alliedconfig::findfirst([
                'idAllied = ?0',
                'bind' => [$idAllied]
    ]);

    $detailConfig = DetailConfig::find([
                'idAlliedconfig = ?0',
                'bind' => [$config->idAlliedconfig]
    ]);
    
//    var_dump($detailConfig);
//    exit;

    for ($i = 0; $i < count($detailConfig); $i++) {
      if ((int) substr($detailConfig[$i]->pricelist->price, -2) == 0) {
        $priceSetted[$i] = (int) $detailConfig[$i]->pricelist->price;
      } else {
        $priceSetted[$i] = $detailConfig[$i]->pricelist->price;
      }
    }

    if (!$allied) {
      $this->notification->error('La cuenta maestra no existe');
      return $this->response->redirect('masteraccount');
    }
    $this->view->setVar("config", $config);
    $this->view->setVar("space", round($this->getSpaceUsedInAllied($idAllied), 2));
    $this->view->setVar("detailConfig", $detailConfig);
    $this->view->setVar("priceSetted", $priceSetted);
    $this->view->setVar("allied", $allied);
  }

  public function configeditAction($idAllied) {

    if (!$idAllied) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('masteraccount');
    }
    $allied = Allied::findfirst([
                'idAllied = ?0',
                'bind' => [$idAllied]
    ]);
    if (!$allied) {
      $this->notification->error('El aliado no existe');
      return $this->response->redirect('masteraccount');
    }
    $alliedConfig = Alliedconfig::findFirst(array(
                'conditions' => 'idAllied = ?0',
                'bind' => array(0 => $idAllied)
    ));
    $mta = $this->modelsManager->createBuilder()
            ->from('Mta')
            ->join('Maxmta', 'Maxmta.idMta = Mta.idMta')
            ->where('Maxmta.idMasteraccount = ' . $allied->idMasteraccount)
            ->orderBy('Mta.name')
            ->getQuery()
            ->execute();
    $this->view->setVar('mta', $mta);

    $adapter = $this->modelsManager->createBuilder()
            ->from('Adapter')
            ->join('Maxadapter', 'Maxadapter.idAdapter = Adapter.idAdapter')
            ->where('Maxadapter.idMasteraccount = ' . $allied->idMasteraccount)
            ->getQuery()
            ->execute();
    $this->view->setVar('adapter', $adapter);

    $mailclass = $this->modelsManager->createBuilder()
            ->from('Mailclass')
            ->join('Maxmailclass', 'Maxmailclass.idMailclass = Mailclass.idMailclass')
            ->where('Maxmailclass.idMasteraccount = ' . $allied->idMasteraccount)
            ->getQuery()
            ->execute();
    $this->view->setVar('mailclass', $mailclass);

    $urldomain = $this->modelsManager->createBuilder()
            ->from('Urldomain')
            ->join('Maxurldomain', 'Maxurldomain.idUrldomain = Urldomain.idUrldomain')
            ->where('Maxurldomain.idMasteraccount = ' . $allied->idMasteraccount)
            ->getQuery()
            ->execute();
    $this->view->setVar('urldomain', $urldomain);
    $alliedForm = new AlliedconfigForm($alliedConfig);
    $this->view->setVar("ConfigForm", $alliedForm);
    $this->view->setVar("masteraccount", $allied->masteraccount);
    $this->view->setVar("allied", $allied);
    $this->view->setVar("config", $alliedConfig);

    if ($this->request->isPost()) {
      try {
        $configEdit = Alliedconfig::findFirst(array(
                    'conditions' => 'idAllied = ?1',
                    'bind' => array(1 => $idAllied)
        ));
        $alliedForm->bind($this->request->getPost(), $alliedConfig);
        $accoutingManager = new \Sigmamovil\General\Misc\AccountingManager();
        $this->db->begin();
        $accoutingManager->alliedConfigEdit($alliedConfig, $configEdit);
        $this->db->commit();
        $this->notification->info('Se ha editado exitosamente la configuración de la cuenta aliada <strong>' . $allied->name . '</strong>.');
        $this->trace("success", "Se edito la configuración de una cuenta aliada");
        return $this->response->redirect("allied/show/{$allied->idAllied}");
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
      } catch (Exception $e) {
        $this->notification->error($e->getMessage());
      }
    }
  }

  public function createconfigAction($idAllied) {
    $allied = Allied::findFirst(array(
                'conditions' => 'idAllied = ?1',
                'bind' => array(1 => $idAllied)
    ));
    $masteraccount = Masteraccount::findFirst([
                'idMasteraccount = ?0',
                'bind' => [$allied->idMasteraccount]
    ]);

    $mta = $this->modelsManager->createBuilder()
            ->from('Mta')
            ->join('Maxmta', 'Maxmta.idMta = Mta.idMta')
            ->where('Maxmta.idMasteraccount = ' . $allied->idMasteraccount)
            ->orderBy('Mta.name')
            ->getQuery()
            ->execute();
    $this->view->setVar('mta', $mta);

    $adapter = $this->modelsManager->createBuilder()
            ->from('Adapter')
            ->join('Maxadapter', 'Maxadapter.idAdapter = Adapter.idAdapter')
            ->where('Maxadapter.idMasteraccount = ' . $allied->idMasteraccount)
            ->getQuery()
            ->execute();
    $this->view->setVar('adapter', $adapter);

    $mailclass = $this->modelsManager->createBuilder()
            ->from('Mailclass')
            ->join('Maxmailclass', 'Maxmailclass.idMailclass = Mailclass.idMailclass')
            ->where('Maxmailclass.idMasteraccount = ' . $allied->idMasteraccount)
            ->getQuery()
            ->execute();
    $this->view->setVar('mailclass', $mailclass);

    $urldomain = $this->modelsManager->createBuilder()
            ->from('Urldomain')
            ->join('Maxurldomain', 'Maxurldomain.idUrldomain = Urldomain.idUrldomain')
            ->where('Maxurldomain.idMasteraccount = ' . $allied->idMasteraccount)
            ->getQuery()
            ->execute();
    $this->view->setVar('urldomain', $urldomain);

    $services = $this->modelsManager->createBuilder()
            ->from('Services')
            ->join('Mxs', 'Mxs.idServices = Services.idServices')
            ->where('Mxs.idMasteraccount = ' . $allied->idMasteraccount)
            ->getQuery()
            ->execute();
    $this->view->setVar('services', $services);

    $configform = new AlliedconfigForm();
    $this->view->setVar('alliedconfigForm', $configform);
    $this->view->setVar('masteraccount', $masteraccount);
    $this->view->setVar("allied", $allied);

    if ($this->request->isPost()) {
      try {
        $config = new Alliedconfig();

        $configMaster = Config::findFirst(array(
                    'conditions' => 'idMasteraccount = ?1',
                    'bind' => array(1 => $masteraccount->idMasteraccount)
        ));

//        var_dump($this->request->getPost());
        ////        var_dump($masteraccount->config);
//        var_dump($config);
//        exit();
        foreach ($allied->alxs as $key) {

          if ($key->idServices == $this->services->sms) {
            $config->smsSpeed = $configMaster->smsVelocity;
            $config->idAllied = $allied->idAllied;
            if ($this->request->getPost("smsLimit") < 0) {
              throw new InvalidArgumentException("El campo limite de SMS no puede tener un valor negativo");
            }
            if ($masteraccount->Config->smsLimit < $this->request->getPost("smsLimit")) {
              throw new InvalidArgumentException("No puede asignarle mas de {$masteraccount->Config->smsLimit} al limite de sms");
            }
            $config->smsLimit = $this->request->getPost("smsLimit");
            $configMaster->smsLimit = $configMaster->smsLimit - $config->smsLimit;
          }
          
          if ($key->idServices == $this->services->sms_two_way) {
//            $config->smsSpeed = $configMaster->smsVelocity;
            $config->idAllied = $allied->idAllied;
            if ($this->request->getPost("smstwowayLimit") < 0) {
              throw new InvalidArgumentException("El campo limite de SMS doble-via no puede tener un valor negativo");
            }
            if ($masteraccount->Config->smstwowayLimit < $this->request->getPost("smstwowayLimit")) {
              throw new InvalidArgumentException("No puede asignarle mas de {$masteraccount->Config->smstwowayLimit} al limite de sms");
            }
            $config->smstwowayLimit = $this->request->getPost("smstwowayLimit");
            $configMaster->smstwowayLimit = $configMaster->smstwowayLimit - $config->smstwowayLimit;
          }
          if ($key->idServices == $this->services->landing_page) {
            //$config->smsSpeed = $configMaster->smsVelocity;
            $config->idAllied = $allied->idAllied;
            if ($this->request->getPost("landingpageLimit") < 0) {
              throw new InvalidArgumentException("El campo limite de landing no puede tener un valor negativo");
            }
            if ($masteraccount->Config->landingpageLimit < $this->request->getPost("landingpageLimit")) {
              throw new InvalidArgumentException("No puede asignarle mas de {$masteraccount->Config->landingpageLimit} al limite de landing");
            }
            $config->landingpageLimit = $this->request->getPost("landingpageLimit");
            $configMaster->landingpageLimit = $configMaster->landingpageLimit - $config->landingpageLimit;
          }

          if ($key->idServices == $this->services->email_marketing) {
            $config->idAdapter = $this->request->getPost("adapterSelected");
            $config->idMta = $this->request->getPost("mtaSelected");
            $config->idMailClass = $this->request->getPost("mailclassSelected");
            $config->idUrldomain = $this->request->getPost("urldomainSelected");
            $config->idAllied = $allied->idAllied;
            $configform->bind($this->request->getPost(), $config);
            if ($this->request->getPost("fileSpace") < 0) {
              throw new InvalidArgumentException("El campo almacenamiento no puede tener un valor negativo");
            }
            if ($this->request->getPost("mailLimit") < 0) {
              throw new InvalidArgumentException("El campo limite de correos no puede tener un valor negativo");
            }
            if ($this->request->getPost("contactLimit") < 0) {
              throw new InvalidArgumentException("El campo limite de contactos no puede tener un valor negativo");
            }
            if ($this->request->getPost("fileSpace") == "") {
              throw new InvalidArgumentException("El campo almacenamiento no puede estar vacio");
            }
            if ($this->request->getPost("mailLimit") == "") {
              throw new InvalidArgumentException("El campo limite de correos no puede estar vacio");
            }
            if ($this->request->getPost("contactLimit") == "") {
              throw new InvalidArgumentException("El campo limite de contactos no puede estar vacio");
            }
            if ($this->request->getPost("mtaSelected") == "") {
              throw new InvalidArgumentException("El campo mta  no puede estar vacio");
            }
            if (!is_numeric($this->request->getPost("mtaSelected"))) {
              throw new InvalidArgumentException("El campo mta  no puede estar vacio");
            }
            /* if ($this->request->getPost("adapterSelected") == "") {
              throw new InvalidArgumentException("El campo adapter  no puede estar vacio");
              }
              if (!is_numeric($this->request->getPost("adapterSelected"))) {
              throw new InvalidArgumentException("El campo adapter  no puede estar vacio");
              } */
            if ($this->request->getPost("mailclassSelected") == "") {
              throw new InvalidArgumentException("El campo mailclass  no puede estar vacio");
            }
            if (!is_numeric($this->request->getPost("mailclassSelected"))) {
              throw new InvalidArgumentException("El campo mailclass  no puede estar vacio");
            }
            if ($this->request->getPost("urldomainSelected") == "") {
              throw new InvalidArgumentException("El campo urldomain  no puede estar vacio");
            }
            if (!is_numeric($this->request->getPost("urldomainSelected"))) {
              throw new InvalidArgumentException("El campo urldomain  no puede estar vacio");
            }

            if ($masteraccount->Config->fileSpace < $this->request->getPost("fileSpace")) {
              throw new InvalidArgumentException("No puede asignarle mas de {$masteraccount->Config->fileSpace} mb al almacenamiento");
            }
            if ($masteraccount->Config->mailLimit < $this->request->getPost("mailLimit")) {
              throw new InvalidArgumentException("No puede asignarle mas de {$masteraccount->Config->mailLimit} al limite de correos");
            }
            if ($masteraccount->Config->contactLimit < $this->request->getPost("contactLimit")) {
              throw new InvalidArgumentException("No puede asignarle mas de {$masteraccount->Config->contactLimit} al limite de contactos");
            }
            $configMaster->contactLimit = $configMaster->contactLimit - $config->contactLimit;
            $configMaster->fileSpace = $configMaster->fileSpace - $config->fileSpace;
            $configMaster->mailLimit = $configMaster->mailLimit - $config->mailLimit;
          }

//          var_dump($configMaster);
        }
//        var_dump($config);
//        exit();
        $this->db->begin();
        if (!$config->save()) {
          $this->db->rollback();
          $this->trace("Error", "No se creo la configuración a el Alias con ID: {$allied->idAllied}");
          $this->logger->log("No guardo la configuración del alias {$allied->idAllied} - {$allied->name}");
          foreach ($config->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        if (!$configMaster->save()) {
          $this->db->rollback();
          $this->trace("Error", "No se edito la configuración del masteraccount con ID {$masteraccount->idMasteraccount}para el Alias con ID: {$allied->idAllied}");
          $this->logger->log("No se edito la configuración del masteraccount allied  {$allied->idAllied} - {$allied->name} masteraccount  {$masteraccount->idMasteraccount} - {$masteraccount->name}");
          foreach ($configMaster->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->db->commit();
        $this->notification->success("Se ha creado la configuracion para el aliado {$allied->name}");
        $this->response->redirect("allied/show/" . $allied->idAllied);
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
        $this->logger->log("Error while create alliedconfig: {$msg}");
        $this->notification->error("Ha ocurrido un error inesperado contacte con el administrador");
        $this->trace("Error", "No se creo la configuracion {$msg->getTraceAsString()}");
      } catch (Exception $e) {
        $this->notification->error($e->getMessage());
        $this->logger->log("Error while create alliedconfig: {$e}");
        $this->notification->error("Ha ocurrido un error inesperado contacte con el administrador");
        $this->trace("Error", "No se creo la configuracion {$e->getTraceAsString()}");
      }
    }
  }

}
