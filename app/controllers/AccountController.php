<?php

class AccountController extends ControllerBase {

  public $detailConfigAccount;
  public $typeRegister;
  public $status;

  public function initialize() {
    $this->tag->setTitle("Cuentas");
    parent::initialize();

    //$this->general = \Phalcon\DI::getDefault()->get('general');
    //$this->habeas_data =  $this->general->habeas_data;
    //var_dump($this->habeas_data);
  }

  public function getCities() {
    $this->cities = City::find();
  }

  public function getStates() {
    $this->states = State::find();
  }

  public function getCountries() {
    $this->countries = Country::find();
  }

  public function indexAction() {
    $detailConfigAllied = DetailConfig::find(array(
                "conditions" => "idAlliedconfig = ?0",
                "bind" => [0 => $this->user->Usertype->Allied->Alliedconfig->idAlliedconfig]
    ));

    foreach ($detailConfigAllied as $ser) {
      if ($ser->idServices == $this->services->sms) {
        $limitSmsAllied = $ser->amount;
      } else if ($ser->idServices == $this->services->sms_two_way) {
        $limitSmstwowayAllied = $ser->amount;
      } else if ($ser->idServices == $this->services->email_marketing) {
        $limitContactAllied = $ser->amount;
        $accountingModeAllied = $ser->accountingMode;
      } else if ($ser->idServices == $this->services->landing_page) {
        $limitContactAllied = $ser->amount;
        $accountingModeAllied = $ser->accountingMode;
      }
    }

    $this->view->setVar("limitSmsAllied", $limitSmsAllied);
    $this->view->setVar("limitSmstwowayAllied", $limitSmstwowayAllied);
    $this->view->setVar("limitLandingpageAllied", $limitLandingpageAllied);
    $this->view->setVar("limitContactAllied", $limitContactAllied);
    $this->view->setVar("accountingModeAllied", $accountingModeAllied);
    if ($this->request->isPost()) {
//
//      $this->getCities();
//      $this->getCountries();

      $msg = $this->session->get("msgSuccess");
      if (isset($msg) && $msg[0] == "info") {
        $this->notification->info($msg[1]);
        $this->session->remove("msgSuccess");
      } else if (isset($msg) && $msg[0] == "success") {
        $this->notification->success($msg[1]);
        $this->session->remove("msgSuccess");
      }

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $page = $data[0];
      $name = $data[1];
      $typeRegister = $data[2];
      $status = $data[3];

//      $currentPage = $this->request->getQuery('page', null, 1);
//      $currentPage = $page;
//      if ($this->user->Usertype->idSubaccount) {
//        $builder = $this->modelsManager->createBuilder()
//                ->from('Account')
//                ->join('Subaccount', 'Subaccount.idAccount = Account.idAccount')
//                ->where("Subaccount.idSubaccount = {$this->user->Usertype->idSubaccount} AND name = '%{$name}%'")
//                ->orderBy('Account.created DESC');
//      }
//
//      if ($this->user->Usertype->idAccount) {
//        $builder = $this->modelsManager->createBuilder()
//                ->from('Account')
//                ->where("Account.idAccount = {$this->user->Usertype->idAccount} AND name = '%{$name}%'")
//                ->orderBy('Account.created DESC');
//      }
//      if ($this->user->Usertype->idAllied) {
      $this->configAllied = Alliedconfig::findFirst(array("conditions" => "idAllied = ?0", "bind" => array(0 => $this->user->Usertype->idAllied)));
//        $builder = $this->modelsManager->createBuilder()
//                ->from('Account');
//            ->join('Axc', 'Axc.idAccount = Account.idAccount')
      //->where("Account.idAllied = {$this->user->Usertype->idAllied}");
      //->orderBy('Account.created DESC');
//      if ($page == 1) {
//        $page = 0;
//      }
//      if ($page != 0) {
//        $page = $page + 1;
//      }
//      if ($page > 1) {
//        $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
//      }
      (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");


  // var_dump("idAllied = ?0 AND name LIKE '%{$name}%' LIMIT ".\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT." OFFSET ".$page);
//        exit;
      if($typeRegister && $typeRegister!= "todosOrg"){
        if($status && $status!= "todosEst"){
          if($status == "activo"){
            $status = 1;
          }else if($status == "inactivo"){
            $status = 0;
          }
          $result = \Account::find(array(
            "conditions" => "idAllied = ?0 AND name LIKE '%{$name}%' AND registerType='{$typeRegister}' AND status={$status} ORDER BY created DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET " . $page,
            "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
          ));
          $total = \Account::count(array(
            "conditions" => "idAllied = ?0 AND name LIKE '%{$name}%' AND registerType='{$typeRegister}' AND status={$status}",
            "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
          ));
        }else{
          $result = \Account::find(array(
            "conditions" => "idAllied = ?0 AND name LIKE '%{$name}%' AND registerType='{$typeRegister}' ORDER BY created DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET " . $page,
            "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
          ));
          $total = \Account::count(array(
            "conditions" => "idAllied = ?0 AND name LIKE '%{$name}%' AND registerType='{$typeRegister}'",
            "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
          ));
        }
      }else{
        if($status && $status!= "todosEst"){
          if($status == "activo"){
            $status = 1;
          }else if($status == "inactivo"){
            $status = 0;
          }
          $result = \Account::find(array(
            "conditions" => "idAllied = ?0 AND name LIKE '%{$name}%' AND status={$status} ORDER BY created DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET " . $page,
            "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
          ));
          $total = \Account::count(array(
            "conditions" => "idAllied = ?0 AND name LIKE '%{$name}%' AND status={$status}",
            "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
          ));
        }else{
          $result = \Account::find(array(
            "conditions" => "idAllied = ?0 AND name LIKE '%{$name}%' ORDER BY created DESC LIMIT " . \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT . " OFFSET " . $page,
            "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
          ));
          $total = \Account::count(array(
            "conditions" => "idAllied = ?0 AND name LIKE '%{$name}%'",
            "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
          ));
        }
      }
//      $consult = array();
//      if (count($footer)) {
//        foreach ($footer as $key => $value) {
//          $consult[$key] = array(
//              "idFooter" => $value->idFooter,
//              "name" => $value->name,
//              "description" => $value->description,
//              "content" => $value->content,
//          );
//        }
//      }
      $myaccount = array();
      $allaccounts = array();
      
      $dt = new DateTime();
      $dt->setTimezone(new DateTimeZone('America/Bogota'));
      
      foreach ($result as $value) {
        $allaccounts["idAccount"] = $value->idAccount;
        $allaccounts["idAllied"] = $value->idAllied;
        $allaccounts["idPaymentPlan"] = $value->idPaymentPlan;
        $allaccounts["idAccountCategory"] = $value->idAccountCategory;
        $allaccounts["idCity"] = $value->idCity;
        $allaccounts["nit"] = $value->nit;
        $dt->setTimestamp($value->created);
        $allaccounts["created"] = $dt->format('d/m/Y G:i:sa');
        $dt->setTimestamp($value->updated);
        $allaccounts["updated"] = $dt->format('d/m/Y G:i:sa');
        $allaccounts["status"] = $value->status;
        $allaccounts["name"] = $value->name;
        $allaccounts["phone"] = $value->phone;
        $allaccounts["email"] = $value->idAccount;
        $allaccounts["address"] = $value->address;
        $allaccounts["createdBy"] = $value->createdBy;
        $allaccounts["updatedBy"] = $value->updatedBy;
        $allaccounts["registerType"] = $value->registerType;

//        foreach ($this->cities as $city){
//          if($city->idCity == $value->idCity){
//            $allaccounts["cityName"] = $city->name;
//            foreach ($this->states as $state){
//              if($city->idState == $state->idState){
//                $allaccounts["stateName"] = $state->name;
//              }
//            }
//            foreach ($this->countries as $country){
//              if($country->idCountry == $country->idCountry){
//                $allaccounts["countryName"] = $country->name;
//              }
//            }
//          }
//        }
        array_push($myaccount, $allaccounts);
      }
      unset($dt);

      $accounts = array("total" => $total, "total_pages" => ceil($total / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT), "items" => $myaccount);
//      }
//      $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
//          "builder" => $builder,
//          "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
//          "page" => $currentPage
//      ));
//
//      $services = Services::find();
//
//      $accounts = $paginator->getPaginate();
//
//      $this->view->setVar("page", $page);
      $this->view->setVar("services", $services);
      
      return $this->set_json_response(["accounts" => $accounts, "configAllied" => $this->configAllied], 200);
    }
  }

  public function createAction() {
    $this->db->begin();
    $allied = $this->user->Usertype->Allied;
    $accountform = new AccountForm();

    $this->view->setVar('accountform', $accountform);
    $this->view->setVar('allied', $allied);
    try {
      $dataJson = $this->request->getRawBody();
      $data2 = json_decode($dataJson, true);

      if (!empty($data2)) {
        $account = new Account();
        $accountform->bind($data2, $account);
        if (!$accountform->isValid()) {
          foreach ($accountform->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }

        if (!is_numeric($data2['city'])) {
          throw new InvalidArgumentException("La ciudad es de caracter obligatorio");
        }
        if (empty($data2['idAccountCategory'])) {
          throw new \InvalidArgumentException("Debes seleccionar una categoría");
        }
        if (isset($data2["url"]) && !empty($data2["url"]) && !filter_var($data2["url"], FILTER_VALIDATE_URL)) {
          throw new \InvalidArgumentException("La url no tiene un formato valido, por favor valide la información.");
        }
        if ($data2['hourInit'] > $data2['hourEnd']) {
          throw new \InvalidArgumentException("La hora inicial no puede ser mayor a la hora final, por favor valide la información.");
        }
        $account->idCity = $data2['city'];
        $account->idAllied = $allied->idAllied;
        $account->idPaymentPlan = $data2['idPaymentPlan'];
        $account->idAccountCategory = $data2['idAccountCategory'];
        //$account->attachments = $data2['attachments'] ? 1 : 0;
        $account->tolerancePeriod = (!empty($data2['tolerance'])) ? $data2['tolerance'] : '';
        $account->url = (!empty($data2['url'])) ? $data2['url'] : '';
        $account->hourInit = $data2['hourInit'];
        $account->hourEnd = $data2['hourEnd'];
        $account->habeasData = $data2['habeasData'];
        $account->idMta = $data2['mta'];
        $account->showMta = $data2['showMta'];


        $alliedConfig = Alliedconfig::findFirst(array(
                    "conditions" => "idAllied = ?0",
                    "bind" => [0 => $allied->idAllied]
        ));

        $detailConfigAllied = DetailConfig::find(array(
                    "conditions" => "idAlliedconfig = ?0",
                    "bind" => [0 => $alliedConfig->idAlliedconfig]
        ));

        $paymentPlanxService = PaymentPlanxservice::find(array(
                    "conditions" => "idPaymentPlan = ?0",
                    "bind" => [0 => $data2['idPaymentPlan']]
        ));

        if (!$paymentPlanxService) {
          throw new InvalidArgumentException("El plan seleccionado no posee configuración");
        }

        if (!$account->save()) {
          $this->db->rollback();
          foreach ($account->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se logro crear una cuenta");
        }
        $this->createCategories($account->idAccount);
        $accountConfig = new AccountConfig();
        $accountConfig->idAccount = $account->idAccount;
        $accountConfig->diskSpace = $paymentPlanxService[0]->PaymentPlan->diskSpace;
        $accountConfig->idFooter = $data2['idFooter'];
        $accountConfig->footerEditable = $data2['footerEditable'];
        $accountConfig->senderAllowed = $data2['senderAllowed'];
        if (!empty($data2['expiryDate'])) {
          $date = explode("T", $data2['expiryDate']);
          $accountConfig->expiryDate = $date[0];
        }

        if (!$accountConfig->save()) {
          foreach ($accountConfig->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }

        foreach ($detailConfigAllied as $key => $configAllied) {
          if (count($paymentPlanxService) == 1) {
            //$this->createOnePlan($paymentPlanxService, $configAllied, $accountConfig);
            $this->createOnePlan($paymentPlanxService, $configAllied, $accountConfig, $account->idMta);
          } else {
            //$this->selectTwoPlan($paymentPlanxService, $key, $configAllied, $accountConfig);
            $this->selectTwoPlan($paymentPlanxService, $key, $configAllied, $accountConfig, $account->idMta);
          }
        }

        $alliedConfig->diskSpace = $alliedConfig->diskSpace - $this->detailConfigAccount->AccountConfig->diskSpace;

        if (!$alliedConfig->save()) {
          foreach ($alliedConfig->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        
        /*$detailConfigAllied = DetailConfig::FindFirst(array(
                    "conditions" => "idAccountConfig = ?0 AND idServices = ?1",
                    "bind" => [0 => $accountConfig->idAccountConfig, 1 => 2]
        ));*/
        
        //Si el plan escogido tiene mail con $account->showMta se asigna un mta
//        if ($account->showMta) {
        /*if ($account->idMta!=0&&$account->idMta!=""&&$account->idMta!=null) {
          if ($detailConfigAllied != null || $detailConfigAllied != false) {
            $arrayDetailConfigAllied = $detailConfigAllied->toArray();
            $arrayFindDcxmt = array('conditions' => 'idDetailConfig = ?0', 'bind' => [0 => (int) $arrayDetailConfigAllied['idDetailConfig']]);
            
            $idUser = $this->session->get('idUser');
            $user = User::findFirst(array(
                        "conditions" => "idUser = ?1",
                        "bind" => array(1 => $idUser)
            ));

            $dcxmta = new \Dcxmta();
            $dcxmta->idDetailConfig = $detailConfigAllied->idDetailConfig;
            $dcxmta->idMta = $account->idMta;
            $dcxmta->created = time();
           $dcxmta->updated = time();
            $dcxmta->createdBy = $user->email;
            $dcxmta->updatedBy = $user->email;

            if (!$dcxmta->save()) {
              foreach ($dcxmta->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
              }
            }
          }
        }
        else{
          if ($detailConfigAllied != null || $detailConfigAllied != false) {
            $dcxmta = new Dcxmta();
            $dcxmta->idDetailConfig = $detailConfigAllied->idDetailConfig;
            $dcxmta->idMta = \Phalcon\DI::getDefault()->get('mtaDefault')->idMtaDefault;
            $dcxmta->created = time();
            $dcxmta->updated = time();
            $dcxmta->createdBy = $user->email;
            $dcxmta->updatedBy = $user->email;
          }
          else{
            throw new \InvalidArgumentException("La cuenta no tiene una configuración en la cuenta");
          }
        }*/

        $this->db->commit();
        $this->notification->success("Se ha creado la cuenta correctamente!");
        return $this->set_json_response(array("idAccount" => $account->idAccount), 200, "OK");
      }
    } catch (InvalidArgumentException $msg) {
      $this->db->rollback();
      $arr[] = $msg->getMessage();
      return $this->set_json_response($arr, 400, "FAIL");
    } catch (Exception $e) {
      $this->db->rollback();
      $this->logger->log("Exception while creating account: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
      return $this->set_json_response($e->getMessage(), 409, "FAIL");
    }
  }

  public function createCategories($idAccount) {
    $categories = array("Promociones", "Noticias");
    foreach ($categories as $category) {
      $mailCategory = new \MailCategory();
      $mailCategory->idAccount = $idAccount;
      $mailCategory->name = $category;
      if (!$mailCategory->save()) {
        $this->db->rollback();
        foreach ($mailCategory->getMessages() as $message) {
          throw new \InvalidArgumentException("No se puedo crear las categorías de correo");
        }
      }
      $smsCategory = new \SmsCategory();
      $smsCategory->idAccount = $idAccount;
      $smsCategory->name = $category;
      if (!$smsCategory->save()) {
        $this->db->rollback();
        foreach ($smsCategory->getMessages() as $message) {
          throw new \InvalidArgumentException("No se puedo crear las categorías de SMS");
        }
      }
      $automaticCampaignCategory = new \AutomaticCampaignCategory();
      $automaticCampaignCategory->idAccount = $idAccount;
      $automaticCampaignCategory->name = $category;
      $automaticCampaignCategory->status = 1;
      if (!$automaticCampaignCategory->save()) {
        $this->db->rollback();
        foreach ($automaticCampaignCategory->getMessages() as $message) {
          throw new \InvalidArgumentException("No se puedo crear las categorías de campañas automaticas");
        }
      }
      $formCategory = new \FormCategory();
      $formCategory->idAccount = $idAccount;
      $formCategory->name = $category;
      if (!$formCategory->save()) {
        $this->db->rollback();
        foreach ($formCategory->getMessages() as $message) {
          throw new \InvalidArgumentException("No se puedo crear las categorías de campañas automaticas");
        }
      }
      $surveyCategory = new \SurveyCategory();
      $surveyCategory->idAccount = $idAccount;
      $surveyCategory->name = $category;
      $surveyCategory->status = 1;
      if (!$surveyCategory->save()) {
        $this->db->rollback();
        foreach ($surveyCategory->getMessages() as $message) {
          throw new \InvalidArgumentException("No se puedo crear las categorías de campañas automaticas");
        }
      }
      $contactlistCategory = new \ContactlistCategory();
      $contactlistCategory->idAccount = $idAccount;
      $contactlistCategory->name = $category;
      if (!$contactlistCategory->save()) {
        $this->db->rollback();
        foreach ($contactlistCategory->getMessages() as $message) {
          throw new \InvalidArgumentException("No se puedo crear las categorías de campañas automaticas");
        }
      }
    }
  }

  public function editAction($idAccount) {
    //var_dump("You're here");exit;
    $flag = false;
    if ($this->user->Role->idRole == -1) {
      $flag = true;
    } else {
      foreach ($this->user->Usertype->Allied->Account as $key) {
        if ($key->idAccount == $idAccount) {
          $flag = true;
        }
      }
    }
    if ($flag == true) {
      $account = Account::findFirst(array(
                  "conditions" => "idAccount = ?1",
                  "bind" => array(1 => $idAccount)
      ));
//      var_dump($account);
//      exit();
    }

    if (!$account) {
      $this->notification->error("La cuenta que intentas editar no existe");
      return $this->response->redirect('account');
    }

    $accounCategory = AccountCategory::findFirst([
                'conditions' => 'idAccountCategory = ' . $account->idAccountCategory
    ]);
    $category = [
        'idAccountCategory' => $account->idAccountCategory,
        'name' => $accounCategory->name,
        'expirationDate' => $accounCategory->expirationDate
    ];
    $form = new AccountForm($account);
    $paymentPlan = PaymentPlan::findFirst([
                'conditions' => 'idPaymentPlan = ' . $account->idPaymentPlan
    ]);

    $arrServ = array();
    $arr = array("idPaymentPlan" => $account->idPaymentPlan, "name" => $paymentPlan->name,
        "diskSpace" => $paymentPlan->diskSpace);


    $detailConfigAllied = DetailConfig::find(array(
                "conditions" => "idAlliedconfig = ?0",
                "bind" => [0 => $account->Allied->Alliedconfig->idAlliedconfig]
    ));

    foreach ($detailConfigAllied as $detailConfig) {
      foreach ($paymentPlan->PaymentPlanxservice as $index => $services) {
        if ($detailConfig->idServices == $services->idServices) {
          $arrServ[$index]['amountConfig'] = $detailConfig->amount;
          $arrServ[$index]['totalAmount'] = $detailConfig->amount - $services->amount;
          $arrServ[$index]['service'] = $services->Services->name;
          $arrServ[$index]['amount'] = $services->amount;
          $arrServ[$index]['accountingMode'] = $services->accountingMode;
        }
      }
    }

    $arr['planxservice'] = $arrServ;

    $this->view->setVar('form', $form);
    $this->view->setVar('account', $account);
    $this->view->setVar('hourInit', $account->hourInit);
    $this->view->setVar('hourEnd', $account->hourEnd);
    $this->view->setVar('category', json_encode($category));
    $this->view->setVar('paymentPlan', json_encode($arr));

    try {
      $dataJson = $this->request->getRawBody();
      $data2 = json_decode($dataJson, true);


      if (!empty($data2)) {
        $this->db->begin();
        $account->idCity = $data2['city'];
        $account->idAccountCategory = $data2['idAccountCategory'];
        $account->hourInit = $data2['hourInit'];
        $account->hourEnd = $data2['hourEnd'];
        //$account->attachments = $data2['attachments'] ? 1 : 0;
        if(isset($data2['habeasdata'])){
            $account->habeasData = $data2['habeasdata'];
        }        
        $form->bind($data2, $account);
        
        if (isset($data2["url"]) && !empty($data2["url"]) && !filter_var($data2["url"], FILTER_VALIDATE_URL)) {
          throw new \InvalidArgumentException("La url no tiene un formato valido, por favor valide la información.");
        }
        if ($data2['hourInit'] > $data2['hourEnd']) {
          throw new \InvalidArgumentException("La hora inicial no puede ser mayor a la hora final, por favor valide la información.");
        }

        if (!$form->isValid()) {
          foreach ($form->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }

        //$account->tolerancePeriod = $data2['tolerancePeriod'];
        if ($account->idPaymentPlan != $data2['idPaymentPlan']) {

          if (!$data2['validateConfirm']) {
            $string = "Antes de continuar tenga en cuenta que los servicios de las subcuentas que pertenecen a esta cuenta serán eliminados junto con los contactos y tendrá que volver a configurar todo";
            throw new \Sigmamovil\General\Exceptions\ValidatePlanException($string, 409);
          }

          $arrayServices = array();
          foreach ($account->AccountConfig->DetailConfig as $index => $detailConfig) {
            if ($detailConfig->idServices == $this->services->sms) {
              $adapter = $this->returnNameAdapter($detailConfig);
              $adapter = trim($adapter, ", ");

              $this->createHistoryPaymentPlan($detailConfig->idAccountConfig, $account->idPaymentPlan, null, "Sms", null, $adapter, null, $detailConfig->amount, null, null, $detailConfig->totalAmount);
            } else if ($detailConfig->idServices == $this->services->sms_two_way){
              
            } else if ($detailConfig->idServices == $this->services->landing_page) {
              $adapter = $this->returnNameAdapter($detailConfig);
              $adapter = trim($adapter, ", ");

              $this->createHistoryPaymentPlan($detailConfig->idAccountConfig, $account->idPaymentPlan, null, "Landing page", null, $adapter, null, $detailConfig->amount, null, null, $detailConfig->totalAmount);
            } else if ($detailConfig->idServices == $this->services->email_marketing) {
              $mailClass = $this->returnNameMailclass($detailConfig);
              $mailClass = trim($mailClass, ", ");
              $mta = $this->returnNameMta($detailConfig);
              $mta = trim($mta, ", ");
              $urldomain = $this->returnNameUrldomain($detailConfig);
              $urldomain = trim($urldomain, ", ");

              $this->createHistoryPaymentPlan($detailConfig->idAccountConfig, $account->idPaymentPlan, $mta, "Email marketing", $mailClass, null, $urldomain, null, $detailConfig->amount, $detailConfig->totalAmount, null);
            }

            $arrayServices[$index]['idDetailConfig'] = $detailConfig->idDetailConfig;
            $arrayServices[$index]['idServices'] = $detailConfig->idServices;
            $arrayServices[$index]['consumed'] = $detailConfig->totalAmount - $detailConfig->amount;
          }

          $paymentPlanxService = PaymentPlanxservice::find(array(
                      "conditions" => "idPaymentPlan = ?0",
                      "bind" => [0 => $data2['idPaymentPlan']]
          ));

          if (!$paymentPlanxService) {
            throw new InvalidArgumentException("El plan seleccionado no posee configuración");
          }

          foreach ($detailConfigAllied as $detailConfig) {
            foreach ($paymentPlan->PaymentPlanxservice as $index => $services) {
              if ($detailConfig->idServices == $services->idServices) {
                $detailConfig->amount += $services->amount;
                if (!$detailConfig->save()) {
                  foreach ($detailConfig->getMessages() as $message) {
                    throw new Exception($message);
                  }
                }
              }
            }
          }

          $alliedconfig = $account->Allied->Alliedconfig;
          $alliedconfig->diskSpace += $account->AccountConfig->diskSpace;
          if (!$alliedconfig->save()) {
            foreach ($alliedconfig->getMessages() as $message) {
              throw new Exception($message);
            }
          }

          $accountConfig = $account->AccountConfig;
          foreach ($detailConfigAllied as $key => $configAllied) {
            if (count($paymentPlanxService) == 1) {
              $this->createOnePlan($paymentPlanxService, $configAllied, $accountConfig);
            } else {
              $this->selectTwoPlan($paymentPlanxService, $key, $configAllied, $accountConfig);
            }
          }

          $this->deleteDcx($arrayServices);
          $time = time();
          $sql = "UPDATE cxcl SET deleted={$time}, active = 0 WHERE idContactlist IN (SELECT c.idContactlist FROM account AS a
	LEFT JOIN subaccount AS s ON s.idAccount = a.idAccount 
    LEFT JOIN contactlist AS c ON c.idSubaccount = s.idSubaccount 
    WHERE a.idAccount = {$idAccount})";
          $this->db->execute($sql);

          $sql = "CALL updateCountersGlobal()";
          $this->db->execute($sql);
          /* $sql1 = "CALL updateCountersAccount({$idAccount})";
            $this->db->fetchAll($sql1); */

          $account->idPaymentPlan = $data2['idPaymentPlan'];
        }
        
        /*$subaccount = $account->Subaccount;
        foreach ($subaccount as $item) {
        $this->deleteServicesSaxs($item->Saxs);
        }*/
        $subaccount = Subaccount::find(array(
            "conditions" => "idAccount = ?0 ",
            "bind" => array($account->idAccount))
        );
        if($account->status){
            $statusDinamic = 1;
        }else{
            $statusDinamic = 0;
        }
        
        foreach($subaccount as $valueSub){
            $saxs = Saxs::find(array(
                "conditions" => "idSubaccount = ?0",
                "bind" => array($valueSub->idSubaccount)
             ));
            foreach($saxs as $valueSaxs){
                $valueSaxs->status = $statusDinamic;
                $valueSaxs->created = time();
                $valueSaxs->updated = time();
                if (!$valueSaxs->save()) {
                  foreach ($valueSaxs->getMessages() as $message) {
                    throw new Exception($message);
                  }
                }
            }
            $valueSub->status = $statusDinamic;
            $valueSub->created = time();
            $valueSub->updated = time();
            if (!$valueSub->save()) {
              foreach ($valueSub->getMessages() as $message) {
                throw new Exception($message);
              }
            }
        }
        $account->created = time();
        $account->updated = time();
        $account->createdBy = $this->user->email;
        $account->updatedBy = $this->user->email;
        if (!$account->save()) {
          foreach ($account->getMessages() as $message) {
            throw new Exception($message);
          }
        }
        $accountConfig = $account->AccountConfig;

        $accountConfig->idFooter = $data2['idFooter'];
        $accountConfig->footerEditable = $data2['footerEditable'];
        $accountConfig->senderAllowed = $data2['senderAllowed'];
        if (!empty($data2['expiryDate'])) {
          $date = explode("T", $data2['expiryDate']);
          $accountConfig->expiryDate = $date[0];
        } else {
          $accountConfig->expiryDate = null;
        }

        if (!$accountConfig->save()) {
          foreach ($accountConfig->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        
        //Para poder editar el mta de la cuenta
        $detailConfigAllied = DetailConfig::FindFirst(array(
                   "conditions" => "idAccountConfig = ?0 AND idServices = ?1",
                    "bind" => [0 => $accountConfig->idAccountConfig, 1 => 2]
        ));
        if ($data2['showMta']) {
          if ($detailConfigAllied != null || $detailConfigAllied != false) {
           $arrayDetailConfigAllied = $detailConfigAllied->toArray();
            $arrayFindDcxmt = array('conditions' => 'idDetailConfig = ?0', 'bind' => [0 => (int) $arrayDetailConfigAllied['idDetailConfig']]);
            $dcxmtaEdit = Dcxmta::FindFirst($arrayFindDcxmt);
           $dcxmtaEdit->idMta = $data2['mta'];

            if (!$dcxmtaEdit->save()) {
              foreach ($dcxmtaEdit->getMessages() as $message) {
               throw new \InvalidArgumentException($message);
              }
            }
          }
        }

        $this->db->commit();

        $this->notification->info("La cuenta se actualizo correctamente");
        return $this->set_json_response(array("La cuenta se actualizo correctamente"), 200, "OK");
      }
    } catch (\Sigmamovil\General\Exceptions\ValidatePlanException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while edit account... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage(), "code" => $ex->getCode()), 409);
    } catch (InvalidArgumentException $msg) {
      $this->db->rollback();
      $arr[] = $msg->getMessage();
      $arr['code'] = 403;
      return $this->set_json_response($arr, 403);
    } catch (Exception $e) {
      $this->db->rollback();
      $this->logger->log("Exception while edit account: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      //$this->notification->error($e->getMessage());
      return $this->set_json_response($e->getMessage(), 500, "FAIL");
    }
  }

//    public function deleteAction($idAccount)
//    {
//        $this->logger->log("Id {$idAccount}");
//        $account = Account::findFirst(array(
//            "conditions" => "idAccount = ?1",
//            "bind" => array(1 => $idAccount)
//        ));
//
//        if($account->delete()){
//            $this->notification->warning("Se ha eliminado la Cuenta correctamente!");
//            $this->trace("success", "Se elimino una cuenta con ID: {$account->idAccount}");
//            return $this->response->redirect('account/index');
//        }
//        else {
//            $this->notification->error("Lo sentimos, ocurrio un error durante la elmiminación de la Cuenta");
//            $this->trace("fail", "No se logro eliminar la cuenta: {$account->idAccount}");
//            return $this->response->redirect('account/index');
//        }
//    }

  public function accountconfigeditAction($idAccountclassification) {
    $flag = false;
    if ($this->user->Role->idRole == -1) {
      $flag = true;
    } else {
      foreach ($this->user->Usertype->Allied->Account as $key) {
        if ($key->idAccountclassification == $idAccountclassification) {
          $flag = true;
        }
      }
    }
    if ($flag == true) {
      $account = Accountclassification::findFirst(array(
                  "conditions" => "idAccountclassification = ?1",
                  "bind" => array(1 => $idAccountclassification)
      ));

      $alliedConfig = Alliedconfig::findFirst(array(
                  "conditions" => "idAllied = ?1",
                  "bind" => array(1 => $this->user->Usertype->idAllied)
      ));
    }

    if (!$account) {
      $this->notification->error('La clasificación de cuenta que desea editar no existe, por favor valide la información');
      return $this->response->redirect('account');
    }

    $form = new AccountclassificationForm($account);
    $this->view->setVar('account_form', $form);
    $this->view->setVar('alliedConfig', $alliedConfig);
    $this->view->setVar('account', $account);

    try {
      if ($this->request->isPost()) {
        $form->bind($this->request->getPost(), $account);
        $accoutingManager = new \Sigmamovil\General\Misc\AccountingManager();
        $this->db->begin();
        $accoutingManager->accountConfigEdit($account, $alliedConfig);
        $this->db->commit();
        $this->notification->info("La configuración de la Cuenta se actualizo correctamente");
        $this->trace("success", "Se edito la clasificación de cuenta: {$account->idAccountclassification}/{$account->name}");
        return $this->response->redirect('account/show/' . $account->account[0]->idAccount);
      }
    } catch (InvalidArgumentException $msg) {
      $this->notification->error($msg->getMessage());
    } catch (Exception $e) {
      $this->logger->log("Exception while creating account: {$e->getMessage()}");
      $this->logger->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function userlistAction($id) {
    $currentPage = $this->request->getQuery('page', null, 1);

    $flag = false;
    if ($this->user->Role->idRole == -1) {
      $flag = true;
    } else if ($this->user->Usertype->Account->idAccount == $id) {
      $flag = true;
    } else {
      foreach ($this->user->Usertype->Allied->Account as $key) {
        if ($key->idAccount == $id) {
          $flag = true;
        }
      }
    }
    if ($flag == true) {
      $builder = $this->modelsManager->createBuilder()
              ->from('User')
              ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
              ->where("Usertype.idAccount  = {$id} AND User.deleted = 0");
      //->orderBy('User.created');
    }

    if (!$builder) {
      $this->notification->error("No tienes permiso para esta accion");
      return $this->response->redirect('account');
    }

    $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
        "builder" => $builder,
        "limit" => 15,
        "page" => $currentPage
    ));
        
    $page = $paginator->getPaginate();
    $this->view->setVar("page", $page);
    $this->view->setVar("idAccount", $id);
  }

  public function usercreateAction($idAccount) {
    $user = new User();

    $form = new UserForm($user, $this->user->role);

    $account = Account::findFirst(array(
                'conditions' => 'idAccount = ?1',
                'bind' => array(1 => $idAccount)
    ));

    if (!$account) {
      $this->notification->error("La cuenta enviada no existe, por favor verifique la información");
      return $this->response->redirect("account/userlist");
    }

    $dataJson = $this->request->getRawBody();
    $data = json_decode($dataJson, true);
    //var_dump($data);
    if (!empty($data)) {
      $userManager = new \Sigmamovil\General\Misc\UserManager();
      try {
        $userManager->creataAccountUser($data, $idAccount);
        $this->notification->success('Se ha creado el usuario exitosamente en la cuenta <strong>' . $account->name . '</strong>');
        //return $this->response->redirect("account/userlist/{$account->idAccount}");
        return $this->set_json_response(array("idAccount" => $account->idAccount), 200, 'OK');
      } catch (InvalidArgumentException $msg) {
        return $this->set_json_response(array($msg->getMessage()), 401, 'error');
      } catch (Exception $ex) {
        $this->logger->log("Exception: {$ex->getMessage()}");
        $this->logger->log("{$ex->getTraceAsString()}");
        return $this->set_json_response(array("Ocurrió un error, por favor contacte al administrador"), 500, 'error');
      }
    }

    $this->view->UserForm = $form;
    $this->view->setVar('account', $account);
  }

  public function usereditAction($id) {
    $userEdit = User::findFirst(array(
                "conditions" => "idUser = ?1",
                "bind" => array(1 => $id)
    ));

    if (!$userEdit) {
      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("account/userlist");
    }

    $obj = new stdClass();
    $obj->name = 'sudo';
    $this->view->setVar("userEdit", $userEdit);
    $form = new UserForm($userEdit, $obj);
    if ($this->request->isPost()) {
      $form->bind($this->request->getPost(), $userEdit);
      $cel = $form->getValue('cellphone');

      $email = strtolower($form->getValue('email'));
      $userEdit->email = $email;
      $userEdit->cellphone = $cel;
      if ($userEdit->save()) {
        $this->notification->info('Se ha editado exitosamente el usuario <strong>' . $userEdit->name . " " . $userEdit->lastname . '</strong>');
        $this->trace("success", "Se edito un usuario con ID: {$userEdit->idUser}");
        return $this->response->redirect("account/userlist/{$userEdit->UserType->idAccount}");
      } else {
        $userEdit->username = $username;
        foreach ($userEdit->getMessages() as $message) {
          $this->notification->error($message);
        }
        $this->trace("fail", "No se edito el usuario con ID: {$userEdit->idUser}");
      }
    }
    $this->view->UserForm = $form;
  }

  public function userdeleteAction($id) {
    $idUser = $this->session->get('idUser');

    if ($id == $idUser) {
      $this->notification->error("No se puede eliminar el usuario que esta actualmente en sesión, por favor verifique la información");
      $this->trace('fail', "Se intento borrar un usuario en sesión: {$idUser}");
      return $this->response->redirect("account/userlist/{$this->user->account->idAccount}");
    }

    $user = User::findFirst(array(
                "conditions" => "idUser = ?1",
                "bind" => array(1 => $id)
    ));
    $email = $user->email;
    if (!$user) {
      $this->notification->error("El usuario que ha intentado eliminar no existe, por favor verifique la información");
      $this->trace('fail', "El usuario no existe: {$idUser}");
      return $this->response->redirect("account/userlist");
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
        return $this->response->redirect("account/userlist/{$user->UserType->idAccount}");
      } 
    }
    $this->notification->warning("Se ha eliminado el usuario <strong>{$user->username}</strong> exitosamente");
    $this->trace('success', "Se elimino el usuario: {$id}");
    return $this->response->redirect("account/userlist/{$user->UserType->idAccount}");
   /* 
    $user->deleted = time();
    $email = str_replace("@", "_1@", $user->email);
    $user->email = $email;
    $user->deletedBy = \Phalcon\DI::getDefault()->get('user')->email;
    if (!$user->update()) {
      foreach ($user->getMessages() as $msg) {
        $this->notification->error($msg);
        $this->logger->log("Error while deleting user {$msg}, user: {$user->idUser}/{$user->username}");
      }
      return $this->response->redirect("account/userlist/{$user->UserType->idAccount}");
    } else {
      $this->notification->warning("Se ha eliminado el usuario <strong>{$user->username}</strong> exitosamente");
      $this->trace('success', "Se elimino el usuario: {$id}");
      return $this->response->redirect("account/userlist/{$user->UserType->idAccount}");
    } */
  }

  public function subaccountlistAction($idAccount) {
    $currentPage = $this->request->getQuery('page', null, 1);

    $paginator = new Phalcon\Paginator\Adapter\Model(array(
        "data" => Subaccount::find(array(
            "conditions" => "idAccount = ?1",
            "bind" => array(1 => $idAccount)
        )),
        "limit" => 15,
        "page" => $currentPage
    ));

    $page = $paginator->getPaginate();

    $this->view->setVar("page", $page);
    $this->view->setVar("idAccount", $idAccount);
  }

  public function subaccountcreateAction($idAccount) {
    $subaccount = new Subaccount();
    $subaccountForm = new SubaccountForm();

    $account = Account::findFirst(array(
                'conditions' => 'idAccount = ?1',
                'bind' => array(1 => $idAccount)
    ));

    if (!$account) {
      $this->notification->error("La cuenta enviada no existe, por favor verifique la información");
      return $this->response->redirect("account/subaccountlist");
    }

    try {
      if ($this->request->isPost()) {
        $subaccountForm->bind($this->request->getPost(), $subaccount);

        $name = $this->request->getPost("name");
        $status = $this->request->getPost("status");
        $prefix = $this->request->getPost("prefix");

        $p = $this->validatePrefix($name, $prefix);

        $subaccount->name = $name;
        $subaccount->idAccount = $account->idAccount;
        $subaccount->status = (empty($status) ? 0 : 1);
        $subaccount->prefix = $p;
        $subaccount->created = time();
        $subaccount->updated = time();

        if (!$subaccount->save()) {
          foreach ($subaccount->getMessages() as $message) {
            throw new Exception($message);
          }
          $this->trace("fail", "No se creo la subcuenta en la cuenta maestra");
        } else {
          $this->notification->success('Se ha creado la subcuenta exitosamente en la cuenta <strong>' . $account->name . '</strong>');
          $this->trace("success", "Se creo una subcuenta con ID: {$subaccount->idSubaccount}");
          return $this->response->redirect("account/subaccountlist/{$account->idAccount}");
        }
      }

      $this->view->setVar("subaccountForm", $subaccountForm);
      $this->view->setVar("account", $account);
    } catch (Exception $e) {
      $this->notification->error($e->getMessage());
    }
  }

  public function subaccounteditAction($idSubaccount) {
    $subaccount = Subaccount::findFirst(array(
                'conditions' => 'idSubaccount = ?1',
                'bind' => array(1 => $idSubaccount)
    ));

    if (!$subaccount) {
      $this->notification->error("La subcuenta que intenta modificar no existe, por favor verifique la información");
    }

    $subaccountForm = new SubaccountForm($subaccount);
    $this->view->setVar("subaccountForm", $subaccountForm);
    $this->view->setVar("subaccount", $subaccount);

    try {
      if ($this->request->isPost()) {
        $subaccountForm->bind($this->request->getPost(), $subaccount);

        $name = $this->request->getPost("name");
        $status = $this->request->getPost("status");
        $prefix = $this->request->getPost("prefix");

        $p = $this->validatePrefix($name, $prefix);

        $subaccount->name = $name;
        $subaccount->status = (empty($status) ? 0 : 1);
        $subaccount->prefix = $p;
        $subaccount->updated = time();

        if (!$subaccount->save()) {
          foreach ($subaccount->getMessages() as $message) {
            throw new Exception($message);
          }
          $this->trace("fail", "No se edito la subcuenta en la cuenta maestra");
        } else {
          $this->notification->success('Se ha editado exitosamente la subcuenta <strong>' . $subaccount->name . '</strong>');
          $this->trace("success", "Se edito una subcuenta con ID: {$subaccount->idSubaccount}");
          return $this->response->redirect("account/subaccountlist/{$subaccount->idAccount}");
        }
      }
    } catch (Exception $e) {
      $this->notification->error($e->getMessage());
    }
  }

  public function subaccountdeleteAction($idSubaccount) {
    $subaccount = Subaccount::findFirst(array(
                'conditions' => 'idSubaccount = ?1',
                'bind' => array(1 => $idSubaccount)
    ));

    if (!$subaccount) {
      $this->notification->error("La subcuenta que intenta eliminar no existe, por favor verifique la información.");
      $this->trace('fail', "La subcuenta no existe: {$idSubaccount}");
    }

    try {
      if (!$subaccount->delete()) {
        foreach ($subaccount->getMessages() as $msg) {
          throw new Exception("Ha ocurrido un error, por favor contacte a el administrador.");
          $this->logger->log("Error while deleting subaccount: {$msg}");
        }
        return $this->response->redirect("account/subaccountlist/{$subaccount->idAccount}");
      } else {
        $this->notification->warning("Se ha eliminado la subcuenta exitosamente");
        $this->trace('success', "Se elimino la subcuenta: {$idSubaccount}");
        return $this->response->redirect("account/subaccountlist/{$subaccount->idAccount}");
      }
    } catch (Exception $e) {
      $this->notification->error($e->getMessage());
    }
  }

  public function passeditAction($id) {

    $editUser = User::findFirst(array(
                "conditions" => "idUser = ?1",
                "bind" => array(1 => $id)
    ));

    if (!$editUser) {
      $this->notification->error("El usuario que intenta editar no existe, por favor verifique la información");
      return $this->response->redirect("account/index");
    }

    $account = $editUser->account;
    $this->view->setVar("userE", $editUser);

    if ($this->request->isPost()) {
//echo $editUser->name;
//      exit;
      $pass = $this->request->getPost('pass1');
      $pass2 = $this->request->getPost('pass2');
      try {
        if ((empty($pass) || empty($pass2))) {
          throw new InvalidArgumentException("El campo Contraseña esta vacío, por favor valide la información");
        }
        if ($pass == $pass2) {
          throw new InvalidArgumentException("Las contraseñas no coinciden");
        }
      } catch (InvalidArgumentException $msg) {
        $this->notification->warning("Se ha eliminado la subcuenta exitosamente");
        $this->trace('success', "Se elimino la cuenta: {}");
//        return $this->response->redirect("account/subaccountlist/{$subaccount->idAccount}");
      } catch (Exception $exc) {
        echo $exc->getTraceAsString();
      }

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
            $editUser->updated = time();

            if (!$editUser->save()) {
              foreach ($editUser->getMessages() as $message) {
                $this->notification->error($message);
              }
              $this->trace("fail", "No se edito la contraseña del usuario con ID: {$editUser->idUser}");
            } else {
              $this->notification->success('Se ha editado la contraseña exitosamente del usuario <strong>' . $editUser->name . '</strong>');
              $this->trace("sucess", "Se edito la contraseña del usuario con ID: {$editUser->idUser}");
              if ($this->user->userType->name == "Allied") {
                return $this->response->redirect("account/userlist/{$editUser->userType->idAccount}");
              }
            }
          }
        }
      }
    }
  }

  protected function saveSendingcategory(Account $account) {
    $scategory = new Sendingcategory();

    $scategory->account = $account;
    $scategory->name = "Por defecto";
    $scategory->description = "Categoria por defecto";
    $scategory->created = time();
    $scategory->updated = time();

    if (!$scategory->save()) {
      foreach ($scategory->getMessages() as $msg) {
        $this->notification->error($msg);
      }
      throw new Exception("Error while saving principal account data base");
    }
  }

  public function searchAction() {
    $search = $_GET['name'];

    if ($search == "") {
      $accounts = Account::find();
    } else {
      $phql1 = "SELECT account.* FROM account WHERE account.name LIKE '%{$search}%'";
      $accounts = $this->modelsManager->executeQuery($phql1, array(0 => "%{$search}%"));
    }

    $objects = array();
    $idaccountsession = $this->user->userType->idAllied;
    if (count($accounts) > 0) {
      foreach ($accounts as $account) {
//        $sender = "";
//        $senders = json_decode($account->sender, true);
//        if (empty($senders)) {
//          $sender = "Esta cuenta no tiene remitentes registrados";
//        }
//        if (is_array($senders) || is_object($senders)) {
//          foreach ($senders as $name => $value) {
//            $sender .= $value['name'] . "/" . $value['email'] . "<br />";
//          }
//        }

        $created = date('d/m/Y h:m a', $account->created);
        $updated = date('d/m/Y h:m a', $account->updated);

//        $phql2 = 'SELECT accountclassification.name FROM accountclassification where accountclassification.idAccountclassification = ?0';
//        $idAccountclassifications = $this->modelsManager->executeQuery($phql2, array(0 => "{$account->idAccountclassification}"));
//        foreach ($idAccountclassifications as $idAccountclassification) {
//          $idAccountclassificationname = $idAccountclassification['name'];
//        }
//
//        $phql3 = 'SELECT services.name,axc.idAccount FROM axc INNER JOIN services ON services.idServices = axc.idServices WHERE axc.idAccount = ?0';
//        $services = $this->modelsManager->executeQuery($phql3, array(0 => "{$account->idAccount}"));
//        $servicename = "";
//        foreach ($services as $service) {
//          $servicename .= "&raquo; <em>" . $service['name'] . "</em><br />";
//        }

        $objects[] = array(
            'id' => $account->idAccount,
            'status' => $account->status,
            'name' => $account->name,
            'prefix' => $account->prefix,
            'idaccountclassification' => $idAccountclassificationname,
            'accountingMode' => $account->accountingMode,
            'subscriptionEmailMode' => $account->subscriptionEmailMode,
            'subscriptionSmsMode' => $account->subscriptionSmsMode,
//            'sender' => $sender,
            'idaccountsession' => $account->idaccountsession = $idaccountsession,
            'created' => $created,
            'updated' => $updated,
            'services' => $servicename,
        );
      }
    }
    return $this->set_json_response($objects);
  }

  public function showAction($idAccount) {
    if (!$idAccount) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('account');
    }
    $account = Account::findfirst([
                'conditions' => 'idAccount = ?0',
                'bind' => array(0 => $idAccount)
    ]);

    if (!$account) {
      $this->notification->error('La cuenta maestra no existe');
      return $this->response->redirect('masteraccount');
    }
    $account->AccountConfig->DetailConfig;

    for ($i = 0; $i < count($account->AccountConfig->DetailConfig); $i++) {
      if ((int) substr($account->AccountConfig->DetailConfig[$i]->pricelist->price, -2) == 0) {
        $priceSetted[$i] = (int) $account->AccountConfig->DetailConfig[$i]->pricelist->price;
      } else {
        $priceSetted[$i] = $account->AccountConfig->DetailConfig[$i]->pricelist->price;
      }
    }

    $accountS = Account::count([
      'conditions' => 'idAccount = ?0 AND registerType = ?1',
      'bind' => array(0 => $idAccount, 1 => "online")
    ]);

    $this->view->setVar("space", round($this->getSpaceUsedInAccount($idAccount), 2));
    $this->view->setVar("account", $account);
    $this->view->setVar("priceSetted", $priceSetted);
    $this->view->setVar("accountS", $accountS);
  }

  public function createconfigAction($idAccount) {
    if (!$idAccount) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('account');
    }
    $account = Account::findfirst([
                'idAccount = ?0',
                'bind' => [$idAccount]
    ]);
    if (!$account) {
      $this->notification->error('La cuenta que desea configurar no existe, por favor valide la información');
      return $this->response->redirect('account');
    }
    $configForm = new AccountclassificationForm();

    $alliedConfig = Alliedconfig::findFirst(array(
                "conditions" => "idAllied = ?1",
                "bind" => array(1 => $this->user->Usertype->idAllied)
    ));

    $this->view->setVar('alliedConfig', $alliedConfig);
    $this->view->setVar('account_form', $configForm);
    $this->view->setVar("account", $account);
    if ($this->request->isPost()) {

      try {
        $accountClassification = new Accountclassification();
        $configForm->bind($this->request->getPost(), $accountClassification);

        foreach ($account->axc as $key) {
          if ($key->idServices == $this->services->sms) {
            $smsLimit = $alliedConfig->smsLimit;
            $totalSmsLimit = $smsLimit - $accountClassification->smsLimit;
            if (!is_numeric($accountClassification->smsLimit)) {
              throw new InvalidArgumentException("El campo Limite de SMS es obligatorio");
            }
            if ($totalSmsLimit < 0 || $totalSmsLimit > $smsLimit) {
              throw new InvalidArgumentException("El Limite de SMS ingresado supera al disponible");
            }
            $alliedConfig->smsLimit = $totalSmsLimit;
          }
          if ($key->idServices == $this->services->landing_page) {
            $landingpageLimit = $alliedConfig->landingpageLimit;
            $totalLandingpageLimit = $landingpageLimit - $accountClassification->landingpageLimit;
            if (!is_numeric($accountClassification->landingpageLimit)) {
              throw new InvalidArgumentException("El campo Limite de landing es obligatorio");
            }
            if ($totalLandingpageLimit < 0 || $totalLandingpageLimit > $landingpageLimit) {
              throw new InvalidArgumentException("El Limite de landing ingresado supera al disponible");
            }
            $alliedConfig->landingpageLimit = $totalLandingpageLimit;
          }
          if ($key->idServices == $this->services->email_marketing) {
            $fileSpace = $alliedConfig->fileSpace;
            $mailLimit = $alliedConfig->mailLimit;
            $contactLimit = $alliedConfig->contactLimit;
            $totalFileSpace = $fileSpace - $accountClassification->fileSpace;
            $totalMailLimit = $mailLimit - $accountClassification->mailLimit;
            $totalContactLimit = $contactLimit - $accountClassification->contactLimit;
            if (!is_numeric($accountClassification->fileSpace)) {
              throw new InvalidArgumentException("El campo Espacio disponible en disco (MB) es obligatorio");
            }
            if ($totalFileSpace < 0 || $totalFileSpace > $fileSpace) {
              throw new InvalidArgumentException("El Espacio disponible en disco (MB) ingresado supera al disponible");
            }
            if (!is_numeric($accountClassification->mailLimit)) {
              throw new InvalidArgumentException("El campo Limite de correos es obligatorio");
            }
            if ($totalMailLimit < 0 || $totalMailLimit > $mailLimit) {
              throw new InvalidArgumentException("El Limite de correos ingresado supera al disponible");
            }
            if (!is_numeric($accountClassification->contactLimit)) {
              throw new InvalidArgumentException("El campo Limite de contactos es obligatorio");
            }
            if ($totalContactLimit < 0 || $totalContactLimit > $contactLimit) {
              throw new InvalidArgumentException("El Limite de contactos ingresado supera al disponible");
            }
            //OPCIONAL
            $accountClassification->idAdapter = $alliedConfig->idAdapter;
            $accountClassification->idMailClass = $alliedConfig->idMailClass;
            $accountClassification->idMta = $alliedConfig->idMta;
            $accountClassification->idUrldomain = $alliedConfig->idUrldomain;
//            if (!is_numeric($accountClassification->idMta)) {
//              throw new InvalidArgumentException("El mta es de caracter obligatorio");
//            }
//            if (!is_numeric($accountClassification->idAdapter)) {
//              throw new InvalidArgumentException("El adapter es de caracter obligatorio");
//            }
//            if (!is_numeric($accountClassification->idUrldomain)) {
//              throw new InvalidArgumentException("El Urldomain es de caracter obligatorio");
//            }
//            if (!is_numeric($accountClassification->idMailClass)) {
//              throw new InvalidArgumentException("El Mail Class es de caracter obligatorio");
//            }
          }
        }

        if (strtotime($accountClassification->expiryDate) < strtotime(date("Y-m-d"))) {
          throw new InvalidArgumentException("La fecha de expiración no puede ser inferior a la fecha actual");
        }
        if (!is_numeric($accountClassification->senderAllowed)) {
          throw new InvalidArgumentException("La fecha de expiración no puede ser inferior a la fecha actual");
        }

        $this->db->begin();
        if (!$accountClassification->save()) {
          $this->db->rollback();
          foreach ($accountClassification->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        if (!$alliedConfig->save()) {
          $this->db->rollback();
          foreach ($alliedConfig->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $account->idAccountclassification = $accountClassification->idAccountclassification;
        if (!$account->save()) {
          $this->db->rollback();
          foreach ($account->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
        $this->db->commit();
        $this->trace("success", "Se creo una cuenta con ID: {$account->idAccount}");
        $this->notification->info('Se ha configurado la cuenta ' . $account->name);
        return $this->response->redirect('account/show/' . $account->idAccount);
      } catch (InvalidArgumentException $msg) {
        $this->notification->error($msg->getMessage());
      } catch (Exception $e) {
        $this->logger->log("Exception while creating account: {$e->getMessage()}");
        $this->logger->log($e->getTraceAsString());
        $this->notification->error($e->getMessage());
      }
    }
  }

  public function configeditAction($idAccount) {
    if (!$idAccount) {
      $this->notification->error('No se ha podido ingresar verifique la información enviada');
      return $this->response->redirect('account');
    }
    $account = Account::findfirst([
                'idAccount = ?0',
                'bind' => [$idAccount]
    ]);
    if (!$account) {
      $this->notification->error('La cuenta no existe');
      return $this->response->redirect('account');
    }

  }

  public function planbycountryaccountAction($idAllied, $idCounty) {
    try {
      $paymentPlan = PaymentPlan::find(array(
                  "conditions" => "idMasteraccount is null AND idAllied = ?0 AND idCountry = ?1 AND deleted = 0",
                  "bind" => array(0 => $idAllied, 1 => $idCounty)
      ));

      $alliedConfig = Alliedconfig::findFirst(array(
                  "conditions" => "idAllied = ?0",
                  "bind" => [0 => $idAllied]
      ));

      $arr = array();
      foreach ($paymentPlan as $key => $value) {
        $arrServ = array();
        $arr[$key] = array("idPaymentPlan" => $value->idPaymentPlan, "name" => $value->name,
            "diskSpace" => $value->diskSpace);
        foreach ($alliedConfig->DetailConfig as $detailConfig) {
          foreach ($value->PaymentPlanxservice as $index => $services) {
            if ($detailConfig->idServices == $services->idServices) {
              $arrServ[$index]['amountConfig'] = $detailConfig->amount;
              $arrServ[$index]['totalAmount'] = isset($detailConfig->amount) || isset($services->amount) ? $detailConfig->amount - $services->amount : null;
              $arrServ[$index]['service'] = $services->Services->name;
              $arrServ[$index]['amount'] = $services->amount;
              $arrServ[$index]['amountQuestion'] = $services->amountQuestion;
              $arrServ[$index]['amountAnswer'] = $services->amountAnswer;
              $arrServ[$index]['amountQuestionConfig'] = $detailConfig->amountQuestion;
              $arrServ[$index]['amountAnswerConfig'] = $detailConfig->amountAnswer;
              $arrServ[$index]['totalAnswer'] = isset($detailConfig->amountAnswer) || isset($services->amountAnswer) ? $detailConfig->amountAnswer - $services->amountAnswer : null;
              $arrServ[$index]['totalQuestion'] = isset($detailConfig->amountQuestion) || isset($services->amountQuestion) ? $detailConfig->amountQuestion - $services->amountQuestion : null;
              $arrServ[$index]['accountingMode'] = $services->accountingMode;
            }
          }
        }
        $arr[$key]['planxservice'] = $arrServ;
      }

      return $this->set_json_response($arr, 200, "OK");
    } catch (Exception $ex) {
      $this->logger->log("Exception while in account: {$ex->getMessage()}");
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
    }
  }

  public function createOnePlan($paymentPlanxService, $configAllied, $accountConfig, $idMta) {
    if ($paymentPlanxService[0]->idServices == $this->services->sms && $configAllied->idServices == $this->services->sms) {
      if ($paymentPlanxService[0]->amount <= $configAllied->amount && $paymentPlanxService[0]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {

        $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, 0, $idMta);
        $configAllied->amount = $configAllied->amount - $paymentPlanxService[0]->amount;

        if (!$configAllied->save()) {
          foreach ($configAllied->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      } else {
        throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
    } else if ($paymentPlanxService[0]->idServices == $this->services->sms_two_way && $configAllied->idServices == $this->services->sms_two_way) {
      if ($paymentPlanxService[0]->amount <= $configAllied->amount && $paymentPlanxService[0]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
        
        $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, 0, $idMta);
        $configAllied->amount = $configAllied->amount - $paymentPlanxService[0]->amount;

        if (!$configAllied->save()) {
          foreach ($configAllied->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      } else {
        throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
    } else if ($paymentPlanxService[0]->idServices == $this->services->landing_page && $configAllied->idServices == $this->services->landing_page) {
      if ($paymentPlanxService[0]->amount <= $configAllied->amount && $paymentPlanxService[0]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {

        $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, 0, $idMta);
        $configAllied->amount = $configAllied->amount - $paymentPlanxService[0]->amount;

        if (!$configAllied->save()) {
          foreach ($configAllied->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      } else {
        throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
    } else if ($paymentPlanxService[0]->idServices == $this->services->email_marketing && $configAllied->idServices == $this->services->email_marketing) {
      if ($paymentPlanxService[0]->amount <= $configAllied->amount && $paymentPlanxService[0]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {

        $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, 0, $idMta);
        $configAllied->amount = $configAllied->amount - $paymentPlanxService[0]->amount;

        if (!$configAllied->save()) {
          foreach ($configAllied->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      } else {
        throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
    } else if ($paymentPlanxService[0]->idServices == $this->services->mail_tester && $configAllied->idServices == $this->services->mail_tester) {
      if ($paymentPlanxService[0]->amount <= $configAllied->amount && $paymentPlanxService[0]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {

        $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, 0, $idMta);
        $configAllied->amount = $configAllied->amount - $paymentPlanxService[0]->amount;

        if (!$configAllied->save()) {
          foreach ($configAllied->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      } else {
        throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
    } else if ($paymentPlanxService[0]->idServices == $this->services->adjuntar_archivos && $configAllied->idServices == $this->services->adjuntar_archivos) {
      if ($paymentPlanxService[0]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
        $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, 0, $idMta);
      } else {
        throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
    }
  }

  public function selectTwoPlan($paymentPlanxService, $key, $configAllied, $accountConfig, $idMta) {
    foreach ($paymentPlanxService as $key => $item) {
      if ($item->idServices == $this->services->sms && $configAllied->idServices == $this->services->sms) {
        if ($item->amount <= $configAllied->amount && $item->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
           $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key, $idMta);
          $configAllied->amount = $configAllied->amount - $item->amount;

          if (!$configAllied->save()) {
            foreach ($configAllied->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
        } else {
          $this->logger->log("Entra en SMS");
          throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
        }
      } else if ($item->idServices == $this->services->sms_two_way && $configAllied->idServices == $this->services->sms_two_way) {
        if ($item->amount <= $configAllied->amount && $item->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
          $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key, $idMta);
          $configAllied->amount = $configAllied->amount - $item->amount;

          if (!$configAllied->save()) {
            foreach ($configAllied->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
        } else {
          $this->logger->log("Entra en SMS doble via");
          throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
        }
      
      } else if ($item->idServices == $this->services->landing_page && $configAllied->idServices == $this->services->landing_page) {
        if ($item->amount <= $configAllied->amount && $item->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
          $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key, $idMta);
          $configAllied->amount = $configAllied->amount - $item->amount;

          if (!$configAllied->save()) {
            foreach ($configAllied->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
        } else {
          $this->logger->log("Entra en Landing");
          throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
        }
      } else if ($item->idServices == $this->services->email_marketing && $configAllied->idServices == $this->services->email_marketing) {
        if ($item->amount <= $configAllied->amount && $item->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
          $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key, $idMta);
          $configAllied->amount = $configAllied->amount - $item->amount;

          if (!$configAllied->save()) {
            foreach ($configAllied->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
        } else {
          $this->logger->log("Entra en Email Marketing");
          throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
        }
      } else if ($item->idServices == $this->services->mail_tester && $configAllied->idServices == $this->services->mail_tester) {
        if ($item->amount <= $configAllied->amount && $item->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
          $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key, $idMta);
          $configAllied->amount = $configAllied->amount - $item->amount;

          if (!$configAllied->save()) {
            foreach ($configAllied->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
        } else {
          throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
        }
      } else if ($item->idServices == $this->services->adjuntar_archivos && $configAllied->idServices == $this->services->adjuntar_archivos) {
        if ($item->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
            $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key, $idMta);
        } else {
          throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
        }
      } else if ($item->idServices == $this->services->survey && $configAllied->idServices == $this->services->survey) {
        if ($item->amountQuestion <= $configAllied->amountQuestion && $item->amountAnswer <= $configAllied->amountAnswer && $item->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
          $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key, $idMta);
          $configAllied->amountQuestion = $configAllied->amountQuestion - $item->amountQuestion;
          $configAllied->amountAnswer = $configAllied->amountAnswer - $item->amountAnswer;

          if (!$configAllied->save()) {
            foreach ($configAllied->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
        } else {
          throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
        }
      }
    }
    /* if (isset($paymentPlanxService[$key])) {
      if ($paymentPlanxService[$key]->idServices == $this->services->sms && $configAllied->idServices == $this->services->sms) {
      if ($paymentPlanxService[$key]->amount <= $configAllied->amount && $paymentPlanxService[$key]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {

      $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key);
      $configAllied->amount = $configAllied->amount - $paymentPlanxService[$key]->amount;

      if (!$configAllied->save()) {
      foreach ($configAllied->getMessages() as $message) {
      throw new \InvalidArgumentException($message);
      }
      }
      } else {
      $this->logger->log("Entra en SMS");
      throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
      } else if ($paymentPlanxService[$key]->idServices == $this->services->email_marketing && $configAllied->idServices == $this->services->email_marketing) {
      if ($paymentPlanxService[$key]->amount <= $configAllied->amount && $paymentPlanxService[$key]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace && $paymentPlanxService[$key]->accountingMode == $configAllied->accountingMode) {

      $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key);
      $configAllied->amount = $configAllied->amount - $paymentPlanxService[$key]->amount;

      if (!$configAllied->save()) {
      foreach ($configAllied->getMessages() as $message) {
      throw new \InvalidArgumentException($message);
      }
      }
      } else {
      $this->logger->log("Entra en Email Marketing");
      throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
      } else if ($paymentPlanxService[$key]->idServices == $this->services->mail_tester && $configAllied->idServices == $this->services->mail_tester) {
      if ($paymentPlanxService[$key]->amount <= $configAllied->amount && $paymentPlanxService[$key]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {

      $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key);
      $configAllied->amount = $configAllied->amount - $paymentPlanxService[$key]->amount;

      if (!$configAllied->save()) {
      foreach ($configAllied->getMessages() as $message) {
      throw new \InvalidArgumentException($message);
      }
      }
      } else {
      throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
      } else if ($paymentPlanxService[$key]->idServices == $this->services->adjuntar_archivos && $configAllied->idServices == $this->services->adjuntar_archivos) {
      if ($paymentPlanxService[$key]->PaymentPlan->diskSpace <= $configAllied->AlliedConfig->diskSpace) {
      $this->createConfigAccount($accountConfig->idAccountConfig, $paymentPlanxService, $key);
      } else {
      throw new InvalidArgumentException("Ha ocurrido un error, la cuenta no tiene la suficiente capacidad para asignar el plan elegido");
      }
      }
      } */
  }
  
  /**
   * Fucntion for create config for accounts
   * @param type $idAccountConfig
   * @param type $paymentPlanxService
   * @param type $key
   * @param type $idMta mta choose in creation account
   * @throws \InvalidArgumentException
   */
  public function createConfigAccount($idAccountConfig, $paymentPlanxService, $key, $idMta) {
    $this->detailConfigAccount = new DetailConfig();

    $this->detailConfigAccount->idAccountConfig = $idAccountConfig;
    $this->detailConfigAccount->idPlanType = $paymentPlanxService[$key]->idPlanType;
    $this->detailConfigAccount->idServices = $paymentPlanxService[$key]->idServices;
    $this->detailConfigAccount->idPriceList = $paymentPlanxService[$key]->idPriceList;
    $this->detailConfigAccount->status = $paymentPlanxService[$key]->status;
    $this->detailConfigAccount->amount = $paymentPlanxService[$key]->amount;
    $this->detailConfigAccount->amountQuestion = $paymentPlanxService[$key]->amountQuestion;
    $this->detailConfigAccount->amountAnswer = $paymentPlanxService[$key]->amountAnswer;
    $this->detailConfigAccount->totalAmountAnswer = $paymentPlanxService[$key]->amountAnswer;
    $this->detailConfigAccount->totalAmountQuestion = $paymentPlanxService[$key]->amountQuestion;
    $this->detailConfigAccount->totalAmount = $paymentPlanxService[$key]->amount;
    $this->detailConfigAccount->speed = $paymentPlanxService[$key]->speed;
    $this->detailConfigAccount->accountingMode = $paymentPlanxService[$key]->accountingMode;

    if (!$this->detailConfigAccount->save()) {
      foreach ($this->detailConfigAccount->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    
    $idUser = $this->session->get('idUser');
    $user = User::findFirst(array(
                "conditions" => "idUser = ?1",
                "bind" => array(1 => $idUser)
    ));
    
    if ($user == null || $user == "") {
      $user->email = "soporte@sigmamovil.com";
    }

    if ($this->detailConfigAccount->idServices == 2) {
      $dcxmta = new Dcxmta();
      if ($idMta != 0 && $idMta != null && $idMta != '') {
        $dcxmta->idDetailConfig = (int) $this->detailConfigAccount->idDetailConfig;
        $dcxmta->idMta = (int) $idMta;
        $dcxmta->created = time();
        $dcxmta->updated = time();
        $dcxmta->createdBy = $user->email;
        $dcxmta->updatedBy = $user->email;
      } else {
        $dcxmta->idDetailConfig = (int) $this->detailConfigAccount->idDetailConfig;
        $dcxmta->idMta = (int) \Phalcon\DI::getDefault()->get('mtaDefault')->idMtaDefault;
        $dcxmta->created = time();
        $dcxmta->updated = time();
        $dcxmta->createdBy = $user->email;
        $dcxmta->updatedBy = $user->email;
      }
      if (!$dcxmta->save()) {
        $this->db->rollback();
        foreach ($dcxmta->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }

    $ppxsxadapter = Ppxsxadapter::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => [0 => $paymentPlanxService[$key]->idPaymentPlanxService]
    ));


    if (count($ppxsxadapter) > 0) {
      foreach ($ppxsxadapter as $ppxsxadapterValue) {
        $dcxadapter = new Dcxadapter();
        $dcxadapter->idDetailConfig = $this->detailConfigAccount->idDetailConfig;
        $dcxadapter->idAdapter = $ppxsxadapterValue->idAdapter;

        if (!$dcxadapter->save()) {
          $this->db->rollback();
          foreach ($dcxadapter->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }

    $ppxsxmailClass = PpxsxmailClass::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => [0 => $paymentPlanxService[$key]->idPaymentPlanxService]
    ));

    if (count($ppxsxmailClass) > 0) {
      foreach ($ppxsxmailClass as $ppxsxmailClassValue) {
        $dcxmailClass = new Dcxmailclass();
        $dcxmailClass->idDetailConfig = $this->detailConfigAccount->idDetailConfig;
        $dcxmailClass->idMailClass = $ppxsxmailClassValue->idMailClass;

        if (!$dcxmailClass->save()) {
          $this->db->rollback();
          foreach ($dcxmailClass->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }

    $ppxsxurldomain = Ppxsxurldomain::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => [0 => $paymentPlanxService[$key]->idPaymentPlanxService]
    ));

    if (count($ppxsxurldomain) > 0) {
      foreach ($ppxsxurldomain as $ppxsxurldomainValue) {
        $dcxurldomain = new Dcxurldomain();
        $dcxurldomain->idDetailConfig = $this->detailConfigAccount->idDetailConfig;
        $dcxurldomain->idUrldomain = $ppxsxurldomainValue->idUrldomain;

        if (!$dcxurldomain->save()) {
          $this->db->rollback();
          foreach ($dcxurldomain->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }
  }

  public function getfootersAction() {
    try {
      $footer = Footer::find(array(
                  "conditions" => "idAllied = ?0 AND deleted = 0",
                  "bind" => array(0 => $this->user->Usertype->idAllied)
      ));
      $arr = array();
      foreach ($footer as $key => $value) {
        $arr[$key]['idFooter'] = $value->idFooter;
        $arr[$key]['name'] = $value->name;
      }

      return $this->set_json_response($arr, 200, "OK");
    } catch (Exception $ex) {
      $this->logger->log("Exception while in account: {$ex->getMessage()}");
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
    }
  }

  public function deleteDcx($arrayServices) {
    foreach ($arrayServices as $item) {

      if ($item['idServices'] == $this->services->sms) {
        $sql = "DELETE FROM dcxadapter WHERE idDetailConfig='" . $item['idDetailConfig'] . "'";
        $this->db->execute($sql);
      } else if ($item['idServices'] == $this->services->email_marketing) {
        $sqlMta = "DELETE FROM dcxmta WHERE idDetailConfig='" . $item['idDetailConfig'] . "'";
        $this->db->execute($sqlMta);

        $sqlMail = "DELETE FROM dcxmailclass WHERE idDetailConfig='" . $item['idDetailConfig'] . "'";
        $this->db->execute($sqlMail);

        $sqlDomain = "DELETE FROM dcxurldomain WHERE idDetailConfig='" . $item['idDetailConfig'] . "'";
        $this->db->execute($sqlDomain);
      }else if ($item['idServices'] == $this->services->survey) {
        $sqlMtaSurvey = "DELETE FROM dcxmta WHERE idDetailConfig='" . $item['idDetailConfig'] . "'";
        $this->db->execute($sqlMtaSurvey);
        
        $sqlMailSurvey = "DELETE FROM dcxmailclass WHERE idDetailConfig='" . $item['idDetailConfig'] . "'";
        $this->db->execute($sqlMailSurvey);
        
        $sqlDomainSurvey = "DELETE FROM dcxurldomain WHERE idDetailConfig='" . $item['idDetailConfig'] . "'";
        $this->db->execute($sqlDomainSurvey);
      }

      $detailConfig = DetailConfig::findfirst(array(
                  "conditions" => "idDetailConfig = ?0",
                  "bind" => [0 => $item['idDetailConfig']]
      ));

      if ($detailConfig) {
        if (!$detailConfig->delete()) {
          foreach ($detailConfig->getMessages() as $msg) {
            throw new Exception($msg);
          }
        }
      }
    }
  }

  public function deleteServicesSaxs($saxs) {
    foreach ($saxs as $item) {
      if (!$item->delete()) {
        foreach ($item->getMessages() as $msg) {
          throw new Exception($msg);
        }
      }
    }
  }

  public function getservicesaccountAction($idAccount) {

    try {
      $account = Account::findFirst(array(
                  'conditions' => 'idAccount = ?1',
                  'bind' => array(1 => $idAccount)
      ));

      if (!$account) {
        throw new InvalidArgumentException("La cuenta enviada no existe, por favor verifique la información");
      }

      $detailConfig = $account->AccountConfig->DetailConfig;
      $arr = array();



      foreach ($detailConfig as $key => $ser) {
        $arr[$key]['idServices'] = $ser->idServices;
        $arr[$key]['name'] = $ser->Services->name;




        if ($ser->idServices == $this->services->sms) {
          $limitSms = $ser->amount;
        } else if ($ser->idServices == $this->services->email_marketing) {
          $limitContact = $ser->amount;
          $accountingMode = $ser->accountingMode;
        } else if ($ser->idServices == $this->services->sms_two_way) {
          $limitSmstwoway = $ser->amount;
        } else if ($ser->idServices == $this->services->landing_page) {
          $limitLandingpage = $ser->amount;
        }
      }

      $result = [
          'services' => $arr,
          'limitSmsAccount' => isset($limitSms) ? $limitSms : '',
          'limitSmstwowayAccount' => isset($limitSmstwoway) ? $limitSmstwoway : '',
          'limitContactAccount' => isset($limitContact) ? $limitContact : '',
          'accountingModeAccount' => isset($accountingMode) ? $accountingMode : '',
          'limitLandingpageAccount' => isset($limitLandingpage) ? $limitLandingpage : '',          
      ];

      return $this->set_json_response($result, 200, "OK");
    } catch (Exception $ex) {
      $this->logger->log("Exception while in account: {$ex->getMessage()}");
      $this->logger->log($ex->getTraceAsString());
      $this->notification->error($ex->getMessage());
    }
  }

  public function rechargeaccountAction() {

    $dataJson = $this->request->getRawBody();
    $data = json_decode($dataJson, true);

    if (!empty($data)) {
      try {
        $account = Account::findfirst(array(
          "conditions" => "idAccount = ?0",
          "bind" => [0 => $data['idAccount']],
          "colums" => "idAccountConfig, idServices, amount, totalAmount"
        ));

        if (!$account) {
          throw new InvalidArgumentException("La cuenta enviada no existe, por favor verifique la información");
        }

        $detailConfig = $account->AccountConfig->DetailConfig;
        $this->db->begin();

        foreach ($detailConfig as $value) {
          if ($value->idServices == $this->services->sms) {
            if (isset($data['smsLimit'])) {
              $amount = $value->amount;
              $totalAmount = $value->totalAmount;
              $value->totalAmount = $value->totalAmount + $data['smsLimit'];
              $value->amount = $value->amount + $data['smsLimit'];
              if (!$value->save()) {
                foreach ($value->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
              $this->createRechageHistory($value->idAccountConfig, $data['smsLimit'], $totalAmount, $value->idServices, null, null,$amount);
            }
          }
          if ($value->idServices == $this->services->email_marketing) {
            if (isset($data['mailLimit'])) {
              $amount = $value->amount;
              $totalAmount = $value->totalAmount;
              $value->totalAmount = $value->totalAmount + $data['mailLimit'];
              $value->amount = $value->amount + $data['mailLimit'];
              if (!$value->save()) {
                foreach ($value->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
              $this->createRechageHistory($value->idAccountConfig, $data['mailLimit'], $totalAmount, $value->idServices, null, null,$amount);
            }
          }
          if ($value->idServices == $this->services->sms_two_way) {
            if (isset($data['smstwowayLimit'])) {
              $totalAmount = $value->totalAmount;
              $value->totalAmount = $value->totalAmount + $data['smstwowayLimit'];
              $value->amount = $value->amount + $data['smstwowayLimit'];
              if (!$value->save()) {
                foreach ($value->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
              $this->createRechageHistory($value->idAccountConfig, $data['smstwowayLimit'], $totalAmount, $value->idServices, null, null,0);
            }
          }
          if ($value->idServices == $this->services->landing_page) {
            if (isset($data['landingpageLimit'])) {
              $totalAmount = $value->totalAmount;
              $value->totalAmount = $value->totalAmount + $data['landingpageLimit'];
              $value->amount = $value->amount + $data['landingpageLimit'];
              if (!$value->save()) {
                foreach ($value->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
              $this->createRechageHistory($value->idAccountConfig, $data['landingpageLimit'], $totalAmount, $value->idServices, null, null,0);
            }
          }
        }

        $detailConfigAllied = DetailConfig::find(array(
          "conditions" => "idAlliedconfig = ?0",
          "bind" => [0 => $this->user->Usertype->Allied->Alliedconfig->idAlliedconfig],
          "colums" => "idAlliedconfig, idServices, amount"                
        ));

        foreach ($detailConfigAllied as $value) {
          if ($value->idServices == $this->services->sms) {
            if (isset($data['smsLimit'])) {
              $value->amount = $value->amount - $data['smsLimit'];
              if (!$value->save()) {
                foreach ($value->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
            }
          } else if ($value->idServices == $this->services->sms_two_way) {
            if (isset($data['smstwowayLimit'])) {
              $value->amount = $value->amount - $data['smstwowayLimit'];
              if (!$value->save()) {
                foreach ($value->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
            }
          } else if ($value->idServices == $this->services->landing_page) {
            if (isset($data['landingpageLimit'])) {
              $value->amount = $value->amount - $data['landingpageLimit'];
              if (!$value->save()) {
                foreach ($value->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
            }
          } else if ($value->idServices == $this->services->email_marketing) {
            if (isset($data['mailLimit'])) {
              $value->amount = $value->amount - $data['mailLimit'];
              if (!$value->save()) {
                foreach ($value->getMessages() as $msg) {
                  throw new Exception($msg);
                }
              }
            }
          }
        }

        //$sql = "CALL updateCountersGlobal()";
        //$this->db->execute($sql);
        /* $sql1 = "CALL updateCountersAccount({$data['idAccount']})";
          $this->db->fetchAll($sql1);
          $sql = "CALL updateAmountAccount({$data['idAccount']},{$this->services->sms})";
          $this->db->execute($sql); */

        $this->db->commit();
        return $this->set_json_response(array("Se recargaron los servicios correctamente"), 200, 'success');
      } catch (InvalidArgumentException $msg) {
        $this->db->rollback();
        return $this->set_json_response(array($msg->getMessage()), 401, 'error');
      } catch (Exception $e) {
        $this->db->rollback();
        $this->logger->log("Exception while charging account: {$e->getMessage()}");
        $this->logger->log($e->getTraceAsString());
        return $this->set_json_response(array("Ocurrió un error, por favor contacte al administrador"), 500, 'error');
      }
    }
  }

  private function createHistoryPaymentPlan($idAccountConfig, $idPaymentPlan, $mta, $services, $mailClass, $adapter, $urlDomain, $amountSms, $amountMail, $totalAmountMail, $totalAmountSms) {
    $historyPaymentPlan = new HistoryPaymentPlan();

    $historyPaymentPlan->idAccountConfig = $idAccountConfig;
    $historyPaymentPlan->idPaymentPlan = $idPaymentPlan;
    $historyPaymentPlan->services = $services;
    $historyPaymentPlan->mta = $mta;
    $historyPaymentPlan->mailClass = $mailClass;
    $historyPaymentPlan->adapter = $adapter;
    $historyPaymentPlan->urlDomain = $urlDomain;
    $historyPaymentPlan->amountSms = $amountSms;
    $historyPaymentPlan->amountMail = $amountMail;
    $historyPaymentPlan->totalAmountMail = $totalAmountMail;
    $historyPaymentPlan->totalAmountSms = $totalAmountSms;

    if (!$historyPaymentPlan->save()) {
      foreach ($historyPaymentPlan->getMessages() as $msg) {
        throw new Exception($msg);
      }
    }
  }

  private function returnNameAdapter($detailConfig) {
    $adapter = "";
    foreach ($detailConfig->DcxAdapter as $item) {
      $adapter .= $item->Adapter->fname . ", ";
    }
    return $adapter;
  }

  private function returnNameMailclass($detailConfig) {
    $mailclass = "";
    foreach ($detailConfig->Dcxmailclass as $item) {
      $mailclass .= $item->Mailclass->name . ", ";
    }
    return $mailclass;
  }

  private function returnNameMta($detailConfig) {
    $mta = "";
    foreach ($detailConfig->DcxMta as $item) {
      $mta .= $item->Mta->name . ", ";
    }
    return $mta;
  }

  private function returnNameUrldomain($detailConfig) {
    $urldomain = "";
    foreach ($detailConfig->Dcxurldomain as $item) {
      $urldomain .= $item->Urldomain->name . ", ";
    }
    return $urldomain;
  }

  public function rechargesAction($idServices){
    $array = [1,2];
    
    if (!in_array($idServices,$array)) {
      $redirect = 'account/show/'.$this->user->Usertype->idAccount;
      $this->notification->error("El servicio no se puede recargar.");
      return $this->response->redirect($redirect);
    }
    $account = null;
    if(isset($this->user->Usertype->Account->idAccount)){
        $account = $this->user->Usertype->Account->idAccount;
    }else {
        $account = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
    }
    $this->view->setVar("idServices", $idServices);
    $this->view->setVar("idAccount", $account);
  }

  public function rechargeservicesAction($idServices){

    $array = [1,2];
    if (in_array($idServices,$array)) {
      $services = \Services::findFirst([
        "columns" => "idServices, name",
        "conditions" => "idServices=?0", 
        "bind" => [0 => $idServices],
      ]);
      //
      $account = $this->user->Usertype->Account;
      $idAccountConf = $this->user->Usertype->Account->AccountConfig->idAccountConfig;
      //
      $history = \RechargeHistory::find([
        "conditions" => "idAccountConfig = ?0 AND idServices=?1", 
        "bind" => [0 =>$idAccountConf,1 => $idServices],
        "order" => "created DESC"
      ]);
      //
      $rangesprices = \RangesPrices::find([
        "columns" => "idRangesPrices, quantity, totalValue", 
        "conditions" => "idServices=?0", 
        "bind" => [$idServices],
      ]);
      //
      $validateuser = false;
      if(isset($this->user->Usertype->Account->idAccount)){
        $validateuser = true;
      }
      $data = [
        "account"       => [
          "idAccount"   => $account->idAccount, 
          "name"        => $account->name,
          "validateUser" => $validateuser
        ],
        "history"       => $history->toArray(),
        "services"      => $services->toArray(),
        "rangesprices"  => $rangesprices->toArray(),        
      ];
      return $this->set_json_response($data, 200);
    } else {
      $redirect = 'account/show/'.$this->user->Usertype->idAccount;
      $this->notification->error("El servicio no se puede recargar.");
      return $this->response->redirect($redirect);
    }
  }

  public function responseAction() {

  }

  public function confirmationAction() {

  }

  //FUNCION PARA DESCARGAR REPORTE EXCEL CON LISTADO DE CUENTAS
  public function downloadexcelaccountsAction(){
    $contentsraw = $this->getRequestContent();
    $data = json_decode($contentsraw);
    $this->typeRegister = $data[0];
    $this->status = $data[1];
    $concatSQL = "idAllied = ?0";
    $title = "Reporte listado de cuentas";
    $dataAccounts = array();
    
    //ARMAR EL STRING DE LA CONSULTA SEGUN LAS VARIABLES
    if(!is_null($this->typeRegister) && $this->typeRegister!= "todosOrg" ){
      $typeRegister = $this->typeRegister;
      $concatSQL .= " AND registerType='{$typeRegister}'";
    }
    if(!is_null($this->status) && $this->status!= "todosEst" ){
      if($this->status == "activo"){
        $status = 1;
      }
      if($this->status == "inactivo"){
        $status = 0;
      }
      $concatSQL .= " AND status={$status}";
    }
    $concatSQL .= " ORDER BY created DESC";

    $accounts = \Account::find(array(
      "conditions" => $concatSQL,
      "bind" => array(0 => \Phalcon\DI::getDefault()->get('user')->Usertype->idAllied)
    ));

    foreach($accounts as $valAccounts){
      
      $accountConfig = \AccountConfig::find([
        'idAccount = ?0',
        'bind' => [$valAccounts->idAccount]
      ]);

      foreach($accountConfig as $valAccountConfig){
        
        $detailConfig = \DetailConfig::find([
          "conditions" => "idAccountConfig = ?0",
          "bind" => array(0 => (int) $valAccountConfig->idAccountConfig)
        ]);

        foreach($detailConfig as $valDetailConfig){

          $services = \Services::find([
            "conditions" => "idServices = ?0",
            "bind" => array(0 => (int) $valDetailConfig->idServices)
          ]);
          
          foreach($services as $valServices){
            
            if($valAccounts->registerType == "form"){
                $sql = "SELECT IFNULL(SUM(amount), 0) as amount FROM saxs LEFT JOIN subaccount AS s ON s.idSubaccount = saxs.idSubaccount WHERE idAccount = ".$valAccounts->idAccount." AND idServices = ".$valServices->idServices;
                $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);
                $amount = $c[0]["amount"];
            }else{
                if(empty($valDetailConfig->amount)){
                    $amount = 0;
                }else{
                    $amount = $valDetailConfig->amount;
                }
                
            }
            
            if(empty($valDetailConfig->totalAmount)){
                $totalAmount = 0;
            }else{
                $totalAmount = $valDetailConfig->totalAmount;
            }
            
            if(empty($valAccounts->companyName)){
                $companyName = "";
            }else{
                $companyName = $valAccounts->companyName;
            }
            
            if($valServices->idServices == 1){
                
                $sqlSMS = "SELECT IFNULL(sum(ms.sent), 0) as countsms FROM sms AS ms LEFT JOIN subaccount AS s ON s.idSubaccount = ms.idSubaccount WHERE s.idAccount = ".$valAccounts->idAccount." AND ms.STATUS = 'sent'";
                $cSMS = \Phalcon\DI::getDefault()->get("db")->fetchAll($sqlSMS);
                $quantitySent = $cSMS[0]["countsms"];
                $accountingMode = "Por envio";
                
            }else if($valServices->idServices == 2){
                $sqlEMAIL = "SELECT	IFNULL(SUM(m.messagesSent), 0) as countemail FROM mail AS m LEFT JOIN subaccount AS s ON s.idSubaccount = m.idSubaccount WHERE s.idAccount = ".$valAccounts->idAccount." AND m.STATUS = 'sent'";
                $cEMAIL = \Phalcon\DI::getDefault()->get("db")->fetchAll($sqlEMAIL);
                $quantitySent = $cEMAIL[0]["countemail"];
                
                if($valDetailConfig->accountingMode == "contact"){
                    $accountingMode = "Contacto";
                }else if($valDetailConfig->accountingMode == "sending"){
                    $accountingMode = "Por envio";
                }
                
            }else{
                $quantitySent = 0;
            }
            
            if($valServices->idServices == 7){
                $accountingMode = "Por envio";
            }
            
            $idAccount = $valAccounts->idAccount;
            $nameAccount = $valAccounts->name;
            $serviceName = $valServices->name;
            

            $dt = new DateTime();
            $dt->setTimezone(new DateTimeZone('America/Bogota'));
            $dt->setTimestamp($valAccounts->created);
            
            $array = array(
              "nit" => $valAccounts->nit,
              "companyName" => $companyName,
              "name" => $nameAccount,
              "correo" => $valAccounts->email,
              "celular" => $valAccounts->phone,
              "service" => $serviceName,
              "accountingMode" => $accountingMode,
              "totalAmount" => $totalAmount,
              "amount" => $amount,
              "quantitySent" => $quantitySent,
              "fecha_creacion" => $dt->format('d/m/Y G:i:s'),
            );

            array_push($dataAccounts, $array);

          }

        }

      }

    }
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    $excel->createStatics();
    $excel->setData($dataAccounts);
    $excel->setTableInfoAccounts($this->typeRegister, $this->status);
    $excel->generatedReportAccounts();
    $nameFull = str_replace(" ", "_", $title) . "_" . date('Y-m-d') . ".xlsx";

   return $this->set_json_response(["title" => $nameFull, "excel_response"=>$excel->downloadExcel($title)], 200);

  }

  public function downloadexcelAction($name) {
    $this->view->disable();
    $nameFull = $name;
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename='.$nameFull);
    header('Pragma: public');
    header('Expires: 0');
    header('Content-Type: application/download');
    $route = __DIR__ . "/../../tmp/".$nameFull;
//    $route = getcwd() . "/tmp/".$nameFull;//local
    $val = file_exists($route);
    if ($val) {
      $valRead = readfile($route);
      if($valRead){
        $valUnlink = unlink($route);
        return $valUnlink;
      }
    } else {
      echo "Ha ocurrido un problema con el servidor por favor vuelva a intentarlo o comuniquese con el administrador del sistema";
    }
  }
  
}
