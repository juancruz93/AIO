<?php

class FlashmessageController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Mensajes Flash");
    parent::initialize();
  }

  public function indexAction() {

    if (\Phalcon\DI::getDefault()->get('user')->UserType->idMasteraccount) {
      $where = "idMasteraccount = " . \Phalcon\DI::getDefault()->get('user')->UserType->idMasteraccount;
    } else if (\Phalcon\DI::getDefault()->get('user')->UserType->idAllied) {
      $where = "idAllied = " . \Phalcon\DI::getDefault()->get('user')->UserType->idAllied;
    } else {
      $where = "idAccount is null and idAllied is null and idMasteraccount is null";
    }

    $currentPage = $this->request->getQuery('page', null, 1);
    $builder = $this->modelsManager->createBuilder()
            ->from('Flashmessage')
            ->where($where)
            ->orderBy('created');

    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
        "builder" => $builder,
        "limit" => 15,
        "page" => $currentPage
    ));

    $page = $paginator->getPaginate();
//    $arraynamestarget = $this->getNamesTarget($page->items);
//    var_dump($count);
//    exit;
    $this->view->setVar("page", $page);
  }

  public function createAction() {
    date_default_timezone_set('America/Bogota');

    $fm = new Flashmessage();
    $form = new FlashmessageForm($fm);

    if ($this->request->isPost()) {

      $form->bind($this->request->getPost(), $fm);

//          var_dump($this->request->getPost("target"));
//      exit;
      $fm->target = json_encode($this->request->getPost("target"));

      if ($this->user->UserType->idMasteraccount) {
        $fm->idMasteraccount = $this->user->UserType->idMasteraccount;
      } else if ($this->user->UserType->idAllied) {
        $fm->idAllied = $this->user->UserType->idAllied;
      }


      $start = $form->getValue('start');
      $end = $form->getValue('end');
      $allAccounts = $form->getValue('allAccounts');
      $anyAccounts = $form->getValue('accounts');
      $allAllied = $form->getValue('allAllied');
      $anyAllied = $form->getValue('allied');
      $target = $form->getValue('target');

//      var_dump($start);
//      var_dump($end);
//      exit;

//      $this->logger->log("Begin: {$start}");
//      $this->logger->log("End: {$end}");
      try {

        list($month1, $day1, $year1, $hour1, $minute1) = preg_split('/[\s\/|-|:]+/', $start);
        $dateBegin = mktime($hour1, $minute1, 0, $month1, $day1, $year1);

        list($month2, $day2, $year2, $hour2, $minute2) = preg_split('/[\s\/|-|:]+/', $end);
        $dateEnd = mktime($hour2, $minute2, 0, $month2, $day2, $year2);

//        var_dump($fm->target=="null");
//        exit;
        if (time() > $dateEnd || $dateEnd < $dateBegin) {
          throw new InvalidArgumentException("Ha selecionado una fecha que ya ha pasado, por favor verifique la información");
        }
//        if (trim($allAccounts) === '' && empty($anyAccounts)) {
//          if (trim($allAllied) === '' && empty($anyAllied)) {
//            throw new InvalidArgumentException("No ha seleccionado una cuenta, por favor verifique la información");
//          }
//        }

        if ($fm->target == "null") {
          throw new InvalidArgumentException("No ha seleccionado una cuenta, por favor verifique la información");
        }

        if (!empty($allAccounts)) {
          $fm->accounts = 'all';
        } else {
          $fm->accounts = json_encode($anyAccounts);
        }
        if (!empty($allAllied)) {
          $fm->allied = 'all';
        } else {
          $fm->allied = json_encode($anyAllied);
        }

        $fm->start = $dateBegin;
        $fm->end = $dateEnd;
        if ($fm->save()) {
          $this->trace("success", "mensaje creado, idMessage: {$fm->idFlashmessage}");
          $this->notification->success('El mensaje fue creado exitosamente');
          return $this->response->redirect('flashmessage/index');
        } else {
          foreach ($fm->getMessages() as $m) {
            $this->trace("fail", "Ocurrio un error guardando el mensaje: " . $m);
            $this->notification->error($m);
          }
        }
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
      } catch (Exception $exc) {
        echo $exc->getTraceAsString();
      }
    }

    $this->view->MessageForm = $form;
  }

  public function editAction($idFlashmessage) {
    try {
//      throw new InvalidArgumentException("LALA");
      $flashMessage = Flashmessage::findFirst(array(
                  'conditions' => 'idFlashmessage = ?1',
                  'bind' => array(1 => $idFlashmessage)
      ));

      if (!$flashMessage) {
        $this->notification->error("No se encontró el mensaje con id: {$idFlashmessage}");
        return $this->response->redirect("flashmessage");
      }

      $flashMessage->target = json_decode($flashMessage->target);

      if ($this->user->UserType->idMasteraccount)
        {
        $target = \Allied::find(array("conditions" => "idMasteraccount= ?0", "bind" => array($this->user->UserType->idMasteraccount)));
        foreach ($target as $tar) {
          $alltarget [$tar->idAllied] = $tar->name;
          if (in_array($tar->idAllied, $flashMessage->target)) {
            $selectedtarget[] = $tar->idAllied;
          }
        }
      } else if ($this->user->UserType->idAllied) {
        $target = \Account::find(array("conditions" => "idAllied= ?0", "bind" => array($this->user->UserType->idAllied)));
        foreach ($target as $tar) {
          $alltarget [$tar->idAccount] = $tar->name;

          if (in_array($tar->idAccount, $flashMessage->target)) {
            $selectedtarget[] = $tar->idAccount;
          }
        }
      } else {
        $target = \Masteraccount::find();
        foreach ($target as $tar) {
          $alltarget [$tar->idMasteraccount] = $tar->name;

          if (in_array($tar->idMasteraccount, $flashMessage->target)) {
            $selectedtarget[] = $tar->idMasteraccount;
          }
        }
      }

//      var_dump($flashMessage);
      $form = new FlashmessageForm($flashMessage);
      
//      $this->view->setVar('accounts', $accounts);
//      $this->view->setVar('allied', $allied);
      $this->view->setVar('flashM', $flashMessage);
      $this->view->setVar('form', $form);
      $this->view->setVar('selectedtarget', $selectedtarget);
      $this->view->setVar('target', $alltarget);


      if ($this->request->isPost()) {
        
        $form->bind($this->request->getPost(), $flashMessage);
//var_dump($form);
//var_dump($this->request->getPost());
//      exit;
        $name = $this->request->getPost('name');
        $messag = $this->request->getPost('message');
        $type = $this->request->getPost('type');
        $start = $this->request->getPost('start');
        $end = $this->request->getPost('end');
        $target2 = $this->request->getPost('target');
        $target = json_encode($target2);


        list($month1, $day1, $year1, $hour1, $minute1) = preg_split('/[\s\/|-|:]+/', $start);
        $dateBegin = mktime($hour1, $minute1, 0, $month1, $day1, $year1);
//          var_dump($dateBegin);
//          exit;
        list($month2, $day2, $year2, $hour2, $minute2) = preg_split('/[\s\/|-|:]+/', $end);
        $dateEnd = mktime($hour2, $minute2, 0, $month2, $day2, $year2);
        $flashMessage->target = $target;
        $flashMessage->end = $dateEnd;
        $flashMessage->start = $dateBegin;
        
        if (trim($name) === '' || trim($messag) === '' || empty($target2) || trim($type) === '' || trim($start) === '' || trim($end) === '') {
          throw new InvalidArgumentException("Ha enviado campos vacios, por favor verifique la información");
        }

        

        if ($dateEnd < $dateBegin || $dateEnd < time()) {
          throw new InvalidArgumentException("Ha selecionado una fecha que ya ha pasado, por favor verifique la información");
        }

        if (empty($target2)) {
          throw new InvalidArgumentException("No ha seleccionado una cuenta, por favor verifique la información");
        }

        
//          var_dump($message->getMessages());
//          exit;
        if (!$flashMessage->update()) {
          foreach ($flashMessage->getMessages() as $m) {
            $this->trace("fail", "Ocurrió un error editando el mensaje: {$m}");
            throw new InvalidArgumentException($m);
          }
        }
//         echo 1;
//            exit;
        $this->trace("success", "Se edito el mensaje correctamente, idMessage: {$idFlashmessage}");
        $this->notification->info('El mensaje fue editado exitosamente');
        return $this->response->redirect('flashmessage/index');
      }
    } 
    catch (InvalidArgumentException $ex) {
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
//          return $this->response->redirect("flashmessage/edit/".$idFlashmessage);
    } 
    catch (Exception $ex) {
      $this->logger->log("Exception while updating flash message: {$idFlashmessage} " . $ex->getMessage());
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error("Ha ocurrido un error, por favor contacta a soporte");
    }
  }

  function deleteAction($idFlashmessage) {
    $message = Flashmessage::findFirst(array(
                'conditions' => 'idFlashmessage = ?1',
                'bind' => array(1 => $idFlashmessage)
    ));

    if ($message) {
      if (!$message->delete()) {
        foreach ($message->getMessages() as $msg) {
          $this->trace("fail", "Ocurrio un error al borrar el mensaje: {$msg}");
          $this->notification->error($msg);
        }
        return $this->response->redirect('flashmessage');
      }
      $this->trace("success", "El mensaje se elimino correctamente: {$idFlashmessage}");
      $this->notification->warning('Se ha eliminado el mensaje exitosamente');
    } else {
      $this->notification->danger('El mensaje que desea eliminar no existe o ya ha sido elminado, por favor verifique la información');
    }

    return $this->response->redirect('flashmessage');
  }

  function getNamesTarget($count) {

    foreach ($count as $c) {
      $target[] = json_decode($c->target);
    }

//    var_dump($target);
//    exit;
    if ($this->user->UserType->idMasteraccount) {

      $results = \Allied::find(array("conditions" => "idMasteraccount= ?0", "bind" => array($this->user->UserType->idMasteraccount)));
      if ($results) {
        foreach ($results as $result) {
          $recipients[$result->idAllied] = $result->name;
        }
      } else {
        $recipients[0] = "No hay cuentas aliadas asociadas";
      }
    } else if ($this->user->UserType->idAllied) {
      $results = \Account::find(array("conditions" => "idAllied= ?0", "bind" => array($this->user->UserType->idAllied)));
      if ($results) {
        foreach ($results as $result) {
          $recipients[$result->idAccount] = $result->name;
        }
      } else {
        $recipients[0] = "No hay cuentas asociadas";
      }
    } else {

      $results = \Masteraccount::find();
      if ($results) {

        foreach ($results as $result) {
          $recipients[$result->idMasteraccount] = $result->name;
        }
      } else {
        $recipients[0] = "No hay cuentas master asociadas";
      }
    }
  }

}
