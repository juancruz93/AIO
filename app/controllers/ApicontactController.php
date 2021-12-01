<?php
use \Sigmamovil\Wrapper\SegmentWrapper as segw;
/**
 * @RoutePrefix("/api/contact")
 */
class ApicontactController extends \ControllerBase {
  
  private $elephant;

  public function getElephant() {
    return $this->elephant;
  }

  public function setElephant($elephant) {
    $this->elephant = $elephant;
  }    

  /**
   * 
   * @Post("/getcontacts/{page:[0-9]+}/{idContactlist:[0-9]+}")
   */
  public function getcontactsAction($page, $idContactlist) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);      
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
//      $wrapper->findAllContacts($idContactlist);
      $wrapper->findContact($page, $idContactlist, $data);
      return $this->set_json_response($wrapper->getContact(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  

  /**
   * 
   * @Post("/deleteselected")
   */
  public function deleteselectedAction() {
    try {
      $this->db->begin();
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->deleteSelected($data[0], $data[1]);
      $this->db->commit();
      $this->trace("success", "Se han eliminado los contactos seleccionados");

      return $this->set_json_response(array('message' => "Se han eliminado los contactos seleccionados"), 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while deleting selected contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while deleting selected contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/getcontactsaccount/{page:[0-9]+}")
   */
  public function getcontactsaccountAction($page) {
    try {
      $contentsraw = $this->getRequestContent();

      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->findContactAccount($page, $contentsraw);
      return $this->set_json_response($wrapper->getContact(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/changestatus")
   */
  public function changestatusAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      return $this->set_json_response($wrapper->unsubscribeContact($data), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/contacts/")
   */
  //{idDbase:[0-9]+}
  public function contactscsvAction() {
    try {
      $ext = explode(".", $_FILES['filecsv']['name']);
      if ($ext[1] != "csv") {
        throw new InvalidArgumentException("El archivo seleccionado no es valido, seleccione un archivo con la extensión .csv.");
      }
      if ($_FILES['filecsv']['size'] > 5242880) {
        throw new InvalidArgumentException("El archivo CSV excede el tamaño las 2 megabytes aceptadas");
      }
      $routeFile = $_FILES['filecsv']['tmp_name'];
      $fileManager = new \Sigmamovil\General\Misc\FileManager();
      $temparray = $fileManager->viewcsv($routeFile);

      return $this->set_json_response($temparray, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/addcontactbatch/{idContactlist:[0-9]+}")
   */
  public function addcontactbatchAction($idContactlist) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->setDataBatch($data);
      $wrapper->saveContactBatch($idContactlist);
      $this->trace("success", "Se ha creado un lote de contactos");

      return $this->set_json_response(array('message' => "Se ha creado un lote de contactos"), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/validatecontactbatch")
   */
  public function validatecontactbatchAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->setDataBatch($data[0]);
      $wrapper->validateContactBatch($data[1]);
      return $this->set_json_response($wrapper->getContactError(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/customfield/{idContactlist:[0-9]+}")
   */
  public function customfieldAction($idContactlist) {
    try {
      $customfield = Customfield::find(array("conditions" => "idContactlist = ?0", "bind" => array(0 => $idContactlist)));
      $arr = array();
      foreach ($customfield as $key) {
        $contactlist = new \stdClass();
        $contactlist->idCustomfield = $key->idCustomfield;
        $contactlist->name = $key->name;
        $contactlist->alternativename = $key->alternativename;
        $contactlist->defaultvalue = $key->defaultvalue;
        $contactlist->type = $key->type;
        $contactlist->value = $key->value;
        array_push($arr, $contactlist);
      }
      return $this->set_json_response($arr, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/customfieldselect/{idContactlist:[0-9]+}")
   */
  public function customfieldselectAction($idContactlist) {
    try {
      $sql = "SELECT * FROM customfield WHERE idContactlist = {$idContactlist} AND type IN ('Select', 'Multiselect') ";
      $customfield = $this->db->fetchAll($sql);
      $arr = array();
      foreach ($customfield as $key) {
        $contactlist = new \stdClass();
        $contactlist->idCustomfield = $key['idCustomfield'];
        $contactlist->name = $key['name'];
        $contactlist->alternativename = $key['alternativename'];
        $contactlist->defaultvalue = $key['defaultvalue'];
        $contactlist->type = $key['type'];
        $contactlist->value = $key['value'];
        array_push($arr, $contactlist);
      }
      return $this->set_json_response($arr, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/addcontact/{idContactlist:[0-9]+}")
   */
  public function addcontactAction($idContactlist) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->setData($data);
      $wrapper->saveContact($idContactlist);
      $this->trace("success", "Se ha creado el contacto");
      if(isset($data->fromApi)){
      return $this->set_json_response(array('message' => "Se ha creado el contacto;".$wrapper->getidContact()), 200);      
      }else{
      return $this->set_json_response(array('message' => "Se ha creado el contacto"), 200);      
      }
      
    } catch (\Sigmamovil\General\Exceptions\ValidateEmailException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage(), "code" => $ex->getCode()), 409);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/editcontact/{idContactlist:[0-9]+}")
   */
  public function editcontactAction($idContactlist) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      if(isset($data->idCustomfield)){
        $customfield = Customfield::findFirst(["conditions" => "idCustomfield = ?0", "bind" => [0 => $data->idCustomfield]]);      
      }
      $contactsegment = Sxc::findFirst([["idContact" => $data->idContact]]);
      $contact = Contact::findFirst([["idContact" => $data->idContact]]);
      if (isset($customfield)) {
        $cxc = Cxc::findFirst([["idContact" => $data->idContact]]);
        $value = $data->value;
        if ($customfield->type == "Date") {
          $value = date("Y-m-d", strtotime($data->value));
        }
        $cxc->idContactlist[$customfield->idContactlist][$customfield->idCustomfield]["value"] = $value;
        $cxc->save();
      } else {
        $position = $data->key;
        $value = $data->value;
        if ($data->key == "birthdate") {
          if (strtotime($data->value) > time()) {
            throw new InvalidArgumentException("La fecha de nacimiento no puede superior a la fecha actual");
          }
          $value = date("Y-m-d", strtotime($data->value));
        }
        if (isset($contactsegment) && $contactsegment) {
          $contactsegment->$position = $value;
          $contactsegment->save();
        }


        $contact->$position = $value;
        if (($contact->email == '') and ( $contact->phone == '')) {
          throw new InvalidArgumentException("El contacto debe contener al menos el correo o el móvil con su respectivo indicativo");
        }
        if (($contact->indicative == 0) and ( $contact->phone != '')) {
          throw new InvalidArgumentException("Falta el indicativo del móvil");
        }
        if (!is_numeric($contact->phone) and $contact->phone != '') {
          throw new InvalidArgumentException("El telÃ©fono debe ser un número");
        }
        if(trim($contact->indicative) == 57 && $contact->phone != ""){
          if (strlen(trim($contact->phone)) != 10 || !is_numeric($contact->phone)) {
            throw new InvalidArgumentException("El número telefónico no cumple con la cantidad de digitos mínimos y máximos de acuerdo al país");
          }
        }
        if($contact->email != ""){
          $contact->email = strtolower($contact->email);
          if (!filter_var($contact->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("La dirección de correo no es correcta");
          }
        }
        if (strlen($contact->name) > 80) {
          throw new InvalidArgumentException("El nombre es muy largo, máximo 80 caracteres");
        }
        if (strlen($contact->lastname) > 80) {
          throw new InvalidArgumentException("El apellido es muy largo, máximo 80 caracteres");
        }
        $contact->blockedEmail = "";
        $contact->blockedPhone = "";

        if ($contact->email) {
          $emailBlocked = Blocked::findFirst([["email" => $contact->email, "idAccount" => (int) $contact->idAccount, "deleted" => 0]]);
          if ($emailBlocked) {
            $contact->blockedEmail = time();
          }
        }
        if ($contact->phone) {
          $phoneBlocked = Blocked::findFirst([["phone" => $contact->phone, "idAccount" => (int) $contact->idAccount, "deleted" => 0]]);
          if ($phoneBlocked) {
            $contact->blockedPhone = time();
          }
        }
        $contact->save();
      }

      $cxclAll = Cxcl::find(["conditions" => "idContact = ?0", "bind" => [0 => $contact->idContact]]);
      foreach ($cxclAll as $key) {
        if($key->singlePhone > 0){
          if($contact->email != ""){
            $key->singlePhone =0;
          }
        }
        if($key->singlePhone == 0){
          if($contact->phone != "" && $contact->email ==""){
            $key->singlePhone =time();
          }
        }
        if ($contact->blockedEmail != "" || $contact->blockedPhone != "") {
          $status = "blocked";
          $key->blocked = time();
        } else {
          $status = "active";
          if ($key->unsubscribed != 0) {
            $status = "unsubscribed";
          }
          if ($key->bounced != 0) {
            $status = "bounced";
          }
          if ($key->spam != 0) {
            $status = "spam";
          }
          $key->blocked = 0;
        }
        $key->status = $status;
        $key->save();
      }
      unset($cxclAll);
      //$sql = "CALL updateCountersGlobal()";
      //$this->db->execute($sql);
      $sql = "CALL updateCounters({$idContactlist})";
      $this->db->execute($sql);
      unset($sql);
      $idAccount = $this->user->usertype->subaccount->idAccount;
      $sql2 = "CALL updateCountersAccount({$idAccount})";
      
      $this->db->fetchAll($sql2);
      unset($sql2);
      unset($idAccount);
      $cxcl = Cxcl::findFirst(["conditions" => "idContact = ?0", "bind" => [0 => $contact->idContact]]);

      $segmentmanager = new Sigmamovil\General\Misc\SegmentManager();
      $segmentmanager->editOneContact($contact->idContact, $cxcl->idContactlist);
      $this->trace("success", "El contacto se ha editado");

      return $this->set_json_response(array('message' => "Se ha editado el contacto exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getallindicative")
   */
  public function getallindicativeAction() {
    try {
      $indicative = Indicative::find();
      $arr = array();
      foreach ($indicative as $key => $value) {
        $obj = $value;
        $arr[] = $obj;
      }

      //Se settea el primer registro vacío
      $vacio->idIndicative = "0";
      $vacio->name = "";
      $vacio->phonecode = "";
      array_unshift($arr, $vacio);
      //

      return $this->set_json_response($arr, 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/deletecontact")
   */
  public function deletecontactAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new Sigmamovil\Wrapper\ContactWrapper();
      return $this->set_json_response($wrapper->deleteContact($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while delecontact... {$ex->getMessage()}", 400);
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding contact... {$ex->getMessage()}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/activecontacts/{idAccount:[0-9]+}")
   */
  public function activecontactsAction($idAccount) {
    try {
      $contactWrapper = new \Sigmamovil\Wrapper\ContactWrapper();

      return $this->set_json_response($contactWrapper->findActiveContacts($idAccount), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/addcontactbyblock/{idContactlist:[0-9]+}")
   */
  public function addcontactbyblockAction($idContactlist) {
    try {

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->saveContactByBlock($idContactlist, $data);
      $this->trace("success", "El contacto se ha creado");

      return $this->set_json_response(array('message' => "Se ha creado el contacto"), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/updatecontact/{idContact:[0-9]+}")
   */
  public function updatecontactAction($idContact) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $idContact = (int) $idContact;

      foreach ($data as $dataValue) {
        $customfield = Customfield::findFirst(["conditions" => "idCustomfield = ?0", "bind" => [0 => $dataValue->key]]);

        $contactsegment = Sxc::findFirst([["idContact" => $idContact]]);
        $contact = Contact::findFirst([["idContact" => $idContact]]);
        if ($customfield) {
          $cxc = Cxc::findFirst([["idContact" => $idContact]]);
          $value = $dataValue->value;
          if ($customfield->type == "Date") {
            $value = date("Y-m-d", strtotime($dataValue->value));
          }
          $cxc->idContactlist[$customfield->idContactlist][$customfield->idCustomfield]["value"] = $value;
          $cxc->save();
        } else {
          $position = $dataValue->key;
          $value = $dataValue->value;

          if ($dataValue->key == "birthdate") {
            $value = date("Y-m-d", strtotime($dataValue->value));
          }
          if (isset($contactsegment) && $contactsegment) {
            $contactsegment->$position = $value;
            $contactsegment->save();
          }

          $contact->$position = $value;
          $contact->save();
        }
        $cxcl = Cxcl::findFirst(["conditions" => "idContact = ?0", "bind" => [0 => $contact->idContact]]);
        $segmentmanager = new Sigmamovil\General\Misc\SegmentManager();
        $segmentmanager->editOneContact($contact->idContact, $cxcl->idContactlist, $dataValue);
      }
      $this->trace("success", "El contacto se ha editado");

      return $this->set_json_response(array('message' => "Se ha realizado la edicion correctamente"), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/findcontact/{idContact:[0-9]+}")
   */
  public function findcontactAction($idContact) {
    try {
      $idContact = (int) $idContact;
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      return $this->set_json_response($wrapper->searchContact($idContact), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function setAccountants($idContactlist) {
    $sql = "CALL updateCounters({$idContactlist})";
    $this->db->execute($sql);
//    $contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0 and deleted = 0", "bind" => array(0 => $idContactlist)));
//    if ($contactlist) {
//      $contactlist->ctotal = \Cxcl::count(array("conditions" => "idContactlist = ?1 AND deleted = 0", "bind" => array(1 => $contactlist->idContactlist)));
//      $contactlist->cactive = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "active", 1 => $contactlist->idContactlist)));
//      $contactlist->cunsubscribed = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "unsubscribed", 1 => $contactlist->idContactlist)));
//      $contactlist->cbounced = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "bounced", 1 => $contactlist->idContactlist)));
//      $contactlist->cblocked = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "blocked", 1 => $contactlist->idContactlist)));
//      $contactlist->cspam = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "spam", 1 => $contactlist->idContactlist)));
//      $contactlist->save();
//    }
  }

  /**
   *
   * @Post("/addcontactform/{idContactlist:[0-9]+}")
   */
  public function addcontactformAction($idForm) {
    try {
      $form = \Form::findFirst(array(
                  "conditions" => "idForm = ?0",
                  "bind" => array(0 => $idForm)
      ));

      if (!$form) {
        throw new \InvalidArgumentException("No se encontró el formulario.");
      }
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
     /* foreach ($data as $key => $value) {//POSIBLE SOLUCION PARA QUITAR MACHETAZO :v
      	if(is_array($value)){
      		for($i=0; $i< count($value); $i++){
	            $data->$key = trim($value[$i]);
	        }
      	}
      } */
	    foreach ($data as $key => $value) {
      	if(is_array($value)){
      		$tmp = $value;
      		$data->$key= null;
      		for($i=0; $i< count($tmp); $i++){
            	$tmp[$i] = trim($tmp[$i]);
        	}
        	$data->$key = $tmp;
      	}
      }
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->setData($data);
      $wrapper->saveContactForm($form);
      $this->trace("success", "El contacto se ha creado");

      return $this->set_json_response(array('message' => "Se ha creado el contacto", 'url' => $form->successUrl), 200);
    } catch (\Sigmamovil\General\Exceptions\ValidateEmailException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage(), "code" => $ex->getCode()), 409);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/getcontactsform/{page:[0-9]+}/{idContactlist:[0-9]+}/{idForm:[0-9]+}")
   */
  public function getcontactsformAction($page, $idContactlist, $idForm) {
    try {
      $contentsraw = $this->getRequestContent();
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->findContactForm($page, $idContactlist, $idForm, $contentsraw);
      return $this->set_json_response($wrapper->getContact(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getcontactlisttomoveselected/{idContactlist:[0-9]+}")
   */
  public function getcontactlisttomoveselectedAction($idContactlist) {
    try {
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->findContactlist($idContactlist);
      return $this->set_json_response(array('contactliststomove' => $wrapper->getContactlists()), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact lists... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact lists... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/movecontactselected")
   */
  public function movecontactselectedAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $idNewContactlist = $data[0][0];
      $idsContacts = $data[1];
      $idContaclistfrom = $data[2];
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->moveContacts($idNewContactlist, $idsContacts, $idContaclistfrom);
      $this->trace("success", "Se han movido exitosamente los contactos seleccionados");

      return $this->set_json_response(array('message' => 'Se han movido exitosamente los contactos seleccionados', 200));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while moving contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while moving contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/validatecopycontactselected")
   */
  public function validatecopycontactselectedAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $idNewContactlist = $data[0][0];
      $idsContacts = $data[1];
      $idContaclistfrom = $data[2];
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->validateCopyContacts($idNewContactlist, $idsContacts, $idContaclistfrom);
      return $this->set_json_response(array('error' => $wrapper->getResponse(), 'arrayError' => $wrapper->getRepitedContacts(), 200));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while copying contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while copying contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/copycontactselected")
   */
  public function copycontactselectedAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $idNewContactlist = $data[0][0];
      $idsContacts = $data[1];
      $idContaclistfrom = $data[2];

      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->copyContacts($idNewContactlist, $idsContacts, $idContaclistfrom);
      $this->trace("success", "Se han copiado exitosamente los contactos seleccionados");

      return $this->set_json_response(array('message' => 'Se han copiado exitosamente los contactos seleccionados', 200));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while copying contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while copying contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Post("/changesuscribeselected")
   */
  public function changesuscribeselectedAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $valueSuscribe = $data[2];
      $idsContacts = $data[0];
      $idContaclistfrom = $data[1];

      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->changeSuscribeSelected($valueSuscribe, $idsContacts, $idContaclistfrom);
      $this->trace("success", "Se ha cambiado la suscripción de los contactos seleccionados");

      return $this->set_json_response(array('message' => 'Se ha cambiado la suscripción de los contactos seleccionados', 200));
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while sus/desusbcribing contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while sus/desusbcribing contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/dobleoptin/{parameters}")
   */
  public function dobleoptinAction($parameters) {
    try {
      $this->encoder = new \Sigmamovil\General\Links\ParametersEncoder();
      $this->encoder->setBaseUri($this->urlManager->get_base_uri(true));
      list($idLink, $idcontaclist, $idContact, $idForm) = $this->encoder->decodeLink('api/contact/dobleoptin', $parameters);
      $form = Form::findFirst(array("conditions" => "idForm = ?0", "bind" => array(((Int) $idForm))));
      //$urlSuccess = "http://www.sigmamovil.com/";
      $urlBase = \Phalcon\DI::getDefault()->get('urlManager')->get_base_uri(true);
      $urlSuccess = $urlBase."subscribe/form/".$form->Subaccount->idAccount;
      $wrapper = new Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->subcribedContact($idcontaclist, $idContact, $form);
      return $this->response->redirect($urlSuccess, true);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while sus/dobleoptin contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while sus/dobleoptin contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Get("/getonecontact/{idContact:[0-9]+}")
   */
  public function getonecontactAction($idContact) {
    try {
      $wrapper = new Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->findOneContact($idContact);
      return $this->set_json_response($wrapper->getContact(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding one contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding one contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getallsms")
   */
  public function getallsmsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $idContact = $data[0];
      $page = $data[1];
      $name = $data[2];
      $wrapper = new Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->findSmsxc($idContact, $page, $name);
      return $this->set_json_response($wrapper->getSmsxc(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding sms per contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms per contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Post("/getallmail")
   */
  public function getallmailAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $idContact = $data[0];
      $page = $data[1];
      $name = $data[2];
      $wrapper = new Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->findMxc($idContact, $page, $name);
      return $this->set_json_response($wrapper->getMxc(), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding mail per contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding mail per contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/preview/{id:[0-9]+}")
   */
  public function previewAction($id) {
    $mailtemplatecontent = MailContent::findFirst(array(
                "conditions" => "idMail = ?0",
                "bind" => array($id)
    ));
    //Editor , html

    if ($mailtemplatecontent->typecontent == 'Editor') {
      $editorObj = new \Sigmamovil\Logic\Editor\HtmlObj();
      $editorObj->setAccount(((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account : ((isset($this->user->Usertype->Subaccount->idSubaccount)) ? $this->user->Usertype->Subaccount->Account : null)));
      $editorObj->assignContent(json_decode($mailtemplatecontent->content));
      $html = $editorObj->render();
    } else if ($mailtemplatecontent->typecontent == 'html' or $mailtemplatecontent->typecontent == 'url') {
      $html = $mailtemplatecontent->content;
    }

    return $this->set_json_response(array('template' => $html));
  }
  
  /**
   *
   * @Post("/countcontact")
   */
  public function getcountcontactsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\SmsWrapper();
      return $this->set_json_response($wrapper->getCountContacts($data), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log      ("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
  /**
   * 
   * @Post("/getdataform")
   */  
  public function getdataformAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      if (empty($data)) {
        throw new \InvalidArgumentException("La información enviada esta vacia");
      }    
      return $this->set_json_response(array('message' => "Se ha recibido la información correctamente", 'data' => $data), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }    
  }
  /**
   *
   * @Post("/addcontactbyform/{idContactlist:[0-9]+}")
   */
   
  public function addcontactbyformAction($idContactlist) {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      if (empty($data)) {
        throw new \InvalidArgumentException("La información enviada esta vacia");
      }else{
          $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();            
          $wrapper->setData($data);
          
          $mailtemplate = \MailTemplate::findFirst(array(
                    "conditions" => "idMailTemplate = ?0 and deleted = 0 and idAccount =".(int) \Phalcon\DI::getDefault()->get('user')->UserType->Subaccount->idAccount,
                    "bind" => array((int) $data->idMailTemplate)
          ));
          if (!$mailtemplate) {
            throw new \InvalidArgumentException("La plantilla que ingresada no está registrada");
          }
          $result = $wrapper->saveContactbyform($idContactlist);
          unset($data);
          $idContact = $result['idContact'];
          
          if(!empty($idContact)){
            $contactlist = \Contactlist::findFirst(array(
                    "conditions" => "idContactlist = ?0",
                    "bind" => array(0 => $idContactlist)
              ));
            $this->addContactSegment($idContact, $contactlist, $mailtemplate);
          } 
          return $this->set_json_response(array('message' => $result['menssage']), 200); 
      } 
                    
    } catch (\Sigmamovil\General\Exceptions\ValidateEmailException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage(), "code" => $ex->getCode()), 409);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 403);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/exportmorecontacts/")
   */  
  public function exportmorecontactsAction(){
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      if (empty($data)) {
        throw new \InvalidArgumentException("La información enviada esta vacia");
      }
      $idContactlist = $data->idContactlist;
      //Consulta para traer la informacion de la lista de contactos
      $contactlist = \Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($idContactlist)));
      if (!$contactlist) {
        throw new \InvalidArgumentException("No se encontró la lista de contactos {$idContactlist}, por favor valide la información.");
      }
      switch ($data->typeExport) {
        case 0:
          $whereTypeExport = "";
          $titleExport = "Todos";
          break;
        case 1:
          $whereTypeExport = "AND status = 'active'";
          $titleExport = "Activos";
          break;
        case 2:
          $whereTypeExport = "AND status = 'unsubscribed'";
          $titleExport = "Desuscritos";
          break;
        case 3:
          $whereTypeExport = "AND status = 'bounced'";
          $titleExport = "Rebotados";
          break;
        case 4:
          $whereTypeExport = "AND status = 'spam'";
          $titleExport = "Spam";
          break;
        case 5:
          $whereTypeExport = "AND status = 'blocked'";
          $titleExport = "Bloqueados";
          break;                
      }
      $typeExport = $whereTypeExport;
      $email = $data->email;
      $idAccount = \Phalcon\DI::getDefault()->get('user')->UserType->subAccount->idAccount;
      $data2 = array($idContactlist,$typeExport,$email,$idAccount);
      $this->setElephant(new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000')));
      /*$wrapper = new segw();
      $idSegment = $wrapper->addSegment($datasegment);*/
      $this->getElephant()->initialize();
      $this->getElephant()->emit('export-contacts', array(
        'data2' => $data2
      ));
      $this->getElephant()->close();
      //$this->sendMail($idContactlist, $titleExport,$email);
      //return $this->set_json_response(array('message' => "Se ha recibido la información correctamente", 'data' => $data), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }    
  }
  
  public function addContactSegment($idContact, $contactlist, $mailtemplate){
   $contact = Contact::findFirst([["idContact" => (int)$idContact, "deleted" => 0]]);
   //validar aquellos contactos que ya tengan un sgmento creado que pasará con eso si se crea un nuevo segmento o no 
   //$contactsegment = \Sxc::find([["idContact" => $contact->idContact, "deleted" => 0,"idAccount" =>(string) $contactlist->Subaccount->idAccount]]);
   $dt = new DateTime();
   $dt->setTimezone(new DateTimeZone('America/Bogota'));
   $dt->setTimestamp(time());
   if(!empty($contact->email) && empty($contact->phone)){     
        $data = json_encode(array(
        	"filters"=> array(
        		"customfield"=> array(
        			"idCustomfield"=> "email",
        			"name"=> "Correo electrónico",
        			"type"=> "Text",
        			"value"=> ""
        		),
        		"conditions"=> "Es igual a",
        		"value"=> $contact->email
        	), 
           	"information" => array(
        		"name"=> "Segmento creación de contacto ".$dt->format('Y-m-d h:i:s'),
        		"contactlist" => array(
        			"idContactlist"=> (string)$contactlist->idContactlist,
        			"idSubaccount"=> (string)$contactlist->idSubaccount,
        			"ctotal"=> (string)$contactlist->ctotal,
        			"cunsubscribed"=> (string)$contactlist->cunsubscribed,
        			"cactive"=> (string) $contactlist->cactive,
        			"cspam"=> (string)$contactlist->cspam,
        			"cbounced"=> (string) $contactlist->cbounced,
        			"created"=> (string) $contactlist->created,
        			"updated"=> (string) $contactlist->updated,
        			"name"=> $contactlist->name,
        			"description"=> $contactlist->description,
        			"idContactlistCategory"=> (string) $contactlist->idContactlistCategory,
        			"cblocked"=> (string) $contactlist->cblocked,
        			"createdBy"=>  $contactlist->createdBy,
        			"updatedBy"=> $contactlist->updatedBy,
        			"deleted"=> (string) $contactlist->deleted
        		),
        		"conditions"=> "Todas las condiciones"
        	)

        ));
   }else if(!empty($contact->phone) && empty($contact->email)){
       $data = json_encode(array(
        		"filters"=> array(
        		"customfield" => array(
        			"idCustomfield"=> "phone",
        			"name"=> "Movil",
        			"type"=> "Numerical",
        			"value"=> ""
        		),
        		"conditions"=> "Es igual a",
        		"value"=> $contact->phone
        	),        	
         	"information" => array(
        		"name"=> "Segmento creación de contacto 2".$dt->format('Y-m-d h:i:s'),
        		"contactlist" => array(
        			"idContactlist"=> (string)$contactlist->idContactlist,
        			"idSubaccount"=> (string)$contactlist->idSubaccount,
        			"ctotal"=> (string)$contactlist->ctotal,
        			"cunsubscribed"=> (string)$contactlist->cunsubscribed,
        			"cactive"=> (string) $contactlist->cactive,
        			"cspam"=> (string)$contactlist->cspam,
        			"cbounced"=> (string) $contactlist->cbounced,
        			"created"=> (string) $contactlist->created,
        			"updated"=> (string) $contactlist->updated,
        			"name"=> $contactlist->name,
        			"description"=> $contactlist->description,
        			"idContactlistCategory"=> (string) $contactlist->idContactlistCategory,
        			"cblocked"=> (string) $contactlist->cblocked,
        			"createdBy"=>  $contactlist->createdBy,
        			"updatedBy"=> $contactlist->updatedBy,
        			"deleted"=> (string) $contactlist->deleted
        		),
        		"conditions"=> "Todas las condiciones"
        	)

        )); 
   } else if(!empty($contact->phone) && !empty($contact->email)){
        $data = json_encode(array(
            	"filters"=> array( array(
            		"customfield"=> array(
            			"idCustomfield"=> "email",
            			"name"=> "Correo electrónico",
            			"type"=> "Text",
            			"value"=> ""
            		),
            		"conditions"=> "Es igual a",
            		"value"=> $contact->email
            	), array(
            		"customfield"=> array(
            			"idCustomfield"=> "phone",
            			"name"=> "Movil",
            			"type"=> "Numerical",
            			"value"=> ""
            		),
            		"conditions"=> "Es igual a",
            		"value"=> $contact->phone
            	)
            	),
                "information" => array(
            		"name"=> "Segmento creación de contacto ".$dt->format('Y-m-d h:i:s'),
            		"contactlist" => array( array(
            			"idContactlist"=> (string)$contactlist->idContactlist,
            			"idSubaccount"=> (string)$contactlist->idSubaccount,
            			"ctotal"=> (string)$contactlist->ctotal,
            			"cunsubscribed"=> (string)$contactlist->cunsubscribed,
            			"cactive"=> (string) $contactlist->cactive,
            			"cspam"=> (string)$contactlist->cspam,
            			"cbounced"=> (string) $contactlist->cbounced,
            			"created"=> (string) $contactlist->created,
            			"updated"=> (string) $contactlist->updated,
            			"name"=> $contactlist->name,
            			"description"=> $contactlist->description,
            			"idContactlistCategory"=> (string) $contactlist->idContactlistCategory,
            			"cblocked"=> (string) $contactlist->cblocked,
            			"createdBy"=>  $contactlist->createdBy,
            			"updatedBy"=> $contactlist->updatedBy,
            			"deleted"=> (string) $contactlist->deleted
                        )
            		),
            		"conditions"=> "Todas las condiciones"
            	)
        ));
   }
   $datasegment = json_decode($data);
unset($data);
   $this->setElephant(new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000')));
   $wrapper = new segw();
   $idSegment = $wrapper->addSegment($datasegment);
   $this->getElephant()->initialize();
      $this->getElephant()->emit('create-segment', array(
          'idSegment' => $idSegment
   ));
   $this->getElephant()->close();
   $this->sendMail($idSegment, $mailtemplate);
  }
  
  public function sendMail($idSegment, $mailtemplate){ 
    $dt = new DateTime();
    $dt->setTimezone(new DateTimeZone('America/Bogota'));
    $dt->setTimestamp(time());
    $Segment = \Segment::findFirst([["idSegment" => $idSegment, "deleted" => 0]]);
    $datamail = json_encode(array(
            	"mail" => array(	
    			"name" => $mailtemplate->name,
    			"category" => array("3468"), // 3468 la de notiicas de siesa
    			"test"=>"0", 
    			"subject" => $mailtemplate->name, 
    			"sender" => "mercadeo@siesa.com/Mercadeo Siesa",
    			"replyto" => "",
    			"scheduleDate" => "now",
    			"gmt" => "-0500",
    			"singleMail" => "1",
    			"target"=>array(
                    "type" =>"segment",
                    "segment" => array(
                                 array("idSegment" => (int) $Segment->idSegment,"name" => $Segment->name)
                    )
    			),
                "externalApi" =>"1"                              
        	),"content" => array(
        		"type" => "template",
        		"content" =>(string) $mailtemplate->idMailTemplate
        	) 				
    )); 
    
    $ApiMail = new \ApimailController();
    
    $ApiMail->sendemailfromApiAction($datamail);
    

  }
 
}
