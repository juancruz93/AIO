<?php

class ContactController extends ControllerBase {

  public $saxs;

  public function initialize() {
    $this->tag->setTitle("Contactos");
    parent::initialize();
  }

  public function indexAction($idContactlist) {
    if (!$idContactlist) {
      $this->notification->error("La lista de contactos que intenta acceder no existe");
      return $this->response->redirect('contactlist/show');
    }
    $contactlist = Contactlist::findFirst(array(
                "conditions" => "idContactlist = ?0",
                "bind" => array(0 => $idContactlist)
    ));
    
    $contactlist->ctotal = \Cxcl::count(array("conditions" => "idContactlist = ?1 AND deleted = 0", "bind" => array(1 => $contactlist->idContactlist)));
    $contactlist->cactive = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "active", 1 => $contactlist->idContactlist)));
    $contactlist->cunsubscribed = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "unsubscribed", 1 => $contactlist->idContactlist)));
    $contactlist->cbounced = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "bounced", 1 => $contactlist->idContactlist)));
    $contactlist->cblocked = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "blocked", 1 => $contactlist->idContactlist)));
    $contactlist->cspam = \Cxcl::count(array("conditions" => "status = ?0 and idContactlist = ?1 AND deleted = 0", "bind" => array(0 => "spam", 1 => $contactlist->idContactlist)));

    $this->view->setVar("contactlist", $contactlist);
    $this->view->setVar("app_name", "contact");
  }

  public function listAction() {
    
  }

  public function importAction($idContactlist) {
    $this->view->setVar("idContactlist", $idContactlist);
  }

  public function importcontactsAction($idContactlist) {
    try {
      $contactlist = Contactlist::findFirst(array(
                  "conditions" => "idContactlist = ?0",
                  "bind" => array(0 => $idContactlist)
      ));

      $this->db->begin();
      if (!$contactlist) {
        throw new InvalidArgumentException("No se encontró la lista de contactos.");
      }

      foreach ($contactlist->Subaccount->Saxs as $value) {
        if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $value->accountingMode == "contact") {
          foreach ($contactlist->Subaccount->Account->AccountConfig->DetailConfig as $item) {
            if ($item->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $item->accountingMode == "contact") {
              $this->saxs = $item;
            }
          }
        }
      }

      /*if (isset($this->saxs) && $this->saxs->amount == 0) {
        throw new InvalidArgumentException("No cuenta con la capacidad suficiente para importar más contactos, por favor contacte a su administrador.");
      }*/

      $sql = "SELECT * FROM customfield WHERE deleted = 0 and idContactlist = " . $idContactlist;

      $customfield = $this->db->fetchAll($sql);
      $jsonCustomfield = json_encode($customfield);

      if ($this->request->isPost()) {

        $ext = explode(".", $_FILES['filecsv']['name']);
        if ($ext[1] != "csv") {
          throw new InvalidArgumentException("El archivo seleccionado no es valido, seleccione un archivo con la extensión .csv.");
        }
        if ($_FILES['filecsv']['size'] > 10485760) {
          throw new InvalidArgumentException("El archivo CSV excede el tamaño las 10 megabytes aceptadas");
        }
        $internalNumber = uniqid();
        $date = date("ymdHi", time());
        $internalName = "{$idContactlist}_{$date}_{$internalNumber}.csv";
        $nameFile = $_FILES['filecsv']['name'];

        $importfile = new Importfile();
        $importfile->idContactlist = $idContactlist;
        $importfile->idUser = $this->user->idUser;
        $importfile->internalname = $internalName;
        $importfile->originalname = $nameFile;



        if (!$importfile->save()) {
          foreach ($importfile->getMessages() as $msg) {
            $this->logger->log($msg);
          }
          return $this->response->redirect("contact/index/{$idContactlist}#/import");
        } else {
          $destiny = $this->tmpPath->dir . $internalName;
          //copy($_FILES['filecsv']['tmp_name'], $destiny);

          $fileManager = new \Sigmamovil\General\Misc\FileManager();
          $temparray = $fileManager->viewcsv($_FILES['filecsv']['tmp_name'], $destiny);

          if ($temparray['rows'] > 140000) {
            throw new InvalidArgumentException("El numero de registros permitidas para importar es 140000, el archivo cargado supera este limite, por favor valide la información");
          } else if (count($temparray['arraytemp']) == 0) {
            throw new InvalidArgumentException("El archivo CSV cargado es invalido, por favor valide la información");
          } else if ($this->saxs->amount < $temparray['countContact'] && $this->saxs->accountingMode == "contact"){
            throw new InvalidArgumentException("El archivo tiene {$temparray['countContact']} contactos y su saldo disponible es de {$this->saxs->amount} contactos, no es posible realizar la importación."); 
            //throw new InvalidArgumentException("Cuenta con {$this->saxs->amount} contactos disponibles a importar y el archivo cargado contiene {$temparray['rows']} contactos y {$temparray['countContact']} contactos validos, algunos contactos no podrán ser importados.");
          }  

          $contactlist->created = date('d/m/Y H:ia', $contactlist->created);
          $contactlist->updated = date('d/m/Y H:ia', $contactlist->updated);

          $this->db->commit();

          $this->view->setVar('idImportfile', $importfile->idImportfile);
          $this->view->setVar('arrayCsvRows', $temparray['rows']);
          $this->view->setVar('arrayCsvRowsEmail', $temparray['countContact']);          
          $this->view->setVar('arrayCsv', $temparray['arraytemp']);
          $this->view->setVar('customfield', $jsonCustomfield);
          $this->view->setVar('contactlist', $contactlist);
        }
      }
    } catch (InvalidArgumentException $ex) {
      $this->db->rollback();
      $this->notification->error($ex->getMessage());
      return $this->response->redirect("contact/index/{$idContactlist}#/import");
    } catch (Exception $ex) {
      $this->db->rollback();
      $this->logger->log("Exception while finding contact... {$ex}");
      return $this->notification->error('Ha ocurrido un error, contacte al administrador');
    }
  }

  public function processfileAction($idContactlist, $idImportfile, $totalRows) {
    $importfile = Importfile::findFirst(array(
                "conditions" => "idImportfile = ?0",
                "bind" => array(0 => $idImportfile)
    ));


    if (!$importfile) {
      throw new InvalidArgumentException("No se encontró el archivo a importar.");
    }

    if ($this->request->isPost()) {
      try {

        $namefile = $importfile->internalname;

        $header = $this->request->getPost('header');
        $delimiter = $this->request->getPost('delimiter');
        $dateformat = $this->request->getPost('dateformat');
        $importmode = $this->request->getPost('importmode');
        $update = $this->request->getPost('update');
        $importrepeated = $this->request->getPost('importrepeated');
        $fields['email'] = $this->request->getPost('email');
        $fields['name'] = $this->request->getPost('name');
        $fields['lastname'] = $this->request->getPost('lastname');
        $birthdate = $this->request->getPost('birthdate');
        $fields['birthdate'] = (empty($birthdate) ? 'd/m/Y' : $birthdate);
        $fields['indicative'] = $this->request->getPost('indicative');
        $fields['phone'] = $this->request->getPost('phone');
        $fields['deleted'] = (int) 0;
        $customfield = Customfield::find(array(
                    "conditions" => "idContactlist = ?0",
                    "bind" => array(0 => $idContactlist)
        ));
         $importRepeatedFile = 0;
        if ($this->request->getPost('importrepeatedCsv') != null) {
          $importRepeatedFile = 1;
        }
    
        foreach ($customfield as $field) {
          $namefield = "campo" . $field->idCustomfield;
          $fields[$field->idCustomfield] = $this->request->getPost($namefield);
        }

        $destiny = $this->tmpPath->dir . $namefile;
        $idSubaccount = $this->user->Usertype->idSubaccount;


        if ($header == "on") {
          $totalRows = $totalRows - 1;
        }


        $importcontactfile = new Importcontactfile();
        $importcontactfile->idSubaccount = $idSubaccount;
        $importcontactfile->idImportfile = $idImportfile;
        $importcontactfile->rows = $totalRows;
        $importcontactfile->status = "Pending";
        $importcontactfile->header = ($header == "on" ? 1 : 0);
        $importcontactfile->delimiter = $delimiter;
        $importcontactfile->dateformat = $dateformat;
        $importcontactfile->importmode = $importmode;
        $importcontactfile->update = ($update == "on" ? 1 : 0);
        $importcontactfile->importrepeated = ($importrepeated == "on" ? 1 : 0);
        $importcontactfile->fieldsmap = json_encode($fields);
        $importcontactfile->importRepeatedFile = $importRepeatedFile;

        if (!$importcontactfile->save()) {
          throw new InvalidArgumentException("No se encontró el archivo a importar.");
        }
        $idImportcontactfile = $importcontactfile->idImportcontactfile;

        $import = array(
            "idImportcontactfile" => $idImportcontactfile,
            "rows" => $importcontactfile->rows,
            "processed" => ""
        );
        $elephant = new \ElephantIO\Client(new ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
        $elephant->initialize();
        $elephant->emit('id-importcontactfile', ['import' => $import]);
        $elephant->close();

        return $this->response->redirect("process/importdetail/{$idImportcontactfile}");
      } catch (InvalidArgumentException $ex) {
        $this->notification->error($ex->getMessage());
        return $this->response->redirect("contact/index/{$idContactlist}#/import");
      } catch (Exception $ex) {
        $this->logger->log("Exception while finding contact... {$ex}");
        $this->notification->error('Ha ocurrido un error, contacte al administrador');
        return $this->response->redirect("contact/index/{$idContactlist}#/import");
      }
    }
  }

  public function newbatchAction() {
    $indicative = Indicative::find();
    $this->view->setVar('indicative', $indicative);
  }

  public function historyAction($idContactlist, $idContact) {
    $this->view->setVar("idContactlist", $idContactlist);
    $this->view->setVar("idContact", $idContact);
    $this->view->setVar("app_name", "contact");
  }

  public function newcontactAction($idContactlist) {
    $customfield = Customfield::find(array("conditions" => "idContactlist = ?0 AND deleted = 0", "bind" => array(0 => $idContactlist)));
    $indicative = Indicative::find();
    $this->view->setVar('indicative', $indicative);
    $this->view->setVar('customfield', $customfield);
  }

  public function contactsegmentAction($idSegment) {
    $segment = Segment::findFirst([["idSegment" => (int) $idSegment]]);
    $this->view->setVar("segment", $segment);
    $this->view->setVar("app_name", "contact");
  }

  /**
   * @Post("/validatetotalcontacts")
   */
  public function validatetotalcontactsAction() {
    try {
      $contentsraw = $this->getRequestContent();
      $data = json_decode($contentsraw);

      $idContactlist = $data->idContactlist;
      $type = $data->typeExport;

      switch ($type) {
        case 0:
          $whereTypeExport = "";
          break;
        case 1:
          $whereTypeExport = "AND status = 'active'";
          break;
        case 2:
          $whereTypeExport = "AND status = 'unsubscribed'";
          break;
        case 3:
          $whereTypeExport = "AND status = 'bounced'";
          break;
        case 4:
          $whereTypeExport = "AND status = 'spam'";
          break;
        case 5:
          $whereTypeExport = "AND status = 'blocked'";
          break;                
      }

      $totalContacts = \Cxcl::count([
        "conditions" => "idContactlist = ?0 ".$whereTypeExport, 
        "bind" => array($idContactlist)
      ]);

      return $this->set_json_response(array('totalContacts'=>$totalContacts), 200);
    } catch (InvalidArgumentException $ex) {
      $this->logger->log("Exception while finding sms per contact... {$ex}");
      return $this->set_json_response(array('message' => $ex->getMessage()), 400);
    } catch (Exception $ex) {
      $this->logger->log("Exception while finding sms per contact... {$ex}");
      return $this->set_json_response(array('message' => 'Ha ocurrido un error, contacte al administrador'), 500, 'Ha ocurrido un error');
    }
  }

  public function exportAction($idContactlist, $typeExport) {
    
    $contactlist = Contactlist::findFirst(array(
                "conditions" => "idContactlist = ?0",
                "bind" => array(0 => $idContactlist)
    ));
    if ($contactlist != Null) {
      $wrapper = new \Sigmamovil\Wrapper\ContactWrapper();
      $wrapper->setTypeExport($typeExport);
      return $wrapper->findExport($idContactlist);
    } else {
      return $this->response->redirect("contactlist/show#/");
    }
  }
  
  public function downloadlcAction($idExportlcdetail) {
    $exportlcdetail = \Exportlcdetail::findFirst(array(
      "conditions" => "idExportlcdetail = ?0",
      "bind" => array(0 => $idExportlcdetail)
    ));
    
    $publicRoute = substr($exportlcdetail->route,21);
    
    if(!file_exists($publicRoute)){ // file does not exist
        die('Archivo no encontrado.');
    } else {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$exportlcdetail->fileName");//NOMBRE QUE TENDRA EL ARCHIVO DESCARGADO
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");

        //EJECUTA LA DESCARGA DEL ARCHIVO
        readfile($publicRoute);
        
    }
  }

}
