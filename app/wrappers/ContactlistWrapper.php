<?php

namespace Sigmamovil\Wrapper;

ini_set('memory_limit', '1024M');

class ContactlistWrapper extends \BaseWrapper {

  private $dbase;
  private $idMail;
  private $contactlist;
  private $contactlists;
  private $totals;
  private $contact = array();
  private $segments = array();

//  private $contactlists = array();

  function __construct() {
    parent::__construct();
  }

  /**
   * @param \Dbase $dbase
   */
  public function setDbase(\Dbase $dbase) {
    $this->dbase = $dbase;
  }

  /**
   * @param \Contactlist $contactlist
   */
  public function setContactlist(\Contactlist $contactlist) {
    $this->contactlist = $contactlist;
  }
  
  public function findtotals(){
    $query = "SELECT sum(ctotal) AS totales,"
            ." sum(cunsubscribed) AS desuscritos,"
            ." sum(cactive) AS activos,"
            ." sum(cspam) AS spam,"
            ." sum(cbounced) AS rebotados,"
            ." sum(cblocked) AS bloqueados"
            ." FROM contactlist"
            ." WHERE idsubaccount = ".\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount
            ." AND deleted = 0";
            
    $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query);
    return array("totales" =>$c[0]["totales"], 
            "desuscritos" =>$c[0]["desuscritos"],
            "activos" =>$c[0]["activos"], 
            "spam" =>$c[0]["spam"],
            "rebotados" =>$c[0]["rebotados"],
            "bloqueados" =>$c[0]["bloqueados"]);
  }

  public function findContactlists($page, $data) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1 ) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitizer = new \Phalcon\Filter;

    $conditions = array(
        "conditions" => "deleted = ?0 AND idSubaccount = :subacc:",
        "bind" => array(0, "subacc" => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "idContactlist DESC",
    );

    if (isset($data->idContactlistCategory)) {
      $conditions["conditions"] .= "AND idContactlistCategory = :category: ";
      $conditions["bind"]["category"] = $data->idContactlistCategory;
    }

    if (isset($data->name)) {
      $conditions["conditions"] .= "AND name LIKE :name: ";
      $conditions["bind"]["name"] = "%{$sanitizer->sanitize($data->name, 'string')}%";
    }
    if (isset($data->email) && !empty($data->email)) {
      $idsContact = "";
      $idsCxcl = "";
      $where = ['email' => ['$regex' => ".*$data->email.*", '$options' => "i"], 'idAccount' => \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount, 'deleted' => 0];
      $contact = \Contact::find([$where]);
      $length = count($contact);
      if ($length <= 0) {
        //throw new \InvalidArgumentException("El correo no se encuentra registrado en la base de datos.");
        $conditions["conditions"] .= " AND idContactlist = 0";
      } else {
        for ($i = 0; $i < $length; $i++) {
          $idsContact .= intval($contact[$i]->idContact) . ",";
        }
        $idsContact = substr($idsContact, 0, strlen($idsContact) - 1);
        $cxcl = \Cxcl::find(["conditions" => " idContact IN ($idsContact) and deleted = 0", "columns" => "idContactlist"])->toArray();
        $lengthCxcl = count($cxcl);
        if ($lengthCxcl > 0) {
          for ($i = 0; $i < $lengthCxcl; $i++) {
            $idsCxcl .= intval($cxcl[$i]["idContactlist"]) . ",";
          }
        } else {
          throw new \InvalidArgumentException("Este contacto fue eliminado de la base de datos.");
        }

        $idsCxcl = substr($idsCxcl, 0, strlen($idsCxcl) - 1);
        $conditions["conditions"] .= " AND idContactlist IN ($idsCxcl)";
      }
    }
    

    $this->data = \Contactlist::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $conditions["columns"] = "idLandinPageTemplate";
    $this->totals = \Contactlist::count($conditions);

    $this->modelData();
  }

  public function findAllContactlists() {
    $this->dataallcontactlist = \Contactlist::find([
                "conditions" => "idSubaccount=?0 and deleted = 0",
                "bind" => [0 => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount]
    ]);
    $this->modelAllContactlist();
  }

  public function findCategories() {
    $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;
    $this->dataCategories = \ContactlistCategory::find(array(
                "conditions" => "idAccount = ?0 AND deleted = 0",
                "bind" => array(0 => $idAccount)
    ));
    $this->modelCategories();
  }

  public function modelCategories() {
    $this->categories = array();
    foreach ($this->dataCategories as $data) {
      $category = new \stdClass();
      $category->idContactlistCategory = $data->idContactlistCategory;
      $category->idAccount = $data->idAccount;
      $category->createdDate = date("d/m/Y", $data->created);
      $category->updatedDate = date("d/m/Y", $data->updated);
      $category->createdHour = date("H:i a", $data->created);
      $category->updatedHour = date("H:i a", $data->updated);
      $category->updatedBy = $data->updatedBy;
      $category->createdBy = $data->createdBy;
      $category->name = $data->name;
      $category->description = $data->description;

      array_push($this->categories, $category);
    }
  }

  public function getCategories() {
    return $this->categories;
  }

  public function modelData() {
    $this->contactlists = array("total" => $this->totals, "total_pages" => ceil($this->totals / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $data) {
      $contactlist = new \stdClass();
      $contactlist->idContactlist = $data->idContactlist;
      $contactlist->ctotal = $data->ctotal;
      $contactlist->cactive = $data->cactive;
      $contactlist->cunsubscribed = $data->cunsubscribed;
      $contactlist->cbounced = $data->cbounced;
      $contactlist->cblocked = $data->cblocked;
      $contactlist->cspam = $data->cspam;
      $contactlist->createdDate = date("d/m/Y", $data->created);
      $contactlist->updatedDate = date("d/m/Y", $data->updated);
      $contactlist->createdHour = date("H:i a", $data->created);
      $contactlist->updatedHour = date("H:i a", $data->updated);
      $contactlist->name = $data->name;
      $contactlist->description = $data->description;
      $contactlist->category = ($data->ContactlistCategory->name) ? $data->contactlistCategory->name : "Sin categoría";

      array_push($arr, $contactlist);
    }
    array_push($this->contactlists, array("items" => $arr));
  }

  public function modelAllContactlist() {
    $this->allcontactlists = array();
    $arr = array();
    foreach ($this->dataallcontactlist as $data) {
      $contactlist = new \stdClass();
      $contactlist->idContactlist = $data->idContactlist;
      $contactlist->ctotal = $data->ctotal;
      $contactlist->cactive = $data->cactive;
      $contactlist->cunsubscribed = $data->cunsubscribed;
      $contactlist->cbounced = $data->cbounced;
      $contactlist->cblocked = $data->cblocked;
      $contactlist->cspam = $data->cspam;
      $contactlist->createdDate = date("d/m/Y", $data->created);
      $contactlist->updatedDate = date("d/m/Y", $data->updated);
      $contactlist->createdHour = date("H:i a", $data->created);
      $contactlist->updatedHour = date("H:i a", $data->updated);
      $contactlist->name = $data->name;
      $contactlist->description = $data->description;

      array_push($arr, $contactlist);
    }
    array_push($this->allcontactlists, array("items" => $arr));
  }

  public function saveContactlist() {
//    echo $this->data->idContactlist;
//    echo $this->data->disabledCustomField;
    $contactlist = new \Contactlist();
    $contactlist->idSubaccount = \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
    $contactlist->name = $this->data->name;
    $contactlist->idContactlistCategory = $this->data->idContactlistCategory;
    $validateName = \Contactlist::findFirst(["conditions" => "name = ?0 AND idSubaccount = ?1 AND deleted = 0", "bind" => [0 => $this->data->name, 1 => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount]]);
    if ($validateName != false) {
      throw new \InvalidArgumentException("El nombre '{$this->data->name}' ya se encuentra registrado, por favor seleccione otro nombre");
    }

    if (strlen($this->data->name) > 50) {
      throw new \InvalidArgumentException("El campo nombre no puede tener mas de 50 caracteres");
    }
    if (strlen($this->data->idContactlistCategory) == null or $this->data->idContactlistCategory == 0) {
      throw new \InvalidArgumentException("Debe elegir una categoría");
    }

    if (!$contactlist->name) {
      throw new \InvalidArgumentException("El campo nombre es de caracter obligatorio");
    }
    if (isset($this->data->description)) {
      $contactlist->description = $this->data->description;
      if (strlen($this->data->description) > 200) {
        throw new \InvalidArgumentException("El campo descripción no puede tener mas de 200 caracteres");
      }
    }
    \Phalcon\DI::getDefault()->get("db")->begin();
    if (!$contactlist->save()) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($contactlist->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    if (isset($this->data->disabledCustomField) && $this->data->disabledCustomField == true) {
      if (!isset($this->data->idContactlist->idContactlist)) {
        throw new \InvalidArgumentException("Debes seleccionar una lista de contactos para clonar los campos personalizados");
      }
      $customfields = \Customfield::find(["conditions" => "idContactlist = ?0", "bind" => [$this->data->idContactlist->idContactlist]]);
      if (count($customfields) > 0) {
        foreach ($customfields as $key) {
          $customfield = new \Customfield();
          $customfield->alternativename = $key->alternativename;
          $customfield->defaultvalue = $key->defaultvalue;
          $customfield->idAccount = $key->idAccount;
          $customfield->idContactlist = $contactlist->idContactlist;
          $customfield->name = $key->name;
          $customfield->type = $key->type;
          $customfield->value = $key->value;
          if (!$customfield->save()) {
            \Phalcon\DI::getDefault()->get("db")->rollback();
            foreach ($customfield->getMessages() as $msg) {
              $this->logger->log("Message: {$msg}");
              throw new \InvalidArgumentException($msg);
            }
          }
        }
      }
    }
    \Phalcon\DI::getDefault()->get("db")->commit();
    return $contactlist;
  }

  public function saveContactlistCategory($data) {
    $idAccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount;

    if ($data->name == "" or $data->name == null) {
      throw new \InvalidArgumentException("Debe indicar un nombre de la categoría");
    }
    $name = ucwords($data->name);             
    $name = ucwords(strtolower($name));
    $query = "SELECT * FROM contactlist_category WHERE idAccount = {$idAccount} AND name = '{$name}' AND deleted = 0";
    $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($query); 
    if(count($c)>0){
       throw new \InvalidArgumentException("El nombre de la categoría ya está registrado en la plataforma"); 
    }
    $category = new \ContactlistCategory();
    $category->name = $data->name;
    $category->idAccount = $idAccount;
    if (!$category->save()) {
      foreach ($category->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    $this->category = $category;
  }

  public function getCategory() {
    return $this->category;
  }

  public function editContactlist() {
    //1. Validamos que no haya otra lista de contactos en la base de datos con el mismo nombre
    $this->validateContactlistName();
    //2. Asignamos los datos
    $this->contactlist->name = $this->data->name;
    $this->contactlist->description = $this->data->description;
    $this->contactlist->idContactlistCategory = $this->data->idContactlistCategory;
    //3. Guardamos la lista de contactos
    if (strlen($this->data->name) > 50) {
      throw new \InvalidArgumentException("El campo nombre no puede tener mas de 50 caracteres");
    }
    if (!$this->data->name) {
      throw new \InvalidArgumentException("El campo nombre es de caracter obligatorio");
    }
    if (strlen($this->data->idContactlistCategory) == null) {
      throw new \InvalidArgumentException("Debe elegir una categoría");
    }
    if ($this->data->description) {
      if (strlen($this->data->description) > 200) {
        throw new \InvalidArgumentException("El campo descripción no puede tener mas de 200 caracteres");
      }
    }
    if (!$this->contactlist->save()) {
      //4. En caso de que haya ocurrido un error, obtenemos los mensajes de error del modelo y generamos una InvalidArgumentException
      foreach ($this->contactlist->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  public function validateContactlistName() {
    //1. Buscamos una lista de contactos en la base de datos con el nombre ingresado
    $cl = \Contactlist::findFirst(array("conditions" => "idSubaccount = ?0 AND name = ?1", "bind" => array(\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount, $this->data->name)));
    //2. Si existe la lista, y no es la misma que se está editando, quiere decir que hay otra lista con el mismo nombre en la base de datos, entonces
    //generamos la una InvalidArgumentException
    if ($cl && $cl->idContactlist != $this->contactlist->idContactlist) {
      throw new \InvalidArgumentException("Ya existe una lista de contactos guardada con el nombre ingresado, por favor valida la información");
    }
  }

  public function modelContactlist() {
    $data = $this->contactlist;
    $this->contactlist = array();
    $this->contactlist['idContactlist'] = $data->idContactlist;
    $this->contactlist['ctotal'] = $data->ctotal;
    $this->contactlist['cunsubscribed'] = $data->cunsubscribed;
    $this->contactlist['cactive'] = $data->cactive;
    $this->contactlist['cspam'] = $data->cspam;
    $this->contactlist['cbounced'] = $data->cbounced;
    $this->contactlist['created'] = $data->created;
    $this->contactlist['updated'] = $data->updated;
    $this->contactlist['name'] = $data->name;
    $this->contactlist['description'] = $data->description;
    $this->contactlist['idContactlistCategory'] = $data->idContactlistCategory;
  }

  public function deleteContactlist() {
    $customLogger = new \TrackLog();
    $customLogger->registerDate = date("Y-m-d h:i:sa");
    $customLogger->idAccount = $this->user->Usertype->Subaccount->idAccount;
    $customLogger->idSubaccount = $this->user->Usertype->Subaccount->idSubaccount;
    $customLogger->idContactlist = $this->contactlist->idContactlist;
    $customLogger->typeName = "deleteContactlistMethod";
    $customLogger->detailedLogDescription = "Se ha eliminado la lista de contactos";
    $customLogger->created = time();
    $customLogger->updated = time();
    $customLogger->save();
    unset($customLogger);
    
    $this->contactlist->deleted = time();
    if (!$this->contactlist->update()) {
      foreach ($this->contactlist->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException("No se pudo eliminar la lista de contactos, es posible que esté relacionada a contactos, contacta al administrador para solicitar más información");
      }
    }
    $sqldelete = "UPDATE cxcl SET deleted =" . time() . ", active=0 WHERE idContactlist=" . $this->contactlist->idContactlist;
    $this->db->execute($sqldelete);
    
    $options = array('projection' => array('idSegment' => 1));                
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $queryRepeat = new \MongoDB\Driver\Query(["contactlist.idContactlist" => "" . $this->contactlist->idContactlist . "","deleted" => 0],$options);
    $segment = $manager->executeQuery("aio.segment", $queryRepeat)->toArray();
    
    if($segment){
        
       $idSegments = array();
        foreach($segment as $keysegment){
            $idSegments[] = $keysegment->idSegment;
        }
     
        unset($segment);
        
        \Phalcon\DI::getDefault()->get('logger')->log("--entro a update de segmentos--"); 
        $deleted = time();
        $idSubaccount =  $this->user->Usertype->Subaccount->idSubaccount;                   
        
        $bulk = new \MongoDB\Driver\BulkWrite;                    
        $bulk->update(['idSubaccount' =>(int) $idSubaccount, 'idSegment' => ['$in' => $idSegments]], ['$set' => ['deleted' =>(int) $deleted]], ['multi' => true]);
        $manager->executeBulkWrite('aio.sxc', $bulk);
                
        \Phalcon\DI::getDefault()->get('logger')->log("--salgo de update de segmentos--");

    }

    $idAccount = $this->user->UserType->Subaccount->idAccount;
    $sql = "CALL updateCountersAccount({$idAccount})";
    $this->db->fetchAll($sql);   
  }

  public function getContactlists() {
    return $this->contactlists;
  }

  public function getAllContactlists() {
    return $this->allcontactlists;
  }

  public function getContactlist() {
    return $this->contactlist;
  }

  public function getContact() {
    return $this->contact;
  }

  public function getSegment() {
    return $this->segments;
  }

  public function getAllContanctList() {
    $this->contactlists = \Contactlist::find(array("conditions" => "idSubaccount = ?0 and deleted = ?1", "bind" => array(0 => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount, 1 => 0)));
    $this->modelDataContactlist();
  }

  public function getAllTags($ids, $way) {

    $this->tags[0]['name'] = 'Nombre';
    $this->tags[0]['tag'] = '%NAME%';
    $this->tags[1]['name'] = 'Apellido';
    $this->tags[1]['tag'] = '%LASTNAME%';
    $this->tags[2]['name'] = 'Fecha de nacimiento';
    $this->tags[2]['tag'] = '%BIRTHDATE%';
    $this->tags[3]['name'] = 'Correo electrónico';
    $this->tags[3]['tag'] = '%EMAIL%';
    $this->tags[4]['name'] = 'Indicativo';
    $this->tags[4]['tag'] = '%INDICATIVE%';
    $this->tags[5]['name'] = 'Móvil';
    $this->tags[5]['tag'] = '%MOBILEPHONE%';

    if ($way == "contactlist") {
      
    } else if ($way == "segment") {
      
    }
    return $this->tags;
  }

  public function getAllSegment() {
    $this->data = \Segment::find([["idSubaccount" => \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount, "deleted" => 0]]);
    $this->modelDataSegment();
  }

  public function modelDataSegment() {
    foreach ($this->data as $data) {
      $segment = new \stdClass();
      $segment->idSegment = $data->idSegment;
      $segment->name = $data->name;
      array_push($this->segments, $segment);
    }
  }

  public function modelDataContactlist() {
    foreach ($this->contactlists as $data) {
      $contactlist = new \stdClass();
      $contactlist->idContactlist = $data->idContactlist;
//      $contactlist->ctotal = $data->ctotal;
//      $contactlist->cunsubscribed = $data->cunsubscribed;
//      $contactlist->cactive = $data->cactive;
//      $contactlist->cspam = $data->cspam;
//      $contactlist->cbounced = $data->cbounced;
//      $contactlist->created = $data->created;
//      $contactlist->updated = $data->updated;
      $contactlist->name = $data->name;
      $contactlist->idContactlistCategory = $data->idContactlistCategory;
//      $contactlist->description = $data->description;
      array_push($this->contact, $contactlist);
    }
  }

  public function getCountContacts($data) {
    $count = 0;
    switch ($data->type) {
      case "contactlist":
        $idContactlist = [];
        foreach ($data->contactlist as $key => $value) {
          $idContactlist[] = (int) $value->idContactlist;
        }
        $contactList = (object) $this->getOneContactlist($idContactlist, $data->singleMail, $data->filters);
        $count = $contactList->count;
        break;
      case "segment":
        $idsSegment = [];
        foreach ($data->segment as $key) {
          $idsSegment[] = (int) $key->idSegment;
        }
        if (count($data->filters) > 0 && isset($data->filters[0]) && (isset($data->filters[0]->mailSelected) || isset($data->filters[0]->linkSelected) )) {
          $count = $this->getIdSxcByFilters($idsSegment, $data);
        } else {
          $count = \Sxc::count([["idSegment" => ['$in' => $idsSegment], "deleted" => (int) 0, "unsubscribed" => (int) 0, "blocked" => (int) 0 ]]);
        }
        break;
      default:
        break;
    } 
    return array("count" => $count);
  }

  public function getIdSxcByFilters($idsSegment, $filters) {

    $c = 0;
    $this->db->begin();
    /*
     * opcional
     */
//    $sqldelete = "DELETE FROM tmp_table_target";
//    $this->db->execute($sqldelete);
    $this->createTableTemporary($this->idMail);
    if (count($filters->filters) > 0) {
      $route = \Phalcon\DI::getDefault()->get('path')->path . "tmp/tmptabletarget.csv";
      $whereAll = " WHERE 1 = 1 ";
      $condition = " AND ";
      if ($filters->condition == "some" && count($filters->filters) > 1) {
        $condition = " OR ";
      }

      $sxc = \Sxc::find([["idSegment" => ['$in' => $idsSegment]]]);
      $idsContact = [];
      foreach ($sxc as $key) {
        $idsContact[] = (int) $key->idContact;
      }

      $flag = false;
      $idMailLink = [];
      foreach ($filters->filters as $key) {
        switch ($key->typeFilters) {
          case 1:
            $whereAll .= " {$condition} (idMail = {$key->mailSelected}) ";
            break;
          case 2:
            $whereAll .= " {$condition} (idMail = {$key->mailSelected} AND open > 0) ";
            break;
          case 3:
            $flag = true;
            $idMailLink[] = (string) $key->linkSelected;
            $whereAll .= " {$condition} (idMail = {$key->mailSelected} AND idMailLink = {$key->linkSelected}) ";
            break;
        }
      }

      if ($flag == false) {
        $w["idContact"] = ['$in' => $idsContact];
        $p = \Mxc::find([$w]);
        $file = fopen($route, "w");
        $i = 1;
        foreach ($p as $value) {
          fwrite($file, $i . " ,");
          fwrite($file, $value->idMail . " ,");
          fwrite($file, $value->idContact . " ,");
          fwrite($file, $value->open . " ,");
          fwrite($file, 0);
          fwrite($file, "\r\n");
          $i++;
        }
        $tmpopen = fclose($file);
        if (!$tmpopen) {
          throw new Exception("No se ha generado el archivo temporal");
        }

        $this->db->query("LOAD DATA INFILE '{$route}' IGNORE INTO TABLE tmp_table_target_{$this->idMail} "
                . "FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' ");

        $sql = "SELECT count( DISTINCT idContact ) AS count FROM tmp_table_target_{$this->idMail} {$whereAll}";
        $count = $this->db->fetchAll($sql);
        $c = $count[0]['count'];
      } else {
        $w["idContact"] = ['$in' => $idsContact];
        $p = \Mxc::find([$w]);
        $w["idMailLink"] = ['$in' => $idMailLink];
        $mxcxl = \Mxcxl::find([$w]);
        $file = fopen($route, "w");
        $i = 1;
        foreach ($p as $value) {
          fwrite($file, $i . " ,");
          fwrite($file, $this->idMail . " ,");
          fwrite($file, $value->idContact . " ,");
          fwrite($file, $value->open . " ,");
          fwrite($file, 0);
          fwrite($file, "\r\n");
          $i++;
        }
        foreach ($mxcxl as $value) {
          fwrite($file, $i . " ,");
          fwrite($file, $value->idMail . " ,");
          fwrite($file, $value->idContact . " ,");
          fwrite($file, "0" . " ,");
          fwrite($file, $value->idMailLink);
          fwrite($file, "\r\n");
          $i++;
        }
        $tmpopen = fclose($file);
        if (!$tmpopen) {
          throw new Exception("No se ha generado el archivo temporal");
        }

        $this->db->query("LOAD DATA INFILE '{$route}' IGNORE INTO TABLE tmp_table_target_{$this->idMail} "
                . "FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' ");
        $sql = "SELECT count( DISTINCT idContact ) AS count FROM tmp_table_target_{$this->idMail} {$whereAll}";

        $count = $this->db->fetchAll($sql);
        $c = $count[0]['count'];
      }
    }
    $this->db->commit();
    return $c;
//    exit;
  }

  public function createTableTemporary($idMail) {
    if (!$this->db->execute("CREATE TEMPORARY TABLE IF NOT EXISTS tmp_table_target_{$idMail} LIKE tmp_table_target")) {
      throw new \InvalidArgumentException("Ha ocurrido un error creando el espacio temporal de la tabla tmp_table_target");
    }
    return true;
  }

  public function getIdContactByFilters($data, $where) {
    $this->db->begin();
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $this->createTableTemporary($this->idMail);
    if (count($data->filters) > 0) {
      $flagWhile = true;
      $route = \Phalcon\DI::getDefault()->get('path')->path . "tmp/tmptabletarget{$this->idMail}.csv";
      $limit = 8000;
      $offset = 0;
      $i = 1;
      $whereAll = " WHERE 1 = 1 ";
      $condition = " AND ";
      if ($data->condition == "some" && count($data->filters) > 1) {
        $condition = " OR ";
      }
      $flag = false;
      $idMailLink = [];
      foreach ($data->filters as $key) {
        if (isset($key->inverted) && $key->inverted == true) {
          switch ($key->typeFilters) {
            case 1:
              $whereAll .= " {$condition} (idMail = {$key->mailSelected}) ";
              break;
            case 2:
              $whereAll .= " {$condition} (idMail = {$key->mailSelected} AND open = 0) ";
              break;
            case 3:
              $flag = true;
              $idMailLink[] = (string) $key->linkSelected;
              $whereAll .= " {$condition} (idMail = {$key->mailSelected} AND idMailLink != {$key->linkSelected}) ";
              break;
          }
        } else {
          switch ($key->typeFilters) {
            case 1:
              $whereAll .= " {$condition} (idMail = {$key->mailSelected}) ";
              break;
            case 2:
              $whereAll .= " {$condition} (idMail = {$key->mailSelected} AND open > 0) ";
              break;
            case 3:
              $flag = true;
              $idMailLink[] = (string) $key->linkSelected;
              $whereAll .= " {$condition} (idMail = {$key->mailSelected} AND idMailLink = {$key->linkSelected}) ";
              break;
          }
        }
      }
   
      $file = fopen($route, "w");
      while ($flagWhile) {
        $w = [];
        $sql = "SELECT DISTINCT idContact  FROM cxcl "
                . " WHERE idContactlist IN ({$where}) AND unsubscribed = 0 AND spam = 0 AND blocked = 0 AND bounced = 0 AND deleted = 0 LIMIT {$limit} OFFSET {$offset}";
        $c = \Phalcon\DI::getDefault()->get("db")->fetchAll($sql);

        $in = array();
        foreach ($c as $value) {
          $in[] = (int) $value['idContact'];
        }
        unset($c);
        $w["idContact"] = ['$in' => $in];
        if (!isset($in) || count($in) < $limit) {
          $flagWhile = false;
        }

        $options = [
            "projection" => [
                "idMail" => 1,
                "idContact" => 1,
                "open" => 1
            ]
        ];
        $queryTmp = new \MongoDB\Driver\Query($w, $options);
        $p = $manager->executeQuery("aio.mxc", $queryTmp)->toArray();
        unset($w);

        foreach ($p as $value) {
          fwrite($file, $i . " ,");
          fwrite($file, $value->idMail . " ,");
          fwrite($file, $value->idContact . " ,");
          fwrite($file, $value->open . " ,");
          fwrite($file, 0);
          fwrite($file, "\r\n");
          $i++;
        }
        unset($p);
        if ($flag) {
          $mxcxl = \Mxcxl::find(array(
                      "conditions" => array(
                          "idContact" => array(
                              '$in' => $in
                          ),
                          "idMailLink" => array(
                              '$in' => $idMailLink
                          )
                      ),
                      "fields" => array(
                          "idMail" => true,
                          "idContact" => true,
                          "idMailLink" => true
                      )
          ));
          foreach ($mxcxl as $value) {
            fwrite($file, $i . " ,");
            fwrite($file, $value->idMail . " ,");
            fwrite($file, $value->idContact . " ,");
            fwrite($file, "0" . " ,");
            fwrite($file, $value->idMailLink);
            fwrite($file, "\r\n");
            $i++;
          }
        }
        $offset += $limit;
        unset($in);
      }
      $tmpopen = fclose($file);
      if (!$tmpopen) {
        throw new Exception("No se ha generado el archivo temporal");
      }
      unset($where);
      $this->db->query("LOAD DATA INFILE '{$route}' IGNORE INTO TABLE tmp_table_target_{$this->idMail} "
              . "FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' ");

      $sql = "SELECT count( DISTINCT idContact ) AS count FROM tmp_table_target_{$this->idMail} {$whereAll}";

      $count = $this->db->fetchAll($sql);
      $c = $count[0]['count'];
      $this->db->commit();
      unset($route);
      return $c;
    }
  }

  public function getAddressees($data) {

    $mail = \Mail::findFirst(["conditions" => "idMail = ?0", "bind" => [0 => $data->idMail]]);

    if (!isset($mail->idMail)) {
      throw new \InvalidArgumentException("No es encuentra el envio verifica la información enviada");
    }
    
    if(count($data->filters) > 0 && $data->condition == 'all'){
        if(!isset($data->filters[0]->typeFilters) || empty($data->filters[0]->typeFilters)){
            throw new \InvalidArgumentException("El filtro se encuentra vacío, no es posible continuar");  
        }
        if(empty($data->filters[0]->mailSelected) || !isset($data->filters[0]->mailSelected)){
            throw new \InvalidArgumentException("No ha escogido una campaña para el filtro, no es posible continuar ");
        }
        
        if($data->filters[0]->typeFilters == 3 && empty($data->filters[0]->linkSelected) ){
            throw new \InvalidArgumentException("No ha escogido un link para el filtro, no es posible continuar ");
        }
    } 
    
    if(count($data->filters) >1){
        throw new \InvalidArgumentException("Sólo puede elegir un filtro para la campaña");
    }
//
//    if ($data->showSegment == true && $data->showContactlist == true) {
//      throw new \InvalidArgumentException("Se debe seleccionar a quien va a ir dirigido el correo");
//    }

    if ($data->showSegment == false) {
//      if (count($data->selectdSegment) == 0) {
//        throw new \InvalidArgumentException("Se debe seleccionar por lo minimo un segmento");
//      }
      $arr = array();
      $array = ["type" => "segment"];
      foreach ($data->selectdSegment as $key) {
        $obj = new \stdClass();
        $obj->idSegment = $key->idSegment;
        $obj->name = $key->name;
        array_push($arr, $obj);
      }
      $array["segment"] = $arr;
    } else if ($data->showContactlist == false) {
//      if (count($data->selectdContactlis) == 0) {
//        throw new \InvalidArgumentException("Se debe seleccionar por lo minimo una lista de contactos");
//      }
      $arr = array();
      $array = ["type" => "contactlist"];
      foreach ($data->selectdContactlis as $key) {
        $obj = new \stdClass();
        $obj->idContactlist = $key->idContactlist;
        $obj->name = $key->name;
        array_push($arr, $obj);
      }
      $array["contactlists"] = $arr;
    }
    if (count($data->filters) >= 1) {
      $mails = $data->filters[0]->mail;
      $mailSelected = $data->filters[0]->mailSelected;
      $data->filters[0]->mail = [];
      foreach ($mails as $value){
        if($value->idMail == $mailSelected){
          $data->filters[0]->mail[0] = $value;
        }
      }
      unset($mails);
      unset($mailSelected);
      $array["filters"] = $data->filters;
      $array["condition"] = $data->condition;
    }
    $mail->target = "";
    $mail->quantitytarget = $data->quantitytarget;
    if ($data->quantitytarget == 0) {
      throw new \Sigmamovil\General\Exceptions\ValidateTargetException("La lista de contactos/segmento se encuentra vacía");
    }
    if (count($array["contactlists"]) > 0 || count($array["segment"]) > 0) {
      $target = json_encode($array);
      $mail->target = $target;
    }
    $mail->typeUnsuscribed = $data->typeUnsuscribed;
    $mail->singleMail = $data->singleMail;
    $mail->alldb = $data->alldb;
    $mail->save();
  }

  public function getContactListBySubaccountAction() {
    $this->contactlists = \Contactlist::find(["conditions" => "idSubaccount = ?0 AND deleted = 0", "bind" => [\Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount]]);
  }

  public function getCustomfield($idContactList) {
    $customfields = \Customfield::find(["conditions" => "idContactlist = ?0", "bind" => [$idContactList]]);
    $arrReturn = array();
    if (count($customfields) > 0) {
      foreach ($customfields as $key => $value) {
        $arrReturn[] = array("name" => $value->name,
            "alternativename" => $value->alternativename,
            "type" => $value->type,
            "defaultValue" => $value->defaultValue,
            "value" => $value->value);
      }
    }

    return $arrReturn;
  }

  function setIdMail($idMail) {
    $this->idMail = $idMail;
  }

  function exportContactsFromOne() {
    $csv = array();
    $this->next = true;
    $this->limit = "8000";
    $this->page = 0;
    $this->init = 0;

    $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $this->contactlist->idContactlist]]);
    $fields = array("name", "lastname", "email", "indicative", "phone");
    foreach ($customfield as $key) {
      array_push($fields, $key->name);
    }
    for ($i = 0; $i < count($fields); $i++) {
      $csv[$this->init][$fields[$i]] = $fields[$i];
    }
    $this->init++;

    while ($this->next) {
      $this->findContact($this->contactlist->idContactlist, "");
      var_dump($this->contact[0]['items']);
      exit;
      foreach ($this->contact[0]['items'] as $contact) {
        for ($i = 0; $i < count($fields); $i++) {
          $csv[$this->init][$fields[$i]] = $contact[$fields[$i]];
        }
        $this->init++;
      }
      $this->page++;
    }
  }

  public function findContact($idContactlist, $stringSearch) {
    $this->cxcl = $this->modelsManager->createBuilder()
            ->from('Cxcl')
            ->join('Contactlist', 'Cxcl.idContactlist = Contactlist.idContactlist')
//            ->where("Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} ")
            ->where("Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist} LIMIT " . $this->limit . " OFFSET {$this->page}")
            ->getQuery()
            ->execute();
    if (count($this->cxcl) == 0) {
      $this->next = false;
    }
    if (count($this->cxcl) == 0 and $this->page == "0") {
      throw new \InvalidArgumentException("Esta lista no tiene contactos para exportar");
    }
    $in = array();
    for ($i = 0; $i < count($this->cxcl); $i++) {
      $in[$i] = (int) $this->cxcl[$i]->idContact;
    };
    $customfield = \Customfield::find(["conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => [0 => $idContactlist]]);
    $fields = array("name", "email", "phone", "lastname");
    foreach ($customfield as $key) {
      array_push($fields, $key->idCustomfield);
    }
    $where = array("idContact" => ['$in' => $in]);
    if ($stringSearch != -1) {
      if ($stringSearch != "") {
        $stringSearch = explode(",", $stringSearch);
        foreach ($fields as $value) {
          foreach ($stringSearch as $key) {
            if ($key or $key != "" or ! empty($key)) {
              $key = trim($key);
              $arr[] = [$value => ['$regex' => ".*$key.*"]];
              $where['$or'] = $arr;
            }
          }
        }
        $this->totals = \Contact::count([$where]);
      } else {
        $this->totals = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist}"]);
      }
    } else {
      $this->totals = \Cxcl::count(["Cxcl.deleted = 0 AND Cxcl.idContactlist  = {$idContactlist}"]);
    }
    unset($arr);
    unset($fields);
    unset($customfield);
    $this->data = \Contact::find(array($where));
    unset($where);
    unset($page);
    $this->modelContact($idContactlist, $in);
  }

  public function modelContact($idContactlist, $in) {
    $this->contact = array("total" => $this->totals, "total_pages" => ceil($this->totals / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));

    $arr = array();
    foreach ($this->data as $key => $val) {
      $cxc = \Cxc::findFirst([["idContact" => $val->idContact]]);
      $thiscontact = \Cxcl::findFirst(array("conditions" => "idContact = ?0  AND deleted = 0 AND idContactlist = ?1",
                  "bind" => array(0 => $val->idContact, 1 => $idContactlist)));
      $this->data[$key]->unsubscribed = $thiscontact->unsubscribed;
      unset($val->_id);
      unset($val->idSubaccount);
      $val = json_encode($val);
      $val = json_decode($val, true);
      $val["status"] = $thiscontact->status;

      if (isset($cxc->idContactlist[$idContactlist])) {
        foreach ($cxc->idContactlist[$idContactlist] as $p => $v) {
          if ($v != null) {
            $customfield = $v["value"];
            $val[$v["name"]] = $customfield;
          }
        }
      }

//      $sql = "SELECT contactlist.idContactlist, contactlist.name FROM cxcl JOIN  contactlist ON cxcl.idContactlist = contactlist.idContactlist"
//              . " WHERE cxcl.deleted = 0 AND cxcl.idContact  = {$val['idContact']} "
//              . " AND contactlist.idSubaccount = " . \Phalcon\DI::getDefault()->get("user")->Usertype->idSubaccount;
//      $val["contactlist"] = $this->db->fetchAll($sql);
//
      array_push($arr, $val);
    }
    array_push($this->contact, array("items" => $arr));
    unset($arr);
  }

  public function setContactlistInfo($idContactlist) {

    $contactlist = \Contactlist::findFirst(array("conditions" => "idContactlist = ?0", "bind" => array(0 => $idContactlist)));
    $contaclistCategory = \ContactlistCategory::findFirst(array(
                "conditions" => "idContactlistCategory = ?0",
                "bind" => array(0 => $contactlist->idContactlistCategory)
    ));
    $contactlist->category = ($contaclistCategory->name) ? $contaclistCategory->name : "Sin categoría";

    // proceso para total de contactos
    $cxcl = \Cxcl::find(array("conditions" => "idContactlist = ?0 and deleted=0", "bind" => array(0 => $idContactlist)));
    $in = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $in[$i] = (int) $cxcl[$i]->idContact;
    }
    $where = array("idContact" => ['$in' => $in], "deleted" => 0);
    $contaTotal = \Contact::count(array($where));

    unset($in);
    unset($cxcl);
    unset($where);

    // proceso para total de contactos activos
    $cxcl = \Cxcl::find(array("conditions" => "idContactlist = ?0 and deleted=0 and status='active'", "bind" => array(0 => $idContactlist)));
    $in = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $in[$i] = (int) $cxcl[$i]->idContact;
    }
    $where = array("idContact" => ['$in' => $in], "deleted" => 0);
    $contaActive = \Contact::count(array($where));

    unset($in);
    unset($cxcl);
    unset($where);

    // proceso para total desuscritos
    $cxcl = \Cxcl::find(array("conditions" => "idContactlist = ?0 and deleted=0 and status='unsubscribed'", "bind" => array(0 => $idContactlist)));
    $in = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $in[$i] = (int) $cxcl[$i]->idContact;
    }
    $where = array("idContact" => ['$in' => $in], "deleted" => 0);
    $contaUnsubscribed = \Contact::count(array($where));

    unset($in);
    unset($cxcl);
    unset($where);
    
    // proceso para total spam
    $cxcl = \Cxcl::find(array("conditions" => "idContactlist = ?0 and deleted=0 and status='spam'", "bind" => array(0 => $idContactlist)));
    $in = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $in[$i] = (int) $cxcl[$i]->idContact;
    }
    $where = array("idContact" => ['$in' => $in], "deleted" => 0);
    $contaSpam = \Contact::count(array($where));
    
    unset($in);
    unset($cxcl);
    unset($where);
    
    // proceso para total bounced
    $cxcl = \Cxcl::find(array("conditions" => "idContactlist = ?0 and deleted=0 and status='bounced'", "bind" => array(0 => $idContactlist)));
    $in = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $in[$i] = (int) $cxcl[$i]->idContact;
    }
    $where = array("idContact" => ['$in' => $in], "deleted" => 0);
    $contaBounced = \Contact::count(array($where));
    
    unset($in);
    unset($cxcl);
    unset($where);
    
    // proceso para total blocked
    $cxcl = \Cxcl::find(array("conditions" => "idContactlist = ?0 and deleted=0 and status='blocked'", "bind" => array(0 => $idContactlist)));
    $in = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $in[$i] = (int) $cxcl[$i]->idContact;
    }
    $where = array("idContact" => ['$in' => $in], "deleted" => 0);
    $contaBlocked = \Contact::count(array($where));


    $contactlist->ctotal = $contaTotal;
    $contactlist->cactive = $contaActive;
    $contactlist->cunsubscribed = $contaUnsubscribed;
    $contactlist->cspam = $contaSpam;
    $contactlist->cbounced = $contaBounced;
    $contactlist->cblocked = $contaBlocked;


    if (!$contactlist->save()) {
      foreach ($contactlist->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $this->contactlist = $contactlist;
  }

  public function getContactlistInfo() {
    return $this->contactlist;
  }
  
  public function getOnly($data) {
    $arrayEmail = [];
    $arrayidContact = [];
    $onlyCount = 0;
    $arrayIdContactList = [];
    foreach ($data->selectdContactlis as $value) {
      $arrayIdContactList[] = (int) $value->idContactlist;
    }
    $query = "SELECT REPLACE(GROUP_CONCAT(DISTINCT ct.name),',',', ') as Name"
            ." FROM contactlist as cl"
            ." LEFT JOIN contactlist_category as ct"
            ." on cl.idContactlistCategory = ct.idContactlistCategory"
            ." WHERE cl.idContactlist IN (".implode(',',$arrayIdContactList).")"
            ." AND cl.deleted = 0";
         
    $result = $this->db->fetchAll($query);
    $categories = $result[0]['Name'];
    $contactList = (object) $this->getOneContactlist($arrayIdContactList, $data->singleMail, $data->filters);
    $onlyCount = $contactList->count;
    //
    /*if ($data->singleMail == true) {
      $uniquerarray = array_unique($contactList->arrayEmail);
      $onlyCount = count($uniquerarray);
    }*/
    unset($data);
    unset($arrayEmail);
    return array("count" => $onlyCount,"categories" =>$categories);
  }
  
  public function getAttachmentPdf($idMail){
    $mailController = new \MailController();
    
    $subaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount;
    $mail = \Mail::findFirst(array(
      'conditions' => 'idSubaccount = ?0 AND idMail = ?1 AND pdf = 1',
      'bind' => [0 => $subaccount->idSubaccount,  1 => $idMail]
    ));

    $contacts = $mailController->findContacts($mail);
    $totalContacts = count($contacts);
    // 
    
    $sql = "SELECT DISTINCT idContact FROM pdfmail WHERE"
          . " idMail = {$idMail} "
          . " AND status = 1 ";
    $totalpdfmail = $this->db->fetchAll($sql);
    $dir = "{$this->asset->dir}{$subaccount->idAccount}/pdf/{$idMail}/";
    //
    $arrayFiles = glob($dir . "{*.pdf}", GLOB_BRACE);
    $arrayFilesNames = array();
    // hago una iteracion sobre el arreglo de ubicaciones de archivos
    if(count($arrayFiles)>0){
      foreach ($arrayFiles as $file) {
        $path_parts = pathinfo($file);
        $basename = $path_parts['basename']; 
        //solamente saco el basename
        $findFirst  = \Pdfmail::findFirst(array(
          'conditions' => 'idMail = ?0 AND name = ?1 AND status = 1',
          'bind' => array(0 => $idMail, 1 => $basename)
        ));
        $arrayFilesNames[] = ['id' => $findFirst->idPdfmail, 'name' => $basename];  // y luego el nombre sin el ".pdf"
      }
    }
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    //$ArrayFileNames = $mailController->getFileNamesInFolder($dir, $idMail);

    $totalNumberOfFiles = count(glob($dir . "{*.pdf}",GLOB_BRACE));
    //

    $result = array();
    if($totalpdfmail != false){
      $result['files'] = $arrayFilesNames;  
      $result['total'] = $totalNumberOfFiles;
          $result['totalfiles'] = $totalNumberOfFiles;
          $result['totalfilematch'] = count($totalpdfmail);
          $result['totalcontacts'] = $totalContacts;
          $result['totalcontactsmatch'] = count($totalpdfmail); // momentaneo
      $result['uploadstatus'] = "success"; //esto se coloca para que pueda mostrar notificacion de error

      $result['enunciadoFinal'] = "Se han cargado los PDF's exitosamente, para continuar con el proceso haga clic en continuar";
    } else {
      $result['files'] = [];  
    }
    
    return $result;
  }
  
  public function getOneContactlist($idContactlist, $singleMail, $filters){
    $arrayidContact = array();
    $arrayEmail = array();
    $countMxc = 0;
    $idContactlist = implode(",", $idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
      . " WHERE idContactlist IN ({$idContactlist})"
      . " AND unsubscribed = 0 "
      . " AND deleted = 0 "
      . " AND spam = 0 "
      . " AND bounced = 0 "
      . " AND blocked = 0 "
      . " AND singlePhone = 0";
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    unset($sql);
    //
    $in = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $in[$i] = (int) $cxcl[$i]['idContact'];
    }
    //
    $where = array(
      "conditions" => array(
        "idContact" => ['$in' => $in],
        "deleted" => 0,  
        "email" => array(
          '$nin' => ["", null, "null"]
        )  
      ),
      "fields" => array(
        "idContact" => true,
        "email" => true,
      )
    );
    $contact = \Contact::find($where);     
    foreach ($contact as $c) {
      array_push($arrayidContact, (int) $c->idContact);
      if($singleMail){
        if(!in_array($arrayEmail, $c->email)){
          $arrayEmail[] = $c->email;
        }
      } else {
        array_push($arrayEmail, $c->email);
      }
    }
    $countMxc = count($arrayEmail);  
    unset($contact);
    //
    if(count($filters)){
      $idMail = $filters[0]->mailSelected;
      $idMailLink = $filters[0]->linkSelected;
      $type = $filters[0]->typeFilters;
      $inverted = $filters[0]->inverted;
      unset($filters);
      //
      switch ($type) {
        case 1:
          if (isset($inverted) && $inverted == true) {
            $mxc = \Mxc::find(array(
              "conditions" => array(
                "idMail" => $idMail,
                "unsubscribed" => 0,
                "spam" => 0,
                "bounced" => 0,
                "email" => array(
                  '$nin' => ["", null, "null"]
                )  
              ),
              "fields" => array(
                "idContact" => true
              )
            ));
            $arrayidContactMxc = array();
            foreach ($mxc as $key => $value) {
              $arrayidContactMxc[] = (int) $value->idContact;
              unset($mxc[$key]);
            }
            unset($mxc);
            $countMxc = count(array_diff($arrayidContact, $arrayidContactMxc));
            unset($arrayidContactMxc);
          } else {
            $countMxc = \Mxc::count(array(
              "conditions" => array(
                "idMail" => $idMail,
                "idContact" => array(
                  '$in' => $arrayidContact
                ),   
                "unsubscribed" => 0,
                "spam" => 0,
                "bounced" => 0,
                "email" => array(
                  '$nin' => ["", null, "null"]
                )  
              )
            ));
          }
          break;
        case 2:
          if (isset($inverted) && $inverted == true) {
            $countMxc = \Mxc::count(array(
              "conditions" => array(
                "idMail" => $idMail,
                "idContact" => array(
                  '$in' => $arrayidContact
                ),
                "open" => "0",
                "unsubscribed" => 0,
                "spam" => 0,
                "bounced" => 0,
                "email" => array(
                  '$nin' => ["", null, "null"]
                )
              )
            ));
          } else {
            $countMxc = \Mxc::count(array(
              "conditions" => array(
                "idMail" => $idMail,
                "idContact" => array(
                  '$in' => $arrayidContact
                ),
                "open" => array(
                  '$gte' => 1
                ),  
                "unsubscribed" => 0,
                "spam" => 0,
                "bounced" => 0,
              )
            ));
          }
          break;
        case 3:  
          if (isset($inverted) && $inverted == true) {
            $mxcxl = \Mxcxl::find(array(
              "conditions" => array(
                "idMail" => $idMail,
                "idMailLink" => $idMailLink
              ),
              "fields" => array(
                "idContact" => true
              )
            ));
            $arrayidContactMxc = array();
            foreach ($mxcxl as $key => $value) {
              $arrayidContactMxc[] = (int) $value->idContact;
              unset($mxcxl[$key]);
            }
            $countMxc = count(array_diff($arrayidContact, $arrayidContactMxc));
            unset($arrayidContactMxc);
          } else {
            $countMxc = \Mxcxl::count(array(
              "conditions" => array(
                "idMail" => $idMail,
                "idMailLink" => $idMailLink,
                "idContact" => array(
                  '$in' => $arrayidContact
                ),  
              )
            ));
          }
          break;
      }
    }
    return [
      "count" => $countMxc, 
      "arrayidContact" => $arrayidContact, 
      "arrayEmail" => $arrayEmail
    ];
  }
}