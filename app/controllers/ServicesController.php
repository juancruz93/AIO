<?php

class ServicesController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Servicios");
    parent::initialize();
  }

  public function indexAction() {
    $currentPage = $this->request->getQuery('page', null, 1); // GET
    $builder = $this->modelsManager->createBuilder()
            ->from('Services')
            ->where('Services.deleted = 0')
            ->orderBy('Services.created DESC');

    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
        "builder" => $builder,
//            "limit"=> PaginationDecorator::DEFAULT_LIMIT,
        "limit" => 15,
        "page" => $currentPage
    ));

    $page = $paginator->getPaginate();

    $this->view->setVar("page", $page);
  }

  public function listapiAction() {
    try {
      if (isset($this->user->Usertype->Masteraccount->idPaymentPlan)) {
        $idPaymentPlan = $this->user->Usertype->Masteraccount->idPaymentPlan;
        $services = $this->queryservices($idPaymentPlan);
      } else if (isset($this->user->Usertype->Allied->idPaymentPlan)) {
        $idPaymentPlan = $this->user->Usertype->Allied->idPaymentPlan;
        $services = $this->queryservices($idPaymentPlan);
      } else {
        $services = Services::find();
      }
      $data = [];
      if (count($services) > 0) {
        foreach ($services as $key => $value) {
          $data[$key] = array(
              "idServices" => $value->idServices,
              "name" => $value->name
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

  public function queryservices($idPaymentPlan) {
    $services = $this->modelsManager->createBuilder()
            ->columns("Services.idServices, Services.name")
            ->from("PaymentPlanxservice")
            ->join("Services")
            ->where("PaymentPlanxservice.idPaymentPlan = :id:")
            ->andWhere("Services.deleted = 0")
            ->andWhere("Services.status = 1")
            ->getQuery()
            ->execute(["id" => $idPaymentPlan]);

    return $services;
  }

  public function createAction() {
    //Imprimimos el formulario de creacion de adaptadores
    $services = new Services();
    $servicesForm = new ServicesForm($services);
    //Se pasan los campos a una variable para que se impriman en el volt (vista)
    $this->view->setVar('services_form', $servicesForm);

    if ($this->request->isPost()) {

      $servicesForm->bind($this->request->getPost(), $services);

      if (!$services->save()) {
        //Se crean las sesiones con los mensajes de las notificaciones
        //Mensaje de error
        foreach ($services->getMessages() as $message) {
          $this->notification->error($message);
        }
        $this->trace("fail", "No se logro crear un nuevo servicio");
      } else {
        $this->notification->success('El servicio fue creado exitosamente.');
        $this->trace("success", "Se creo un servicio con ID: {$services->idServices}");
        return $this->response->redirect('services/index');
      }
    }
  }

  public function editAction($idServices) {
    $services = Services::findFirst(array(
                'conditions' => "idServices = ?1",
                'bind' => array(1 => $idServices)
    ));

    $this->view->setVar('services_value', $services);

    $servicesForm = new ServicesForm($services);
    if ($this->request->isPost()) {
      $servicesForm->bind($this->request->getPost(), $services);
      $status = $this->request->getPost('status');
      $services->status = (empty($status) ? 0 : 1);

      if (!$services->save()) {
        //Se crean las sesiones con los mensajes de las notificaciones
        //Mensaje de error
        foreach ($services->getMessages() as $message) {
          $this->notification->error($message);
        }

        $this->trace("fail", "No se logro actualizar el servicio con ID: {$services->idServices}");
      } else {
        $this->notification->info('El servicio fue actualizado exitosamente.');
        $this->trace("success", "Se actualizo el servicio con ID: {$mta->idServices}");
        return $this->response->redirect('services/index');
      }
    }
    if (!$services) {
      $this->notification->error('No se encontro el servicio, por favor valide la informacion');
      return $this->response->redirect('services/index');
    }
  }

  public function deleteAction($idServices) {
    $services = Services::findFirst(array(
                'conditions' => "idServices = ?1",
                'bind' => array(1 => $idServices)
    ));

    if (!$services->delete()) {
      $this->notification->danger('No se logro eliminar el servicio. Intentelo de nuevo.');
      $this->trace("fail", "No se logro eliminar el servicio con ID: {$services->idServices}");
      return $this->response->redirect('services/index');
    }

    $this->notification->warning('El servicio fue eliminado exitosamente.');
    $this->trace("success", "Se elimino el servicio con ID: {$services->idServices}");
    return $this->response->redirect('services/index');
  }
  
  public function getallserviceAction(){
    try {
      $services = Services::find(array("conditions" => "name != ?0 AND name != ?1", "bind" => array('Mail Tester','Automatic Campaing')));
      $data = [];
      if (count($services) > 0) {
        foreach ($services as $key => $value) {
          $data[$key] = array(
              "idServices" => $value->idServices,
              "name" => $value->name
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
