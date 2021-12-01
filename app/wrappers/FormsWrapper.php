<?php

namespace Sigmamovil\Wrapper;

/**
 * Description of FormsWrapper
 *
 * @author desarrollo3
 */
class FormsWrapper extends \BaseWrapper {

  public $page,
          $report,
          $totals,
          $forms = array(),
          $search,
          $form,
          $db,
          $formoption,
          $mailWelcome,
          $notificationMail,
          $infoDetail = array(),
          $inIdContact = array();

  function __construct() {
    $this->db = \Phalcon\DI::getDefault()->get('db');
    $this->modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');
    $this->limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
  }

  public function saveBasicInformation() {

    $this->form = new \Form();
    $this->formoption = new \FormOptin();
    $this->mailWelcome = new \FormWelcomeMail();
    $this->notificationMail = new \FormNotificationMail();

    $this->validBasicInformation();
    $this->db->begin();
    $this->form->idSubaccount = \Phalcon\DI::getDefault()->get('user')->userType->idSubaccount;
    $this->form->type = "suscription";
    $this->form->status = 1;

    if ($this->data->doubleOptin->active) {
      $this->form->optin = 1;
    }

    if ($this->data->mailWelcome->active) {
      $this->form->welcomeMail = 1;
    }

    if ($this->data->notification->active) {
      $this->form->notificationMail = 1;
    }

    if (!$this->form->save()) {
      $this->db->rollback();
      foreach ($this->form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    if ($this->data->notification->active) {
      $this->setNotification();
    }
    if ($this->data->mailWelcome->active) {
      $this->setMailWelcome();
    }
    if ($this->data->doubleOptin->active) {
      $this->setDoubleOption();
    }
    $this->db->commit();
    return array("idForm" => $this->form->idForm);
  }

  public function updateBasicInformation($idForm) {
    $conditions = array("conditions" => "idForm = ?0", "bind" => array($idForm));
    $this->form = \Form::findFirst($conditions);
    $this->formoption = \FormOptin::findFirst($conditions);
    $this->mailWelcome = \FormWelcomeMail::findFirst($conditions);
    $this->notificationMail = \FormNotificationMail::findFirst($conditions);
    if (!$this->formoption) {
      $this->formoption = new \FormOptin();
    }
    if (!$this->mailWelcome) {
      $this->mailWelcome = new \FormWelcomeMail();
    }
    if (!$this->notificationMail) {
      $this->notificationMail = new \FormNotificationMail();
    }

    $this->validBasicInformation();
    $this->db->begin();
    $this->form->idSubaccount = \Phalcon\DI::getDefault()->get('user')->userType->idSubaccount;
    $this->form->type = "suscription";
    $this->form->status = 1;

    if ($this->data->doubleOptin->active) {
      $this->form->optin = 1;
    } else {
      $this->form->optin = 0;
    }

    if ($this->data->mailWelcome->active) {
      $this->form->welcomeMail = 1;
    } else {
      $this->form->welcomeMail = 0;
    }

    if ($this->data->notification->active) {
      $this->form->notificationMail = 1;
    } else {
      $this->form->notificationMail = 0;
    }

    if (!$this->form->save()) {
      $this->db->rollback();
      foreach ($this->form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    if ($this->data->notification->active) {
      $this->setNotification();
    }
    if ($this->data->mailWelcome->active) {
      $this->setMailWelcome();
    }

    if ($this->data->doubleOptin->active) {
      $this->setDoubleOption();
    }
    $this->db->commit();
    return array("idForm" => $this->form->idForm);
  }

  public function setNotification() {
    $subjet = trim($this->data->notification->subject);
    if (!isset($subjet) || $subjet == "") {
      throw new \InvalidArgumentException("El asunto de la notificacion no puede estar vacío");
    }
    $this->notificationMail->idForm = $this->form->idForm;
    $this->notificationMail->subject = $subjet;

    $nameSender = trim($this->data->notification->nameSender);
    if (!isset($nameSender) || $nameSender == "") {
      throw new \InvalidArgumentException("El nombre del remitente de la notificacion no puede estar vacío");
    }
    $this->notificationMail->nameSender = $nameSender;

    $emailSender = trim($this->data->notification->emailSender);
    if (!isset($emailSender) || $emailSender == "") {
      throw new \InvalidArgumentException("El correo del remitente de la notificacion no puede estar vacío");
    }
    $this->notificationMail->emailSender = $emailSender;

    $replyto = trim($this->data->notification->replyTo);
    if (isset($replyto) && $replyto != null && $replyto != "" && !filter_var($replyto, FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("El campo 'responder a' de la notificacion no tiene el formato de correo correcto.");
    }
    $this->notificationMail->replyTo = $replyto;

    $this->notificationMail->idMailTemplate = $this->data->notification->idMailTemplate;
    $this->notificationMail->emails = $this->data->notification->emails;
    if (!$this->notificationMail->save()) {
      $this->db->rollback();
      foreach ($this->notificationMail->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function setMailWelcome() {
    $subjet = trim($this->data->mailWelcome->subject);
    if (!isset($subjet) || $subjet == "") {
      throw new \InvalidArgumentException("El asunto del correo de bienvenida no puede estar vacío");
    }
    $this->mailWelcome->idForm = $this->form->idForm;
    $this->mailWelcome->subject = $subjet;

    $nameSender = trim($this->data->mailWelcome->nameSender);
    if (!isset($nameSender) || $nameSender == "") {
      throw new \InvalidArgumentException("El nombre del remitente del correo de bienvenida no puede estar vacío");
    }
    $this->mailWelcome->nameSender = $nameSender;

    $emailSender = trim($this->data->mailWelcome->emailSender);
    if (!isset($emailSender) || $emailSender == "") {
      throw new \InvalidArgumentException("El correo del remitente del correo de bienvenida no puede estar vacío");
    }
    $this->mailWelcome->emailSender = $emailSender;

    $replyto = trim($this->data->mailWelcome->replyTo);
    if (isset($replyto) && $replyto != null && $replyto != "" && !filter_var($replyto, FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("El campo 'responder a' del correo de bienvenida no tiene el formato correcto");
    }
    $this->mailWelcome->replyTo = $replyto;

    $this->mailWelcome->idMailTemplate = $this->data->mailWelcome->idMailTemplate;
    if (!$this->mailWelcome->save()) {
      $this->db->rollback();
      foreach ($this->mailWelcome->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function setDoubleOption() {
    $subjet = trim($this->data->doubleOptin->subject);
    if (!isset($subjet) || $subjet == "") {
      throw new \InvalidArgumentException("El asunto del doble optin no puede estar vacío");
    }
    $this->formoption->idForm = $this->form->idForm;
    $this->formoption->subject = $subjet;
    $nameSender = trim($this->data->doubleOptin->nameSender);
    if (!isset($nameSender) || $nameSender == "") {
      throw new \InvalidArgumentException("El nombre del nombre del doble optin no puede estar vacío");
    }
    $this->formoption->nameSender = $nameSender;
    $emailSender = trim($this->data->doubleOptin->emailSender);
    if (!isset($emailSender) || $emailSender == "") {
      throw new \InvalidArgumentException("El correo del remitente del doble optin no puede estar vacío");
    }
    $this->formoption->emailSender = $emailSender;
    $replyto = trim($this->data->doubleOptin->replyTo);
    if (isset($replyto) && $replyto != null && $replyto != "" && !filter_var($replyto, FILTER_VALIDATE_EMAIL)) {
      throw new \InvalidArgumentException("El campo 'responder a' del doble optin no tiene un formato valido");
    }
    $this->formoption->replyTo = $replyto;
    /*$url = trim($this->data->doubleOptin->urlSuccess);
    if (!isset($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
      throw new \InvalidArgumentException("El enlace de bienvenida del doble optin no puede estar vacío o tiene un formato incorrecto");
    }
    $this->formoption->urlSuccess = $url;*/
    $this->formoption->idMailTemplate = $this->data->doubleOptin->idMailTemplate;
    if (!$this->formoption->save()) {
      $this->db->rollback();
      foreach ($this->formoption->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function validBasicInformation() {
//    var_dump($this->data);
//    exit();
    $name = trim($this->data->name);
    if (!isset($name) || $name == "") {
      throw new \InvalidArgumentException("El nombre no puede estar vacío");
    }
    if (strlen($name) > 40) {
      throw new \InvalidArgumentException("El nombre no puede tener más de 40 caracteres");
    }
    $this->form->name = $name;
    if (isset($this->data->description)) {
      $description = trim($this->data->description);
      if (strlen($description) > 160) {
        throw new \InvalidArgumentException("La descripción no puede tener más de 160 caracteres");
      }
      $this->form->description = $this->data->description;
    }
    if (!isset($this->data->idFormCategory)) {
      throw new \InvalidArgumentException("La categoría es obligatoria");
    }
    $this->form->idFormCategory = $this->data->idFormCategory;
    if (isset($this->data->successUrl) && !filter_var($this->data->successUrl, FILTER_VALIDATE_URL)) {
      throw new \InvalidArgumentException("La url de registro exitoso o esta vacía o tiene un formato incorrecto");
    }
    $this->form->successUrl = $this->data->successUrl;
    if (isset($this->data->errorUrl) && !filter_var($this->data->errorUrl, FILTER_VALIDATE_URL)) {
      throw new \InvalidArgumentException("La url de registro erroneo o esta vacía o tiene un formato incorrecto");
    }
    $this->form->errorUrl = $this->data->errorUrl;
    if (!isset($this->data->idContactlist)) {
      throw new \InvalidArgumentException("Debes seleccionar por lo menos una lista de contactos");
    }
    if(!empty($this->data->habeasData)){
      $this->form->habeasData = $this->data->habeasData;
    }else{
      $this->form->habeasData = null;  //se le asigna este null para que muestre el habeas data por defecto.
    }
    
    $this->form->idContactlist = $this->data->idContactlist;
  }

  public function listForms($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $name = (isset($filter->name) ? " AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');
    $category = (isset($filter->idFormCategory) ? " AND idFormCategory = '{$sanitize->sanitize($filter->idFormCategory, "string")}'" : '');

    $conditions = array(
        "conditions" => "idSubaccount = ?0 AND deleted = 0 {$category} {$name}",
        "bind" => array(\Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $forms = \Form::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \Form::count($conditions);
    $data = array();
    $structureForm = file_get_contents(__DIR__ . "/../views/forms/structureform.volt", true);
    $urlBase = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true);
    if (count($forms) > 0) {
      foreach ($forms as $key => $value) {
        //Se agrega la busqueda del habeas data
        $Habeasdata = \Phalcon\DI::getDefault()->get('habeasData')->habeasData;
        if(!empty($value->habeasData)){
          $Habeasdata = $value->habeasData;
        }else if(!empty($value->Subaccount->Account->habeasData)){
          $Habeasdata = $value->Subaccount->Account->habeasData;
        }
        
        $data[$key] = array(
            "idForm" => $value->idForm,
            "idFormCategory" => $value->idFormCategory,
            "idSubaccount" => $value->idSubaccount,
            "name" => $value->name,
            "description" => $value->description,
            "type" => $value->type,
            "status" => $value->status,
            "updated" => date("Y-m-d", $value->updated),
            "created" => date("Y-m-d", $value->created),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "html" => str_replace(array("%%IDFORM%%", "%%urlBase%%","%%HB%%"), array($value->idForm, $urlBase, $Habeasdata), $structureForm),
            "iframe" => "<iframe src=\"{$urlBase}forms/structureform/{$value->idForm}\" style=\"width: 100%; height: 100%; border:none\"></iframe>",
            "content" => \FormContent::findFirst(array("conditions" => "idForm = ?0", "bind" => [$value->idForm]))
        );
      }
    }

    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );
  }

  public function getAllFormsCategories() {
    $formcategory = \FormCategory::find(array(
                'conditions' => "deleted = 0 AND idAccount = " . \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount
    ));

    $array = array();
    foreach ($formcategory as $value) {
      $arr = array();
      $arr['idFormCategory'] = $value->idFormCategory;
      $arr['name'] = $value->name;
      $array[] = $arr;
    }

    return $array;
  }

  public function getInformationForm($idInfo) {

    $form = \Form::findFirst(array(
                'conditions' => " idForm = ?0 ",
                "bind" => array($idInfo)
    ));

    if (!$form) {
      throw new \InvalidArgumentException("El formulario con el id: {$idInfo} no se encuentra registrado.");
    }

    $data = array(
        "idFormCategory" => $form->idFormCategory,
        "idContactlist" => $form->idContactlist,
        "status" => $form->status,
        "optin" => $form->optin,
        "welcomeMail" => $form->welcomeMail,
        "notificationMail" => $form->notificationMail,
        "type" => $form->type,
        "name" => $form->name,
        "description" => $form->description,
        "successUrl" => $form->successUrl,
        "errorUrl" => $form->errorUrl,
        "welcomeUrl" => $form->welcomeUrl,
        "successMessage" => $form->successMessage,
        "errorMessage" => $form->errorMessage,
        "welcomeMessage" => $form->welcomeMessage,
        "habeasData" => $form->habeasData,
    );
    return $data;
  }

  public function getContentForm($idForm) {
    $deleted_form = false;
    $deleted_cl = false;
    $form = \FormContent::findFirst(array(
                'conditions' => " idForm = ?0 ",
                "bind" => array($idForm)
    ));

    if (!$form) {
      throw new \InvalidArgumentException("El formulario con el id: {$idForm} no se encuentra registrado.");
    }
    
    $formvalidate = \Form::findFirst(array(
                'conditions' => " idForm = ?0 AND deleted = 0 AND status = 1",
                "bind" => array($idForm)
    ));
    if($formvalidate){
        $contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => array(0 =>(int) $formvalidate->idContactlist)));
        if(!$contactlist){  
         $deleted_cl = true;   
        }
    }else{
        $deleted_form = true;   
    }
    $data = array(
        "content" => $form->content,
        "deleted_cl" => $deleted_cl,
        "deleted_form" => $deleted_form
    ); 

    $arr = json_decode($data["content"]);
    $cadena ="";
    $indicatives = array();
    foreach ($arr->form as $key =>$value) {
      foreach($value as $key1=>$value1){
        if($key1 == "id" && $value1 == "indicative"){
              $indicatives = $value->options;  
              $indicativeCol= array_search("(+57) Colombia",$indicatives);//busca a Colombia en el arreglo
              unset($indicatives[$indicativeCol]);//borra la posicion de colombia en el arreglo
              array_unshift($indicatives, "(+57) Colombia");//pone a Colombia de primero
              unset($value->options);
              $value->options = $indicatives;
           
        break;
        }
      }
   }

   $data["content"] = json_encode($arr);

    return $data;
  }

  public function getOptinForm($idForm) {
    $form = \FormOptin::findFirst(array(
                'conditions' => " idForm = ?0 ",
                "bind" => array($idForm)
    ));

    if (!$form) {
      return false;
    }

    $data = array(
        "idMailTemplate" => $form->idMailTemplate,
        "nameSender" => $form->nameSender,
        "emailSender" => $form->emailSender,
        "replyTo" => $form->replyTo,
        "urlSuccess" => $form->urlSuccess,
        "subject" => $form->subject,
    );
    return $data;
  }

  public function getWelcomeMailForm($idForm) {
    $form = \FormWelcomeMail::findFirst(array(
                'conditions' => " idForm = ?0 ",
                "bind" => array($idForm)
    ));

    if (!$form) {
      return false;
    }

    $data = array(
        "idMailTemplate" => $form->idMailTemplate,
        "nameSender" => $form->nameSender,
        "emailSender" => $form->emailSender,
        "replyTo" => $form->replyTo,
        "subject" => $form->subject,
    );
    return $data;
  }

  public function getNotificationForm($idForm) {
    $form = \FormNotificationMail::findFirst(array(
                'conditions' => " idForm = ?0 ",
                "bind" => array($idForm)
    ));

    if (!$form) {
      return false;
    }

    $data = array(
        "idMailTemplate" => $form->idMailTemplate,
        "nameSender" => $form->nameSender,
        "emailSender" => $form->emailSender,
        "replyTo" => $form->replyTo,
        "subject" => $form->subject,
        "emails" => $form->emails,
    );
    return $data;
  }

  public function saveForm($idInfo, $content) {
    $this->db->begin();
    $form = \Form::findFirst(array(
                'conditions' => " idForm = ?0 ",
                "bind" => array($idInfo)
    ));

    if (!$form) {
      throw new \InvalidArgumentException("El formulario con el id: {$idInfo} no se encuentra registrado.");
    }

    $formContent = \FormContent::findFirst(array(
                'conditions' => " idForm = ?0 ",
                "bind" => array($idInfo)
    ));
    if (!$formContent) {
      $formContent = new \FormContent();
    }
    $formContent->idForm = $idInfo;
    $formContent->content = json_encode($content);

    if (!$formContent->save()) {
      $this->db->rollback();
      foreach ($formContent->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    $this->db->commit();
    return array("message" => "Se registro el formulario correctamente.");
  }

  public function saveFormCategoryAction($arrayData) {
    $name = trim($arrayData['category']);

    if (!isset($name) || empty($name)) {
      throw new \InvalidArgumentException("El campo categoría es obigatorio");
    }
    if (is_numeric($name)) {
      throw new \InvalidArgumentException("El campo categoría no se permite solo numeros.");
    }
    $formCategory = \FormCategory::findFirst(["conditions" => "name = ?0 and idAccount = ?1", "bind" => array($name, \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount)]);

    if ($formCategory) {
      throw new \InvalidArgumentException("La categoría ya se encuentra registrada.");
    }
    if (!empty($arrayData)) {

      $formCategory = new \FormCategory();
      $formCategory->idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount;
      $formCategory->name = $name;
      $formCategory->deleted = 0;

      if (!$formCategory->save()) {
        foreach ($formCategory->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }

      return $formCategory->idFormCategory;
    }
  }

  public function modelDataForm() {
    $this->report = $this->calculateTotalPages($this->totals);
    $arr = array();
    foreach ($this->data as $key => $value) {
      $obj = new \stdClass();
      $obj->$key = $value;
      array_push($arr, $value);
    }
    array_push($this->report, ["items" => $arr]);
  }

  /**
   * This function can get the data the Form
   * @param int $idForm
   * @return Object
   * @throws \InvalidArgumentException
   */
  public function getContactsForm($idForm, $page) {
   $forms_temp = \Form::findFirst(array(
      "conditions" => "idForm = ?0 AND deleted = 0",
      "bind" => array($idForm)
    ));
   $idContactlist = (int) $forms_temp->idContactlist;
    if($page!=-1){
      if ($page != 0) {
        $page = $page + 1;
      }
      if ($page > 1) {
        $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
      }
    }
    
    
    //Export excel no limit with 0
    if($page==-1){
      $this->data = $this->modelsManager->createBuilder()
            ->columns(
                      [
                        "idContact AS idContacts"
                      ]
                    )
            ->from('Cxcl')
            ->where("idForm = " . $idForm . " AND deleted = 0")
            ->getQuery()
            ->execute();
    }
    else{
      $this->data = $this->modelsManager->createBuilder()
            ->columns(
                      [
                        "Cxcl.idContact AS idContacts"
                      ]
                    )
            ->from('Cxcl')
            ->where("Cxcl.idForm = " . $idForm . " AND Cxcl.deleted = 0 AND Cxcl.idContactlist = ".$idContactlist)
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT, $page)
            ->getQuery()
            ->execute();
    }
    
    $arrayRespuest = [];
    foreach ($this->data as $key => $value) {
      array_push($arrayRespuest, (int) $value['idContacts']);
    }
    
    $this->report['lisIdContactos'] = $arrayRespuest;
    
    $this->data = $this->modelsManager->createBuilder()
            ->columns(
                    [
                        "GROUP_CONCAT(DISTINCT(Cxcl.idContact)) AS idContacts",
                        "Cxcl.idContactlist AS idContactlist",
                        "Form.createdBy AS createdBy",
                        "Form.updatedBy AS updatedBy",
                        "FROM_UNIXTIME(Form.created) AS created",
                        "FROM_UNIXTIME(Form.updated) AS updated",
                        "FormCategory.name AS name",
                        "Form.name AS nameForm"
            ])
            ->from('Cxcl')
            ->innerjoin("Form", "Form.idForm = Cxcl.idForm")
            ->innerjoin("FormCategory", "FormCategory.idFormCategory = Form.idFormCategory")
            ->where("Form.idForm = " . $idForm . " AND Cxcl.deleted = 0 AND Form.deleted = 0 ")
            ->getQuery()
            ->execute();
    array_push($this->report, ["items" => $this->data[0]]);
    
    //Total register
    $this->totals = $this->modelsManager->createBuilder()
            ->columns(
                    [
                        "DISTINCT(Cxcl.idContact) AS idContact"
            ])
            ->from('Cxcl')
            ->innerjoin("Form", "Form.idForm = Cxcl.idForm")
            ->innerjoin("FormCategory", "FormCategory.idFormCategory = Form.idFormCategory")
            ->where("Form.idForm = " . $idForm . " AND Cxcl.deleted = 0 AND Form.deleted = 0 AND Cxcl.idContactlist = ".$idContactlist." AND Form.idContactlist = ".$idContactlist)
            ->getQuery()
            ->execute();
    array_push($this->report, ["total" => ceil(count($this->totals))]);
    array_push($this->report, ["total_pages" => ceil((count($this->totals))/(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT))]);
  }

  public function getIdContactsString($stringIdContacts) {
    $arrayContactsString = explode(',', $stringIdContacts);
    $arrayContactsInt = array();
    for ($i = 0; $i < count($arrayContactsString); $i++) {
      $varInt = (int) $arrayContactsString[$i];
      array_push($arrayContactsInt, $varInt);
    }
    return $arrayContactsInt;
  }

  /**
   * Function get propertys contacts names forms
   * @param type $stringPropertysFormContact
   */
  public function getPropertysFormContact($stringPropertysFormContact) {
    $arrayObjectPropertys = [];
    $arrayNamesPropertyes = [];
    $arrayIdLabel = [];
    
    foreach ($stringPropertysFormContact->form as $value) {
      if ($value->id != 'encabezado' && $value->id != 'button') {
        $arrayObjectPropertys[$value->id] = true;
        $arrayNamesPropertyes[$value->label] = true;
        array_push($arrayIdLabel, [$value->label=>$value->id]);
      }
    }
    if($this->forms->optin == 1){
      $arrayObjectPropertys["confirmation"] = true;
      $arrayNamesPropertyes["Confirmación"] = true;
      $arrayObjectPropertys["idContact"] = true;
      array_push($arrayIdLabel, ["Confirmación"=>"confirmation"]);
    }   

    $arrayObjectPropertys['created'] = true;//get register insert
    $this->report[0]['lisFieldsContacs'] = $arrayObjectPropertys;//id field
    $this->report[0]['lisFieldsNames'] = $arrayNamesPropertyes;//name field
    //get names keys titles
    $this->report[0]['lisKeysNames'] = $arrayIdLabel;
   }
  
  /**
   * Function can get fields contacts forms personalized
   * and combine contact mongo data and cxc.contactlist fields personalized
   */
  public function getContactsXForm($stringSearch) {
    //Get idContacts sql concat
    $idContactlist = (string) $this->forms->idContactlist;
    $conditions = [];
    if($stringSearch==""||$stringSearch==-1){
        $conditions = array(
          'conditions' =>
            array('deleted' => 0,'idContact' =>
                //array('$in' => $this->report[0]['lisIdContact'])
                array('$in' => $this->report['lisIdContactos'])
            ),
          'fields' => $this->report[0]['lisFieldsContacs']
      );
    }else{
      $stringRegex = new \MongoRegex("/^{$stringSearch}/i");
      //$stringRegex = new \MongoDB\BSON\Regex('^{$stringSearch}');
     $string = trim($stringSearch);
     $conditions = array();
     if(filter_var($string, FILTER_VALIDATE_EMAIL)){
          $where = ["email" => strtolower($string)];
      $conditions = array(
          'conditions' => array(
              'deleted' => 0,
              'idContact' => array(
                          '$in' => $this->report['lisIdContactos']
                      ),
                  'email' => strtolower($string)
              ),        
              
              'fields' => $this->report[0]['lisFieldsContacs']          
          );          
     } else if(is_numeric((int)$string)){
        $number = (int)$string;
          $conditions = array(
              'conditions' => array(
                  'idContact' => array(
                              '$in' => $this->report['lisIdContactos']
                          ),
                  "phone" =>$number
              ),        
              
              'fields' => $this->report[0]['lisFieldsContacs']          
          );
     } else{
        $conditions = array(
          'conditions' => array(
              'idContact' => array(
                          '$in' => $this->report['lisIdContactos']
                      ),
              '$or' => array(
                  array(
                      'name' => $stringRegex
                  ),
                  array(
                      'lastname' => $stringRegex
                  )
              )
          ),
          'fields' => $this->report[0]['lisFieldsContacs']
      );
    }


    }
    //Get fields contact mongo
    $dataMongoContacts = \Contact::find($conditions);
    $keys = array_keys($this->report[0]['lisFieldsContacs']);
    $arrayData = array();
    //position titles and fields data contact
    foreach ($dataMongoContacts as $key => $value) {
      
      $ar = [];
      foreach ($keys as $val) {
        foreach ($this->report[0]['lisKeysNames'] as $keyName => $valueNames) {
          if(current($valueNames)== $val){
            if($val=='created'){
              $ar[key($valueNames)] = date("Y-m-d", $value->$val);
            }
            else{
              $ar[key($valueNames)] =  $value->$val;
            }
            if($val=='confirmation'){
              $status = $this->inIdContact[$value->idContact];
              $ar[key($valueNames)] = $status != 'unsubscribed' ? 'Si' : 'No';
            }
          }
        }
      }
      array_push($arrayData, $ar);
    }
   
    //get contactlist with fields personalized
    $conditionsMongo = array(
        array(
            'idContact'=>array(
                '$in'=>$this->report['lisIdContactos']
            )
        ),
        array(
            //revisar esta parte
            '"'.'idContactlist.'.$idContactlist.'"'=>true
            //'"'.'idContactlist.'.$this->report[0]['items']['idContactlist'].'"'=>true
        )
    );
    $dataMongoCxc = \Cxc::find($conditionsMongo);
    $arrayResult = [];
    $arrayContacts = [];
    //get idKeys from data mongo
    $arrayKeysForm = array_keys($this->report[0]['lisFieldsNames']);
   
    //get data mongo from fields personalized with value but label is the key and key is value
    foreach ($dataMongoCxc as $keyCxc =>  $valueCxc) {
      $arrayDataFieldsPerzonal = [];
      foreach ($valueCxc->idContactlist as $keyContactList => $valueContactlist) {
            foreach ($valueContactlist as $keyFields => $valueFieldst) {
              if(in_array($valueFieldst['name'], $arrayKeysForm)){
                array_push($arrayDataFieldsPerzonal, [$valueFieldst['name']=>$valueFieldst['value']]);
              }
            }
            array_push($arrayResult,['dataUniqueForm'=>$arrayDataFieldsPerzonal]);   
      }
    }
    //imprimir arrayresult
    //can set data value fields mongo with data form contact null
    $indice = 0;
    foreach ($arrayResult as $keyResult => $valueResult) {
      $dataUniqueForm = $valueResult['dataUniqueForm'];
      foreach ($dataUniqueForm as $keydataUnique => $valuedataUnique) {
        $arrayKeysContact = array_keys($valuedataUnique);
        $arrayKesyContactActually = array_keys($arrayData[$indice]);
        if(in_array($arrayKeysContact[0], $arrayKesyContactActually)){
            if(is_array($valuedataUnique[$arrayKeysContact[0]])){
                $arrayData[$indice][$arrayKeysContact[0]] = implode(", ",$valuedataUnique[$arrayKeysContact[0]]);
            }else{
                $arrayData[$indice][$arrayKeysContact[0]] = $valuedataUnique[$arrayKeysContact[0]];   
            }
        }
      }
      $indice++;
    }
    
    array_push($this->report, ["contactsinfo" => $arrayData]);
    array_push($this->report, ['fieldsPersonal'=>$arrayResult]);
    
  }

  /**
   * Function can get fields personalities the a forms
   * @param int $idForm
   */
  public function getFieldsPersonalitiesForms($idForm) {
    $this->data = $this->modelsManager->createBuilder()
            ->columns(
                    [
                        "Customfield.idCustomfield AS idCustomfield",
                        "Customfield.alternativename AS alternativename",
                        "Customfield.type AS type",
            ])
            ->from('Customfield')
            ->innerjoin("Contactlist", "Contactlist.idContactlist = Customfield.idContactlist")
            ->innerjoin("Form", "Form.idContactlist = Contactlist.idContactlist")
            ->where("Form.idForm = " . $idForm)
            ->getQuery()
            ->execute();
    $arrayFieldsPersonalty = array();
    foreach ($this->data as $value) {
      array_push($arrayFieldsPersonalty, ["fieldsforms" => $value]);
    }
    array_push($this->report, $arrayFieldsPersonalty);
  }
  /**
   * Get data from Form and 
   * invoke the functions getPropertysFormContact and getContactsXForm
   * @param type $idForm
   */
  public function getFieldsForms($idForm, $stringSearch) {
    $this->data = \FormContent::findFirst(array(
        "columns" => "content",
        "conditions" => "idForm = ?0",
        "bind" => array($idForm)
    ));
    
    //Definition Inscription Date this is not in form_content only mongo contact
    $obj = new \stdClass();
    $obj->id = 'created';
    $obj->label = 'Fecha Inscripción';
    
    $content = json_decode($this->data->content);
    
    if($content->form[0]->id == "encabezado"){
        $content->form[0] = $obj;
    }else{
        array_unshift($content->form, $obj);
    }
    
    $arrayFieldsPersonalty = array();
    array_push($this->report, $content);
    $jsonFieldsPersonal = $content;//json_decode($content);
    $this->getPropertysFormContact($jsonFieldsPersonal);    
    $this->getContactsXForm($stringSearch);
  }

  function setPage($page) {
    $this->page = $page;
  }

  function getReport() {
    return $this->report;
  }

  function setSearch($search) {
    $this->search = $search;
  }

  function setReport($arrayDef) {
    $this->report = $arrayDef;
  }
  
  public function dowloadReportContactForms($idForm, $contentsraw) {

    $this->forms = \Form::findFirst(array(
      "conditions" => "idForm = ?0 AND deleted = 0",
      "bind" => array($idForm)
    ));

    $cxcl = \Cxcl::find(array(
      "conditions" => "idContactlist = ?0 AND deleted = 0",
      "bind" => array($this->forms->idContactlist)
    ));
    if($cxcl != false){
      foreach ($cxcl as $value) {
        $this->inIdContact[$value->idContact] = $value->status; 
      }
    }
    
    $this->data = \FormContent::findFirst(array(
        "columns" => "content",
        "conditions" => "idForm = ?0",
        "bind" => array($idForm)
    ));
    
    //Definition Inscription Date this is not in form_content only mongo contact
    $obj = new \stdClass();
    $obj->id = 'created';
    $obj->label = 'Fecha Inscripción';
    
    $content = json_decode($this->data->content);
    
    if($content->form[0]->id == "encabezado"){
        $content->form[0] = $obj;
    }else{
        array_unshift($content->form, $obj);
    }
    
    array_push($this->report, $content);
    $jsonFieldsPersonal = $content;//json_decode($this->data->content);
    
    $this->getContactsForm($idForm, -1);
    $this->getPropertysFormContact($jsonFieldsPersonal);
    $this->getContactsXForm($stringSearch);  
    $this->data = $this->report;
    $this->data = $this->modelDataInfoContactForm();
    return $this->modelDataInfoContactFormDownload();
  }
  
  public function modelDataInfoContactForm() {
    $arr = [];
    $arrayCotnacts = $this->data[3];
    $obj = [];
    foreach ($arrayCotnacts['contactsinfo'] as $key => $value) {
      foreach ($value as $key2 => $value2) {
        $obj[$key][$key2] = $value2;
      }
    }
    return $obj;
  }
  
  public function modelDataInfoContactFormDownload() {
    $excel = new \Sigmamovil\General\Misc\reportExcel();
    //$excel->basicPropertiesInfoContactsForm();
    //$excel->createStaticsReportChangePlan();
    $excel->setData($this->data);
    //$excel->setInfoDetailContactsForm();
    $excel->generatedReportContactForm($this->report[0]['items'], $this->report[1]['total']);
//    $excel->download();
    $name = 'Formulario_'. $this->report[0]['items']['nameForm'];
    return $excel->downloadExcel($name);
  }
  
  function getInfoDetail() {
    return $this->infoDetail;
  }
  
  public function deleteForm($objForm){
    
    $form = \Form::findFirst(array(
        "conditions" => "idForm = ?0", 
        "bind" => array((Int) $objForm->idform)));
    $form->deleted = time();
    
    if(!$form->update()){
      
      foreach ($form->getMessage() as $msg){
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

}
