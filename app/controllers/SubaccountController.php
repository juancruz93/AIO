<?php

class SubaccountController extends ControllerBase {

  public function initialize() {
    $this->tag->setTitle("Subcuentas");
    parent::initialize();
  }

  public function indexAction($idAccount) {

    $msg = $this->session->get("msgSuccess");
    if (isset($msg) && $msg[0] == "info") {
      $this->notification->info($msg[1]);
      $this->session->remove("msgSuccess");
    } else if (isset($msg) && $msg[0] == "success") {
      $this->notification->success($msg[1]);
      $this->session->remove("msgSuccess");
    }

    $currentPage = $this->request->getQuery('page', null, 1);
    $flag = false;
    if ($this->user->Role->idRole == -1) {
      $flag = true;
    } else if ($this->user->Usertype->idAccount == $idAccount) {
      $flag = true;
    }
    if ($flag) {
      $configAccount = Account::findFirst(array("conditions" => "idAccount = ?0", "bind" => array(0 => $idAccount)));
      $paginator = new Phalcon\Paginator\Adapter\Model(array(
          "data" => Subaccount::find(array(
              "conditions" => "idAccount = ?1 and deleted = 0",
              "order" => "created DESC",
              "bind" => array(1 => $idAccount)
          )),
          "limit" => 10,
          "page" => $currentPage
      ));
    } else {
      $this->notification->error('No tienes permiso para esta accion');
      return $this->response->redirect('account');
    }

    $page = $paginator->getPaginate();
    $services = Services::find();
    $this->view->setVar("services", $services);
    $this->view->setVar("configAccount", $configAccount->Accountclassification);
    $this->view->setVar("page", $page);
    $this->view->setVar("idAccount", $idAccount);
  }

