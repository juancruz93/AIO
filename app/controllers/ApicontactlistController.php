<?php

/**
 * @RoutePrefix("/api/contactlist")
 */
class ApicontactlistController extends ControllerBase {

  /**
   * 
   * @Post("/getcontactlists/{page:[0-9]+}")
   */
  public function getcontactlistsAction($page) {
    try {
      $json = $this->getRequestContent();
      $data = json_decode($json);

      $contactlist = Contactlist::find(array(
                  "conditions" => "idSubaccount = ?0",
                  "bind" => array(0 => $this->user->userType->idSubaccount)
      ));

      if (!$contactlist) {
        throw new InvalidArgumentException("Verifique la información enviada");
      }

      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->findContactlists($page, $data);

      return $this->set_json_response(array("contactlistxpage" => $wrapper->getContactlists(), "allcontactlist" => $wrapper->getAllContactlists()), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }
  
   /**
   * 
   * @Post("/gettotalcontactlist")
   */
  public function gettotalcontactlistAction() {
    try {
      
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      return $this->set_json_response(array("totals" => $wrapper->findtotals()), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/add")
   */
  public function addAction() {
    try {

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      if(isset($data->email) && isset($data->motive)){//SE CREA ESTE IF PARA PODER ENVIAR CONTACTOS A BLOQUEAR DESDE LA API DE api/contactlist/add
        $data2 = (object) array(
          "email" => $data->email,
          "motive" => $data->motive
        );
        $wrapper = new \Sigmamovil\Wrapper\BlockadeWrapper();
        $wrapper->saveBlocked($data2);
        $this->trace("success", "Se ha bloqueado un contacto");

      return $this->set_json_response(array("message" => "Se ha bloqueado un contacto"), 200);
      }else{
        $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
          $wrapper->setData($data);
          $contactlist = $wrapper->saveContactlist();
          $this->trace("success", "Se crea una nueva lista de contactos");
          return $this->set_json_response(array("message" => "Se ha creado la lista de contactos exitosamente", "contactlis" => $contactlist), 200);
      }
      
    } catch (\InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/savecategory")
   */
  public function savecategoryAction() {
    try {

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->saveContactlistCategory($data);
      $this->trace("success", "Se crea una nueva categoría de lista de contactos");
      return $this->set_json_response(array("message" => "Se ha creado la categoría  exitosamente", "category" => $wrapper->getCategory()), 200);
    } catch (\InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Post("/addcustomfield")
   */
  public function addcustomfieldAction() {
    try {

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
//      $data->updated = time();
//      $data->created = time();
      $string = new Sigmamovil\General\Misc\CleanString();
      $customfield = new \Customfield();
      $manager = \Phalcon\DI::getDefault()->get('mongomanager');
      $bulk = new \MongoDB\Driver\BulkWrite;
      $customfield->alternativename = $string->clear($data->name);


      if (empty($data->name)) {
        throw new \InvalidArgumentException("El campo nombre es obligatorio");
      }
      if (empty($data->typefield)) {
        throw new \InvalidArgumentException("El campo tipo de formato es obligatorio");
      }

      if ($data->typefield == 'Select' || $data->typefield == 'Multiselect') {
        if (empty($data->value)) {
          throw new \InvalidArgumentException("El campo valor es obligatorio si es por formato de selección");
        }
      }
      
      //VALIDAR QUE EL CAMPO NAME NO TENGA CARACTERES ESPECIALES DIFERENTES A GUIONES
      $Arrsearch = array('/','%','(',')','[',']',',','*','{','}','º','€','|','^','"',"'");
      
      for($i=0; $i < count($Arrsearch); $i++){
        
        if( strrpos($data->name , $Arrsearch[$i] ) ){
            throw new \InvalidArgumentException("El nombre solo puede contener guiones o espacios");
        }
        
      }
      
      $customfield->name = $data->name;

//      var_dump(date("Y-m-d H:i:s",$data->created));
//      exit;
      if (isset($data->defaultvalue)) {
        $customfield->defaultvalue = $data->defaultvalue;
      }
      $customfield->type = $data->typefield;
      $customfield->deleted = 0;
//      $customfield->created = $data->created;
//      $customfield->updated = $data->updated;

      $customfield->value = implode(",", $data->value);
      $customfield->idContactlist = $data->idContactlist;
      $customfield->idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;

      if ($this->validateExistNameCustomfield($customfield->name, $data->idContactlist)) {
        throw new \InvalidArgumentException("El nombre del campo ya existe");
      }
      $this->db->begin();
      if (!$customfield->save()) {
        $this->db->rollback();
        foreach ($customfield->getMessages() as $msg) {
          $this->logger->log("Message: {$msg}");
          throw new \InvalidArgumentException($msg);
        }
      }
      
      /*$obj = [
          "name" => $customfield->name,
          "value" => $customfield->defaultvalue,
          "type" => $customfield->type
      ];*/
      if (isset($customfield->defaultvalue)) {
          $value = $customfield->defaultvalue;
        if ($customfield->type != "Multiselect") {
            $value = trim($value);
        }
        $obj[$customfield->idCustomfield] = ["name" => $customfield->name, "value" => $value, "type" => $customfield->type];
      } else {
        $obj[$customfield->idCustomfield] = ["name" => $customfield->name, "value" => "", "type" => $customfield->type];
      }
    
      //$cxcl = Cxcl::find(["conditions" => "idContactlist = ?0", "bind" => [0 => $data->idContactlist]]);

      $sql = "SELECT DISTINCT idContact FROM cxcl"
            . " WHERE idContactlist = {$data->idContactlist}"
            . " AND deleted = 0 ";
        
      $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);

      foreach ($cxcl as $value) { 
        if (count($cxcl) >= 1) {
          $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);                 
          $cxc = \Cxc::findFirst([['idContact' => (int) $value['idContact']]] );
          
          if(!empty($cxc)){
            $tmp = $cxc->idContactlist;
            foreach($obj as $value => $key){
                $tmp[$customfield->idContactlist][$value] = $key;
            }
            $cxc->idContactlist = null;
            $cxc->idContactlist= (object) $tmp;
            $cxc->save();
            unset($cxc);
                        
          }else{
            $cxc = new \Cxc();
            $cxc->idContact = (int) $value['idContact'];
            $cxc->idContactlist = [$customfield->idContactlist => $obj];
            $cxc->save();
            unset($cxc);  
          }
        }
      }
      
      $this->db->commit();
      $this->trace("success", "Se ha creado el campo personalizado");
      return $this->set_json_response(array("message" => "Se ha creado el campo personalizado exitosamente", "customfield" => $customfield), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function validateExistNameCustomfield($name, $idContactlist) {
    $result = \Customfield::find(["conditions" => "name = ?0 AND deleted= 0 AND idContactlist = ?1",
                "bind" => [0 => $name, 1 => $idContactlist]]);

    if (count($result) > 0) {
      return true;
    }
    return false;
  }

  /**
   * 
   * @Put("/edit/{idContactlist:[0-9]+}")
   */
  public function editAction($idContactlist) {
    try {
      $contactlist = Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($idContactlist)));
      if (!$contactlist) {
        throw new InvalidArgumentException("No se ha encontrado la lista de contactos, por favor intenta de nuevo");
      }

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);

      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->setContactlist($contactlist);
      $wrapper->setData($data);
      $wrapper->editContactlist();
      $this->trace("success", "Se ha editado la lista de contactos '" . $wrapper->getContactlist()->name . "'");
      return $this->set_json_response(array("message" => "Se ha editado la lista de contactos exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Put("/editcustomfield/{idCustomfield:[0-9]+}")
   */
  public function editcustomfieldAction($idCustomfield) {
    try {
      $customfield = Customfield::findFirst(array('conditions' => "idCustomfield = ?0", 'bind' => array($idCustomfield)));
      if (!$customfield) {
        throw new InvalidArgumentException("No se ha encontrado el campo personalizado, por favor intenta de nuevo");
      }

      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);
      $data->updated = time();
      $wrapper = new \Sigmamovil\Wrapper\CustomfieldWrapper();
      $wrapper->setCustomfield($customfield);
      $wrapper->setData($data);
      $wrapper->editCustomfield();
      $this->trace("success", "Se ha editado el campo personalizado");

      return $this->set_json_response(array("message" => "Se ha editado el campo personalizado exitosamente", "customfield" => $customfield), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getcontactlist/{idContactlist:[0-9]+}")
   */
  public function getcontactlistAction($idContactlist) {
    try {
      $contactlist = Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($idContactlist)));
      if (!$contactlist) {
        throw new InvalidArgumentException("No se ha encontrado la lista de contactos, por favor intenta de nuevo");
      }

      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->setContactlist($contactlist);
      $wrapper->modelContactlist();
      return $this->set_json_response($wrapper->getContactlist(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/exportcontacts/{idContactlist:[0-9]+}")
   */
  public function exportcontactsAction($idContactlist) {
    try {
      $contactlist = Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($idContactlist)));
      if (!$contactlist) {
        throw new InvalidArgumentException("No se ha encontrado la lista de contactos, por favor intenta de nuevo");
      }

      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->setContactlist($contactlist);
      $wrapper->exportContactsFromOne();
      $this->trace("success", "Se han exportado contactos de la lista de contactos '" . $wrapper->getContactlist()->name . "'");

      return $this->set_json_response($wrapper->getContactlist(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while exporting contacts... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getonecustomfield/{idCustomfield:[0-9]+}")
   */
  public function getonecustomfieldAction($idCustomfield) {
    try {
      $customfield = Customfield::findFirst(array('conditions' => "idCustomfield = ?0", 'bind' => array($idCustomfield)));
      if (!$customfield) {
        throw new InvalidArgumentException("No se ha encontrado el campo personalizado");
      }

      $wrapper = new \Sigmamovil\Wrapper\CustomfieldWrapper();
      $wrapper->setCustomfield($customfield);
      $wrapper->modelCustomfield();

      return $this->set_json_response($wrapper->getCustomfield(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/delete/{idContactlist:[0-9]+}", methods="DELETE")
   */
  public function deleteAction($idContactlist) {
    try {
      $contactlist = Contactlist::findFirst(array('conditions' => "idContactlist = ?0", 'bind' => array($idContactlist)));
      if (!$contactlist) {
        throw new InvalidArgumentException("No se ha encontrado la lista de contactos, por favor intenta de nuevo");
      }

      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->setContactlist($contactlist);
      $wrapper->deleteContactlist();
      $this->trace("success", "Se ha eliminado la lista de contactos '" . $wrapper->getContactlist()->name . "'");

      return $this->set_json_response(array("message" => "Se ha eliminado la lista de contactos exitosamente"), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * @Route("/deletecustomfield/{idCustomfield:[0-9]+}", methods="DELETE")
   */
  public function deletecustomfieldAction($idCustomfield) {
    try {
      $customfield = Customfield::findFirst(array('conditions' => "idCustomfield = ?0", 'bind' => array($idCustomfield)));
      if (!$customfield) {
        throw new InvalidArgumentException("No se ha el campo personalizado, por favor intenta de nuevo");
      }

      $wrapper = new \Sigmamovil\Wrapper\CustomfieldWrapper();
      $wrapper->setCustomfield($customfield);
      $wrapper->deleteCustomfield();

      return $this->set_json_response(array("message" => "Se ha eliminado el campo personalizado exitosamente", "customfield" => $customfield), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/listcustomfield/{idcontactlist:[0-9a-zA-Z]+}/{page:[0-9]+}")
   */
  public function customfieldAction($idContactslist, $page) {
    try {

      $customfield = Customfield::find(array(
                  "conditions" => "idContactlist = ?0",
                  "bind" => array(0 => $idContactslist)
      ));
      if (!$customfield) {
        throw new InvalidArgumentException("No tienes campos personalizados");
      }
      $wrapper = new \Sigmamovil\Wrapper\CustomfieldWrapper();
      $wrapper->findCustomfield($idContactslist, $page);
      return $this->set_json_response($wrapper->getCustomfields(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getcontactlistinfo/{idContactlist:[0-9]+}")
   */
  public function getcontactlistinfoAction($idContactlist) {
    try {

      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->setContactlistInfo($idContactlist);

//      $contactlist->created = date('d/m/Y H:ia', $contactlist->created);
//      $contactlist->updated = date('d/m/Y H:ia', $contactlist->updated);
//
//      $contactlist->ctotal = \Cxcl::count(array("conditions" => "idContactlist = ?1 AND deleted = 0", "bind" => array(1 => $contactlist->idContactlist)));
//      $contactlist->cactive = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "active", 1 => $contactlist->idContactlist)));
//      $contactlist->cunsubscribed = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "unsubscribed", 1 => $contactlist->idContactlist)));
//      $contactlist->cbounced = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "bounced", 1 => $contactlist->idContactlist)));
//      $contactlist->cblocked = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "blocked", 1 => $contactlist->idContactlist)));
//      $contactlist->cspam = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "spam", 1 => $contactlist->idContactlist)));
      return $this->set_json_response(json_encode($wrapper->getContactlistInfo(), true), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   *
   * @Get("/getcontactlistbysubaccount")
   */
  public function getContactListBySubaccountAction() {
    try {

      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->getContactListBySubaccountAction();
      $wrapper->modelDataContactlist();
      return $this->set_json_response($wrapper->getContact(), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/getcontactlistcategory")
   */
  public function getcontactlistcategoryAction() {
    try {

      $wrapper = new \Sigmamovil\Wrapper\ContactlistWrapper();
      $wrapper->findCategories();

      return $this->set_json_response(array("categories" => $wrapper->getCategories()), 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  /**
   * 
   * @Get("/permissioncustomfield/{idContactlist:[0-9]+}")
   */
  public function permissioncustomfieldAction($idContactslist) {
    try {

      $customfield = Customfield::count(array(
                  "conditions" => "idContactlist = ?0 and deleted=0",
                  "bind" => array(0 => $idContactslist)
      ));
      $account = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
       //PERMITE CREAR 15 CF POR LC, LO QUE RESTEMOS A CF DEBE DAR COMO TOTAL 15 PARA PODER QUE PASE LA VALIDACION
      if($account == "912" || $account == 912 || $account == 817){
        $customfield = $customfield - 4;
      }else if($account == "49" || $account == 49 || $account == "1547" || $account == 1547){
        $customfield = $customfield - 15;
      }
      return $this->set_json_response($customfield, 200);
    } catch (InvalidArgumentException $ex) {
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding contactlist... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

}
