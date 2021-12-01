<?php

class MailclassController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("MailClass");
    parent::initialize();
  }

  public function indexAction() {
    $currentPage = $this->request->getQuery('page', null, 1); // GET
    $builder = $this->modelsManager->createBuilder()
            ->from('Mailclass')
            ->orderBy('Mailclass.created DESC');

    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
        "builder" => $builder,
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "page" => $currentPage
    ));

    $page = $paginator->getPaginate();

    $this->view->setVar("page", $page);
  }

  public function createAction() {
    //Imprimimos el formulario de creacion de adaptadores
    $mailclass = new Mailclass();
    $mailclassForm = new MailclassForm($mailclass);
    //Se pasan los campos a una variable para que se impriman en el volt (vista)
    $this->view->setVar('mailclass_form', $mailclassForm);

    if ($this->request->isPost()) {
      $mailclassForm->bind($this->request->getPost(), $mailclass);
      $mailclass->status = 1;
      $mailclass->created = time();
      $mailclass->updated = time();

      if (!$mailclassForm->isValid()) {
        foreach ($mailclassForm->getMessages() as $msg) {
          $this->notification->error($msg);
        }
        return $this->response->redirect('mailclass/create');
        ;
      }
      $mailclass->name = strtoupper($mailclass->name);
      if (!$mailclass->save()) {
        //Se crean las sesiones con los mensajes de las notificaciones
        //Mensaje de error
        foreach ($mailclass->getMessages() as $message) {
          $this->notification->error($message);
        }

        $this->trace("fail", "No se logro crear una Mail Class");
      } else {
        $this->notification->success('La Mail Class fue creada exitosamente.');
        $this->trace("success", "Se creo una Mail Class con ID: {$mailclass->idMailClass}");
        return $this->response->redirect('mailclass/index');
      }
    }
  }

  public function editAction($idMailClass) {
    $mailclass = Mailclass::findFirst(array(
                'conditions' => "idMailClass = ?1",
                'bind' => array(1 => $idMailClass)
    ));

    $mailclassForm = new MailclassForm($mailclass);

    $this->view->setVar('mailclass_value', $mailclass);
    $this->view->setVar('formMailclass', $mailclassForm);

    if ($this->request->isPost()) {
      $status = $this->request->getPost('status');
      if (!isset($status)){
        $mailclass->status = 0;
      }
      $mailclassForm->bind($this->request->getPost(), $mailclass);

      $mailclass->updated = time();

      if (!$mailclassForm->isValid()) {
        foreach ($mailclassForm->getMessages() as $msg) {
          $this->notification->error($msg);
        }
        return;
      }
      $mailclass->name = strtoupper($mailclass->name);
      if (!$mailclass->save()) {
        foreach ($mailclass->getMessages() as $message) {
          $this->notification->error($message);
        }

        $this->trace("fail", "No se logro actualizar la Mail Class con ID: {$mailclass->idMailClass}");
      } else {
        $this->trace("success", "Se actualizo la Mail Class con ID: {$mailclass->idMailClass}");
        $this->notification->info('La Mail Class fue actualizada exitosamente.');
        return $this->response->redirect('mailclass/index');
      }

      if (!$mailclass) {
        $this->notification->error('No se encontro la Mail Class, por favor valide la informacion');
        $this->trace("fail", "No se encontro la Mail Class");
        return $this->response->redirect('mailclass/index');
      }
    }
  }

  public function deleteAction($idMailClass) {
    try {
      $mailclass = Mailclass::findFirst(array(
                  'conditions' => "idMailClass = ?1",
                  'bind' => array(1 => $idMailClass)
      ));

      if (!$mailclass->delete()) {
        throw new Exception('No se logro eliminar la Mail Class. Intentelo de nuevo.');
        $this->trace("fail", "No se logro eliminar la Mail Class con ID: {$mailclass->idMailClass}");
      }

      throw new Exception('La Mail Class fue eliminada exitosamente.');
      $this->trace("success", "Se elimino la Mail Class con ID: {$mailclass->idMailClass}");
    } catch (Exception $e) {
      $this->notification->warning($e->getMessage());
      return $this->response->redirect('mailclass/index');
    }
  }

  public function listfullmailclassAction() {
    try {
      if (isset($this->user->Usertype->Masteraccount->idPaymentPlan)) {
        $idPaymentPlan = $this->user->Usertype->Masteraccount->idPaymentPlan;
        $mailclass = $this->queryMailclass($idPaymentPlan);
      } elseif (isset($this->user->Usertype->Allied->idPaymentPlan)) {
        $idPaymentPlan = $this->user->Usertype->Allied->idPaymentPlan;
        $mailclass = $this->queryMailclass($idPaymentPlan);
      } else {
        $mailclass = Mailclass::find(array(
                    "conditions" => "status = ?0 AND deleted = ?1",
                    "bind" => array(1, 0)
        ));
      }

      $data = [];
      if (count($mailclass) > 0) {
        foreach ($mailclass as $key => $value) {
          $data[$key] = array(
              "idMailClass" => $value->idMailClass,
              "name" => $value->name
          );
        }
      }
      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get listfullmailclass ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function queryMailclass($idPaymentPlan) {
    $ppxs = PaymentPlanxservice::findFirst(array(
                "conditions" => "idPaymentPlan = ?0 AND idServices = ?1",
                "bind" => array($idPaymentPlan, 2)
    ));

    $mailclass = $this->modelsManager->createBuilder()
            ->columns("Mailclass.idMailClass, Mailclass.name")
            ->from("PpxsxmailClass")
            ->join("Mailclass")
            ->where("PpxsxmailClass.idPaymentPlanxService = :id:")
            ->getQuery()
            ->execute(["id" => $ppxs->idPaymentPlanxService]);

    return $mailclass;
  }

}