  public function createAction($idAccount) {
    $subaccountForm = new SubaccountForm();

    $account = Account::findFirst(array(
                'conditions' => 'idAccount = ?1',
                'bind' => array(1 => $idAccount)
    ));

    if (!$account) {
      throw new InvalidArgumentException("La cuenta enviada no existe, por favor verifique la información");
    }
    $nameServ = array();
    $detailConfig = $account->AccountConfig->DetailConfig;
    $accountConfig = $account->AccountConfig;

    foreach ($detailConfig as $ser) {
      $services = Services::findByIdServices($ser->idServices);
      array_push($nameServ, $services[0]->name);

      if ($ser->idServices == $this->services->sms) {
        $limitSms = $ser->amount;
      }else if ($ser->idServices == $this->services->sms_two_way){
        $limitSmstwoway = $ser->amount;
      }else if ($ser->idServices == $this->services->email_marketing){
        $limitContact = $ser->amount;
        $accountingMode = $ser->accountingMode;
      } else if ($ser->idServices == $this->services->survey) {
        $amountQuestion = $ser->amountQuestion;
        $amountAnswer = $ser->amountAnswer;
      } else if ($ser->idServices == $this->services->landing_page) {
        $limitLandingpage = $ser->amount;
      }
    }

    $this->view->setVar('servicesAvailable', $detailConfig);
    $capacity = $accountConfig->diskSpace;

    $this->view->setVar("subaccountForm", $subaccountForm);
    $this->view->setVar("account", $account);
    $this->view->setVar("fileSpace", $capacity);
    $this->view->setVar("accountingMode", $accountingMode);
    $this->view->setVar("limitContact", $limitContact);
    $this->view->setVar("limitSms", $limitSms);
    $this->view->setVar("limitSmstwoway", $limitSmstwoway);
    $this->view->setVar("limitLandingpage", $limitLandingpage);
    $this->view->setVar("amountQuestion", $amountQuestion);
    $this->view->setVar("amountAnswer", $amountAnswer);
    $this->view->setVar("services", $nameServ);
    try {

      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson, true);

      if (!empty($data)) {
        $subaccount = new Subaccount();

        $subaccountForm->bind($data, $subaccount);
        if (!$subaccountForm->isValid()) {
          foreach ($subaccountForm->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }

        $idCity = $data['city'];
        $status = $data['status'];
        $subaccount->idAccount = $account->idAccount;
        $subaccount->idCity = $idCity;
        $subaccount->status = ($status ? 1 : 0);
        $this->db->begin();

        $this->saveTable($subaccount);

        /* $fileSpace = $data['diskSpace'];
          if (!isset($fileSpace)) {
          $fileSpace = 0;
          }
          $totalFileSpace = $capacity - $fileSpace;

          if ($totalFileSpace < 0 || $totalFileSpace > $capacity) {
          throw new InvalidArgumentException("El espacio ingresado supera al espacio disponible");
          }

          $accountConfig->diskSpace = $totalFileSpace;
          $this->saveTable($accountConfig); */

        foreach ($data["services"] as $key) {
          $saxs = new Saxs();
          if ($key == $this->services->sms) {
            $smsLimit = $data['smsLimit'];
            if (!isset($smsLimit)) {
              $smsLimit = 0;
            }
            $totalSms = $limitSms - $smsLimit;
            if (!is_numeric($smsLimit)) {
              throw new InvalidArgumentException("El campo Limite de Mensajes de Texto es obligatorio");
            }
            if ($totalSms < 0 || $totalSms > $limitSms) {
              throw new InvalidArgumentException("El limite de Mensajes de Texto ingresado supera al disponible");
            }

            $saxs->idSubaccount = $subaccount->idSubaccount;
            $saxs->idServices = $key;
            $saxs->amount = $smsLimit;
            $saxs->totalAmount = $smsLimit;

            $this->saveTable($saxs);

            foreach ($detailConfig as $ser) {
              if ($ser->idServices == $this->services->sms && $ser->idServices == $key) {
                $ser->amount = $totalSms;
                $this->saveTable($ser);
              }
            }
          }
          
          if ($key == $this->services->sms_two_way) {
            $smstwowayLimit = $data['smstwowayLimit'];
            if (!isset($smstwowayLimit)) {
              $smstwowayLimit = 0;
            }
            $totalSmstwoway = $limitSmstwoway - $smstwowayLimit;
            if (!is_numeric($smstwowayLimit)) {
              throw new InvalidArgumentException("El campo Limite de Mensajes de Texto es obligatorio");
            }
            if ($totalSmstwoway < 0 || $totalSmstwoway > $limitSmstwoway) {
              throw new InvalidArgumentException("El limite de Mensajes de Texto ingresado supera al disponible");
            }

            $saxs->idSubaccount = $subaccount->idSubaccount;
            $saxs->idServices = $key;
            $saxs->amount = $smstwowayLimit;
            $saxs->totalAmount = $smstwowayLimit;

            $this->saveTable($saxs);

            foreach ($detailConfig as $ser) {
              if ($ser->idServices == $this->services->sms && $ser->idServices == $key){
                $ser->amount = $totalSms;
                $this->saveTable($ser);
              }
              if ($ser->idServices == $this->services->sms_two_way && $ser->idServices == $key){
                $ser->amount = $totalSmstwoway;
                $this->saveTable($ser);
              }
            }
          }

          if ($key == $this->services->landing_page) {
            $landingpageLimit = $data['landingpageLimit'];
            if (!isset($landingpageLimit)) {
              $landingpageLimit = 0;
            }
            $totalLandingpage = $limitLandingpage - $landingpageLimit;
            if (!is_numeric($landingpageLimit)) {
              throw new InvalidArgumentException("El campo Limite de visualizaciones es obligatorio");
            }
            if ($totalLandingpage < 0 || $totalLandingpage > $limitLandingpage) {
              throw new InvalidArgumentException("El limite de visualizaciones ingresado supera al disponible");
            }

            $saxs->idSubaccount = $subaccount->idSubaccount;
            $saxs->idServices = $key;
            $saxs->amount = $landingpageLimit;
            $saxs->totalAmount = $landingpageLimit;

            $this->saveTable($saxs);

            foreach ($detailConfig as $ser) {
              if ($ser->idServices == $this->services->landing_page && $ser->idServices == $key) {
                $ser->amount = $totalLandingpage;
                $this->saveTable($ser);
              }
            }
          }
          
          if ($key == $this->services->email_marketing) {
            if ($accountingMode == "contact") {
              //$contactLimit = $data['contactLimit'];

              /* if (!isset($contactLimit)) {
                $contactLimit = 0;
                } */
              //$totalContact = $limitContact - $contactLimit;

              /* if (!is_numeric($contactLimit)) {
                throw new InvalidArgumentException("El campo Limite de Contactos es obligatorio");
                }
                if ($totalContact < 0 || $totalContact > $limitContact) {
                throw new InvalidArgumentException("El limite de contactos ingresado supera al disponible");
                } */

              $saxs->idSubaccount = $subaccount->idSubaccount;
              $saxs->idServices = $key;
              $saxs->accountingMode = $accountingMode;

              $this->saveTable($saxs);

              /* foreach ($detailConfig as $ser) {
                if ($ser->idServices == $this->services->email_marketing && $ser->idServices == $key){
                $ser->amount = $totalContact;
                $this->saveTable($ser);
                }
                } */
            } else if ($accountingMode == "sending") {
              $mailLimit = $data['mailLimit'];

              if (!isset($mailLimit)) {
                $mailLimit = 0;
              }
              $totalContact = $limitContact - $mailLimit;

              if ($totalContact < 0 || $totalContact > $limitContact) {
                throw new InvalidArgumentException("El limite de contactos ingresado supera al disponible");
              }
              if (!is_numeric($mailLimit)) {
                throw new InvalidArgumentException("El campo Limite de Correos es obligatorio");
              }

              $saxs->idSubaccount = $subaccount->idSubaccount;
              $saxs->idServices = $key;
              $saxs->amount = $mailLimit;
              $saxs->totalAmount = $mailLimit;
              $saxs->accountingMode = $accountingMode;

              $this->saveTable($saxs);

              foreach ($detailConfig as $ser) {
                if ($ser->idServices == $this->services->email_marketing && $ser->idServices == $key) {
                  $ser->amount = $totalContact;
                  $this->saveTable($ser);
                }
              }
            }
          }
          if ($key == $this->services->survey) {
            $questionLimit = $data['questionLimit'];
            $answerLimit = $data['answerLimit'];

            if (!isset($questionLimit)) {
              $questionLimit = 0;
            }
            if (!isset($answerLimit)) {
              $answerLimit = 0;
            }
            $totalAmountQuestion = $amountQuestion - $questionLimit;
            $totalAmountAnswer = $amountAnswer - $answerLimit;

            if (!is_numeric($questionLimit)) {
              throw new InvalidArgumentException("El campo Limite de preguntas es obligatorio");
            }
            if ($totalAmountQuestion < 0 || $totalAmountQuestion > $amountQuestion) {
              throw new InvalidArgumentException("El Limite de preguntas ingresado supera al disponible");
            }

            if (!is_numeric($answerLimit)) {
              throw new InvalidArgumentException("El campo Limite de respuestas es obligatorio");
            }
            if ($totalAmountAnswer < 0 || $totalAmountAnswer > $amountAnswer) {
              throw new InvalidArgumentException("El Limite de respuestas ingresado supera al disponible");
            }

            $saxs->idSubaccount = $subaccount->idSubaccount;
            $saxs->idServices = $key;
            $saxs->amountQuestion = $questionLimit;
            $saxs->amountAnswer = $answerLimit;
            $saxs->totalAmountQuestion = $questionLimit;
            $saxs->totalAmountAnswer = $answerLimit;

            $this->saveTable($saxs);

            foreach ($detailConfig as $ser) {
              if ($ser->idServices == $this->services->survey && $ser->idServices == $key) {
                $ser->amountQuestion = $totalAmountQuestion;
                $ser->amountAnswer = $totalAmountAnswer;
                $this->saveTable($ser);
              }
            }
          }
        }

        $this->db->commit();
        $this->notification->success("Se ha creado la subcuenta correctamente!");
        return $this->set_json_response(array("idSubaccount" => (int) $subaccount->idSubaccount), 200, "OK");
      }
    } catch (InvalidArgumentException $e) {
      $this->logger->log("Exception while creating account: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      return $this->set_json_response(array($e->getMessage()), 409, "FAIL");
    } catch (Exception $e) {
      $this->logger->log("Exception while creating account: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      return $this->set_json_response(array("Ocurrió un error, por favor contacte a un administrador"), 409, "FAIL");
    }
  }

  public function editAction($idSubaccount) {

    $flag = false;
    if ($this->user->Role->idRole == -1) {
      $flag = true;
    } else {
      foreach ($this->user->Usertype->Account->Subaccount as $key) {
        if ($key->idSubaccount == $idSubaccount) {
          $flag = true;
        }
      }
    }

    if ($flag == true) {
      $subaccount = Subaccount::findFirst(array(
                  'conditions' => 'idSubaccount = ?1',
                  'bind' => array(1 => $idSubaccount)
      ));
    }

    if (!$subaccount) {
      $this->notification->error("La subcuenta que intenta modificar no existe, por favor verifique la información");
      return $this->response->redirect('subaccount/index/' . $this->user->Usertype->idAccount);
    }

    $nameServ = array();
    $detailConfig = $subaccount->Account->AccountConfig->DetailConfig;
    $limitSmsAccount = 0;
    $limitSms = 0;
    $limitContactAccount = 0;
    $limitSmstwoway = 0;
    $amountQuestionAccount = 0;
    $amountAnswerAccount = 0;
    $amountAnswer = 0;
    $amountQuestion = 0;
    $limitLandingpageAccount = 0;
    $limitLandingpage = 0;
    $limitSmstwowayAccount = 0;
    foreach ($detailConfig as $ser) {
      $services = Services::findByIdServices($ser->idServices);
      //array_push($nameServ, $services[0]->name);
      $nameServ[]= $services[0]->name;

      if ($ser->idServices == $this->services->sms) {
        $limitSmsAccount = $ser->amount;
      }else if ($ser->idServices == $this->services->sms_two_way){
        $limitSmstwowayAccount = $ser->amount;
      }else if ($ser->idServices == $this->services->email_marketing){
        $limitContactAccount = $ser->amount;
        $accountingMode = $ser->accountingMode;
      } else if ($ser->idServices == $this->services->survey) {
        $amountQuestionAccount = $ser->amountQuestion;
        $amountAnswerAccount = $ser->amountAnswer;
      } else if ($ser->idServices == $this->services->landing_page) {
        $limitLandingpageAccount = $ser->amount;
      }
    }

    $saxs = $subaccount->saxs;
    $idServices = [];
    foreach ($saxs as $item) {
      $idServices[] = $item->idServices;

      if ($item->idServices == $this->services->sms) {
        $limitSms = $item->amount;
      }else if ($item->idServices == $this->services->sms_two_way){
        $limitSmstwoway = $item->amount;
      }else if ($item->idServices == $this->services->email_marketing){
        $limitContact = $item->amount;
      } else if ($item->idServices == $this->services->survey) {
        $amountQuestion = $item->amountQuestion;
        $amountAnswer = $item->amountAnswer;
      } else if ($item->idServices == $this->services->landing_page) {
        $limitLandingpage = $item->amount;
      }
    }

    if (!isset($limitContact)) {
      $limitContact = 0;
    }

    $countSmsxc = Smsxc::count([["idSubaccount" => (string) $idSubaccount,"status"=>"sent"]]);

    $data = $this->modelsManager->createBuilder()
            ->columns(["COUNT(Smslote.idSmslote) AS total"])
            ->from('Sms')
            ->leftJoin("Smslote", "Smslote.idSms = Sms.idSms")
            ->where("Sms.idSubaccount = " . $idSubaccount .
                    " AND Sms.status = 'sent' AND Smslote.status = 'sent'")
            ->getQuery()
            ->execute();

    $totalSmsSend = $countSmsxc + $data[0]->total;
    
    //---------------MODIFICAR CONSULTA PARA LOTEWOWAY
    /*$data1 = $this->modelsManager->createBuilder()
        ->columns(["COUNT(Smslotetwoway.idSmsLoteTwoway) AS total"])
        ->from('Smstwoway')
        ->leftJoin("Smslotetwoway", "Smslotetwoway.idSmsTwoway = Smstwoway.idSmsTwoway")
        ->where("Smstwoway.idSubaccount = " . $idSubaccount .
            " AND Smstwoway.status = 'sent' AND Smslotetwoway.status = 'sent'")
        ->getQuery()
        ->execute();

    $totalSmstwowaySend = $countSmsxc + $data1[0]->total;*/
    //----------------MODIFICAR CONSULTA PARA LOTEWOWAY
    $this->view->setVar('servicesAvailable', $detailConfig);

    $this->view->setVar("limitContactAccount", $limitContactAccount);
    $this->view->setVar("limitSmsAccount", $limitSmsAccount);
    //$this->view->setVar("limitSmstwowayAccount", $limitSmstwowayAccount);
    $this->view->setVar("accountingMode", $accountingMode);
    $this->view->setVar("limitContact", $limitContact);
    $this->view->setVar("limitSms", $limitSms);
    //$this->view->setVar("limitSmstwoway", $limitSmstwoway);
    $this->view->setVar("amountQuestionAccount", $amountQuestionAccount);
    $this->view->setVar("amountAnswerAccount", $amountAnswerAccount);
    $this->view->setVar("amountAnswer", $amountAnswer);
    $this->view->setVar("amountQuestion", $amountQuestion);
    $this->view->setVar("services", $nameServ);
    $this->view->setVar("totalSmsSend", $totalSmsSend);
    //$this->view->setVar("totalSmstwowaySend", $totalSmstwowaySend);
    $this->view->setVar("limitLandingpageAccount", $limitLandingpageAccount);
    $this->view->setVar("limitLandingpage", $limitLandingpage);



    $subaccountForm = new SubaccountForm($subaccount);
    $this->view->setVar("subaccountForm", $subaccountForm);
    $this->view->setVar("subaccount", $subaccount);
    $this->view->setVar("idServices", json_encode($idServices));

    $this->view->setVar("account", $subaccount->account);
    try {
      $dataJson = $this->request->getRawBody();
      $data = json_decode($dataJson);

      if (!empty($data)) {
        //$subaccountForm->bind($this->request->getPost(), $subaccount);
        $name = $data->name;
        $description = "";        
        if(isset($data->description)) $description = $data->description ;

        $idCity = $data->city;
        $status = $data->status;

        $arrMsg = array();
        if (empty($name)) {
          throw new InvalidArgumentException("El campo Nombre es obligatorio");
        }

        if (empty($idCity)) {
          throw new InvalidArgumentException("El campo Ciudad es obligatorio");
        }
        $this->db->begin();
        $subaccount->idCity = $idCity;
        $subaccount->status = ($status ? 1 : 0);
        $subaccount->name = $name;
        $subaccount->description = $description;

        if (!$subaccount->save()) {
          foreach ($subaccount->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->db->rollback();
          $this->trace("fail", "No se edito la subcuenta en la cuenta maestra");
          $this->response->setStatusCode(409, 'fail');
          $arrMsg[] = "No se pudo editar la Subcuenta";
        }
        $arrSaxsServices = array();
        foreach ($saxs as $key => $item) {
          $arrSaxsServices[$item->idServices]['idSaxs'] = $item->idSaxs;
          $arrSaxsServices[$item->idServices]['idServices'] = $item->idServices;
          $arrSaxsServices[$item->idServices]['accountingMode'] = $item->accountingMode;
          $arrSaxsServices[$item->idServices]['amount'] = $item->amount;
          $arrSaxsServices[$item->idServices]['totalAmount'] = $item->totalAmount;
          $arrSaxsServices[$item->idServices]['amountQuestion'] = $item->amountQuestion;
          $arrSaxsServices[$item->idServices]['amountAnswer'] = $item->amountAnswer;
          $arrSaxsServices[$item->idServices]['totalAmountQuestion'] = $item->totalAmountQuestion;
          $arrSaxsServices[$item->idServices]['totalAmountAnswer'] = $item->totalAmountAnswer;

          if (!$item->delete()) {
            foreach ($item->getMessages() as $msg) {
              throw new Exception($msg);
            }
          }
        }
        
        $this->recalculateSaxsBySms($subaccount->idSubaccount);

        foreach ($data->services as $key) {
          $saxs = new Saxs();
          if ($key == $this->services->sms) {
            $smsLimit = $data->smsLimit;
            if (!isset($smsLimit)) {
              $smsLimit = 0;
            }

            $totalSms = $limitSmsAccount + ($limitSms - $smsLimit);

            if (!is_numeric($smsLimit)) {
              throw new InvalidArgumentException("El campo Limite de Mensajes de Texto es obligatorio");
            }
            if ($totalSms < 0) {
              throw new InvalidArgumentException("El limite de Mensajes de Texto ingresado supera al disponible");
            }

            $saxs->idSubaccount = $subaccount->idSubaccount;
            $saxs->idServices = $key;

            if ($smsLimit < $limitSms) {
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'] - ($arrSaxsServices[$key]['amount'] - $smsLimit);
            } else if ($smsLimit > $limitSms) {
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'] + ($smsLimit - $arrSaxsServices[$key]['amount']);
            } else {
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'];
            }

            $saxs->amount = $smsLimit;
            $this->saveTable($saxs);
            
            /* foreach ($detailConfig as $ser) {
              if ($ser->idServices == $this->services->sms && $ser->idServices == $key){
              $ser->amount = $totalSms;
              $this->saveTable($ser);
              }
              } */
          }
          
          /*if ($key == $this->services->sms_two_way) {
            $smstwowayLimit = $data->smstwowayLimit;
            if (!isset($smstwowayLimit)) {
              $smstwowayLimit = 0;
            }

            $totalSmstwoway = $limitSmstwowayAccount + ($limitSmstwoway - $smstwowayLimit);

            if (!is_numeric($smstwowayLimit)) {
              throw new InvalidArgumentException("El campo Limite de Mensajes de Texto es obligatorio");
            }
            if ($totalSmstwoway < 0) {
              throw new InvalidArgumentException("El limite de Mensajes de Texto ingresado supera al disponible");
            }

            $saxs->idSubaccount = $subaccount->idSubaccount;
            $saxs->idServices = $key;

            if ($smstwowayLimit < $limitSmstwoway){
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'] - ($arrSaxsServices[$key]['amount'] - $smstwowayLimit);
            }else if ($smstwowayLimit > $limitSmstwoway){
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'] + ($smstwowayLimit - $arrSaxsServices[$key]['amount']);
            }else {
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'];
            }

            $saxs->amount = $smstwowayLimit;
            $this->saveTable($saxs);
            
            $this->recalculateSaxsBySmstwoway($idSubaccount);
            foreach ($detailConfig as $ser) {
              if ($ser->idServices == $this->services->sms && $ser->idServices == $key){
                $ser->amount = $totalSms;
                $this->saveTable($ser);
              }
            }
          }*/

          if ($key == $this->services->landing_page) {
            $landingpageLimit = $data->landingpageLimit;
            if (!isset($landingpageLimit)) {
              $landingpageLimit = 0;
            }

            $totalLandingpage = $limitLandingpageAccount + ($limitLandingpage - $landingpageLimit);

            if (!is_numeric($landingpageLimit)) {
              throw new InvalidArgumentException("El campo Limite de visualizaciones de Texto es obligatorio");
            }
            if ($totalLandingpage < 0) {
              throw new InvalidArgumentException("El limite de visualizaciones ingresado supera al disponible");
            }

            $saxs->idSubaccount = $subaccount->idSubaccount;
            $saxs->idServices = $key;

            if ($landingpageLimit < $limitLandingpage) {
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'] - ($arrSaxsServices[$key]['amount'] - $landingpageLimit);
            } else if ($landingpageLimit > $limitLandingpage) {
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'] + ($landingpageLimit - $arrSaxsServices[$key]['amount']);
            } else {
              $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'];
            }

            $saxs->amount = $landingpageLimit;
            $this->saveTable($saxs);
            //pendiente por recarcular en landing page procedimiento que descuesta las visualizacione de la landing
            //$this->recalculateSaxsBySms($idSubaccount);
          }

          if ($key == $this->services->email_marketing) {
            if ($accountingMode == "contact") {

              $saxs->idSubaccount = $subaccount->idSubaccount;
              $saxs->idServices = $key;
              $saxs->accountingMode = $accountingMode;

              $this->saveTable($saxs);
              /* foreach ($detailConfig as $ser) {
                if ($ser->idServices == $this->services->email_marketing && $ser->idServices == $key){
                $ser->amount = $totalContact;
                $this->saveTable($ser);
                }
                } */
            } else if ($accountingMode == "sending") {
              $mailLimit = $data->mailLimit;
              
              if (!isset($mailLimit)) {
                $mailLimit = 0;
              }

              $totalContact = $limitContactAccount + ($limitContact - $mailLimit);

              if (!is_numeric($mailLimit)) {
                throw new InvalidArgumentException("El campo Limite de Envios de Email es obligatorio");
              }
              if ($totalContact < 0) {
                throw new InvalidArgumentException("El limite de Mensajes de Texto ingresado supera al disponible");
              }

              if ($totalContact < 0 || $totalContact > $limitContactAccount) {
                throw new InvalidArgumentException("El limite de contactos ingresado supera al disponible");
              }
              if (!is_numeric($mailLimit)) {
                throw new InvalidArgumentException("El campo Limite de Correos es obligatorio");
              }

              $saxs->idSubaccount = $subaccount->idSubaccount;
              $saxs->idServices = $key;

              if ($mailLimit < $limitContactAccount) {
                $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'] - ($arrSaxsServices[$key]['amount'] - $mailLimit);
              } else if ($mailLimit > $limitContactAccount) {
                $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'] + ($mailLimit - $arrSaxsServices[$key]['amount']);
              } else {
                $saxs->totalAmount = $arrSaxsServices[$key]['totalAmount'];
              }

              

              $saxs->amount = $mailLimit;
              //$saxs->totalAmount = $saxs->totalAmount + $mailLimit;
              $saxs->accountingMode = $accountingMode;

              $this->saveTable($saxs);

              foreach ($detailConfig as $ser) {
                if ($ser->idServices == $this->services->email_marketing && $ser->idServices == $key) {
                  $ser->amount = $totalContact;
                  $this->saveTable($ser);
                }
              }
             
              $sql = "CALL updateCountersSendingSaxs({$saxs->idSubaccount})";
              $this->db->execute($sql);
            }
          }
          if ($key == $this->services->survey) {
            $questionLimit = $data->questionLimit;
            $answerLimit = $data->answerLimit;

            if (!isset($questionLimit)) {
              $questionLimit = 0;
            }
            if (!isset($answerLimit)) {
              $answerLimit = 0;
            }

            $totalAmountQuestion = $amountQuestionAccount + ($amountQuestion - $questionLimit);
            $totalAmountAnswer = $amountAnswerAccount + ($amountAnswer - $answerLimit);

            if (!is_numeric($questionLimit)) {
              throw new InvalidArgumentException("El campo Limite de preguntas es obligatorio");
            }
            if ($totalAmountQuestion < 0) {
              throw new InvalidArgumentException("El Limite de preguntas ingresado supera al disponible");
            }

            if (!is_numeric($answerLimit)) {
              throw new InvalidArgumentException("El campo Limite de respuestas es obligatorio");
            }
            if ($totalAmountAnswer < 0) {
              throw new InvalidArgumentException("El Limite de respuestas ingresado supera al disponible");
            }

            $saxs->idSubaccount = $subaccount->idSubaccount;
            $saxs->idServices = $key;

            if ($questionLimit < $amountQuestion) {
              $saxs->totalAmountQuestion = $arrSaxsServices[$key]['totalAmountQuestion'] - ($arrSaxsServices[$key]['amountQuestion'] - $questionLimit);
            } else if ($questionLimit > $amountQuestion) {
              $saxs->totalAmountQuestion = $arrSaxsServices[$key]['totalAmountQuestion'] + ($questionLimit - $arrSaxsServices[$key]['amountQuestion']);
            } else {
              $saxs->totalAmountQuestion = $arrSaxsServices[$key]['totalAmountQuestion'];
            }

            if ($answerLimit < $amountQuestion) {
              $saxs->totalAmountAnswer = $arrSaxsServices[$key]['totalAmountAnswer'] - ($arrSaxsServices[$key]['amountAnswer'] - $answerLimit);
            } else if ($answerLimit > $amountQuestion) {
              $saxs->totalAmountAnswer = $arrSaxsServices[$key]['totalAmountAnswer'] + ($answerLimit - $arrSaxsServices[$key]['amountAnswer']);
            } else {
              $saxs->totalAmountAnswer = $arrSaxsServices[$key]['totalAmountAnswer'];
            }

            $saxs->amountQuestion = $questionLimit;
            $saxs->amountAnswer = $answerLimit;

            $this->saveTable($saxs);
          }
        }

        $sql = "CALL updateAmountAccount({$subaccount->idAccount},{$this->services->sms})";
        $this->db->execute($sql);

        $sql = "CALL updateAmountAccount({$subaccount->idAccount},{$this->services->survey})";
        $this->db->execute($sql);

        $sql = "CALL updateCountersAccount({$subaccount->idAccount})";
        $this->db->fetchAll($sql);

        $this->db->commit();
        $arrMsg[0] = "info";
        $arrMsg[1] = "Se ha editado la subcuenta correctamente!";
        unset($sql);
        $this->response->setStatusCode(200, 'ok');
        $this->session->set('msgSuccess', $arrMsg);
        return $this->set_json_response((int) $subaccount->idAccount, 200, "OK");
      }
    } catch (InvalidArgumentException $e) {
      $this->response->setStatusCode(409, 'fail');
      $arrMsg[] = $e->getMessage();
      $this->response->setJsonContent($arrMsg);
      return $this->response;
    } catch (Exception $e) {
      $this->trace("fail", $e->getMessage());
      $this->logger->log("Exception while creating masteraccount: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
    }
  }

  public function deleteAction($idSubaccount) {
    $subaccount = Subaccount::findFirst(array(
                'conditions' => 'idSubaccount = ?1',
                'bind' => array(1 => $idSubaccount)
    ));

    if (!$subaccount) {
      $this->notification->error("La subcuenta que intenta eliminar no existe, por favor verifique la información.");
      $this->trace('fail', "La subcuenta no existe: {$idSubaccount}");
    }

    $accountClasification = Accountclassification::findFirstByIdAccountclassification($subaccount->account->idAccountclassification);

    $tempFileSpace = $accountClasification->fileSpace + $subaccount->fileSpace;
    $tempMailLimit = $accountClasification->mailLimit + $subaccount->mailLimit;
    $tempContactLimit = $accountClasification->contactLimit + $subaccount->contactLimit;
    $tempSmsLimit = $accountClasification->smsLimit + $subaccount->smsLimit;
    $tempLandingpageLimit = $accountClasification->landingpageLimit + $subaccount->landingpageLimit;

    $this->db->begin();

    $accountClasification->fileSpace = $tempFileSpace;
    $accountClasification->mailLimit = $tempMailLimit;
    $accountClasification->contactLimit = $tempContactLimit;
    $accountClasification->smsLimit = $tempSmsLimit;
    $accountClasification->landingpageLimit = $tempLandingpageLimit;
    try {
      if (!$subaccount->delete()) {
        $this->db->rollback();
        foreach ($subaccount->getMessages() as $msg) {
          $this->logger->log("Error while deleting subaccount: {$msg}");
          throw new Exception("Ha ocurrido un error, por favor contacte a el administrador.");
        }
        return $this->response->redirect("subaccount/index/{$subaccount->idAccount}");
      } else {
        if (!$accountClasification->save()) {
          $this->db->rollback();
          foreach ($accountClasification->getMessages() as $msg) {
            $this->trace("fail", $msg);
            $this->logger->log("Error while updating accountClasification: {$msg}");
            throw new Exception("Ha ocurrido un error, por favor contacte a el administrador.");
          }
        }
        $this->db->commit();
        $this->notification->warning("Se ha eliminado la subcuenta exitosamente");
        $this->trace('success', "Se elimino la subcuenta: {$idSubaccount}");
        return $this->response->redirect("subaccount/index/{$subaccount->idAccount}");
      }
    } catch (Exception $e) {
      $this->notification->error($e->getMessage());
    }
  }

  public function countryAction() {
    try {
      $modelManager = Phalcon\DI::getDefault()->get('modelsManager');
      $array = array();
      $sql = "SELECT * FROM Country ORDER BY name";
      $country = $modelManager->executeQuery($sql);
      foreach ($country as $value => $key) {
        $array[$value] = $key;
      }
      $this->response->setStatusCode(200, 'ok');
      $this->response->setJsonContent($array);
      return $this->response;
    } catch (Exception $ex) {
      $this->notification->error($ex->getMessage());
    }
  }

  public function stateAction($idCountry) {
    try {
      $modelManager = Phalcon\DI::getDefault()->get('modelsManager');
      $array = array();
      $sql = "SELECT * FROM State WHERE idCountry = :idCountry: ORDER BY name";
      $state = $modelManager->executeQuery($sql, array('idCountry' => $idCountry));
      foreach ($state as $value => $key) {
        $array[$value] = $key;
      }
      $this->response->setStatusCode(200, 'ok');
      $this->response->setJsonContent($array);
      return $this->response;
    } catch (Exception $ex) {
      $this->notification->error($ex->getMessage());
    }
  }

  public function citiesAction($idState) {
    try {
      $modelManager = Phalcon\DI::getDefault()->get('modelsManager');
      $array = array();
      $sql = "SELECT * FROM City WHERE idState = :idState: ";
      $city = $modelManager->executeQuery($sql, array("idState" => $idState));
      foreach ($city as $value => $key) {
        $key->name = utf8_encode($key->name);
        $array[$value] = $key;
      }
      return $this->set_json_response($array, 200, "OK");
    } catch (Exception $ex) {
      $this->notification->error($ex->getMessage());
    }
  }

  public function userlistAction($idSubaccount) {
    $currentPage = $this->request->getQuery('page', null, 1);
    if (!$idSubaccount) {
      $this->notification->error("La cuenta enviada no existe, por favor verifique la información");
      return $this->response->redirect("subaccount/index/" . $this->user->Usertype->idAccount);
    }
    $builder = $this->modelsManager->createBuilder()
            ->from('User')
            ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
            ->where("Usertype.idSubaccount  = {$idSubaccount} AND User.deleted = 0")
            ->orderBy('User.created');

    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
        "builder" => $builder,
        "limit" => 15,
        "page" => $currentPage
    ));

    $page = $paginator->getPaginate();
    $subaccount = Subaccount::findFirst(array("conditions" => "idSubaccount = ?0", "bind" => array(0 => $idSubaccount)));
    $this->view->setVar("name", $subaccount->name);
    $this->view->setVar("page", $page);
    $this->view->setVar("idSubaccount", $idSubaccount);
  }

  public function createuserAction($idSubaccount) {
    $subaccount = Subaccount::findFirst(array(
                'conditions' => 'idSubaccount = ?1',
                'bind' => array(1 => $idSubaccount)
    ));
    if (!$subaccount) {
      $this->notification->error("La cuenta enviada no existe, por favor verifique la información");
      return $this->response->redirect("");
    }
    $user = new User();
    $this->view->setVar("subaccount", $subaccount);
    $form = new UserForm($user);
    $this->view->UserForm = $form;

    $dataJson = $this->request->getRawBody();
    $data = json_decode($dataJson, true);

    if (!empty($data)) {
      try {
        $userManager = new \Sigmamovil\General\Misc\UserManager();
        $user = $userManager->creataSubaccountUser($data, $idSubaccount);
        $this->notification->success('Se ha creado el usuario exitosamente en la subcuenta <strong>' . $subaccount->name . '</strong>');

        return $this->set_json_response(array("idSubaccount" => $idSubaccount), 200, 'OK');
      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response(array($msg->getMessage()), 401, 'error');
      } catch (Exception $ex) {
        $this->logger->log("Exception: {$ex->getMessage()}");
        return $this->set_json_response(array($ex->getMessage()), 500, 'error');
      }
    }
  }

  public function edituserAction($idUser) {

    $userE = User::findFirst(array(
                "conditions" => "idUser = ?1",
                "bind" => array(1 => $idUser)
    ));

    if (!$userE) {
      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("");
    }

    $city = City::findByIdCity($userE->idCity);
    $state = State::findByIdState($city[0]->idState);
    $idState = $city[0]->idState;
    $idcountry = $state[0]->idCountry;
    $this->view->setVar("idCountry", $idcountry);
    $this->view->setVar("idState", $idState);
    $this->view->setVar("userE", $userE);
    $form = new UserForm($userE);
    $this->view->UserForm = $form;

    if ($this->request->isPost()) {
      $form->bind($this->request->getPost(), $userE);
      $name = $form->getValue('name');
      $lastname = $form->getValue('lastname');
      $cellphone = $form->getValue('cellphone');
      try {
        if (empty($name) or empty($lastname) or empty($cellphone)) {
          throw new \InvalidArgumentException("Todos los campos son obligatorios");
        }
        $userE->idCity = $this->request->getPost('citySelectedUser');
        if ($userE->save()) {
          $this->notification->success('Se ha editado exitosamente el usuario <strong>' . $userE->name . '</strong>');
          $this->trace("success", "Se edito un usuario con ID: {$userE->idUser}");
          return $this->response->redirect("subaccount/userlist/{$userE->UserType->idSubaccount}");
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

  public function passeditAction($idUser) {
    $editUser = User::findFirst(array(
                "conditions" => "idUser = ?1",
                "bind" => array(1 => $idUser)
    ));

    if (!$editUser) {
      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("");
    }
    $this->view->setVar("userE", $editUser);
    if ($this->request->isPost()) {
      $pass = $this->request->getPost('pass1');
      $pass2 = $this->request->getPost('pass2');
      if ((empty($pass) || empty($pass2))) {
        $this->notification->error('El campo Contraseña esta vacío, por favor valide la información');
      } else {
        if (($pass != $pass2)) {
          $this->notification->error('Las contraseñas no coinciden');
        } else {
          if (strlen($pass) < 8) {
            $this->notification->error('La contraseña es muy corta, debe tener como minimo 8 caracteres');
          } else {
            $editUser->password = $this->security->hash($pass);
            if (!$editUser->save()) {
              foreach ($editUser->getMessages() as $message) {
                $this->notification->error($message);
              }
              $this->trace("fail", "No se edito la contraseña del usuario con ID: {$editUser->idUser}");
            } else {
              $this->notification->success('Se ha editado la contraseña exitosamente del usuario <strong>' . $editUser->name . '</strong>');
              $this->trace("sucess", "Se edito la contraseña del usuario con ID: {$editUser->idUser}");
              return $this->response->redirect("subaccount/userlist/{$editUser->UserType->idSubaccount}");
            }
          }
        }
      }
    }
  }

  public function deleteuserAction($id) {
    try {
      $idUser = $this->session->get('idUser');

      $user = User::findFirst(array(
                  "conditions" => "idUser = ?1",
                  "bind" => array(1 => $id)
      ));
      $email = $user->email;
      if ($id == $idUser) {
        throw new InvalidArgumentException("No se puede eliminar el usuario que esta actualmente en sesión, por favor verifique la información");
        $this->trace('fail', "Se intento borrar un usuario en sesión: {$idUser}");
      }

      if (!$user) {
        throw new InvalidArgumentException("El usuario que ha intentado eliminar no existe, por favor verifique la información");
        $this->trace('fail', "El usuario no existe: {$idUser}");
      }
      $users = User::find(array(
                  "conditions" => "email = ?1",
                  "bind" => array(1 => $email)
      ));
      //Aqui se eliminan todos los usuarios asociados a ese correo ya se subaccount o account
      foreach($users as $us){
          $us->deleted = time();
          $email = str_replace("@", "_1@", $us->email);
          $us->email = $email;
          $us->deletedBy = \Phalcon\DI::getDefault()->get('user')->email;
          if (!$us->update()) {
            foreach ($us->getMessages() as $msg) {
              throw new Exception($msg);
            }
          } 
      }

        $this->notification->warning("Se ha eliminado el usuario <strong>{$user->name}</strong> exitosamente");
        $this->trace('success', "Se elimino el usuario: {$id}");
        return $this->response->redirect("subaccount/userlist/{$user->UserType->idSubaccount}");
      
    } catch (InvalidArgumentException $msg) {
      $this->notification->error($msg->getMessage());
      return $this->response->redirect("account");
    } catch (Exception $e) {
      $this->trace("fail", $e->getTraceAsString());
      $this->logger->log("Exception while creating masteraccount: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error("Ocurrió un error, por favor contacte al administrador");
    }
  }

//  public function configeditAction($idSubaccount){
//    echo $idSubaccount;
//   $config = Alliedconfig::findFirst(array(
//          'conditions' => 'idAllied = ?1',
//          'bind' => array(1 => $idSubaccount)
//    ));
//
//    if (!$config) {
//      $this->notification->error("La cuenta aliada que intenta modificar no existe, por favor verifique la información");
//    }
//
//    $alias = Allied::findFirst(array(
//          'conditions' => 'idAllied = ?1',
//          'bind' => array(1 => $idAlias)
//    ));
//    $idMasteraccount = $alias->idMasteraccount;
//    $configMasteraccount = Masteraccount::findFirst(array(
//          'conditions' => 'idMasteraccount = ?1',
//          'bind' => array(1 => $idMasteraccount)
//    ));
//    $mta = $this->modelsManager->createBuilder()
//        ->from('Mta')
//        ->join('Maxmta', 'Maxmta.idMta = Mta.idMta')
//        ->where('Maxmta.idMasteraccount = ' . $idMasteraccount)
//        ->orderBy('Mta.name')
//        ->getQuery()
//        ->execute();
//    $this->view->setVar('mta', $mta);
//
//    $adapter = $this->modelsManager->createBuilder()
//        ->from('Adapter')
//        ->join('Maxadapter', 'Maxadapter.idAdapter = Adapter.idAdapter')
//        ->where('Maxadapter.idMasteraccount = ' . $idMasteraccount)
//        ->getQuery()
//        ->execute();
//    $this->view->setVar('adapter', $adapter);
//
//    $mailclass = $this->modelsManager->createBuilder()
//        ->from('Mailclass')
//        ->join('Maxmailclass', 'Maxmailclass.idMailclass = Mailclass.idMailclass')
//        ->where('Maxmailclass.idMasteraccount = ' . $idMasteraccount)
//        ->getQuery()
//        ->execute();
//    $this->view->setVar('mailclass', $mailclass);
//
//    $urldomain = $this->modelsManager->createBuilder()
//        ->from('Urldomain')
//        ->join('Maxurldomain', 'Maxurldomain.idUrldomain = Urldomain.idUrldomain')
//        ->where('Maxurldomain.idMasteraccount = ' . $idMasteraccount)
//        ->getQuery()
//        ->execute();
//    $this->view->setVar('urldomain', $urldomain);
////        $configEdit = $config;
//    $configform = new ConfigForm();
//    $this->view->setVar('ConfigForm', $configform);
//    $this->view->setVar('config', $config);
//    $this->view->setVar('alias', $alias);
//    $this->view->setVar('masteraccount', $configMasteraccount);
//    try {
//      if ($this->request->isPost()) {
//        $configEdit = Alliedconfig::findFirst(array(
//              'conditions' => 'idAllied = ?1',
//              'bind' => array(1 => $idAlias)
//        ));
//        $configform->bind($this->request->getPost(), $config);
//        $accoutingManager = new \Sigmamovil\General\Misc\AccountingManager();
//        $this->db->begin();
//        $accoutingManager->alliedConfigEdit($config, $configEdit);
//        $this->db->commit();
//        $this->notification->info('Se ha editado exitosamente la configuración de la cuenta aliada <strong>' . $alias->name . '</strong>.');
//        $this->trace("success", "Se edito la configuración de una cuenta aliada");
//        return $this->response->redirect("masteraccount/aliaslist/{$alias->idMasteraccount}");
//      }
//    } catch (InvalidArgumentException $msg) {
//      $this->notification->error($msg->getMessage());
//    } catch (Exception $e) {
//      $this->notification->error($e->getMessage());
//    }
//  }

  public function saveTable($nameTable) {
    if (!$nameTable->save()) {
      $this->db->rollback();
      foreach ($nameTable->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return $nameTable;
  }

  public function showAction($idSubaccount) {
    if (!$idSubaccount) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('account');
    }
    $subaccount = Subaccount::findfirst([
                'conditions' => 'idSubaccount = ?0',
                'bind' => array(0 => $idSubaccount)
    ]);

    if (!$subaccount) {
      $this->notification->error('La cuenta maestra no existe');
      return $this->response->redirect('masteraccount');
    }

    $this->view->setVar("space", round($this->getSpaceUsedInAccount(), 2));
    $this->view->setVar("subaccount", $subaccount);
  }

  public function showconfigAction($idSubaccount) {
    if (!$idSubaccount) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('account');
    }
    $subaccount = Subaccount::findfirst([
                'conditions' => 'idSubaccount = ?0',
                'bind' => array(0 => $idSubaccount)
    ]);

    if (!$subaccount) {
      $this->notification->error('La cuenta maestra no existe');
      return $this->response->redirect('masteraccount');
    }

    $this->view->setVar("subaccount", $subaccount);
    $this->view->setVar("space", round($this->getSpaceUsedInAccount(), 2));
  }

  public function recalculateSaxsBySms($idSubaccount) {
    $inSms = Sms::count(array(
      "conditions" => array(
        "idSubaccount" => (int) $idSubaccount,
        "status"  => "sent",
        "logicodeleted" => 0,
        "type" => "contact"
      ),
      "colums" => "idSms"
    ));
    $count = 0;
    if($inSms > 0){
      $collectionSmsXc = [[ '$match' => ['idSubaccount' => (string) $idSubaccount] ],[ '$group' => ['_id' => '$idSubaccount', 'messageCount' => ['$sum' => '$messageCount']]]];
      $count1 = \Smsxc::aggregate($collectionSmsXc);
      
      if(isset($count1['result'][0]['messageCount'])){
        $count = $count1['result'][0]['messageCount'];
      }
      unset($count1);    
    }                                                          
    $sql = "CALL updateCountersSmsSaxs({$idSubaccount},{$count})";                                                                     
    $this->db->execute($sql);
  }
    
  public function recalculateSaxsBySmstwoway($idSubaccount) {
    //$count = Smsxc::count([["idSubaccount" => (string) $idSubaccount]]);
    $cero = 0;
    $sql = "CALL updateCountersSmstwowaySaxs({$idSubaccount},{$cero},{$cero})";
    $this->db->execute($sql);
  }

  public function desactivateserviceAction($idSaxs){
    try{            
      $saxs =  Saxs::findfirst([
          'conditions' => 'idSaxs = ?0 AND status = 1',
          'bind' => array(0 => $idSaxs)]);
      $saxs->status=0;
      if (!$saxs->save()) {
        foreach ($saxs->getMessages() as $msg) {
          throw new Exception($msg);
        }
      } 

      $this->notification->warning("Se ha desactivado el servicio <strong>{$saxs->Service->name}</strong> exitosamente");
        
      return $this->response->redirect("subaccount/index/" .$saxs->Subaccount->idAccount);

    } catch (InvalidArgumentException $msg) {
      $this->notification->error($msg->getMessage());
      return $this->response->redirect("subaccount/index/" .$saxs->Subaccount->idAccount);
    } catch (Exception $e) {
      $this->trace("fail", $e->getTraceAsString());
      $this->logger->log("Exception while creating masteraccount: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error("Ocurrió un error, por favor contacte al administrador");
      return $this->response->redirect("subaccount/index/" .$saxs->Subaccount->idAccount);
    }         
  }



  public function activateserviceAction($idSaxs){
    
    try{ 
      $saxs = Saxs::findfirst([
        'conditions'=> 'idSaxs = ?0 AND status = 0',
        'bind' => array(0=> $idSaxs ) ]);
        $saxs->status=1;
        if (!$saxs->save()) {
          foreach ($saxs->getMessages() as $msg) {
            throw new Exception($msg);
          }
        } 
    
      $this->notification->success("Se activó el servicio <strong>{$saxs->Service->name}</strong> exitosamente");
            
      return $this->response->redirect("subaccount/index/" .$saxs->Subaccount->idAccount);
    } catch (InvalidArgumentException $msg) {
      $this->notification->error($msg->getMessage());
      return $this->response->redirect("subaccount/index/" .$saxs->Subaccount->idAccount);
    } catch (Exception $e) {
      $this->trace("fail", $e->getTraceAsString());
      $this->logger->log("Exception while creating masteraccount: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error("Ocurrió un error, por favor contacte al administrador");
      return $this->response->redirect("subaccount/index/" .$saxs->Subaccount->idAccount);
    }
          
  }

  //FUNCION PARA ELIMINAR LA SUBCUENTA
  public function deletesubaccountAction($idSubaccount){
    try{
      $arrSaxsServices = array();
      if (!$idSubaccount) {
        $this->notification->error('Error: No se encontro la subcuenta que se desea eliminar');
        return $this->response->redirect("subaccount/index/".$this->user->Usertype->idAccount);
      }
      //INACTIVAMOS LA SUBCUENTA
      $subaccount = Subaccount::findfirst([
                  'conditions' => 'idSubaccount = ?0',
                  'bind' => array(0 => $idSubaccount)
      ]);
      $subaccount->status = 0;
      $subaccount->deleted = 1;
      if (!$subaccount->save()) {
        foreach ($subaccount->getMessages() as $msg) {
          throw new Exception($msg);
        }
      }
      //INACTIVAR LOS SERVICIOS DE LA SUBCUENTA
      $saxs = \Saxs::find(array(
          "conditions" => "idSubaccount = ?0 and status=1",
          "bind" => array($idSubaccount)
      ));
      if ($saxs) {
        foreach ($saxs as $value) {
          if($value->idServices==1 || $value->idServices==2 || $value->idServices==7){
            if($value->accountingMode != "contact"){
              array_push($arrSaxsServices, array(
                "idSaxs" => $value->idSaxs,
                "idService" => $value->idServices,
                "amount" => $value->amount,
                "idAccountConfig" => $subaccount->Account->AccountConfig->idAccountConfig
              ));
            }
          }
          $value->status = 0;
          if (!$value->save()) {
            foreach ($saxs->getMessages() as $msg) {
              throw new Exception($msg);
            }
          }
        }
      }
      //BUSCAMOS EL DETAIL CONFIG PARA ENVIAR EL SALDO DE LA SUBCUENTA A LA CUENTA
      $accountConfig = \AccountConfig::findfirst([
        'idAccount = ?0',
        'bind' => [$subaccount->idAccount]
      ]);
      
      for($i=0; $i < count($arrSaxsServices); $i++){
        
        $DetailConfig = \DetailConfig::findFirst(array(
            "conditions" => "idAccountConfig = ?0 AND idServices = ?1", 
            "bind" => array($arrSaxsServices[$i]["idAccountConfig"], $arrSaxsServices[$i]["idService"])
        ));
        if($DetailConfig->accountingMode != "contact"){
            $DetailConfig->amount = $DetailConfig->amount+$arrSaxsServices[$i]["amount"];
            if (!$DetailConfig->save()) {
                foreach ($DetailConfig->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
        }
        
      }
      //BUSCAMOS LOS USERTYPE POR SUBCUENTA
      $usertype = Usertype::find(
        array(
          "conditions" => "idSubaccount = ?0", "bind" => array($idSubaccount)
        )
      );
      foreach ($usertype as $value) {
        //BUSCAMOS LOS USUARIOS QUE PERTENEZCAN AL USERTYPE
        $user = User::findfirst([
          'conditions' => 'idUsertype = ?0',
          'bind' => array(0 => $value->idUsertype)
        ]);
        //INACTIVAMOS LOS USUARIOS
        $user->deleted = 1;
        if (!$user->save()) {
          foreach ($user->getMessages() as $msg) {
            throw new Exception($msg);
          }
        }
        //BUSCAMOS LAS APIKEY QUE ESTEN RELACIONADAS CON LOS USUARIOS
        $apikey = Apikey::findfirst([
          'conditions' => 'idUser = ?0 and status=1',
          'bind' => array(0 => $user->idUser)
        ]);
        if($apikey){
          $apikey->status = 0;
          if (!$apikey->save()) {
            foreach ($apikey->getMessages() as $msg) {
              throw new Exception($msg);
            }
          }
        }
        
      }
      $this->notification->warning("Se ha inacivado la subcuenta <strong>{$subaccount->name}</strong> exitosamente"); 
      return $this->response->redirect("subaccount/index/".$subaccount->idAccount);

    } catch (InvalidArgumentException $msg) {
      $this->notification->error($msg->getMessage());
      return $this->response->redirect("subaccount/index/".$subaccount->idAccount);
    } catch (Exception $e) {
      $this->trace("fail", $e->getTraceAsString());
      $this->logger->log("Exception while creating masteraccount: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error("Ocurrió un error, por favor contacte al administrador");
      return $this->response->redirect("subaccount/index/".$subaccount->idAccount);
    }
  }

}
