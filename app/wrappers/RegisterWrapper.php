<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\Wrapper;

use Sigmamovil\General\Links\ParametersEncoder;

/**
 * Description of RegisterWrapper
 *
 * @author juan.pinzon
 */
class RegisterWrapper extends \BaseWrapper {

  private $nameRoles;
  private $roles;
  private $security;
  private $configFb;
  private $session;

  public function __construct() {
    $this->nameRoles = \Phalcon\DI::getDefault()->get('nameRoles');
    $this->roles = \Phalcon\DI::getDefault()->get('roles');
    $this->security = \Phalcon\DI::getDefault()->get('security');
    $this->configFb = \Phalcon\DI::getDefault()->get('configFb');
    $this->session = \Phalcon\DI::getDefault()->get('session');
    parent::__construct();
  }

  public function createAccSubaccount($data) {

    $account = new \Account;
    $form = new \AccountForm();
    $form->bind($data["account"], $account);

    if (!$form->isValid()) {
      foreach ($form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    if (!isset($data["account"]["idCity"])) {
      throw new \InvalidArgumentException("Debe seleccionar la ciudad");
    }

    $allied = \Allied::findFirst(array(
                "conditions" => "idAllied  = ?0",
                "bind" => array($this->idAllied->idAlliedSigma)
    ));

    $detailConfigAllied = $allied->AlliedConfig->DetailConfig;

    $account->name .= "{$data['account']['lastname']}";
    if(isset($data["account"]["nit"])){
        $account->nit = $data["account"]["nit"];
    }
    if(isset($data["account"]["nomemp"])){
        $account->companyName = $data["account"]["nomemp"];
    }
    $account->idCity = $data["account"]["idCity"];
    $account->idAllied = $allied->idAllied;
    $account->status = 0;
    $account->phone = $account->phone;
    $account->createdBy = $account->email;
    $account->updatedBy = $account->email;
    $account->registerType = "form";
    $account->registerType = "form";
    $termsconditions = 0;
    if ($data["account"]["acceptTermsConditions"] == true) {
      $termsconditions = 1;
    };
    $account->termsconditions = $termsconditions;
    $account->ip = $this->getIpClient();



    $account->idAccountCategory = $allied->idAccountCategory;

    if (!$account->save()) {
      foreach ($account->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $paymentplan = \PaymentPlan::findFirst(array(
                "conditions" => "idCountry = ?0 AND courtesyplan = ?1 AND idAllied = ?2",
                "bind" => array($account->City->State->Country->idCountry, 1, $allied->idAllied)
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El país donde se encuentra no tiene plan de cortesía");
    }

    $account->idPaymentPlan = $paymentplan->idPaymentPlan;

    $account->save();

    $this->createCategories($account->idAccount);

    $dataUser = array(
        "name" => $data["account"]["name"],
        "lastname" => $data["account"]["lastname"],
        "email" => $data["account"]["email"],
        "cellphone" => $data["account"]["phone"],
        "pass1" => $data["account"]["pass1"],
        "pass2" => $data["account"]["pass2"],
        "idCity" => $data["account"]["idCity"]
    );


    $this->createUser($dataUser, $this->createUsertype($account->idAccount, 1), $this->roles->account);
    $subaccount = $this->createSubaccount($account);
    $userSubaccount = $this->createUser($dataUser, $this->createUsertype($subaccount->idSubaccount, 2), $this->roles->subaccount);

//Inicio de configuración de plan de pago
    $paymentPlanxService = $paymentplan->paymentPlanxService;

    $accountConfig = new \AccountConfig();
    $accountConfig->idAccount = $account->idAccount;
    $accountConfig->diskSpace = $paymentPlanxService[0]->PaymentPlan->diskSpace;

    if (!$accountConfig->save()) {
      foreach ($accountConfig->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $accountController = new \AccountController();

    foreach ($detailConfigAllied as $key => $configAllied) {
      if (count($paymentPlanxService) == 1) {
        $accountController->createOnePlan($paymentPlanxService, $configAllied, $accountConfig);
        //$this->createOnePlan($paymentPlanxService, $configAllied, $accountConfig);
      } else {
        $accountController->selectTwoPlan($paymentPlanxService, $key, $configAllied, $accountConfig);
      }
    }

    $detailConfigAccount = \DetailConfig::find(array(
                "conditions" => "idAccountConfig  = ?0",
                "bind" => array($accountConfig->idAccountConfig)
    ));

    foreach ($detailConfigAccount as $value) {
      $saxs = new \Saxs();
      if ($value->idServices == $this->services->sms) {

        $saxs->idSubaccount = $subaccount->idSubaccount;
        $saxs->idServices = $value->idServices;
        $saxs->amount = $value->amount;
        $saxs->totalAmount = $value->totalAmount;

        $this->saveTable($saxs);

        $value->amount = 0;
        $this->saveTable($value);
      }
      if ($value->idServices == $this->services->email_marketing) {
        if ($value->accountingMode == "contact") {

          $saxs->idSubaccount = $subaccount->idSubaccount;
          $saxs->idServices = $value->idServices;
          $saxs->accountingMode = $value->accountingMode;

          $this->saveTable($saxs);
        } else if ($value->accountingMode == "sending") {

          $saxs->idSubaccount = $subaccount->idSubaccount;
          $saxs->idServices = $value->idServices;
          $saxs->amount = $value->amount;
          $saxs->totalAmount = $value->totalAmount;
          $saxs->accountingMode = $value->accountingMode;

          $this->saveTable($saxs);

          $value->amount = 0;
          $this->saveTable($value);
        }
      }
    }
//Final de configuración de plan de pago
    $this->sendMail($userSubaccount);
    $this->contentMailNotificationAllied($userSubaccount);
    return $userSubaccount->idUser;
  }

  public function createUser($dataUser, $idUsertype, $rol) {
    $user = new \User();
    $form = new \UserForm();
    $form->bind($dataUser, $user);

    $user->idRole = $rol;
    $user->idUsertype = $idUsertype;
    $user->idCity = $dataUser["idCity"];
    $user->password = $this->comparePassword($dataUser["pass1"], $dataUser["pass2"]);
    $user->registerType = 1;
    $user->createdBy = $user->email;
    $user->updatedBy = $user->email;

    if (isset($dataUser["registerType"])) {
      $user->registerType = $dataUser["registerType"];
    }

    $us = \User::findFirst(array(//us -> abreviado para user
                "columns" => "email, idUsertype, idRole",
                "conditions" => "email = ?0 AND idRole = ?1",
                "bind" => array($user->email, $user->idRole)
    ));

    if ($us) {
      throw new \InvalidArgumentException("El correo con el que se intenta suscribir ya existe en la plataforma");
    }

    if (!$user->save()) {
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $user;
  }

  public function createUsertype($idTypeAccount, $type) {
    $usertype = new \Usertype();
    if ($type == 1) {
      $usertype->idAccount = $idTypeAccount;
      $usertype->name = $this->nameRoles->account;
    } elseif ($type == 2) {
      $usertype->idSubaccount = $idTypeAccount;
      $usertype->name = $this->nameRoles->subaccount;
    }

    if (!$usertype->save()) {
      foreach ($usertype->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $usertype->idUsertype;
  }

  public function createSubaccount(\Account $account) {
    $subaccount = new \Subaccount();
    $subaccount->idAccount = $account->idAccount;
    $subaccount->idCity = $account->idCity;
    $subaccount->name = $account->name;
    $subaccount->description = "";
    $subaccount->status = 0;
    $subaccount->createdBy = $account->email;
    $subaccount->updatedBy = $account->email;

    if (!$subaccount->save()) {
      foreach ($subaccount->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $subaccount;
  }

  public function comparePassword($pass1, $pass2) {
    if ($pass1 !== $pass2) {
      throw new \InvalidArgumentException("Las contraseñas no coinciden, por favor verifique los datos");
    }

    return $this->security->hash($pass1);
  }

  public function listpaymentplan() {
    $paymentplan = \PaymentPlan::find(array(
                "columns" => "idPaymentPlan, name",
                "conditions" => "deleted = ?0 AND status = ?1 AND type = ?2 AND idAllied = ?3",
                "bind" => array(0, 1, 'public', 146)
    ));

    $data = [];
    if (count($paymentplan) > 0) {
      foreach ($paymentplan as $key => $value) {
        $data[$key] = array(
            "idPaymentPlan" => $value->idPaymentPlan,
            "name" => $value->name
        );
      }
    }

    return $data;
  }

  public function detailPaymentPlan($idPaymentPlan) {
    $paymentplan = \PaymentPlan::findFirst(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($idPaymentPlan)
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El plan de pago al que solicitó no existe");
    }

    $ppxs = \PaymentPlanxservice::find(array(
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($paymentplan->idPaymentPlan)
    ));

    $services = [];
    $finalPrice = 0;
    if (count($ppxs) > 0) {
      foreach ($ppxs as $key => $value) {
        $finalPrice += $value->amount * $value->PriceList->price;
        $services[$key] = array(
            "plantype" => $value->PlanType,
            "service" => $value->Services,
            "pricelist" => $value->PriceList,
            "amount" => $value->amount,
            "speed" => $value->speed,
            "accountingMode" => $value->accountingMode
        );
      }
    } else {
      throw new \InvalidArgumentException("El plan de pago que ha seleccionado no tiene servicios asignados");
    }

    $data = array(
        "idPaymentPlan" => $paymentplan->idPaymentPlan,
        "name" => $paymentplan->name,
        "description" => $paymentplan->description,
        "diskSpace" => $paymentplan->diskSpace,
        "finalPrice" => $finalPrice,
        "services" => $services
    );

    return $data;
  }

  public function sendMail($user) {

    $contactAdmin = \SupportContact::findFirst(array(
                "conditions" => "deleted = ?0 AND idAllied = ?1 AND type = ?2",
                "bind" => array(0, $this->idAllied->idAlliedSigma, "administrative")//Cambiar ese uno por el id del aliado de SigmaMóvil
    ));

    if (!$contactAdmin) {
      throw new \InvalidArgumentException("No existe al menos un contacto administrativo para envíar el correo de verificación");
    }

    $data = new \stdClass();
    $data->fromEmail = $contactAdmin->email;
    $data->fromname = $contactAdmin->name . ' ' . $contactAdmin->lastname;
    $data->subject = "Activar su cuenta de AIO";
    $data->html = str_replace("tmp-url", "prueba", $this->contentMail($this->linkActivation($user)));
    $data->plainText = "Activación de cuenta AIO.";
    $data->to = array($user->email);
    $data->from = $contactAdmin->email;

    //\Phalcon\DI::getDefault()->get('logger')->log(print_r($data, true));
    $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48',25);
    $mtaSender->setDataMessage($data);
    $mtaSender->sendMail();
  }

  public function linkActivation($user) {
    $parametersencoder = new ParametersEncoder();
    $parametersencoder->setBaseUri($this->urlManager->get_base_uri(true));
    $linkVerify = $parametersencoder->encodeLink("register/validatemail", array(
        $user->idUser
    ));

    return $linkVerify;
  }

  public function contentMail($link) {
    $linkYoutube = "https://www.youtube.com/channel/UCC_-Dd4-718gwoCPux8AtwQ/playlists";
    $content = '<!DOCTYPE html>'
            . '<html>'
            . '<head>'
            . '<meta charset="UTF-8">'
            . '</head>'
            . '<table style="background-color: #E6E6E6; width: 100%;">'
            . '<tbody>'
            . '<tr>'
            . '<td style="padding: 20px;"><center>'
            . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
            . '<tbody>'
            . '<tr>'
            . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
            . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
            . '<tbody></tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '<tr>'
            . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
            . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
            . '<tbody>'
            . '<tr>'
            . '<td style="padding-left: 0px; padding-right: 0px;">'
            . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
            . '<tbody>'
            . '<tr>'
            . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
            . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
            . '<tbody>'
            . '<tr>'
            . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
            . '<p></p>'
            . '<h2><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
            . 'Estimado usuario:'
            . '</span></h2>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '<tr>'
            . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
            . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
            . '<tbody>'
            . '<tr>'
            . '<td style="padding-left: 0px; padding-right: 0px;">'
            . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
            . '<tbody>'
            . '<tr>'
            . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
            . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
            . '<tbody>'
            . '<tr>'
            . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
            . '<p></p>'
            . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
            . 'Gracias por registrarse en nuestra plataforma AIO. <br><br>'
            . 'Ahora debes activar tu cuenta, para ello debes hacer click en el siguiente link:'
            . '</span></p>'
            . '<p style="text-align: center;"><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
            . "<a href='{$link}' style='font-size: 20px'>Verificar Cuenta</a>"
            . '</span></p>'
            . '<p style="text-align: center;"><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
            . 'Gracias por registrarse en nuestra plataforma AIO. <br><br>'
            . 'Conoce el paso a paso de cómo usar nuestra plataforma All In One.<br><br>'
            . "<a href='{$linkYoutube}' style='font-size: 20px'>Haz click aquí</a>"
            . '</span></p>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p style="text-align: justify;"><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>'
            . '</html>';


    return $content;
  }

  public function verifyAccount($id) {
    if (!isset($id)) {
      throw new \InvalidArgumentException("No hay ningún dato de cuenta para verificar");
    }

    $subaccount = \Subaccount::findFirst(array(
                "conditions" => "idSubaccount = ?0",
                "bind" => array($id)
    ));

    $account = \Account::findFirst(array(
                "columns" => "idAccount",
                "conditions" => "status = ?0 AND idAccount = ?1", // AND idPaymentPlan IS NULL",
                "bind" => array(0, $subaccount->idAccount)
    ));

    if (!$account) {
      throw new \InvalidArgumentException("La cuenta que intenta verificar ya fue verificada o no existe");
    }

    return $subaccount;
  }

  public function assignPaymentPlanToAccount($data) {
    if (!isset($data->idSub) || !isset($data->paymentPlan)) {
      throw new \InvalidArgumentException("No hay datos para la asignación de plan de pago");
    }

    $paymentplan = \PaymentPlan::findFirst(array(
                "columns" => "idPaymentPlan",
                "conditions" => "idPaymentPlan = ?0",
                "bind" => array($data->paymentPlan)
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El plan de pago seleccionado no existe");
    }

    $subaccount = $this->verifyAccount($data->idSub);
    $account = $subaccount->Account;

    $account->idPaymentPlan = $paymentplan->idPaymentPlan;

    if (!$account->save()) {
      foreach ($account->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

  public function getAppIdFacebookLogin() {
    if (!isset($this->configFb->idApp)) {
      throw new \InvalidArgumentException("No se encontró el idApp de la aplicación de AIO para el inicio de sesión");
    }
    return ["idAppFb" => $this->configFb->idApp];
  }

  public function createAccountWithFacebook($data) {

    $this->validateDataFB($data);
    $allied = \Allied::findFirst(array(
                "columns" => "idAllied, idAccountCategory",
                "conditions" => "idAllied  = ?0",
                "bind" => array($this->idAllied->idAlliedSigma)
    ));

    if (!$allied) {
      throw new \InvalidArgumentException("No existe el alido de Sigma Móvil para asociar esta cuenta");
    }
    $data->registerType = "facebook";
    $account = $this->createSimpleAccount($data, $allied);
    $dataUser = array(
        "name" => $data->first_name,
        "lastname" => $data->last_name,
        "email" => $data->email,
        "pass1" => $data->id,
        "pass2" => $data->id,
        "registerType" => 2,
        "idCity" => NULL
    );
    $this->createUser($dataUser, $this->createUsertype($account->idAccount, 1), $this->roles->account);
    $subaccount = $this->createSimpleSubaccount($data, $account);
    $userSubaccount = $this->createUser($dataUser, $this->createUsertype($subaccount->idSubaccount, 2), $this->roles->subaccount);

    $this->sendMail($userSubaccount);
    $this->contentMailNotificationAllied($userSubaccount);
    return ["idUser" => $userSubaccount->idUser];
  }

  public function validateDataFB($data) {
    if (!isset($data->email)) {
      throw new \InvalidArgumentException("No se ha podido obtener el correo de su facebook, por favor habilitelo para poder suscribirse");
    }
    if (!isset($data->first_name)) {
      throw new \InvalidArgumentException("No se ha podido obtener el nombre de su facebook, por favor habilitelo para poder suscribirse");
    }
    if (!isset($data->last_name)) {
      throw new \InvalidArgumentException("No se ha podido obtener el apellido de su facebook, por favor habilitelo para poder suscribirse");
    }
    if (!isset($data->id)) {
      throw new \InvalidArgumentException("No se ha podido obtener el id de su facebook, por favor valide su facebook o intente otro método de suscripción");
    }
  }

  public function createSimpleAccount($data, $allied) {
    $termsConditions = 0;
    if ($data->termsConditions == true) {
      $termsConditions = 1;
    }
    $account = new \Account();
    $account->idAllied = $allied->idAllied;
    $account->idAccountCategory = $allied->idAccountCategory;
    $account->name = "{$data->first_name} {$data->last_name}";
    $account->email = $data->email;
    $account->status = 0;
    $account->createdBy = $account->email;
    $account->updatedBy = $account->email;
    $account->registerType = $data->registerType;
    $account->ip = $this->getIpClient();
    $account->termsconditions = $termsConditions;

    if (!$account->save()) {
      foreach ($account->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $account;
  }

  public function createSimpleSubaccount($data, $account) {
    $subaccount = new \Subaccount();
    $subaccount->idAccount = $account->idAccount;
    $subaccount->name = "{$data->first_name} {$data->last_name}";
    $subaccount->description = "";
    $subaccount->status = 0;
    $subaccount->createdBy = $account->email;
    $subaccount->updatedBy = $account->email;

    if (!$subaccount->save()) {
      foreach ($subaccount->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $subaccount;
  }

  public function saveTable($nameTable) {
    if (!$nameTable->save()) {
      foreach ($nameTable->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    return $nameTable;
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

  public function completeProfileUser($data) {
    if (!isset($data)) {
      throw new \InvalidArgumentException("Debe seleccionar una ciudad para continuar con el inicio de sesión");
    }

    $us = $this->session->get("parcialUser");

    if (!isset($us)) {
      $this->logger->log("Esta es la excepción producida para el inicio de sesión");
      \Phalcon\DI::getDefault()->get('notification')->error("Ha ocurrido un error autenticando sus datos, por favor contacte con soporte");
      return ["status" => "inauthorized"];
    }

    $user = $us;

    $user->idCity = $data;

    if (!$user->save()) {
      foreach ($user->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $useracc = \User::findFirst(array(
                "conditions" => "email = ?0 AND idRole = ?1",
                "bind" => array($user->email, $this->roles->account)
    ));

    $useracc->idCity = $data;

    if (!$useracc->save()) {
      foreach ($useracc->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $subaccount = $user->Usertype->Subaccount;
    $subaccount->idCity = $data;

    if (!$subaccount->save()) {
      foreach ($subaccount->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $account = $subaccount->Account;
    $account->idCity = $data;
    $account->save();

    $allied = $account->Allied;

    $paymentplan = \PaymentPlan::findFirst(array(
                "conditions" => "idCountry = ?0 AND courtesyplan = ?1 AND idAllied = ?2",
                "bind" => array($account->City->State->Country->idCountry, 1, $allied->idAllied)
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El país donde se encuentra no tiene plan de cortesía");
    }

    $account->idPaymentPlan = $paymentplan->idPaymentPlan;

    if (!$account->save()) {
      foreach ($account->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $this->createCategories($account->idAccount);

    $detailConfigAllied = $allied->AlliedConfig->DetailConfig;

//Inicio de configuración de plan de pago
    $paymentPlanxService = $paymentplan->paymentPlanxService;

    $accountConfig = new \AccountConfig();
    $accountConfig->idAccount = $account->idAccount;
    $accountConfig->diskSpace = $paymentPlanxService[0]->PaymentPlan->diskSpace;

    if (!$accountConfig->save()) {
      foreach ($accountConfig->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $accountController = new \AccountController();

    foreach ($detailConfigAllied as $key => $configAllied) {
      if (count($paymentPlanxService) == 1) {
        $accountController->createOnePlan($paymentPlanxService, $configAllied, $accountConfig);
        //$this->createOnePlan($paymentPlanxService, $configAllied, $accountConfig);
      } else {
        $accountController->selectTwoPlan($paymentPlanxService, $key, $configAllied, $accountConfig);
      }
    }

    $detailConfigAccount = \DetailConfig::find(array(
                "conditions" => "idAccountConfig  = ?0",
                "bind" => array($accountConfig->idAccountConfig)
    ));

    foreach ($detailConfigAccount as $value) {
      $saxs = new \Saxs();
      if ($value->idServices == $this->services->sms) {

        $saxs->idSubaccount = $subaccount->idSubaccount;
        $saxs->idServices = $value->idServices;
        $saxs->amount = $value->amount;
        $saxs->totalAmount = $value->totalAmount;

        $this->saveTable($saxs);

        $value->amount = 0;
        $this->saveTable($value);
      }
      if ($value->idServices == $this->services->email_marketing) {
        if ($value->accountingMode == "contact") {

          $saxs->idSubaccount = $subaccount->idSubaccount;
          $saxs->idServices = $value->idServices;
          $saxs->accountingMode = $value->accountingMode;

          $this->saveTable($saxs);
        } else if ($value->accountingMode == "sending") {

          $saxs->idSubaccount = $subaccount->idSubaccount;
          $saxs->idServices = $value->idServices;
          $saxs->amount = $value->amount;
          $saxs->totalAmount = $value->totalAmount;
          $saxs->accountingMode = $value->accountingMode;

          $this->saveTable($saxs);

          $value->amount = 0;
          $this->saveTable($value);
        }
      }
    }
//Final de configuración de plan de pago

    $this->session->set('idUser', $user->idUser);
    $this->session->set('authenticated', true);
    $this->session->remove('parcialUser');

    return ["status" => "authorized"];
  }

  public function getIpClient() {

    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
      return $_SERVER["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
      return $_SERVER["HTTP_X_FORWARDED"];
    } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
      return $_SERVER["HTTP_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
      return $_SERVER["HTTP_FORWARDED"];
    } else {
      return $_SERVER["REMOTE_ADDR"];
    }
  }

  public function contentMailNotificationAllied($user) {

    //informacion del usuario

    $informationUser = new \stdClass();
    $informationUser->name = $user->name;
    $informationUser->last_name = $user->lastname;
    $informationUser->phone = $user->cellphone;
    $informationUser->email = $user->email;
    $informationUser->country = $user->City->State->Country->name;
    $informationUser->location = $user->City->State->name;
    $informationUser->city = $user->City->name;


    $contactAdmin = \SupportContact::findFirst(array(
                "conditions" => "deleted = ?0 AND idAllied = ?1 AND type = ?2",
                "bind" => array(0, $this->idAllied->idAlliedSigma, "administrative")//Cambiar ese uno por el id del aliado de SigmaMóvil
    ));
//    $systemMail = \Systemmail::findFirst(array(
//                'conditions' => 'category = ?0 and idAllied = ?1',
//                'bind' => array(0 => 'mail-notificationNewAccount', 1 => $user->Usertype->Subaccount->Account->idAllied),
//    ));
    $paymentPlan = \PaymentPlan::findFirst(array(
                'conditions' => 'deleted = 0 and courtesyplan = 1  and idAllied = ?0',
                'bind' => array(0 => $user->Usertype->Subaccount->Account->idAllied),
    ));

    $data = new \stdClass();
//    if ($systemMail) {
//      $data->fromName = $systemMail->fromName;
//      $data->fromEmail = $systemMail->fromEmail;
//      $data->subject = $systemMail->subject;
//      $systemMail->content = str_replace("%NAME_SENT%", $maildata->name, $systemMail->content);
//      $systemMail->content = str_replace("%DATETIME_SENT%", $maildata->scheduleDate, $systemMail->content);
//      $systemMail->content = str_replace("%LINK_COMPLETE_SENT%", $this->encodeLink($maildata->idMail, $msn->idSubaccount, "complete"), $systemMail->content);
//      $systemMail->content = str_replace("%LINK_SUMMARY_SENT%", $this->encodeLink($maildata->idMail, $msn->idSubaccount, "summary"), $systemMail->content);
//      $systemMail->content = str_replace("%TOTAL_SENT%", $maildata->messagesSent, $systemMail->content);
//    } else {
    $data->fromEmail = $contactAdmin->email;
    $data->fromName = $contactAdmin->name . ' ' . $contactAdmin->lastname;
    $data->from = $contactAdmin->email;
    $data->subject = "Notificación de cracion de cuenta gratuita";
    $content = '<table style="background-color: #E6E6E6; width: 100%;">'
            . '<tbody>'
            . '<tr>'
            . '<td style="padding: 20px;"><center>'
            . '<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
            . '<tbody>'
            . '<tr>'
            . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
            . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
            . '<tbody></tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '<tr>'
            . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
            . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
            . '<tbody>'
            . '<tr>'
            . '<td style="padding-left: 0px; padding-right: 0px;">'
            . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
            . '<tbody>'
            . '<tr>'
            . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
            . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
            . '<tbody>'
            . '<tr>'
            . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
            . '<p></p>'
            . '<h2><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif;">'
            . 'Estimado usuario:'
            . '</span></h2>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '<tr>'
            . '<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
            . '<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
            . '<tbody>'
            . '<tr>'
            . '<td style="padding-left: 0px; padding-right: 0px;">'
            . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
            . '<tbody>'
            . '<tr>'
            . '<td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%">'
            . '<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%">'
            . '<tbody>'
            . '<tr>'
            . '<td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">'
            . '<p></p>'
            . '<p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">'
            . 'Se le informa que el usuario  <b>' . $informationUser->name . ' ' . $informationUser->last_name . '</b> se ha registrado satisfactoriamente su cuentá de cortesía en la fecha <b>' . gmdate("Y-m-d", $user->created) . "</b>"
            . '</span></p>'
            . '</td>'
            . '</tr>'
            . '<tr>
    <td style = "word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">
    <span data-redactor = "verified" data-redactor-inlinemethods = "" style = "font-family: Trebuchet MS, sans-serif;">
    Información del usuario :
    </span>
    </td>
    </tr>
    <tr>
    <td style = "word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">
    <span data-redactor = "verified" data-redactor-inlinemethods = "" style = "font-family: Trebuchet MS, sans-serif;">
    Nombre :   ' . $informationUser->name . '  ' . $informationUser->last_name
            . '</span>
    </td>
    </tr>
    <tr>
    <td style = "word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">
    <span data-redactor = "verified" data-redactor-inlinemethods = "" style = "font-family: Trebuchet MS, sans-serif;">
    Teléfono :   ' . $informationUser->phone
            . '</span>
    </td>
    </tr>
    <tr>
    <td style = "word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">
    <span data-redactor = "verified" data-redactor-inlinemethods = "" style = "font-family: Trebuchet MS, sans-serif;">
    Correo :   ' . $informationUser->email
            . '</span>
    </td>
    </tr>
    <tr>
    <td style = "word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">
    <span data-redactor = "verified" data-redactor-inlinemethods = "" style = "font-family: Trebuchet MS, sans-serif;">
    País :   ' . $informationUser->country
            . '</span>
    </td>
    </tr>
    <tr>
    <td style = "word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">
    <span data-redactor = "verified" data-redactor-inlinemethods = "" style = "font-family: Trebuchet MS, sans-serif;">
    Departamento :   ' . $informationUser->location
            . '</span>
    </td>
    </tr>
    <tr>
    <td style = "word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;">
    <span data-redactor = "verified" data-redactor-inlinemethods = "" style = "font-family: Trebuchet MS, sans-serif;">
    Ciudad :   ' . $informationUser->city
            . '</span>
    </td>
    </tr>'
            . '</tbody>'
            . '</table>'
            . '</td>'
            . '</tr>'
            . '</tbody>'
            . '</table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p><span data-redactor="verified" data-redactor-inlinemethods="" style="color: rgb(54, 96, 146); font-family: Trebuchet MS, sans-serif; font-size: 18px;"></span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="padding-left: 0px; padding-right: 0px;"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%"><tbody><tr><td style="width: 100%; padding-left: 0px; padding-right: 0px;" width="100%"><table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width: 100%;" cellpadding="0" width="100%"><tbody><tr><td style="word-break: break-word; padding: 15px 15px; font-family: Helvetica, Arial, sans-serif;"><p></p><p><span data-redactor="verified" data-redactor-inlinemethods="" style="font-family: Trebuchet MS, sans-serif;">Si no ha solicitado ningún cambio, simplemente ignore este mensaje. Si tiene cualquier otra pregunta acerca de su cuenta, por favor, use nuestras Preguntas frecuentes o contacte con nuestro equipo de asistencia en&nbsp;</span><span style="color: rgb(227, 108, 9); font-family: Trebuchet MS, sans-serif; background-color: initial;">soporte@sigmamovil.com.</span></p></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;"><table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0"><tbody></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table>';

    $data->html = str_replace("tmp-url", "prueba", $content);
    $data->plainText = "Se ha enviado un correo electronico.";
//    }
    $data->to = array();
    $prueba = (array) json_decode($paymentPlan->emailnotification);
    $prueba[] = "leads@sigmamovil.com";
//    $prueba[] = "comercial@sigmamovil.com";
    foreach ($prueba as $key => $value) {
      array_push($data->to, trim($value));
    }
    $mtaSender = new \Sigmamovil\General\Misc\MtaSender(\Phalcon\DI::getDefault()->get('mta')->address, \Phalcon\DI::getDefault()->get('mta')->port);
    $mtaSender->setDataMessage($data);
    $mtaSender->sendMail();
  }

  public function register($data){
    $this->db->begin();  
    $account = new \Account;

    
    $allied = \Allied::findFirst(array(
      "conditions" => "idAllied  = ?0",
      "bind" => array($this->idAllied->idAlliedSigma)
    ));

    $detailConfigAllied = $allied->AlliedConfig->DetailConfig;

    $account->name = "{$data['name']} {$data['lastname']}";

    $account->idCity = $data["idCity"];
    $account->idAllied = $allied->idAllied;
    $account->status = 0;
    $account->phone = $data['phone'];
    $account->email = $data['email'];
    $account->address = $data['address'];
    $account->nit = $data['nit'];
    $account->createdBy = $data['email'];
    $account->updatedBy = $data['email'];
    $account->registerType = "online";
    $account->tolerancePeriod = 0;
    $account->deleted = 0;
    $termsconditions = 0;
    if ($data["acceptTermsConditions"] == true) {
      $termsconditions = 1;
    };
    $account->termsconditions = $termsconditions;
    $account->ip = $this->getIpClient();

    $account->idAccountCategory = 78;

    if (!$account->save()) {
      foreach ($account->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $paymentplan = \PaymentPlan::findFirst(array(
      "conditions" => "idCountry = ?0 AND courtesyplan = ?1 AND idAllied = ?2",
      "bind" => array($account->City->State->Country->idCountry, 1, $allied->idAllied)
    ));

    if (!$paymentplan) {
      throw new \InvalidArgumentException("El país donde se encuentra no tiene plan de cortesía");
    }

    $account->idPaymentPlan = $paymentplan->idPaymentPlan;

    $account->save();

    $this->createCategories($account->idAccount);

    $dataUser = array(
      "name" => $data["name"],
      "lastname" => $data["lastname"],
      "email" => $data["email"],
      "cellphone" => $data["phone"],
      "pass1" => $data["pass1"],
      "pass2" => $data["pass2"],
      "idCity" => $data["idCity"]
    );


    $this->createUser($dataUser, $this->createUsertype($account->idAccount, 1), $this->roles->account);
    $subaccount = $this->createSubaccount($account);
    $userSubaccount = $this->createUser($dataUser, $this->createUsertype($subaccount->idSubaccount, 2), $this->roles->subaccount);

    //Inicio de configuración de plan de pago
    $paymentPlanxService = $paymentplan->paymentPlanxService;

    $accountConfig = new \AccountConfig();
    $accountConfig->idAccount       = $account->idAccount;
    $accountConfig->diskSpace       = $paymentPlanxService[0]->PaymentPlan->diskSpace;
    $accountConfig->idFooter        = 39;
    $accountConfig->senderAllowed   = 1;
    $accountConfig->footerEditable  = 0;

    if (!$accountConfig->save()) {
      foreach ($accountConfig->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    $this->db->commit();
    //Preguntar cuales son los datos que se deben de devolver.
    return ["idAccount" => $account->idAccount, "status" => 0];
  }

  public function continueRegister($data){
    $this->db->begin();
    $account = \Account::findFirst(array(
      "conditions" => "idAccount  = ?0 AND status = 0",
      "bind" => array($data->idAccount)
    ));

    if (!$account) {
      throw new \InvalidArgumentException("La cuenta con el idAccount {$data->idAccount} no existe o esta activa");
    }
    $accountConfig = \AccountConfig::findFirst(array(
      "conditions" => "idAccount  = ?0",
      "bind" => array($data->idAccount)
    ));
    if (!$accountConfig) {
      throw new \InvalidArgumentException("La cuenta no tiene configuración");
    }

    $idAccountConfig = $accountConfig->idAccountConfig;

    if(count($data->services) > 0){
      foreach ($data->services as $value) {
        $idRangesPrices = $value['idRangesPrices'];
        $rangesPrices = \RangesPrices::findFirst(array(
          "conditions" => "idRangesPrices = ?0",
          "bind" => array($idRangesPrices)
        ));

        if (!$rangesPrices) {
          throw new \InvalidArgumentException("El precio del rango no existe.");
        }

        $detailConfigAccount = new \DetailConfig();

        if($rangesPrices->idServices == 1){
          $detailConfigAccount->idPriceList = 52;
          $detailConfigAccount->speed = $rangesPrices->unitValue;
          $amount = 0;
        }
        if($rangesPrices->idServices == 2){
          $detailConfigAccount->idPriceList = 57;
          $detailConfigAccount->accountingMode = $rangesPrices->accountingMode;
          $amount = $rangesPrices->quantity;
        }

        $detailConfigAccount->idPlanType = 1;
        $detailConfigAccount->amount = $amount;
        $detailConfigAccount->totalAmount = $rangesPrices->quantity;
        $detailConfigAccount->idAccountConfig = $idAccountConfig;
        $detailConfigAccount->idServices = $rangesPrices->idServices;
        $detailConfigAccount->status = 1;       
        $detailConfigAccount->created = time(); 
        $detailConfigAccount->updated = time(); 
        $detailConfigAccount->createdBy = $account->Usertype->User[0]->email; 
        $detailConfigAccount->updatedBy = $account->Usertype->User[0]->email; 

        if (!$detailConfigAccount->save()) {
          foreach ($detailConfigAccount->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }

        $this->findDetailConfig($detailConfigAccount->idDetailConfig, $detailConfigAccount->idServices, $account->Usertype->User[0]->email);
        
        $saxs = new \Saxs();

        if($rangesPrices->idServices == 1){
          $saxs->idPriceList = 52;
          $saxs->speed = $rangesPrices->unitValue;
        }
        if($rangesPrices->idServices == 2){
          $saxs->idPriceList = 57;
          $saxs->accountingMode = $rangesPrices->accountingMode;
        }
        $saxs->idPlanType = 1;
        $saxs->amount = $rangesPrices->quantity;
        $saxs->totalAmount = $rangesPrices->quantity;
        $saxs->idSubaccount = $account->Subaccount[0]->idSubaccount;
        $saxs->idServices = $rangesPrices->idServices;
        $saxs->status = 1;
        $saxs->createdBy = $account->Usertype->User[0]->email;        
        $saxs->updatedBy = $account->Usertype->User[0]->email;        

        if (!$saxs->save()) {
          foreach ($saxs->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }

        $rechargeHistory = new \RechargeHistory();

        $rechargeHistory->idAccountConfig = $idAccountConfig;
        $rechargeHistory->rechargeAmount = $rangesPrices->quantity;;
        $rechargeHistory->idServices = $rangesPrices->idServices;
        $rechargeHistory->initialAmount = $rangesPrices->quantity;;
        $rechargeHistory->initialTotal = $rangesPrices->quantity;;

        if (!$rechargeHistory->save()) {
          foreach ($rechargeHistory->getMessages() as $msg) {
            throw new Exception($msg);
          }
        }
      }
      
      $detailConfigAdjuntarArchivos = new \DetailConfig();
      $detailConfigAdjuntarArchivos->idAccountConfig = $idAccountConfig;
      $detailConfigAdjuntarArchivos->idPlanType = 1;
      $detailConfigAdjuntarArchivos->idServices = 6;
      $detailConfigAdjuntarArchivos->idPriceList = 56;
      $detailConfigAdjuntarArchivos->status = 1;
      $detailConfigAdjuntarArchivos->created = time(); 
      $detailConfigAdjuntarArchivos->updated = time(); 
      $detailConfigAdjuntarArchivos->createdBy = $account->Usertype->User[0]->email; 
      $detailConfigAdjuntarArchivos->updatedBy = $account->Usertype->User[0]->email; 
      if (!$detailConfigAdjuntarArchivos->save()) {
        foreach ($detailConfigAdjuntarArchivos->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
        }
      }
      $subaccount = \Subaccount::findFirst(array(
        "conditions" => "idAccount  = ?0 AND status = 0",
        "bind" => array($data->idAccount)
      ));
      if($subaccount) {
        $subaccount->status = 1;
        if (!$subaccount->save()) {
          foreach ($subaccount->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      }
      unset($subaccount);

      $account->status = 1;
      if (!$account->save()) {
        foreach ($account->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
    $this->db->commit();

    //AGREGAMOS EL METODO PARA ENVIAR CORREO DE BIENVENIDA
    if(isset($data->data)){
      $arrEmails = array($data->data["x_customer_email"], $account->email);
      $this->sendMailNotificationWelcome($arrEmails);
    }

    return ["idAccount" => $account->idAccount, "status" => 1];
  }

  public function sendMailNotificationWelcome($arrEmails) {
    try {
      
      for($i=0; $i < count($arrEmails); $i++){
        //Objeto que guardara la informacion de envio de correo
          $data = new \stdClass();
    
          //Datos del correo
          $data->fromEmail = "soprote@sigmamovil.com.co";
          $data->fromName = "Servicio Bienvenida - AIO";
          $data->from = array($data->fromEmail => $data->fromName);
          $data->subject = "Bienvenido a nuestra Plataforma AIO";
    
          //Contenido del correo
          $content = '<html>'
                    .'<head>'
                    .'<meta charset="utf-8">'
                    .'<style>*{font-family: Arial_TrueType_2,Arial,serif;}.title{font-size: 35px;font-weight: bold;margin: 0!important;}.subtitle{font-size: 25px;}.parraf{text-align: justify;font-size: 18px;}.beneficios{font-weight: bold;margin: 0!important;}a{text-decoration: none;}.button {background-color: #ff6e00;color: #ffffff;padding: 11px 20px;text-align: center;font-size: 16px;margin: 4px 2px;transition-duration: 0.4s;cursor: pointer;border: 1px solid #ff6e00;}.button:hover {background-color: #ffffff;color: #ff6e00;text-decoration: none;}.icons div{text-align: center;}em{text-align: justify;font-size: 18px;}'
                    .'</style>'
                    .'</head>'
                    .'<body>'
                    .'<table style="background-color: #ffffff; width: 100%;">'
                    .'<tbody>'
                    .'<tr>'
                    .'<td style="padding: 0px;">'
                    .'<center>'
                    .'<table style="width: 600px;" width="600px" cellspacing="0" cellpadding="0">'
                    .'<tbody>'
                    .'<tr>'
                    .'<td style="width: 100%; vertical-align: top; padding:0; background-color: #FFFFFF; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border-color: #FFFFFF; border-style: none; border-width: 0px;">'
                    .'<table style="table-layout: fixed; width:100%; border-spacing: 0px;" width="100%" cellpadding="0">'
                    .'<tbody>'
                    .'<tr>'
                    .'<td style="padding-left: 0px; padding-right: 0px;">'
                    .'<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%; border-spacing: 0px" cellpadding="0" width="100%">'
                    .'<tbody>'
                    .'<tr>'
                    .'<td style="padding-left: 0px; padding-right: 0px;">'
                    .'<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 0px; margin-bottom: 0px; width:100%;" cellpadding="0" width="100%">'
                    .'<tr>'
                    .'<td><p class="title">Bienvenido a AIO</p><p class="subtitle">Fácil, potente y efectivo</p><p class="parraf">Encuentra todo lo que buscas en un mismo lugar! Características únicas y efectivas hacen de <b>AIO</b> la mejor opción para la <i><b>comunicación digital</b></i> de tu empresa. Nunca antes había sido tan fácil comunicarte con miles de clientes de manera instantánea con un solo clic.</p></td>'
                    .'<td><img src="https://aio.sigmamovil.com/themes/default/images/aio.png" alt="AIO" align="middle" width="200" height="200"/></td>'
                    .'</tr>'
                    .'</table>'
                    .'</td>'
                    .'</tr>'
                    .'<tr>'
                    .'<td style="padding-left: 0px; padding-right: 0px;">'
                    .'<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 20px; margin-bottom: 0px; width:100%;" cellpadding="0" width="100%">'
                    .'<tbody>'
                    .'<tr>'
                    .'<td><p class="parraf">Ingresa ya a tu cuenta en AIO y despeguemos juntos!<a href="https://aio.sigmamovil.com" target="_blank" class="button button1">INGRESAR</a></p></td>'
                    .'</tr>'
                    .'</tbody>'
                    .'</table>'
                    .'</td>'
                    .'</tr>'
                    .'<tr>'
                    .'<td style="padding-left: 0px; padding-right: 0px;">'
                    .'<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 20px; margin-bottom: 0px; width:100%;" cellpadding="0" width="100%">'
                    .'<tbody>'
                    .'<tr>'
                    .'<td><p class="subtitle beneficios"><img src="https://aio.sigmamovil.com/themes/default/images/favicons/favicon48x48.ico" alt="AIO" width="35" height="35"/><span>Beneficios</span></p></td>'
                    .'</tr>'
                    .'</tbody>'
                    .'</table>'
                    .'</td>'
                    .'</tr>'
                    .'<tr>'
                    .'<td style="padding-left: 0px; padding-right: 0px;">'
                    .'<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 20px; margin-bottom: 0px; width:100%;" cellpadding="0" width="100%">'
                    .'<tbody>'
                    .'<tr style="text-align: center;">'
                    .'<td><img src="https://aio.sigmamovil.com/images/welcome/optimiza_tiempo.png" alt="AIO" width="80px" height="80px"/><em><b>Optimiza Tiempo</b></em></td>'
                    .'<td><img src="https://aio.sigmamovil.com/images/welcome/comunicacion_directa.png" alt="AIO" width="80px" height="80px"/><em><b>Comunicación Directa</b></em></td>'
                    .'<td><img src="https://aio.sigmamovil.com/images/welcome/aumenta_ventas.png" alt="AIO" width="80px" height="80px"/><em><b>Aumenta tus Ventas </b></em></td>'
                    .'<td><img src="https://aio.sigmamovil.com/images/welcome/fideliza_clientes.png" alt="AIO" width="80px" height="80px"/><em><b>Fideliza Clientes </b></em></td>'
                    .'<td><img src="https://aio.sigmamovil.com/images/welcome/automatiza_comunicacion.png" alt="AIO" width="75px" height="75px"/><em><b>Automatiza tu comunicación</b></em></td></tr>'
                    .'</tbody>'
                    .'</table>'
                    .'</td>'
                    .'</tr>'
                    .'<tr>'
                    .'<td style="padding-left: 0px; padding-right: 0px;">'
                    .'<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 20px; margin-bottom: 0px; width:100%;" cellpadding="0" width="100%">'
                    .'<tbody>'
                    .'<tr>'
                    .'<td><p class="parraf">Aprenda a despegar AIO mirando sus videotutoriales <a href="https://www.youtube.com/channel/UCC_-Dd4-718gwoCPux8AtwQ/videos" style="text-decoration:none; color: rgb(227, 108, 9);"><b>aquí</b></a></p></td>'
                    .'</tr>'
                    .'</tbody>'
                    .'</table>'
                    .'</td>'
                    .'</tr>'
                    .'<tr>'
                    .'<td style="padding-left: 0px; padding-right: 0px;">'
                    .'<table style="border-color: #FFFFFF; border-style: none; border-width: 0px; background-color: transparent; border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; margin-top: 20px; margin-bottom: 0px; width:100%;" cellpadding="0" width="100%">'
                    .'<tbody>'
                    .'<tr>'
                    .'<td><p class="parraf">Si tienes cualquier otra pregunta acerca de tu cuenta o del funcionamiento de AIO, por favor contáctate con nuestro equipo de soporte en <span style="color: rgb(227, 108, 9); background-color: initial;font-size:inherit"><b>soporte@sigmamovil.com.co</b></span></p></td>'
                    .'</tr>'
                    .'</tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></center></td></tr></tbody></table></body></html>';
    
          $data->html = str_replace("tmp-url", "prueba", $content);
          $data->plainText = "Se ha enviado un correo de bienvenida.";
          //CORREO DEL CLIENTE AL QUE LE VA A ALLEGAR EL EMAIL
          $data->to = $arrEmails[$i];
    
          $mtaSender = new \Sigmamovil\General\Misc\MtaSender('34.204.240.48', 25);
          $mtaSender->setDataMessage($data);
          $mtaSender->sendMail();
      }
      
    } catch (InvalidArgumentException $e) {
      $this->notification->error($e->getMessage());
    } catch (Exception $e) {
      \Phalcon\DI::getDefault()->get('logger')->log("Exception while sending email notification SMS balance: {$e->getMessage()}");
      \Phalcon\DI::getDefault()->get('logger')->log($e->getTraceAsString());
      $this->notification->error($e->getMessage());
    }
  }

  public function findDetailConfig($idDetailConfig, $idServices, $email){
    //
    if ($idServices == 1) {
      $dcxadapter = new \Dcxadapter();
      $dcxadapter->idDetailConfig = $idDetailConfig;
      $dcxadapter->idAdapter = 3;
      $dcxadapter->created = time();
      $dcxadapter->updated = time();
      $dcxadapter->createdBy = $email; 
      $dcxadapter->updatedBy = $email; 

      if (!$dcxadapter->save()) {
        $this->db->rollback();
        foreach ($dcxadapter->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
    //
    if ($idServices == 2) {
      $dcxmta = new \Dcxmta();
      $dcxmta->idDetailConfig = (int) $idDetailConfig;
      $dcxmta->idMta = (int) \Phalcon\DI::getDefault()->get('mtaDefault')->idMtaDefault;
      $dcxmta->created = time();
      $dcxmta->updated = time();
      $dcxmta->createdBy = $email;
      $dcxmta->updatedBy = $email;

      if (!$dcxmta->save()) {
        $this->db->rollback();
        foreach ($dcxmta->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      $dcxurldomain = new \Dcxurldomain();
      $dcxurldomain->idDetailConfig = $idDetailConfig;
      $dcxurldomain->idUrldomain = 1;
      $dcxurldomain->created = time();
      $dcxurldomain->updated = time();
      $dcxurldomain->createdBy = $email;
      $dcxurldomain->updatedBy = $email;

      if (!$dcxurldomain->save()) {
        $this->db->rollback();
        foreach ($dcxurldomain->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      $dcxmailClass = new \Dcxmailclass();
      $dcxmailClass->idDetailConfig = $idDetailConfig;
      $dcxmailClass->idMailClass = 1;
      $dcxmailClass->created = time();
      $dcxmailClass->updated = time();
      $dcxmailClass->createdBy = $email;
      $dcxmailClass->updatedBy = $email;

      if (!$dcxmailClass->save()) {
        $this->db->rollback();
        foreach ($dcxmailClass->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }
  }

  public function rangesPrices($idRangesPrices, $idAccount) {
    $rangesPrices = \RangesPrices::findFirst(array(
      "conditions" => "idRangesPrices = ?0",
      "bind" => array($idRangesPrices)
    ));

    if (!$rangesPrices) {
      throw new \InvalidArgumentException("El precio del rango no existe.");
    }

    $account = \Account::findFirst(array(
      "conditions" => "idAccount = ?0",
      "bind" => array($idAccount)
    ));
    if (!$account) {
      throw new \InvalidArgumentException("La cuenta no tiene registro");
    }

    $accountConfig = \AccountConfig::findFirst(array(
      "conditions" => "idAccount  = ?0",
      "bind" => array($idAccount)
    ));
    if (!$accountConfig) {
      throw new \InvalidArgumentException("La cuenta no tiene configuración");
    }
    
    $detailConfigAccount = \DetailConfig::findFirst(array(
      "conditions" => "idAccountConfig  = ?0 AND idServices = ?1",
      "bind" => array($accountConfig->idAccountConfig, $rangesPrices->idServices)
    ));

    if (!$accountConfig) {
      throw new \InvalidArgumentException("El detail config no tiene configuración");
    }

    $amount = $rangesPrices->quantity;
    $amountHistory = $detailConfigAccount->amount;
    $detailConfigAccount->amount = $detailConfigAccount->amount + $amount;
    $detailConfigAccount->totalAmount = $detailConfigAccount->totalAmount + $amount;
    $detailConfigAccount->updated = time();  
    $detailConfigAccount->updatedBy = $account->Usertype->User[0]->email; 

    if (!$detailConfigAccount->save()) {
      foreach ($detailConfigAccount->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $rechargeHistory = new \RechargeHistory();

    $rechargeHistory->idAccountConfig = $accountConfig->idAccountConfig;
    $rechargeHistory->rechargeAmount = $rangesPrices->quantity;
    $rechargeHistory->idServices = $rangesPrices->idServices;
    $rechargeHistory->initialAmount = $amountHistory;
    $rechargeHistory->initialTotal = $rechargeHistory->initialAmount + $rangesPrices->quantity;

    if (!$rechargeHistory->save()) {
      foreach ($rechargeHistory->getMessages() as $msg) {
        throw new Exception($msg);
      }
    }

    return ["message" => "La cuenta {$account->name} recargo {$rangesPrices->quantity} a ${$rangesPrices->totalValue} correctamente."];
  }

}
