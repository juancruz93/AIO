<?php

namespace Sigmamovil\Wrapper;

class PaymentplanWrapper extends \BaseWrapper {

  private $form;

  public function __construct() {
    $this->form = new \PaymentplanForm();
    parent::__construct();
  }

  public function listPaymentPlan($page, $name) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $filter = new \Phalcon\Filter;

    $filterName = $filter->sanitize(((isset($name)) ? $name : ""), "string");
    $idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : '');
    $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : '');
    $cond1 = ((!empty($idMasteraccount)) ? "AND idMasteraccount = {$filter->sanitize($idMasteraccount, "int")}" : "AND idMasteraccount IS NULL");
    $cond2 = ((!empty($idAllied)) ? "AND idAllied = {$filter->sanitize($idAllied, "int")}" : "AND idAllied IS NULL");

    $conditions = array(
        "conditions" => "deleted = ?0 {$cond1} {$cond2} AND name LIKE '%{$filterName}%'",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $paymentplan = \PaymentPlan::find($conditions);
    unset($conditions["limit"], $conditions["offset"]);
    $total = \PaymentPlan::find($conditions);

    $data = [];
    if (count($paymentplan) > 0) {
      foreach ($paymentplan as $key => $value) {
        $data[$key] = array(
            "idPaymentPlan" => $value->idPaymentPlan,
            "idMasteraccount" => $value->idMasteraccount,
            "idAllied" => $value->idAllied,
            "country" => $value->Country->name,
            "created" => date("Y-m-d", $value->created),
            "updated" => date("Y-m-d", $value->updated),
            "deleted" => $value->deleted,
            "status" => $value->status,
            "type" => $value->type,
            "name" => $value->name,
            "description" => $value->description,
            "diskSpace" => $value->diskSpace,
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "listppxs" => $this->listppxsMin($value->idPaymentPlan)
        );
      }
    }

    $array = array(
        "total" => count($total),
        "total_pages" => ceil(count($total) / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );

    return $array;
  }

  public function listppxtax($idPaymentplan) {
    $ppxtax = \PaymentPlanxtax::find(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($idPaymentplan)
    ));

    $data = [];
    if (count($ppxtax) > 0) {
      foreach ($ppxtax as $key => $value) {
        $data[$key] = array(
            "name" => $value->Tax->name
        );
      }
    }

    return $data;
  }

  /**
   * Esta funcion es la corta de consultar los servicios
   */
  public function listppxsMin($idPaymentPlan) {
    $ppxs = \PaymentPlanxservice::find(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($idPaymentPlan)
    ));

    $data = [];
    if (count($ppxs) > 0) {
      foreach ($ppxs as $key => $value) {
        $data[$key] = array(
            "Service" => $value->Services->name
        );
      }
    }

    return $data;
  }

  public function getViewPaymentPlan($idPaymentPlan) {
    $paymentplan = \PaymentPlan::findFirst(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($idPaymentPlan)
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El plan de pago que intenta ver no existe");
    }

    $data = array(
        "idPaymentPlan" => $paymentplan->idPaymentPlan,
        "idMasteraccount" => $paymentplan->idMasteraccount,
        "idAllied" => $paymentplan->idAllied,
        "country" => $paymentplan->Country->name,
        "created" => date("Y-m-d", $paymentplan->created),
        "updated" => date("Y-m-d", $paymentplan->updated),
        "deleted" => $paymentplan->deleted,
        "status" => $paymentplan->status,
        "type" => $paymentplan->type,
        "name" => $paymentplan->name,
        "description" => $paymentplan->description,
        "diskSpace" => $paymentplan->diskSpace,
        "createdBy" => $paymentplan->createdBy,
        "updatedBy" => $paymentplan->updatedBy,
        "tax" => $this->listppxtax($paymentplan->idPaymentPlan),
        "services" => $this->listppxs($paymentplan->idPaymentPlan)
    );

    return $data;
  }

  public function listppxs($idPaymentPlan) {
    $ppxs = \PaymentPlanxservice::find(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($idPaymentPlan)
    ));

    $data = [];
    if (count($ppxs) > 0) {
      foreach ($ppxs as $key => $value) {
        $data[$key] = array(
            "Plantype" => $value->PlanType->name,
            "idService" => $value->idServices,
            "Service" => $value->Services->name,
            "idPriceList" => $value->idPriceList,
            "namePriceList" => $value->Pricelist->name,
            "namePriceList" => $value->Pricelist->name,
            "status" => $value->status,
            "amount" => $value->amount,
            "speed" => $value->speed,
            "accountingMode" => $value->accountingMode,
            "adapter" => $this->listppxsxadapter($value->idPaymentPlanxService),
            "mailClass" => $this->listppxsxmailclass($value->idPaymentPlanxService),
            "mta" => $this->listppxsxmta($value->idPaymentPlanxService),
            "urldomain" => $this->listppxsxurldomain($value->idPaymentPlanxService)
        );
      }
    }

    return $data;
  }

  public function listppxsxadapter($idPaymentPlanxService) {
    $ppxsxadapter = \Ppxsxadapter::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($idPaymentPlanxService)
    ));

    $data = [];
    if (count($ppxsxadapter) > 0) {
      foreach ($ppxsxadapter as $key => $value) {
        $data[$key] = array(
            "name" => $value->Adapter->fname
        );
      }
    }

    return $data;
  }

  public function listppxsxmailclass($idPaymentPlanxService) {
    $ppxsxmailclass = \PpxsxmailClass::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($idPaymentPlanxService)
    ));

    $data = [];
    if (count($ppxsxmailclass) > 0) {
      foreach ($ppxsxmailclass as $key => $value) {
        $data[$key] = array(
            "name" => $value->Mailclass->name
        );
      }
    }

    return $data;
  }

  public function listppxsxmta($idPaymentPlanxService) {
    $ppxsxmta = \Ppxsxmta::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($idPaymentPlanxService)
    ));

    $data = [];
    if (count($ppxsxmta)) {
      foreach ($ppxsxmta as $key => $value) {
        $data[$key] = array(
            "name" => $value->Mta->name
        );
      }
    }

    return $data;
  }

  public function listppxsxurldomain($idPaymentPlanxService) {
    $ppxsxurldomain = \Ppxsxurldomain::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($idPaymentPlanxService)
    ));

    $data = [];
    if (count($ppxsxurldomain) > 0) {
      foreach ($ppxsxurldomain as $key => $value) {
        $data[$key] = array(
            "name" => $value->Urldomain->name
        );
      }
    }

    return $data;
  }

  public function createPaymentPlan($data) {
    $dataEmail = null;
    $courtesy = 0;
    $email = null;
    if (isset($data["data"]["courtesy"]) && $data["data"]["courtesy"] == true) {
      if (isset($data["data"]["email"])) {
        $emailNotification = explode(",", $data["data"]["email"]);
        for ($index = 0; $index < count($emailNotification); $index++) {
          if ($this->ValidateEmail($emailNotification[$index]) == 1) {
            $dataEmail[] = $emailNotification[$index];
          }
        }
      }
      if (count($dataEmail) > 0) {
        $email = json_encode((object) $dataEmail);
      } else {
        $dataEmail[] = $this->user->email;
        $email = json_encode((object) $dataEmail);
      }
      $courtesy = 1;
    }

    $paymentplan = new \PaymentPlan();
    $this->form->bind($data["data"], $paymentplan);
    $status = (($data["data"]["status"] == true) ? 1 : 0);
    $paymentplan->status = $status;
    $paymentplan->idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : NULL);
    $paymentplan->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);
    $paymentplan->courtesyplan = $courtesy;
    $paymentplan->emailnotification = $email;

    if (!$this->form->isValid() || !$paymentplan->save()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($paymentplan->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $this->savePaymentPlanxtax($data["tax"]["idTax"], $paymentplan);
    $this->savePaymentPlanxservice($data, $paymentplan);
    return ["message" => "El plan de pago <b>{$paymentplan->name}</b> se ha guardado exitosamente"];
  }

  public function savePaymentPlanxtax($data, $paymentplan) {
    if (count($data) > 0) {
      foreach ($data as $value) {
        $paymentplanxtax = new \PaymentPlanxtax();
        $paymentplanxtax->idPaymentPlan = $paymentplan->idPaymentPlan;
        $paymentplanxtax->idTax = $value;
        $paymentplanxtax->status = 1;
        if (!$paymentplanxtax->save()) {
          foreach ($paymentplanxtax->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }
  }

  public function savePaymentPlanxservice($data, $paymentplan) {

    $this->validateConfigurationService($data);
    foreach ($data["service"]["idServices"] as $value) {
      $services = \Services::findFirst(array(
                  "conditions" => "idServices = ?0",
                  "bind" => array($value)
      ));

      $paymentplanxservice = new \PaymentPlanxservice();
 
      switch (strtolower($services->name)) {
        case "sms":
          $sms = (object) $data["sms"];
          $paymentplanxservice->idPaymentPlan = $paymentplan->idPaymentPlan;
          $paymentplanxservice->idPlanType = $sms->idPlanType;
          $paymentplanxservice->idServices = $services->idServices;
          $paymentplanxservice->idPriceList = $sms->idPriceList;
          $paymentplanxservice->status = $paymentplan->status;
          $paymentplanxservice->amount = $sms->amount;
          $paymentplanxservice->speed = $sms->speed;
          break;
        case "email marketing":
          $email = (object) $data["email"];
          /*$conf = NULL;
          switch (strtolower($this->user->Usertype->name)) {
            case "root":
              $conf = NULL;
              break;
            case "master":
              $conf = $this->user->Usertype->Masteraccount->MasterConfig->DetailConfig;
              break;
            case "allied":
              $conf = $this->user->Usertype->Allied->Alliedconfig->DetailConfig;
              break;
          }

          if ($conf != null) {
            $detailConfig = NULL;
            foreach ($conf as $value) {
              if ($value->idServices == $this->services->email_marketing) {
                $detailConfig = $value;
                break;
              }
            }
            $paymentplanxservice->accountingMode = $detailConfig->accountingMode;
          } else {*/
          $paymentplanxservice->accountingMode = $email->accountingMode;

          $pricelis = \PriceList::findFirst(array(
                      "conditions" => "idPriceList = ?0",
                      "bind" => array($email->idPriceList)
          ));

          if ($pricelis->accountingMode !== $email->accountingMode) {
            throw new \InvalidArgumentException("El modo de de cobro de email debe ser igual al de la lista de precios");
          }
          //}
          $paymentplanxservice->idPaymentPlan = $paymentplan->idPaymentPlan;
          $paymentplanxservice->idPlanType = $email->idPlanType;
          $paymentplanxservice->idServices = $services->idServices;
          $paymentplanxservice->idPriceList = $email->idPriceList;
//          $paymentplanxservice->accountingMode = $email->accountingMode;
          $paymentplanxservice->status = $paymentplan->status;
          $paymentplanxservice->amount = $email->amount;
          break;
        case "mail tester":
          $mailtester = (object) $data["mailtester"];
          $paymentplanxservice->idPaymentPlan = $paymentplan->idPaymentPlan;
          //$paymentplanxservice->idPlanType = $mailtester->idPlanType;
          $paymentplanxservice->idServices = $services->idServices;
          $paymentplanxservice->idPriceList = $mailtester->idPriceList;
          $paymentplanxservice->status = $paymentplan->status;
          $paymentplanxservice->amount = $mailtester->amount;
          break;
        case "adjuntar archivos":
          $attachment = (object) $data["attachment"];
          $paymentplanxservice->idPaymentPlan = $paymentplan->idPaymentPlan;
          $paymentplanxservice->idServices = $services->idServices;
          $paymentplanxservice->idPriceList = $attachment->idPriceList;
          $paymentplanxservice->status = $paymentplan->status;
          break;
        case "survey":
          $survey = (object) $data["survey"];
          $paymentplanxservice->idPaymentPlan = $paymentplan->idPaymentPlan;
          $paymentplanxservice->idServices = $services->idServices;
          $paymentplanxservice->idPriceList = $survey->idPriceList;
          $paymentplanxservice->status = $paymentplan->status;
          $paymentplanxservice->amountQuestion = $survey->amountQuestion;
          $paymentplanxservice->amountAnswer = $survey->amountAnswer;
          break;
        case "sms doble-via":
          $smstwoway = (object) $data["smstwoway"];
          $paymentplanxservice->idPaymentPlan = $paymentplan->idPaymentPlan;
          $paymentplanxservice->idPlanType = $smstwoway->idPlanType;
          $paymentplanxservice->idServices = $services->idServices;
          $paymentplanxservice->idPriceList = $smstwoway->idPriceList;
          $paymentplanxservice->status = $paymentplan->status;
          $paymentplanxservice->amount = $smstwoway->amount;
//          $paymentplanxservice->speed = $sms->speed;
          break;
        case "landing page":
          $smstwoway = (object) $data["landingpage"];
          $paymentplanxservice->idPaymentPlan = $paymentplan->idPaymentPlan;
          $paymentplanxservice->idPlanType = $smstwoway->idPlanType;
          $paymentplanxservice->idServices = $services->idServices;
          $paymentplanxservice->idPriceList = $smstwoway->idPriceList;
          $paymentplanxservice->status = $paymentplan->status;
          $paymentplanxservice->amount = $smstwoway->amount;
//          $paymentplanxservice->speed = $sms->speed;
          break;
        default:
          throw new \InvalidArgumentException("Ha ocurrido un error guardando el plan de pago, mirad el código");
      }

      if (!$paymentplanxservice->save()) {
        foreach ($paymentplanxservice->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      if (strtolower($services->name) === "sms") {
        $this->saveAdapter($data["sms"]["idAdapter"], $paymentplanxservice);
      } else if (strtolower($services->name) === "email marketing") {
        $this->saveMailClass($data["email"]["idMailClass"], $paymentplanxservice);
        $this->saveMta($data["email"]["idMta"], $paymentplanxservice);
        $this->saveUrldomain($data["email"]["idUrldomain"], $paymentplanxservice);
      } else if (strtolower($services->name) === "survey") {
        $this->saveMailClass($data["survey"]["idMailClass"], $paymentplanxservice);
        $this->saveMta($data["survey"]["idMta"], $paymentplanxservice);
        $this->saveUrldomain($data["survey"]["idUrldomain"], $paymentplanxservice);
      }
    }
  }

  public function validateConfigurationService($data) {
    $sms = $data["sms"];
    $smstwoway = $data["smstwoway"];
    $email = $data["email"];
    $mailtester = $data["mailtester"];
    $attachment = $data["attachment"];
    $survey = $data["survey"];

    if (isset($sms)) {
      if (!isset($sms["idPlanType"]) && !is_numeric($sms["idPlanType"])) {
        throw new \InvalidArgumentException("Debe seleccionar un tipo de plan para SMS");
      }
      if (!isset($sms["idPriceList"]) && !is_numeric($sms["idPriceList"])) {
        throw new \InvalidArgumentException("Debe seleccionar una lista de precios para SMS");
      }
      if (!isset($sms["amount"]) && !is_numeric($sms["amount"]) || $sms["amount"] < 1) {
        throw new \InvalidArgumentException("Debe ingresar una cantidad de SMS y esta debe ser mayor o igual 1");
      }
      if (!isset($sms["speed"]) && !is_numeric($sms["speed"]) || $sms["speed"] < 1) {
        throw new \InvalidArgumentException("Debe ingresar una velocidad y esta debe ser mayor o igual 1 y menor o igual a 100");
      }
    }

    if (isset($smstwoway)) {
      if (!isset($smstwoway["idPlanType"]) && !is_numeric($smstwoway["idPlanType"])) {
        throw new \InvalidArgumentException("Debe seleccionar un tipo de plan para SMS doble-via");
      }
      if (!isset($smstwoway["idPriceList"]) && !is_numeric($smstwoway["idPriceList"])) {
        throw new \InvalidArgumentException("Debe seleccionar una lista de precios para SMS doble-via");
      }
      if (!isset($smstwoway["amount"]) && !is_numeric($smstwoway["amount"]) || $smstwoway["amount"] < 1) {
        throw new \InvalidArgumentException("Debe ingresar una cantidad de SMS doble-via y esta debe ser mayor o igual 1");
      }
    }

    if (isset($smstwoway)) {
      if (!isset($smstwoway["idPlanType"]) && !is_numeric($smstwoway["idPlanType"])) {
        throw new \InvalidArgumentException("Debe seleccionar un tipo de plan para SMS doble-via");
      }
      if (!isset($smstwoway["idPriceList"]) && !is_numeric($smstwoway["idPriceList"])) {
        throw new \InvalidArgumentException("Debe seleccionar una lista de precios para SMS doble-via");
      }
      if (!isset($smstwoway["amount"]) && !is_numeric($smstwoway["amount"]) || $smstwoway["amount"] < 1) {
        throw new \InvalidArgumentException("Debe ingresar una cantidad de SMS doble-via y esta debe ser mayor o igual 1");
      }
    }

    if (isset($landingpage)) {
      if (!isset($landingpage["idPlanType"]) && !is_numeric($landingpage["idPlanType"])) {
        throw new \InvalidArgumentException("Debe seleccionar un tipo de plan para Landing Page");
      }
      if (!isset($landingpage["idPriceList"]) && !is_numeric($landingpage["idPriceList"])) {
        throw new \InvalidArgumentException("Debe seleccionar una lista de precios para Landing Page");
      }
      if ($this->user->Usertype->name == "root") {
        if (!isset($landingpage["accountingMode"]) && !empty($landingpage["accountingMode"])) {
          throw new \InvalidArgumentException("Debe seleccionar un modo");
        }
      }
      if (!isset($landingpage["amount"]) && !is_numeric($landingpage["amount"]) || $landingpage["amount"] < 1) {
        throw new \InvalidArgumentException("Debe ingresar una cantidad y esta debe ser mayor o igual 1");
      }
    }

    if (isset($mailtester)) {
      /* if (!isset($mailtester["idPlanType"]) && !is_numeric($mailtester["idPlanType"])) {
        throw new \InvalidArgumentException("Debe seleccionar un tipo de plan para Mail Tester");
        } */
      if (!isset($mailtester["idPriceList"]) && !is_numeric($mailtester["idPriceList"])) {
        throw new \InvalidArgumentException("Debe seleccionar una lista de precios para Mail Tester");
      }
      if (!isset($mailtester["amount"]) && !is_numeric($mailtester["amount"]) || $mailtester["amount"] < 1) {
        throw new \InvalidArgumentException("Debe ingresar una cantidad y esta debe ser mayor o igual 1");
      }
    }
    if (isset($attachment)) {
      if (!isset($attachment["idPriceList"]) && !is_numeric($attachment["idPriceList"])) {
        throw new \InvalidArgumentException("Debe seleccionar una lista de precios para Adjuntar archivos");
      }
    }
    if (isset($survey)) {
      if (!isset($survey["idPriceList"]) && !is_numeric($survey["idPriceList"])) {
        throw new \InvalidArgumentException("Debe seleccionar una lista de precios para Encuestas");
      }
      if (!isset($survey["amountQuestion"]) && !is_numeric($survey["amountQuestion"]) || $survey["amountQuestion"] < 1) {
        throw new \InvalidArgumentException("Debe ingresar una cantidad de preguntas y esta debe ser mayor o igual 1");
      }
      if (!isset($survey["amountAnswer"]) && !is_numeric($survey["amountAnswer"]) || $survey["amountAnswer"] < 1) {
        throw new \InvalidArgumentException("Debe ingresar una cantidad de respuestas y esta debe ser mayor o igual 1");
      }
    }
  }

  public function saveAdapter($adapters, \PaymentPlanxservice $paymentplanxservice) {
    if (!isset($adapters)) {
      throw new \InvalidArgumentException("Debe seleccionar al menos un adaptador");
    }

    foreach ($adapters as $value) {
      $ppxsxadapter = new \Ppxsxadapter();
      $ppxsxadapter->idPaymentPlanxService = $paymentplanxservice->idPaymentPlanxService;
      $ppxsxadapter->idAdapter = $value;
      if (!$ppxsxadapter->save()) {
        foreach ($ppxsxadapter->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
  }

  public function saveMailClass($mailclass, \PaymentPlanxservice $paymentplanxservice) {
    if (!isset($mailclass)) {
      throw new \InvalidArgumentException("Debe seleccionar al menos un MailClass");
    }

    foreach ($mailclass as $value) {
      $ppxsxmailclass = new \PpxsxmailClass();
      $ppxsxmailclass->idPaymentPlanxService = $paymentplanxservice->idPaymentPlanxService;
      $ppxsxmailclass->idMailClass = $value;
      if (!$ppxsxmailclass->save()) {
        foreach ($ppxsxmailclass->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
  }

  public function saveMta($mtas, \PaymentPlanxservice $paymentplanxservice) {
    if (!isset($mtas)) {
      throw new \InvalidArgumentException("Debe seleccionar al menos un Mta");
    }

    foreach ($mtas as $value) {
      $ppxsxmta = new \Ppxsxmta();
      $ppxsxmta->idPaymentPlanxService = $paymentplanxservice->idPaymentPlanxService;
      $ppxsxmta->idMta = $value;
      if (!$ppxsxmta->save()) {
        foreach ($ppxsxmta->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
  }

  public function saveUrldomain($urldomains, \PaymentPlanxservice $paymentplanxservice) {
    if (!isset($urldomains)) {
      throw new \InvalidArgumentException("Debe seleccionar al menos un UrlDomain");
    }

    foreach ($urldomains as $value) {
      $ppxsxurldomain = new \Ppxsxurldomain();
      $ppxsxurldomain->idPaymentPlanxService = $paymentplanxservice->idPaymentPlanxService;
      $ppxsxurldomain->idUrldomain = $value;
      if (!$ppxsxurldomain->save()) {
        foreach ($ppxsxmta->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
  }

  public function getPaymentPlan($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de plan de pago inválido");
    }

    $paymentplan = \PaymentPlan::findFirst(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($id)
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El plan de pago que intenta editar no existe");
    }

    $data = array(
        "idPaymentPlan" => $paymentplan->idPaymentPlan,
        "idMasteraccount" => $paymentplan->idMasteraccount,
        "idAllied" => $paymentplan->idAllied,
        "idCountry" => $paymentplan->idCountry,
        "created" => $paymentplan->created,
        "updated" => $paymentplan->updated,
        "deleted" => $paymentplan->deleted,
        "status" => $paymentplan->status,
        "type" => $paymentplan->type,
        "name" => $paymentplan->name,
        "description" => $paymentplan->description,
        "diskSpace" => (int) $paymentplan->diskSpace,
        "createdBy" => $paymentplan->createdBy,
        "updatedBy" => $paymentplan->updatedBy,
        "paymentplanxtax" => $this->getpaymentplanxtax($paymentplan),
        "services" => $this->getpaymentplanxservice($paymentplan)
    );

    return $data;


    //var_dump($data);
    //exit;
  }

  public function getpaymentplanxtax(\PaymentPlan $paymentplan) {
    $paymentplanxtax = \PaymentPlanxtax::find(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($paymentplan->idPaymentPlan)
    ));

    $paytax = [];
    if (count($paymentplanxtax) > 0) {
      foreach ($paymentplanxtax as $key => $value) {
        $paytax[$key] = array(
            "idPaymentPlanxTax" => $value->idPaymentPlan,
            "idTax" => $value->idTax,
            "name" => $value->Tax->name
        );
      }
    }
    return $paytax;
  }

  public function getpaymentplanxservice(\PaymentPlan $paymentplan) {
    $paymentplanxservice = \PaymentPlanxservice::find(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($paymentplan->idPaymentPlan)
    ));

    $payxservice = [];
    if (count($paymentplanxservice) > 0) {
      foreach ($paymentplanxservice as $key => $value) {
        $payxservice[$key] = array(
            "idPaymentPlanxService" => $value->idPaymentPlanxService,
            "idPaymentPlan" => $value->idPaymentPlan,
            "idPlanType" => $value->idPlanType,
            "idServices" => $value->idServices,
            "idPriceList" => $value->idPriceList,
            "created" => $value->created,
            "updated" => $value->updated,
            "status" => $value->status,
            "amount" => (int) $value->amount,
            "amountQuestion" => (int) $value->amountQuestion,
            "amountAnswer" => (int) $value->amountAnswer,
            "speed" => (int) $value->speed,
            "accountingMode" => $value->accountingMode,
            "name" => $value->Services->name,
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "ppxsxadapter" => $this->getppxsxadapter($value->idPaymentPlanxService),
            "ppxsxmailclass" => $this->getppxsxmailclass($value->idPaymentPlanxService),
            "ppxsxmta" => $this->getppxsxmta($value->idPaymentPlanxService),
            "ppxsxurldomain" => $this->geturldomain($value->idPaymentPlanxService)
        );
      }
    }

    return $payxservice;
  }

  public function getppxsxadapter($paymentplanxservice) {
    $ppxsxadapter = \Ppxsxadapter::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($paymentplanxservice)
    ));

    $ppxsxadap = [];
    if (count($ppxsxadapter) > 0) {
      foreach ($ppxsxadapter as $key => $value) {
        $ppxsxadap[$key] = array(
            "idPpxsxAdapter" => $value->idPpxsxAdapter,
            "idAdapter" => $value->idAdapter,
            "name" => $value->Adapter->fname
        );
      }
    }

    return $ppxsxadap;
  }

  public function getppxsxmailclass($paymentplanxservice) {
    $ppxsxmailclass = \PpxsxmailClass::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($paymentplanxservice)
    ));

    $ppxsxmailcla = [];
    if (count($ppxsxmailclass) > 0) {
      foreach ($ppxsxmailclass as $key => $value) {
        $ppxsxmailcla[$key] = array(
            "idPpxsxMailClass" => $value->idPpxsxMailClass,
            "idMailClass" => $value->idMailClass,
            "name" => $value->Mailclass->name
        );
      }
    }

    return $ppxsxmailcla;
  }

  public function getppxsxmta($paymentplanxservice) {
    $ppxsxmta = \Ppxsxmta::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($paymentplanxservice)
    ));

    $ppxsxmtas = [];
    if (count($ppxsxmta) > 0) {
      foreach ($ppxsxmta as $key => $value) {
        $ppxsxmtas[$key] = array(
            "idPpxsxMta" => $value->idPpxsxMta,
            "idMta" => $value->idMta,
            "name" => $value->Mta->name
        );
      }
    }

    return $ppxsxmtas;
  }

  public function geturldomain($paymentplanxservice) {
    $ppxsxurldomain = \Ppxsxurldomain::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($paymentplanxservice)
    ));

    $ppxsxurl = [];
    if (count($ppxsxurldomain) > 0) {
      foreach ($ppxsxurldomain as $key => $value) {
        $ppxsxurl[$key] = array(
            "idPpxsxUrldomain" => $value->idPpxsxUrldomain,
            "idUrldomain" => $value->idUrldomain,
            "name" => $value->Urldomain->name
        );
      }
    }

    return $ppxsxurl;
  }

  public function editPaymentPlan($data) {
    $paymentplan = \PaymentPlan::findFirst(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($data["data"]["idPaymentPlan"])
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El plan de pago que intenta editar no existe");
    }

    $this->form->bind($data["data"], $paymentplan);
    $status = (($data["data"]["status"] == true) ? 1 : 0);
    $paymentplan->status = $status;

    if (!$this->form->isValid() || !$paymentplan->update()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      foreach ($paymentplan->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    $this->editPaymentPlanxtax($data, $paymentplan);
    $this->editPaymentPlanxService($data, $paymentplan);

    return ["message" => "El plan de pago se ha actualizado exitosamente"];
  }

  public function editPaymentPlanxtax($data, $paymentplan) {
    $paymentplanxtaxs = \PaymentPlanxtax::find(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($paymentplan->idPaymentPlan)
    ));

    if (count($paymentplanxtaxs) > 0) {
      foreach ($paymentplanxtaxs as $value) {
        if (!$value->delete()) {
          foreach ($value->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }
    $this->savePaymentPlanxtax($data["tax"]["idTax"], $paymentplan);
  }

  public function editPaymentPlanxService($data, $paymentplan) {
    $this->cleanPaymentPlanxService($paymentplan);
    $this->savePaymentPlanxservice($data, $paymentplan);
  }

  public function cleanPaymentPlanxService($paymentplan) {
    $paymentplanxservice = \PaymentPlanxservice::find(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($paymentplan->idPaymentPlan)
    ));

    foreach ($paymentplanxservice as $key => $ppxs) {
      $name = strtolower($ppxs->Services->name);

      if ($name === "sms") {
        $this->cleanAdapter($ppxs);
        $this->deletePaymentPlanxService($ppxs);
        unset($key);
      }

      if ($name === "sms doble-via") {
        $this->deletePaymentPlanxService($ppxs);
        unset($key);
      }
    
      if ($name === "landing page") {
        $this->deletePaymentPlanxService($ppxs);
        unset($key);
      }

      if ($name === "email marketing") {
        $this->cleanMailClass($ppxs);
        $this->cleanMta($ppxs);
        $this->cleanUrldomain($ppxs);
        $this->deletePaymentPlanxService($ppxs);
        unset($key);
      }
      if ($name === "mail tester") {
        $this->deletePaymentPlanxService($ppxs);
        unset($key);
      }
      if ($name === "adjuntar archivos") {
        $this->deletePaymentPlanxService($ppxs);
        unset($key);
      }
      if ($name === "survey") {
        $this->cleanMailClass($ppxs);
        $this->cleanMta($ppxs);
        $this->cleanUrldomain($ppxs);
        $this->deletePaymentPlanxService($ppxs);
        unset($key);
      }
    }
  }

  public function deletePaymentPlanxService($paymentPlanxService) {
    if (!$paymentPlanxService->delete()) {
      foreach ($paymentPlanxService->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

  public function cleanAdapter($paymentplanxservice) {
    $ppxsxadapter = \Ppxsxadapter::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($paymentplanxservice->idPaymentPlanxService)
    ));

    if (count($ppxsxadapter) > 0) {
      foreach ($ppxsxadapter as $value) {
        if (!$value->delete()) {
          foreach ($value->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }
  }

  public function cleanMailClass($paymentplanxservice) {
    $ppxsxmailclass = \PpxsxmailClass::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($paymentplanxservice->idPaymentPlanxService)
    ));

    if (count($ppxsxmailclass) > 0) {
      foreach ($ppxsxmailclass as $value) {
        if (!$value->delete()) {
          foreach ($value->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }
  }

  public function cleanMta($paymentplanxservice) {
    $ppxsxmta = \Ppxsxmta::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($paymentplanxservice->idPaymentPlanxService)
    ));

    if (count($ppxsxmta) > 0) {
      foreach ($ppxsxmta as $value) {
        if (!$value->delete()) {
          foreach ($value->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }
  }

  public function cleanUrldomain($paymentplanxservice) {
    $ppxsxurldomain = \Ppxsxurldomain::find(array(
                "conditions" => "idPaymentPlanxService = ?0",
                "bind" => array($paymentplanxservice->idPaymentPlanxService)
    ));

    if (count($ppxsxurldomain) > 0) {
      foreach ($ppxsxurldomain as $value) {
        if (!$value->delete()) {
          foreach ($value->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
    }
  }

  public function deletePaymentPlan($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("Dato de plan inválido");
    }

    $paymentplan = \PaymentPlan::findFirst(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($id)
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El plan de pago que intenta eliminar no existe");
    }

    $paymentplan->deleted = time();
    if (!$paymentplan->update()) {
      foreach ($paymentplan->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return ["message" => "El plan de pago se ha eliminado exitosamente"];
  }

  /**
   * @author Jordan Zapata <jordan.zapata@sigmamovil.com>
   * @param type $email, recibe un email
   * @return int confirmacion de si es correcto o incorrecto
   */
  public function ValidateEmail($email) {
    $emailRight = 0;
    if ((strlen($email) >= 6) && (substr_count($email, "@") == 1) && (substr($email, 0, 1) != "@") && (substr($email, strlen($email) - 1, 1) != "@")) {
      if ((!strstr($email, "'")) && (!strstr($email, "\"")) && (!strstr($email, "\\")) && (!strstr($email, "\$")) && (!strstr($email, " "))) {
        if (substr_count($email, ".") >= 1) {
          $term_dom = substr(strrchr($email, '.'), 1);
          if (strlen($term_dom) > 1 && strlen($term_dom) < 5 && (!strstr($term_dom, "@"))) {
            $antes_dom = substr($email, 0, strlen($email) - strlen($term_dom) - 1);
            $caracter_ult = substr($antes_dom, strlen($antes_dom) - 1, 1);
            if ($caracter_ult != "@" && $caracter_ult != ".") {
              $emailRight = 1;
            }
          }
        }
      }
    }
    if ($emailRight)
      return 1;
    else
      return 0;
  }

  /**
   * @author Jordan Zapata <jordan.zapata@sigmamovil.com>
   * @return type confirmacion de la validacion del plan de cortesia
   */
  public function validateCourtesyPlan($data) {

    $paymentplan = \PaymentPlan::find(array(
                "conditions" => "idAllied = ?0 and idCountry = ?1 and deleted = 0 and courtesyplan = 1",
                "bind" => array($this->user->Usertype->idAllied, $data->country)
    ));
    $confir = false;
    if (count($paymentplan) > 1) {
      $confir = true;
    }
    return $confir;
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
  
  public function getServices() {
    if (isset($this->user->Usertype->Masteraccount->idPaymentPlan)) {
        $idPaymentPlan = $this->user->Usertype->Masteraccount->idPaymentPlan;
        $services = $this->queryservices($idPaymentPlan);
      } else if (isset($this->user->Usertype->Allied->idPaymentPlan)) {
        $idPaymentPlan = $this->user->Usertype->Allied->idPaymentPlan;
        $services = $this->queryservices($idPaymentPlan);
      } else {
        $services = \Services::find();
      }
      $data = [];
      if (count($services) > 0) {
        foreach ($services as $key => $value) {
          if ($value->idServices == 4) {
            continue;
          }
          $data[$key] = array(
              "idServices" => $value->idServices,
              "name" => $value->name
          );
        }
      }

      return $data;
  }

}
