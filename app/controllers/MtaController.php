<?php

class MtaController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("MTA");
    $this->user = \Phalcon\DI::getDefault()->get('user');
    parent::initialize();
  }

  public function indexAction() {
    $currentPage = $this->request->getQuery('page', null, 1);
    $builder = $this->modelsManager->createBuilder()
            ->from('Mta')
            ->where('Mta.deleted = 0')
            ->orderBy('Mta.created DESC');

   $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
        "builder" => $builder,
        "limit" => 15,
        "page" => $currentPage
    ));

    $page = $paginator->getPaginate();

    $this->view->setVar("page", $page);
  }

  public function createAction() {
    $mta = new Mta();
    $form = new MtaForm();

    if ($this->request->isPost()) {
      $form->bind($this->request->getPost(), $mta);
      $mta->status = (empty($mta->status) ? 0 : 1);

      if (!$form->isValid()) {
        foreach ($form->getMessages() as $msg) {
          $this->notification->error($msg);
        }
      } else {
        if (!$mta->save()) {
          foreach ($mta->getMessages() as $message) {
            $this->notification->error($message);
          }

          $this->trace("fail", "No se logro crear un MTA");
        } else {
          $this->notification->success("Se ha creado el MTA correctamente!");
          $this->trace("success", "Se creo un MTA con ID: {$mta->idMta}");
          return $this->response->redirect('mta');
        }
      }
    }

    $this->view->setVar('form', $form);
  }

  public function editAction($idMta) {
    $mta = Mta::findFirst(array(
                "conditions" => "idMta = ?0 AND deleted = 0",
                "bind" => array($idMta),
    ));

    if (!$mta) {
      $this->notification->error("No se encontró el registro del MTA, por favor valide la información");
      return $this->response->redirect("mta");
    }

    $form = new MtaForm($mta);
    $this->view->setVar('form', $form);
    $this->view->setVar('mtaData', $mta);

    if ($this->request->isPost()) {
      $form->bind($this->request->getPost(), $mta);

      $status = $this->request->getPost('status');


      if (!$form->isValid()) {
        foreach ($form->getMessages() as $msg) {
          $this->notification->error($msg);
        }
      } else {
        $mta->status = (empty($status) ? 0 : 1);
        if (!$mta->save()) {
          foreach ($mta->getMessages() as $message) {
            $this->notification->error($message);
          }

          $this->trace("fail", "No se logró actualizar el MTA con ID: {$mta->idMta}");
        } else {
          $this->notification->info("El MTA se actualizó correctamente");
          $this->trace("success", "Se actualizo el MTA con ID: {$mta->idMta}");
          return $this->response->redirect('mta');
        }
      }
    }
  }

  public function deleteAction($idMta) {
    $mta = Mta::findFirst(array(
                "conditions" => "idMta = ?1",
                "bind" => array(1 => $idMta)
    ));

    if ($mta->delete()) {
      $this->notification->warning("Se ha eliminado el MTA correctamente!");
      $this->trace("success", "Se elimino el MTA con ID: {$mta->idMta}");
      return $this->response->redirect('mta/index');
    } else {
      $this->notification->error("Lo sentimos, ocurrio un error durante la elmiminacion del MTA");
      $this->trace("fail", "No se logro eliminar el MTA con ID: {$mta->idMta}");
      return $this->response->redirect('mta/index');
    }
  }

  public function listfullmtaAction() {
    try {
      if (isset($this->user->Usertype->Masteraccount->idPaymentPlan)) {
        $idPaymentPlan = $this->user->Usertype->Masteraccount->idPaymentPlan;
        $mta = $this->queryMta($idPaymentPlan);
      } elseif (isset($this->user->Usertype->Allied->idPaymentPlan)) {
        $idPaymentPlan = $this->user->Usertype->Allied->idPaymentPlan;
        $mta = $this->queryMta($idPaymentPlan);
      } else {
        $mta = Mta::find(array(
                    "conditions" => "status = ?0 AND deleted = ?1",
                    "bind" => array(1, 0)
        ));
      }

      $data = [];
      if (count($mta) > 0) {
        foreach ($mta as $key => $value) {
          $data[$key] = array(
              "idMta" => $value->idMta,
              "name" => $value->name
          );
        }
      }
      return $this->set_json_response($data);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while get listfullmta ... {$ex->getMessage()} --> {$ex->getTraceAsString()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function queryMta($idPaymentPlan) {
    $ppxs = PaymentPlanxservice::findFirst(array(
                "conditions" => "idPaymentPlan = ?0 AND idServices = ?1",
                "bind" => array($idPaymentPlan, 2)
    ));

    $mta = $this->modelsManager->createBuilder()
            ->columns("Mta.idMta, Mta.name")
            ->from("Ppxsxmta")
            ->join("Mta")
            ->where("Ppxsxmta.idPaymentPlanxService = :id:")
            ->getQuery()
            ->execute(["id" => $ppxs->idPaymentPlanxService]);

    return $mta;
  }
  
  /**
   * Functio for get all Mtas
   * @param type $idMta
   * @return type json
   */
  public function getallmtaAction($idMta){
    if(!$idMta){
      $arrayMtas = array('conditions'=>'status = ?0 AND deleted =?1',
                         'bind'=>array(1,0));
      $mtas = Mta::find($arrayMtas);
      $data = [];
      if(count($mtas)>0){
        foreach ($mtas as $key => $value) {
          $data[$key] = array(
            "idMta" => $value->idMta,
            "name" => $value->name
          );
        }
      }
      return $this->set_json_response($data);
    }
  }
  
  
  /**
   * Get a mta with idAccount
   * @param type $idMta
   * @return type
   */
  public function getidmtadcxmtaAction(){
    
    //var_dump($this->request);exit;
    //var_dump($this->getRequestContent());
    $idMta = json_decode($this->getRequestContent());
    if($idMta){
      $mtaData = $this->modelsManager->createBuilder()
                ->columns("Dcxmta.idMta")
                ->from("Account")
               ->innerjoin('AccountConfig', 'AccountConfig.idAccount = Account.idAccount')
                ->innerjoin('DetailConfig', 'DetailConfig.idAccountConfig = AccountConfig.idAccountConfig')
                ->innerjoin('Dcxmta', 'Dcxmta.idDetailConfig = DetailConfig.idDetailConfig')
                ->where("Account.idAccount = :id:")
                ->getQuery()
                ->execute(["id" => $idMta]);
      $arrayData = $mtaData->toArray();
      if(count($arrayData)<=0){
        $arrayData = null;
      }
     /*if(count($mta)>0){
        foreach ($mtas as $key => $value) {
          $data[$key] = array(
           "idMta" => $value->idMta,
            "name" => $value->name
          );
        }
     }*/
      return $this->set_json_response($arrayData);
    }
  }

}