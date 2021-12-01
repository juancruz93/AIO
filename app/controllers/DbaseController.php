<?php

class DbaseController extends ControllerBase
{

  public function indexAction() {
    $currentPage = $this->request->getQuery('page', null, 1); // GET
    $builder = $this->modelsManager->createBuilder()
        ->from('Dbase')
//            ->where("idAccount = {$this->user->idAccount}")
        ->orderBy('Dbase.created');

    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
      "builder" => $builder,
      "limit" => 100,
      "page" => $currentPage
    ));

    $page = $paginator->getPaginate();

    $this->view->setVar("page", $page);
  }

  public function createAction() {
    $dbase = new Dbase();
    $dbaseForm = new DbaseForm($dbase);
    $this->view->setVar('dbase_form', $dbaseForm);
    try {
      if ($this->request->isPost()) {
        $dbaseName = $this->request->getPost('name');
        $rename = trim($dbaseName);
        $rname = strtolower($rename);

        $objects = array();
        $dbasess = Dbase::findByIdAccount($this->user->idAccount);
        foreach ($dbasess as $dbases) {
          $objects[] = $dbases->name;
        }
        if (in_array($rname, $objects)) {
          $this->trace("fail", "No se logro crear una base de datos en la cuenta {$this->user->idAccount}");
          throw new Exception('Ya existe una base de datos con este nombre, por favor valide la información');
        }

        if (empty($rname)) {
          $this->trace("fail", "No se logro crear una base de datos en la cuenta {$this->user->idAccount}");
          throw new Exception('La base de datos debe tener un nombre, por favor valide la información');
        }

        $dbaseForm->bind($this->request->getPost(), $dbase);

        if ($this->request->getPost('description') == "") {
          $dbase->description = "Sin descripción";
        }

        $dbase->idAccount = $this->user->idAccount;
        $dbase->ctotal = 0;
        $dbase->cactive = 0;
        $dbase->cunsuscribed = 0;
        $dbase->cbounced = 0;
        $dbase->cspam = 0;
        $dbase->created = time();
        $dbase->updated = time();
        $dbase->name = $rname;

        if (!$dbase->save()) {
          //Se crean las sesiones con los mensajes de las notificaciones
          //Mensaje de error
          foreach ($dbase->getMessages() as $message) {
            $this->notification->error($message);
          }

          $this->trace("fail", "No se logro crear una base de datos");
        } else {
          $this->notification->success('La base de datos fue creada exitosamente.');
          $this->trace("success", "Se creo una base de datos en la cuenta {$this->user->idAccount} con ID: {$dbase->idDbase}");
          return $this->response->redirect('dbase/index');
        }
      }
    } catch (Exception $e) {
      $this->notification->error($e->getMessage());
    }
  }

  public function editAction($idDbase) {

    $dbaseName = $this->request->getPost('name');
    $rename = strtolower($dbaseName);

//    $dbase = Dbase::findFirst(array(
//                'conditions' => "idDbase = ?1",
//                'bind' => array(1 => $idDbase)
//    ));

    $dbase = Dbase::findFirst(array(
          'conditions' => "idDbase = ?1",
          'bind' => array(1 => $idDbase)
    ));

    // Valida que el idAccount del usuario que esta en sesión coincida con el de la base de datos
    // a la que se intenta acceder, en caso que no redireccione al index de las bases de datos
    // y muestra el mensaje de error que la base de datos no existe.
//    if ($this->user->idAccount != $dbase->idAccount) {
//      $this->notification->error('No se encontró la base de datos, por favor valide la información');
//      return $this->response->redirect('dbase/index');
//    }

    $this->view->setVar('dbase_value', $dbase);

    if ($this->request->isPost()) {

      $rname = trim($rename);
      if (empty($rname)) {
        $this->notification->error('La base de datos debe tener un nombre, por favor valide la información');
        $this->trace("fail", "No se logro crear una base de datos en la cuenta {$this->user->idAccount}");
        return $this->response->redirect('dbase/create');
      }

      if ($dbase->name != $rname) {
        $objects = array();
        $dbasess = Dbase::findByIdAccount($dbase->idAccount);
        foreach ($dbasess as $dbases) {
          $objects[] = $dbases->name;
        }
      }

      if (in_array($rname, $objects)) {
        $this->notification->error('Ya existe una base de datos con este nombre, por favor valide la información');
        $this->trace("fail", "No se logro actualizar la base de datos con ID: {$dbase->idDbase}");
        return $this->response->redirect('dbase/edit/' . $dbase->idDbase);
      }
      $dbase->name = $rname;
      $dbase->description = $this->request->getPost('description');
      $dbase->color = $this->request->getPost('color');
      $dbase->updated = time();

      if (!$dbase->save()) {
        foreach ($dbase->getMessages() as $message) {
          $this->notification->error($message);
        }

        $this->trace("fail", "No se logro actualizar la base de datos con ID: {$dbase->idDbase}");
      } else {
        $this->notification->info('La base de datos fue actualizada exitosamente.');
        $this->trace("success", "Se actualizo la base de datos con ID: {$dbase->idDbase}");
        return $this->response->redirect('dbase/index');
      }
    }

    if (!$dbase) {
      $this->notification->error('No se encontró la base de datos, por favor valide la información');
      return $this->response->redirect('dbase/index');
    }
  }

  public function deleteAction($idDbase) {
    $dbase = Dbase::findFirst(array(
          'conditions' => "idDbase = ?1",
          'bind' => array(1 => $idDbase)
    ));
    try {
      if (!$dbase->delete()) {
        $this->trace("fail", "No logro eliminar la base de datos con ID: {$dbase->idDbase}");
        throw new Exception('No se logro eliminar la base de datos. Intentelo de nuevo.');
      }

      $this->notification->warning('La base de datos fue eliminada exitosamente.');
      $this->trace("success", "Se elimino la base de datos con ID: {$dbase->idDbase}");
      return $this->response->redirect('dbase/index');
    } catch (Exception $e) {
      $this->notification->error($e->getMessage());
    }
  }

}
