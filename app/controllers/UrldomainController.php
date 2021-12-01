<?php

class UrldomainController extends ControllerBase {
  
  public function initialize() {
    $this->tag->setTitle("Urldomain");
    parent::initialize();
  }

    public function indexAction() {
        $currentPage = $this->request->getQuery('page', null, 1);
        $builder = $this->modelsManager->createBuilder()
                ->from('Urldomain')
                ->where('Urldomain.deleted = 0')
                ->orderBy('Urldomain.created DESC');

        $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
            "builder" => $builder,
            "limit" => 15,
            "page" => $currentPage
        ));

        $page = $paginator->getPaginate();

        $this->view->setVar("page", $page);
    }

    public function createAction() {
        $url = new Urldomain();
        $url_form = new UrldomainForm($url);

        if ($this->request->isPost()) {
            $url_form->bind($this->request->getPost(), $url);

            if (!$url->save()) {
                foreach ($url->getMessages() as $message) {
                    $this->notification->error($message);
                }

                $this->trace("fail", "No se logro crear una URL Domain");
            } else {
                $this->notification->success("Se ha creado el URL correctamente!");
                $this->trace("success", "Se creo la URL Domain con ID: {$url->idUrldomain}");
                return $this->response->redirect('urldomain');
            }
        }
        
        $this->view->setVar('url_form', $url_form);
    }

    public function editAction($idUrldomain) {
        $url = Urldomain::findFirstByIdUrldomain($idUrldomain);
        $form = new UrldomainForm($url);
        $this->view->setVar('form', $form);
        $this->view->setVar('urldomain', $url);

        if ($this->request->isPost()) {

            $form->bind($this->request->getPost(), $url);
            
            $status = $this->request->getPost('status');
            $url->status = (empty($status) ? 0 : 1);

            if (!$url->save()) {
                foreach ($url->getMessages() as $message) {
                    $this->notification->error($message);
                }

                $this->trace("fail", "No se logro actualizar la URL Domain con ID: {$url->idUrldomain}");
            } else {
                $this->notification->info("La URL se actualizo correctamente");
                $this->trace("success", "Se logro actualizar la URL Domain con ID: {$url->idUrldomain}");
                return $this->response->redirect('urldomain');
            }
        }
    }

    public function deleteAction($idUrldomain) {
//    $this->logger->log("Id {$idUrldomain}");
        $url = Urldomain::findFirst(array(
                    "conditions" => "idUrldomain = ?1",
                    "bind" => array(1 => $idUrldomain)
        ));

        if ($url->delete()) {
            $this->notification->warning("Se ha eliminado la URL correctamente!");
            $this->trace("success", "Se elimino la URL Domain con ID: {$url->idUrldomain}");
            return $this->response->redirect('urldomain/index');
        } else {
            $this->notification->error("Lo sentimos, ocurrio un error durante la elmiminacion de la URL");
            $this->trace("fail", "No se logro eliminar la URL Domain con ID: {$url->idUrldomain}");
            return $this->response->redirect('urldomain/index');
        }
    }

    public function listfullurldomainAction() {
        try {
            if (isset($this->user->Usertype->Masteraccount->idPaymentPlan)) {
                $idPaymentPlan = $this->user->Usertype->Masteraccount->idPaymentPlan;
                $urldomain = $this->queryUrldomain($idPaymentPlan);
            } elseif (isset($this->user->Usertype->Allied->idPaymentPlan)) {
                $idPaymentPlan = $this->user->Usertype->Allied->idPaymentPlan;
                $urldomain = $this->queryUrldomain($idPaymentPlan);
            } else {
                $urldomain = Urldomain::find(array(
                            "conditions" => "status = ?0 AND deleted = ?1",
                            "bind" => array(1, 0)
                ));
            }

            $data = [];
            if (count($urldomain) > 0) {
                foreach ($urldomain as $key => $value) {
                    $data[$key] = array(
                        "idUrldomain" => $value->idUrldomain,
                        "name" => $value->name
                    );
                }
            }
            return $this->set_json_response($data);
        } catch (InvalidArgumentException $ex) {
            return $this->set_json_response(array('message' => $ex->getMessage()), 400);
        } catch (Exception $ex) {
            $this->logger->log("Exception while get listfullurldomain ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
            return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
        }
    }

    public function queryUrldomain($idPaymentPlan) {
        $ppxs = PaymentPlanxservice::findFirst(array(
                    "conditions" => "idPaymentPlan = ?0 AND idServices = ?1",
                    "bind" => array($idPaymentPlan, 2)
        ));

        $urldomain = $this->modelsManager->createBuilder()
                ->columns("Urldomain.idUrldomain, Urldomain.name")
                ->from("Ppxsxurldomain")
                ->join("Urldomain")
                ->where("Ppxsxurldomain.idPaymentPlanxService = :id:")
                ->getQuery()
                ->execute(["id" => $ppxs->idPaymentPlanxService]);

        return $urldomain;
    }

}
