<?php

require_once __DIR__ . '/../../public/library/php-jwt-master/src/JWT.php';

class AdapterController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Adaptadores");
    parent::initialize();
  }

    public function indexAction() {
        $currentPage = $this->request->getQuery('page', null, 1); // GET
        $builder = $this->modelsManager->createBuilder()
                ->from('Adapter')
                ->where('Adapter.deleted = 0')
                ->orderBy('Adapter.created DESC');

        $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
            "builder" => $builder,
            //            "limit"=> PaginationDecorator::DEFAULT_LIMIT,
            "limit" => 15,
            "page" => $currentPage
        ));

        $page = $paginator->getPaginate();

        $this->view->setVar("page", $page);
    }

    public function createAction() {
        //Imprimimos el formulario de creacion de adaptadores
        $adapter = new Adapter();
        $adapterForm = new AdapterForm($adapter);
        //Se pasan los campos a una variable para que se impriman en el volt (vista)
        $this->view->setVar('adapter_form', $adapterForm);

        if ($this->request->isPost()) {
            $adapterForm->bind($this->request->getPost(), $adapter);
            $adapter->fname = strtoupper($adapter->fname);
            $adapter->passw = \Firebase\JWT\JWT::encode($adapter->passw, $this->keyjwt->key);
            $adapter->status = 1;
            $adapter->deleted = 0;
            $adapter->international = (empty($adapter->international) ? 0 : 1);
            if (!$adapter->save()) {
                //Se crean las sesiones con los mensajes de las notificaciones
                //Mensaje de error
                foreach ($adapter->getMessages() as $message) {
                    $this->notification->error($message);
                }

                $this->trace("fail", "No se logro crear un adaptador");
            } else {
                $this->notification->success('El adaptador fue creado exitosamente.');
                $this->trace("success", "Se creo un adaptador con ID: {$adapter->idAdapter}");
                return $this->response->redirect('adapter/index');
            }
        }
    }

    public function editAction($idAdapter) {
        $adapter = Adapter::findFirst(array(
                    'conditions' => "idAdapter = ?1",
                    'bind' => array(1 => $idAdapter)
        ));


        if (!$adapter) {
            $this->notification->error('No se encontró el adaptador, por favor valide la información');
            return $this->response->redirect('adapter/index');
        }

        $adapterForm = new AdapterForm($adapter);

        $this->view->setVar('adapter_value', $adapter);
        $this->view->setVar('adapter_form', $adapterForm);

        if ($this->request->isPost()) {
            $adapterForm->bind($this->request->getPost(), $adapter);

            $password = $this->request->getPost("password");
            $status = $this->request->getPost('status');
            $adapter->status = (empty($status) ? 0 : 1);
            $password = trim($password);
            $international = $this->request->getPost('international');
            $adapter->international = (empty($international) ? 0 : 1);

            if (!empty($password)) {
                if (strlen($password) < 4) {
                    $this->notification->error("Debes enviar una contraseña valida, esta debe contener al menos 4 caracteres");
                    return;
                } else {
                    $adapter->passw = \Firebase\JWT\JWT::encode($password, $this->keyjwt->key);
                }
            }

            $adapter->fname = strtoupper($adapter->fname);
            if (!$adapter->save()) {
                foreach ($adapter->getMessages() as $message) {
                    $this->notification->error($message);
                }

                $this->trace("fail", "No se logro actualizar el adaptador con ID: {$adapter->idAdapter}");
            } else {
                $this->notification->info('El adaptador fue actualizado exitosamente.');
                $this->trace("success", "Se actualizo el adaptador con ID: {$adapter->idAdapter}");
                return $this->response->redirect('adapter/index');
            }
        }
    }

    public function deleteAction($idAdapter) {
        $adapter = Adapter::findFirst(array(
                    'conditions' => "idAdapter = ?1",
                    'bind' => array(1 => $idAdapter)
        ));

        if (!$adapter->delete()) {
            $this->notification->danger('No se logro eliminar el adaptador. Intentelo de nuevo.');
            $this->trace("fail", "No logro eliminar el adaptador con ID: {$adapter->idAdapter}");
            return $this->response->redirect('adapter/index');
        }

        $this->notification->warning('El adaptador fue eliminado exitosamente.');
        $this->trace("success", "Se elimino el adaptador con ID: {$adapter->idAdapter}");
        return $this->response->redirect('adapter/index');
    }

    public function listfulladapterAction() {
        try {
            if (isset($this->user->Usertype->Masteraccount->idPaymentPlan)) {
                $idPaymentPlan = $this->user->Usertype->Masteraccount->idPaymentPlan;
                $adapter = $this->queryAdapter($idPaymentPlan);
            } elseif (isset($this->user->Usertype->Allied->idPaymentPlan)) {
                $idPaymentPlan = $this->user->Usertype->Allied->idPaymentPlan;
                $adapter = $this->queryAdapter($idPaymentPlan);
            } else {
                $adapter = Adapter::find(array(
                            "columns" => "idAdapter, fname, international",
                            "conditions" => "status = ?0 AND deleted = ?1",
                            "bind" => array(1, 0)
                ));
            }

            $data = [];
            if (count($adapter) > 0) {
                foreach ($adapter as $key => $value) {
                    $data[$key] = array(
                        "idAdapter" => $value->idAdapter,
                        "name" => $value->fname,
                        "international" => $value->international
                    );
                }
            }
            return $this->set_json_response($data);
        } catch (InvalidArgumentException $ex) {
            return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
            $this->logger->log("Exception while finding listfulladapter ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }

    public function queryAdapter($idPaymentPlan) {
        $ppxs = PaymentPlanxservice::findFirst(array(
                    "conditions" => "idPaymentPlan = ?0 AND idServices = ?1",
                    "bind" => array($idPaymentPlan, 1)
        ));
        $adapter = $this->modelsManager->createBuilder()
                ->columns("Adapter.idAdapter, Adapter.fname")
                ->from("Ppxsxadapter")
                ->join("Adapter")
                ->where("Ppxsxadapter.idPaymentPlanxService = :id:")
                ->getQuery()
                ->execute(["id" => $ppxs->idPaymentPlanxService]);

        return $adapter;
    }

}
