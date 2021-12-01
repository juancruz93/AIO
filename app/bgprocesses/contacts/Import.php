  <?php

  /**
   * Created by PhpStorm.
   * User: juan.dorado
   * Date: 14/07/2016
   * Time: 09:41
   */
  ini_set("auto_detect_line_endings", true);
  ini_set('memory_limit', '768M');
  require_once(__DIR__ . "/../bootstrap/index.php");
  $id = 0;
  if (isset($argv[1])) {
    $id = $argv[1];
  }

  $import = new Import();

  $import->importStart($id);

  class Import {

    public function __construct() {
      $this->db = \Phalcon\DI::getDefault()->get('db');
    }

    public $emailbuffers = array();
    public $countIdEmail;
    public $countIdPhone;
    public $arrayTmp;
    public $newArrayEmail;
    public $newArrayPhone;
    public $emailInsert;
    public $domainInsert;
    public $phoneInsert;
    public $tmpInsert;
    public $bulkEmail;
    public $bulkPhone;
    public $bulkDomain;
    public $bulkTmpCont;
    public $bulkTmpId;
    public $contactEmail;
    public $contactPhone;
    public $contactId;
    public $arrayPhones = array();
    public $arrayEmails = array();
    public $arrayContacts = array();
    public $arrayTmpDomain = array();
    public $saxs;
    public $importcontactfile;
    public $db;
    public $createdBy;
    public $contactUpdate;
    public $importrepeated;
    public $arrayDataBlockedEmail = array();
    public $arrayDataBlockedPhone = array();
    public $arrayDataBounced = array();

    public function importStart($id) {
      
      try {
        
        $importcontactfile = \Importcontactfile::findFirst(array(
                    "conditions" => "idImportcontactfile = ?0",
                    "bind" => array(0 => $id)
        ));
        $this->contactUpdate = $importcontactfile->update;
        $this->createdBy = $importcontactfile->createdBy;
        $this->importrepeated = $importcontactfile->importrepeated;
        $this->importMode = $importcontactfile->importmode;
        if (!$importcontactfile) {
          throw new InvalidArgumentException("No se encontró el proceso a importar.");
        }
        $this->importcontactfile = $importcontactfile;

        if ($importcontactfile->status != "pending") {
          throw new InvalidArgumentException("Este proceso no se encuentra programado.");
        }
        $importfile = Importfile::findFirst(array(
                    "conditions" => "idImportfile = ?0",
                    "bind" => array(0 => $importcontactfile->idImportfile)
        ));
        if (!$importfile) {
          throw new InvalidArgumentException("El archivo a importar no existe.");
        }
        $customfield = Customfield::find(array(
                    "conditions" => "idContactlist = ?0",
                    "bind" => array(0 => $importfile->idContactlist)
        ));
        $fieldsmap = json_decode($importcontactfile->fieldsmap, true);
        foreach ($importcontactfile->Subaccount->Saxs as $value) {
          if ($value->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing && $value->accountingMode == "contact") {
            foreach ($importcontactfile->Subaccount->Account->AccountConfig->DetailConfig as $item) {
              if ($item->idServices == \Phalcon\DI::getDefault()->get('services')->email_marketing) {
                $this->saxs = $item;
              }
            }
          }
        }
        /**
         * Instacia para ejecutar los servicios de MongoDB
         */
        $manager = \Phalcon\DI::getDefault()->get('mongomanager');
        /**
         * Crea las tablas temporales en MongoDB
         */
        $createCollectionTmp = new MongoDB\Driver\Command(['eval' => "createcollection('tmp{$id}')"]);
        $cursor = $manager->executeCommand('aio', $createCollectionTmp);

        $createCollectionTmpId = new MongoDB\Driver\Command(['eval' => "createcollection('tmpid{$id}')"]);
        $manager->executeCommand('aio', $createCollectionTmpId);
        /**
         * Ruta donde se encuentra almacenado el csv que sube el usuario
         */
        $route = __DIR__ . "/../../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . $importfile->internalname;
        /**
         * Variables que se traen de la base de datos para pasarlas como parametros a las diferentes funciones a lo
         * largo del script */
        $emailUser = $importfile->User->email;
        $idAccount = $importfile->User->Usertype->Subaccount->idAccount;
        $idContactlist = $importfile->idContactlist;
        $delimiter = $importcontactfile->delimiter;
        $dateFormat = $importcontactfile->dateformat;
        $importRepeatedFile = $importcontactfile->importRepeatedFile;

        $this->createTmpDomain($manager, $idAccount);
        /**
         * Si las colecciones temporales en mongo son creadas se continua con el proceso
         */
        if ($cursor->toArray()[0]->retval->ok) {
          unset($cursor);
          /**
           * Funciones para consultar todos los registros blocked y bounced
           */
          if($this->importMode == "blocked" || $this->importMode == "respectiveState"){
            $this->findBlocked($idAccount);
          }
          if($this->importMode == "bounced" || $this->importMode == "respectiveState"){
            $this->findBounced($idAccount);
          }
          /**
           * Funcion que limpia el archivo csv de contactos repetidos e invalidos y los guarda en la tabla temporal
           */
          $csv = $this->csvCleaner($route, $delimiter, $fieldsmap, $dateFormat, $customfield, $importcontactfile, $emailUser, $idAccount, $manager, $id, $idContactlist, $importRepeatedFile);
          if ($importcontactfile->header) {
            unset($csv['arrayDisabled'][0]);
          }

          $this->bulkEmail = new MongoDB\Driver\BulkWrite;
          $this->bulkDomain = new MongoDB\Driver\BulkWrite;
          $this->bulkPhone = new MongoDB\Driver\BulkWrite;
          $this->bulkTmpCont = new MongoDB\Driver\BulkWrite;
          $this->bulkTmpId = new MongoDB\Driver\BulkWrite;
          $this->bulkContact = new MongoDB\Driver\BulkWrite;
          $date = time();

          /**
           * Funcion que consulta las colecciones en mongo y crea arrays temporales de los emails, telefonos
           */
          $rows = $importcontactfile->rows;

          $this->createArraysTmp($manager, $idAccount, $rows);
          $importcontactfile->status = "processing";
          $importcontactfile->update();

          /**
           * Funcion que se encarga de pasar los contactos de la coleccion temporal a la de contactos en mongo
           */
          $this->bulkData($manager, $idAccount, $emailUser, $date, $idContactlist, $id);

          /**
           * Atualiza los autoincrementales en mogo
           */
          $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);

          /**
           * Funcion que se encarga de guardar lo procesado anteriormente en mongo
           */
          $this->executeBulk($manager, $writeConcern, $id);
          $this->clearVarTmp();
  //        $this->contactUpdate = 1;
          
          $importstart = new MongoDB\Driver\Command(array('eval' => "importstart({$id}, {$idContactlist},{$this->contactUpdate})", 'nolock' => true));

          $cursorimport = $manager->executeCommand('aio', $importstart);
          if ($cursorimport->toArray()[0]->retval) {
            unset($cursorimport);
            $importcontactfile->status = "saving";
            $importcontactfile->update();
            $status = $importcontactfile->importmode;
            $this->generateCsvCxcl($manager, $id, $status, $idAccount);

            $routecsv = \Phalcon\DI::getDefault()->get('path')->path . "tmp/cxcl/cxcl{$id}.csv";
//            $routecsv = "C:/Users/juan.pinzon/Documents/NetBeansProjects/aio/tmp/cxcl/cxcl{$id}.csv";
  //          exit;   
            $condition = "";

            if ($importcontactfile->update == 1) {
              $condition = "REPLACE ";
            } else {
              $condition = "IGNORE";
            }
            $res = \Phalcon\DI::getDefault()->get('db')->query("LOAD DATA INFILE '{$routecsv}' {$condition} INTO TABLE cxcl FIELDS
             TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' (@value1,@value2,@value3,@value4,
             @value5,@value6,@value7,@value8,@value9,@value10,@value11,@value12,@value13,@value14) set idContactlist=@value1, idContact=@value2,
              created=@value3, updated=@value4, createdBy=@value5, updatedBy=@value6, unsubscribed=@value7, deleted=@value8, singlePhone=@value9, status=@value10,
               spam=@value11, bounced=@value12, blocked=@value13, active=@value14;");

            
            $queryRepeat = new \MongoDB\Driver\Query(["contactlist.idContactlist" => "" . $idContactlist . ""]);
            $segment = $manager->executeQuery("aio.segment", $queryRepeat)->toArray();
            if($segment){
                \Phalcon\DI::getDefault()->get('logger')->log("++++++++++++++ENTRO A addContactSegment++++++++++++++");
                $this->addContactSegment($manager, $id, $idContactlist);
                \Phalcon\DI::getDefault()->get('logger')->log("++++++++++++++salgo A addContactSegment++++++++++++++");
            }
            unset($segment);
            
            $dropCollectionTmp = new MongoDB\Driver\Command(['eval' => "dropCollectionTmp({$id})"]);
            $cursorTmp = $manager->executeCommand('aio', $dropCollectionTmp);
            $arrTmp = $cursorTmp->toArray();
            unset($cursorTmp);
            
            $importcontactfile->status = "finished";
            $importcontactfile->imported = $arrTmp[0]->retval[0]->totalTmp;
            $importcontactfile->exists = $arrTmp[0]->retval[1]->totalTmpExist;
            $importcontactfile->repeated = count($csv['arrayRepeat']);
            $importcontactfile->invalids = count($csv['arrayDisabled']) + count($csv["arrayErrors"]);
            $importcontactfile->update();

            //unset($arrTmp);
            $arr = array_merge($csv['arrayRepeat'], $csv['arrayDisabled'], $csv['arrayErrors']);

            $this->generateCsvErrors($arr, $id);
            unset($arr);

            $sql = "CALL updateCounters({$idContactlist})";
            \Phalcon\DI::getDefault()->get('db')->execute($sql);

            if (isset($this->saxs)) {
              $sql = "CALL updateCountersAccount({$idAccount})";
              \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
              //$this->saxs->amount = $this->saxs->amount - ($importcontactfile->imported - $importcontactfile->exists);
              //$this->saxs->save();
            }
            $this->db->begin();
            $contactlist = \Contactlist::findFirst(array(
                        "conditions" => "idContactlist = ?0",
                        "bind" => array(0 => $idContactlist)
            ));

            $contactlist->updated = time();
            $contactlist->updatedBy = $this->createdBy;

            if (!$contactlist->update()) {
              foreach ($contactlist->getMessages() as $message) {
                $this->db->rollback();
                throw new \InvalidArgumentException($message);
              }
            }
            $this->db->commit();
            return true;
          }

          \Phalcon\DI::getDefault()->get('logger')->log("Fallo la importacion de contactos");
          return false;
        } else {
          throw new InvalidArgumentException("Ha ocurrido un error, por favor contacte al administrador.");
        }
      } catch (InvalidArgumentException $ex) {
        $this->importcontactfile->status = "canceled";
        $this->importcontactfile->update();
        $manager = \Phalcon\DI::getDefault()->get('mongomanager');
        $dropCollectionTmp = new MongoDB\Driver\Command(['eval' => "dropCollectionTmp({$id})"]);
        $manager->executeCommand('aio', $dropCollectionTmp);
        \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
        \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
      } catch (\Exception $ex) {
        $this->importcontactfile->status = "canceled";
        $this->importcontactfile->update();
        $manager = \Phalcon\DI::getDefault()->get('mongomanager');
        $dropCollectionTmp = new MongoDB\Driver\Command(['eval' => "dropCollectionTmp({$id})"]);
        $manager->executeCommand('aio', $dropCollectionTmp);
        \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
        \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
      }
    }

    public function executeBulk($manager, $writeConcern, $id) {
      if ($this->emailInsert) {
        $manager->executeBulkWrite('aio.email', $this->bulkEmail, $writeConcern);
      }
      if ($this->domainInsert) {
        $manager->executeBulkWrite('aio.domain', $this->bulkDomain);
      }
      if ($this->phoneInsert) {
        $manager->executeBulkWrite('aio.phone', $this->bulkPhone, $writeConcern);
      }
      if ($this->tmpInsert) {
        $manager->executeBulkWrite("aio.tmp{$id}", $this->bulkTmpCont, $writeConcern);
        $manager->executeBulkWrite("aio.tmpid{$id}", $this->bulkTmpId, $writeConcern);
      }
    }

    public function limitArrayTmp($manager, $id, $count) {
      $options = array(
          'limit' => 20000,
          'skip' => 0 + $count
      );
      $queryTmp = new MongoDB\Driver\Query(array(), $options);
      $this->arrayTmp = $manager->executeQuery("aio.tmp{$id}", $queryTmp)->toArray();
      if (count($this->arrayTmp) > 0) {
        return true;
      } else {
        return false;
      }
    }

    public function bulkData($manager, $idAccount, $emailUser, $date, $idContactlist, $id) {
      $route = __DIR__ . "/../../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../success/success{$id}.csv";
      $file = fopen($route, "w");
      $count = 0;
      while ($this->limitArrayTmp($manager, $id, $count)) {
        foreach ($this->arrayTmp as $values) {
          //if que validan si hay registros nuevos para crear en mail y phone
          if (isset($this->newArrayEmail[$values->email]) && !empty($values->email)) {
            $domain = $this->extractDomainEmail($values->email);
            $idDomain = null;
            if (isset($this->arrayTmpDomain[$domain])) {
              $idDomain = $this->arrayTmpDomain[$domain];
            }
            if ($idDomain == null) {
              $idDomain = $this->createDomain($domain, $idAccount);
              $this->arrayTmpDomain[$domain] = $idDomain;
              $this->domainInsert = true;
              $arrDomain = ["idDomain" => $idDomain, "idAccount" => $idAccount, "domain" => $domain, "deleted" => 0, "status" => 1];
              $this->bulkDomain->insert($arrDomain);
  //            $this->createTmpDomain($manager, $idAccount);
            }
            $array = array('idAccount' => $idAccount, 'idDomain' => $idDomain, 'idEmail' => $this->countIdEmail, 'email' => $values->email,
                'created' => $date, 'updated' => $date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser);
            $this->bulkEmail->insert($array);

            if ($count % 20000 == 0) {
              if ($this->domainInsert) {
                $manager->executeBulkWrite('aio.domain', $this->bulkDomain);
              }
              $manager->executeBulkWrite('aio.email', $this->bulkEmail);
              unset($this->bulkDomain);
              unset($this->bulkEmail);
              $this->bulkEmail = new MongoDB\Driver\BulkWrite;
              $this->bulkDomain = new MongoDB\Driver\BulkWrite;
  //                $this->createTmpDomain($manager, $idAccount);
            }
            $this->emailInsert = true;
            $this->countIdEmail++;
            unset($array);
          }
          if (isset($this->newArrayPhone[$values->phone]) && !empty($values->phone)) {
            $array = array('idAccount' => $idAccount, 'idPhone' => $this->countIdPhone, 'phone' => $values->phone,
                'created' => $date, 'updated' => $date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser);
            $this->bulkPhone->insert($array);
            if ($count % 20000 == 0) {
              $manager->executeBulkWrite('aio.phone', $this->bulkPhone);
              unset($this->bulkPhone);
              $this->bulkPhone = new MongoDB\Driver\BulkWrite;
            }
            $this->phoneInsert = true;
            $this->countIdPhone++;
            unset($array);
          }
          /* $queryEmailContact = new MongoDB\Driver\Query(array('email' => $values->email, 'phone' => $values->phone));
            $contactExists = $manager->executeQuery("aio.contact", $queryEmailContact)->toArray(); */
          if ($this->importrepeated != 1) {
            if (!empty($values->email) && !empty($values->phone)) {
              $singlePhone = 0;
              $queryEmailContact = new MongoDB\Driver\Query(array('email' => $values->email,'phone' => $values->phone, 'idAccount' => $idAccount, 'deleted' => 0));
            }  else if (!empty($values->email) && empty($values->phone)) {
              $singlePhone = 0;
              $queryEmailContact = new MongoDB\Driver\Query(array('email' => $values->email,'phone' => "", 'idAccount' => $idAccount, 'deleted' => 0));
            }  else if (empty($values->email) && !empty($values->phone)) {
              $singlePhone = time();
              $queryEmailContact = new MongoDB\Driver\Query(array('email' => "",'phone' => $values->phone, 'idAccount' => $idAccount, 'deleted' => 0));
            }
            $contactExists = $manager->executeQuery("aio.contact", $queryEmailContact)->toArray();
            unset($queryEmailContact);
            $arr = array();
            if ($contactExists != false) {
              $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
              foreach ($contactExists as $value) {
                $arr[] = (int)$value->idContact;
              }

              $unsubscribed = 0;
              $spam = 0;
              $bounced = 0;
              $blocked = 0;
              $active = 0;
              $stringIdcontact = implode(",", $arr);
              $sql = "SELECT DISTINCT(idContact), idCxcl FROM cxcl INNER JOIN contactlist ON contactlist.idContactlist = cxcl.idContactlist INNER JOIN subaccount ON subaccount.idSubaccount = contactlist.idSubaccount WHERE subaccount.idAccount = {$idAccount} AND cxcl.idContactlist = {$idContactlist} AND cxcl.idContact IN ({$stringIdcontact}) AND cxcl.deleted = 0";
              $cxcl = $this->db->fetchAll($sql);
              //
              $contactUpdate = $this->contactUpdate;
              if($cxcl != false) {
                $idContact = (int)$cxcl[0]['idContact'];
                $idCxcl = (int)$cxcl[0]['idCxcl'];
                unset($cxcl);
                /*$valueTemp = \Cxcl::findFirst(array( 
                  "conditions" => "idContactlist = ?0 AND idContact = ?1 AND deleted = 0",
                  "columns" => "idCxcl",
                  "bind" => array(0 => $idContactlist,  1 => $idContact)
                ));*/
                switch ($this->importMode) {
                  case 'active' :
                    $active = (int) time();
                    $sql = "UPDATE cxcl SET unsubscribed = 0, spam = 0, bounced = 0, blocked = 0, active={$active}, `status` = 'active', singlePhone = {$singlePhone} WHERE idCxcl = {$idCxcl};";
                    unset($idCxcl);
                    break;
                  case 'unsubscribed' :
                    $unsubscribed = (int) time();
                    $sql = "UPDATE cxcl SET unsubscribed = {$unsubscribed}, spam = 0, bounced = 0, blocked = 0, active = 0, `status` = 'unsubscribed', singlePhone = {$singlePhone} WHERE idCxcl = {$idCxcl};";
                    unset($idCxcl);
                    break;
                  case 'spam' :
                    $spam = (int) time();
                    $sql = "UPDATE cxcl SET unsubscribed = 0, spam = {$spam}, bounced = 0, blocked = 0,  `status` = 'spam', singlePhone = {$singlePhone} WHERE idCxcl = {$idCxcl};";
                    unset($idCxcl);
                    break;
                  case 'bounced' :
                    $bounced = (int) time();
                    $sql = "UPDATE cxcl SET unsubscribed = 0, spam = 0, bounced = {$bounced}, blocked = 0,  `status` = 'bounced', singlePhone = {$singlePhone} WHERE idCxcl = {$idCxcl};";
                    unset($idCxcl);
                    break;
                  case 'blocked' :
                    $blocked = (int) time();
                    $sql = "UPDATE cxcl SET unsubscribed = 0, spam = 0, bounced = 0, blocked = {$blocked},  `status` = 'blocked', singlePhone = {$singlePhone} WHERE idCxcl = {$idCxcl};";
                    unset($idCxcl);
                    break;
                  case 'respectiveState':
                    $time = (int) time();
                    $sql = "UPDATE cxcl SET updated = {$time} WHERE idCxcl = {$idCxcl};";
                    if($values->email != ""){
                      if(in_array($value->email, $this->arrayDataBlockedEmail)){
                        $sql = "UPDATE cxcl SET unsubscribed = 0, spam = 0, bounced = 0, blocked = {$time},  `status` = 'blocked', singlePhone = {$singlePhone} WHERE idCxcl = {$idCxcl};";
                      } else {
                        if(in_array($value->email, $this->arrayDataBounced)){
                          $sql = "UPDATE cxcl SET unsubscribed = 0, spam = 0, bounced = {$time}, blocked = 0,  `status` = 'bounced', singlePhone = {$singlePhone} WHERE idCxcl = {$idCxcl};";
                        }
                      }
                    }
                    if($values->phone != ""){
                      if(in_array($value->phone, $this->arrayDataBlockedPhone)){
                        $sql = "UPDATE cxcl SET unsubscribed = 0, spam = 0, bounced = 0, blocked = {$time},  `status` = 'blocked', singlePhone = {$singlePhone} WHERE idCxcl = {$idCxcl};";
                      }
                    }
                    unset($idCxcl);
                    break;
                }
                $this->db->query($sql);
                unset($sql);
                $this->bulkTmpCont->update(array('_id' => $values->_id), array('$set' => ['idContact' => $idContact, 'deleted' => 0]));  
                $array = array('idContactlist' => $idContactlist, 'idContact' => "", 'created' => $date,'updated' => $date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser, 'unsubscribed' => 0, 'deleted' => 0, 'singlePhone' => $singlePhone, 'email' => $values->email, 'phone' => $values->phone, 'indicative' => $values->indicative);
                $this->bulkTmpId->insert($array); 
                unset($array);
                unset($idContact);
                $contactUpdate = 0;
                $this->tmpInsert = true;
              } 
              if ($contactUpdate == 1) {
                $idContact = (int)$arr[0];
                unset($arr);
                $this->bulkTmpCont->update(array('_id' => $values->_id), array('$set' => ['idContact' => $idContact, 'deleted' => 0]));  
                $array = array('idContactlist' => $idContactlist, 'idContact' => $idContact, 'created' => $date, 'updated' =>$date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser, 'unsubscribed' => 0, 'deleted' => 0, 'singlePhone' => $singlePhone, 'email' => $values->email, 'phone' => $values->phone, 'indicative' => $values->indicative);
                $this->bulkTmpId->insert($array);
                unset($idContact);
                unset($writeConcern);
                $this->tmpInsert = true;
                $this->contactUpdate = 1;
              }
            } else {
              if($this->importMode == 'respectiveState'){
                $active = (int) time();
                $array = array('idContactlist' => $idContactlist, 'idContact' => "", 'created' => $date,'updated' => $date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser, 'unsubscribed' => 0, 'deleted' => 0, 'singlePhone' => $singlePhone, 'email' => $values->email, 'phone' => $values->phone, 'indicative' => $values->indicative);
                $this->bulkTmpId->insert($array);
              }
               
            }
            unset($contactExists);
          }
          $count++;
          
          unset($values->updatedBy);
          unset($values->createdBy);
          unset($values->phoneexists);
          unset($values->emailexists);
          unset($values->idSubaccount);
          unset($values->idAccount);
          unset($values->idContact);
          unset($values->idContactlist);
          unset($values->_id);

          $arr = json_encode($values);
          $arr = json_decode($arr, true);
          fputcsv($file, $arr);
          unset($arr);
        }
        unset($this->arrayTmp);
      }

      fclose($file);
    }

    public function clearVarTmp() {
      unset($this->emailInsert);
      unset($this->phoneInsert);
      unset($this->tmpInsert);
      unset($this->bulkEmail);
      unset($this->bulkDomain);
      unset($this->bulkPhone);
      unset($this->bulkTmpCont);
      unset($this->countIdEmail);
      unset($this->countIdPhone);
    }

    public function createArraysTmp($manager, $idAccount, $rows) {
      $bulkAutoIncrement = new MongoDB\Driver\BulkWrite;

      $queryIdEmail = new MongoDB\Driver\Query(array('field_id' => 'id_email'));
      $nextIdEmail = $manager->executeQuery("aio.autoincrementcollection", $queryIdEmail)->toArray();
      $this->countIdEmail = $nextIdEmail[0]->nextId;
      unset($nextIdEmail);
      $totalMail = $this->countIdEmail + $rows + 2;
      $bulkAutoIncrement->update(array('field_id' => 'id_email'), array('$set' => array('nextId' => $totalMail)));

      $queryIdPhone = new MongoDB\Driver\Query(array('field_id' => 'id_phone'));
      $nextIdPhone = $manager->executeQuery("aio.autoincrementcollection", $queryIdPhone)->toArray();
      $this->countIdPhone = $nextIdPhone[0]->nextId;
      unset($nextIdPhone);
      $totalPhone = $this->countIdPhone + $rows + 2;
      $bulkAutoIncrement->update(array('field_id' => 'id_phone'), array('$set' => array('nextId' => $totalPhone)));

      $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
      $manager->executeBulkWrite('aio.autoincrementcollection', $bulkAutoIncrement, $writeConcern);
      unset($bulkAutoIncrement);

      $optionsEmail = array(
          'projection' => array('_id' => 0, 'email' => 1),
      );
      $queryIn = array('email' => ['$in' => $this->arrayEmails], 'idAccount' => $idAccount);

      $queryEmail = new MongoDB\Driver\Query($queryIn, $optionsEmail);
      $arrayEmail = $manager->executeQuery("aio.email", $queryEmail)->toArray();
      unset($optionsEmail);
      unset($queryIn);
      unset($queryEmail);

      $emailsDiff = array_diff($this->arrayEmails, $this->fixArrayEmail($arrayEmail));

      unset($arrayEmail);
      $this->newArrayEmail = array_fill_keys($emailsDiff, true);
      unset($emailsDiff);

      $optionsPhone = array(
          'projection' => array('_id' => 0, 'phone' => 1),
      );
      $queryIn = array('phone' => ['$in' => $this->arrayPhones]);

      $queryPhone = new MongoDB\Driver\Query($queryIn, $optionsPhone);
      $arrayPhone = $manager->executeQuery("aio.phone", $queryPhone)->toArray();
      unset($optionsPhone);
      unset($queryIn);
      unset($queryPhone);

      $phonesDiff = array_diff($this->arrayPhones, $this->fixArrayPhone($arrayPhone));
      unset($arrayPhone);

      $this->newArrayPhone = array_fill_keys($phonesDiff, true);
      unset($phonesDiff);

      $optionsContact = array(
          'projection' => array('_id' => 0, 'idContact' => 1, 'email' => 1, 'phone' => 1),
      );

  //    $queryIn = array('$or' => array(array('email' => ['$in' => $this->arrayEmails]), array('phone' => ['$in' => $this->arrayPhones])), 'idAccount' => $idAccount);
      $queryIn = array('email' => ['$in' => $this->arrayEmails], 'phone' => ['$in' => $this->arrayPhones], 'idAccount' => $idAccount);
      unset($this->arrayPhones);
      unset($this->arrayEmails);
      $queryContact = new MongoDB\Driver\Query($queryIn, $optionsContact);

      $resultContact = $manager->executeQuery("aio.contact", $queryContact)->toArray();

      unset($optionsContact);
      unset($queryIn);
      unset($queryContact);

      $this->fixArrayContact($resultContact);
      unset($resultContact);

      $this->emailInsert = false;
      $this->domainInsert = false;
      $this->phoneInsert = false;
      $this->tmpInsert = false;
    }

    public function createArrayInsert($bulk, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist) {
      $tmpcontacts = array();
      $obj = array();
      $tmpcontacts['idContact'] = false;
      $tmpcontacts['idAccount'] = $idAccount;
      $tmpcontacts['idSubaccount'] = (int) $idSubaccount;
      $tmpcontacts['email'] = trim(strtolower($record[$fieldsmap['email']]));
      $this->arrayEmails[] = trim(strtolower($record[$fieldsmap['email']]));
      $tmpcontacts['name'] = trim($record[$fieldsmap['name']]);
      $tmpcontacts['lastname'] = trim($record[$fieldsmap['lastname']]);
      $tmpcontacts['birthdate'] = trim($record[$fieldsmap['birthdate']]);
      $tmpcontacts['indicative'] = trim($record[$fieldsmap['indicative']]);
      $tmpcontacts['phone'] = trim($record[$fieldsmap['phone']]);
      $tmpcontacts['deleted'] = 0;
      if($tmpcontacts['phone'] != ""){
        $tmpcontacts['phone'] = str_replace(" ", "", $tmpcontacts['phone']);
      }
      $this->arrayPhones[] = trim($record[$fieldsmap['phone']]);
      foreach ($customfield as $field) {
        $num = $field->idCustomfield;
        if (isset($record[$fieldsmap[$field->idCustomfield]])) {
          $valor = trim($record[$fieldsmap[$field->idCustomfield]]);
          $obj[$num] = ['name' => $field->name, 'value' => $valor, 'type' => $field->type];
        } else {
          $obj[$num] = ['name' => $field->name, 'value' => "", 'type' => $field->type];
        }
      }
      $tmpcontacts['idContactlist'] = [$idContactlist => $obj];
      $tmpcontacts['emailexists'] = false;
      $tmpcontacts['phoneexists'] = false;
      $tmpcontacts['createdBy'] = $createBy;
      $tmpcontacts['updatedBy'] = $createBy;
      //-----

          $status = $importcontactfile->importmode;
    
      $tmpcontacts['active'] = 0;
      $tmpcontacts['unsubscribed'] = 0;
      $tmpcontacts['spam'] = 0;
      $tmpcontacts['bounced'] = 0;
      $tmpcontacts['blocked'] = 0;
      $tmpcontacts['blockedEmail'] = "";
      $tmpcontacts['blockedPhone'] = "";

      if ($status == 'active') {
        $tmpcontacts['active'] = time();
      } else if ($status == 'unsubscribed'){
        $tmpcontacts['unsubscribed'] = time();
      } else if ($status == 'spam'){
        $tmpcontacts['spam'] = time();
      } else if ($status == 'bounced'){
        $bounce = new \Bouncedmail();
        $contactManager = new Sigmamovil\General\Misc\ContactManager();
        $nextIdAnswer = $contactManager->autoIncrementCollection("id_bouncedmail");

        $bounce->idBouncedMail = $nextIdAnswer;
        $bounce->idAccount = [(int)$idAccount];
        $date = new \DateTime();
        $bounce->email = $tmpcontacts['email'];
        $bounce->datetime = $date->format('Y-m-d H:i:s');
        $bounce->description = "Se registra el contacto por petición del cliente por el método de Importación de Contactos";
        $bounce->type = "bounced_all";
        $bounce->code = "10";
        if (!$bounce->save()) {
          foreach ($bounce->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        $tmpcontacts['bounced'] = time();
      } else if($status == 'blocked'){
        $tmpcontacts['blocked'] = time();
        if($tmpcontacts['email'] != ""){
          $tmpcontacts['blockedEmail'] = (int) time();
        }
        if($tmpcontacts['phone'] != ""){          
          $tmpcontacts['blockedPhone'] = (int) time();
        }
      } else if($status == 'respectiveState'){
        if($tmpcontacts['email'] != ""){
          if(in_array($tmpcontacts['email'], $this->arrayDataBlockedEmail)){
            $tmpcontacts['blocked'] = (int) time();
            $tmpcontacts['blockedEmail'] = (int) time();
          } else {
            if(in_array($tmpcontacts['email'], $this->arrayDataBounced)){
              $tmpcontacts['bounced'] = (int) time();
            } else {
              $tmpcontacts['active'] = (int) time();
            }
          }
        }
        if($tmpcontacts['phone'] != ""){          
          if(in_array($tmpcontacts['phone'], $this->arrayDataBlockedPhone)){
            $tmpcontacts['blocked'] = (int) time();
            $tmpcontacts['blockedPhone'] = (int) time();
          }
        }
      }
      //-----

      $bulk->insert($tmpcontacts);
      if ($count % 1000 == 0) {
        $importcontactfile->processed = $count;
        $importcontactfile->update();
      }
      unset($tmpcontacts);
    }

    public function csvCleaner($route, $delimiter, $fieldsmap, $dateFormat, $customfield, $importcontactfile, $createBy, $idAccount, $manager, $id, $idContactlist, $importRepeatedFile) {
      $handle = fopen($route, "r");
      $arrayDisabled = array();
      $arrayRepeat = array();
      $arrayErrors = array();
      $count = 0;
      $idSubaccount = $importcontactfile->idSubaccount;
      $importcontactfile->status = "preprocessing";
      $importcontactfile->update();
      $bulkTmpCont = new MongoDB\Driver\BulkWrite;
      $country = \Country::find(array("conditions" => "phoneCode <> ' ' and maxDigits > 0"));
      $arrayCountry = array();
      foreach ($country as $value) {
        $arrayCountry[$value->phoneCode] = array(
            "minDigits" => $value->minDigits,
            "maxDigits" => $value->maxDigits
        );
      }
      $amount = isset($this->saxs) ? $this->saxs->amount : null;
      if (!$handle) {
        $this->changeStatusToCanceled($importcontactfile);
        \Phalcon\DI::getDefault()->get('logger')->log("Error al abrir el archivo original");
        throw new \InvalidArgumentException('Error al procesar el archivo. Contacte a su administrador!');
      }
      $emails = array();
      //while ((($record = fgets($handle)) !== false)) {
      while ((($record = fgetcsv($handle, 0, $delimiter)) !== false)) {
        // $record = explode(";", $record);
        if (filter_var($record[$fieldsmap["email"]], FILTER_VALIDATE_EMAIL)) {
          $emails[] = $record[$fieldsmap["email"]];
        }
        if (isset($amount) && ($count + 1) > $amount && count($emails) > $amount) {
          break;
        }
        if (!isset($fieldsmap['email']) && !isset($fieldsmap['phone'])) {
          $this->changeStatusToCanceled($importcontactfile);
          //$dropCollectionTmp = new MongoDB\Driver\Command(['eval' => "dropCollectionTmp({$id})"]);
          //$manager->executeCommand('aio', $dropCollectionTmp);
          throw new InvalidArgumentException("Error al procesar el archivo csv.");
        }
        if (isset($fieldsmap["email"]) && isset($fieldsmap["indicative"]) && isset($fieldsmap["phone"])) {
          if (filter_var($record[$fieldsmap["email"]], FILTER_VALIDATE_EMAIL)) {
            if (!empty($record[$fieldsmap["phone"]]) && is_numeric($record[$fieldsmap["phone"]])) {
              $record[$fieldsmap['phone']] = str_replace(" ", "", $record[$fieldsmap['phone']]);
              if (strlen($record[$fieldsmap["phone"]]) != $arrayCountry[$record[$fieldsmap["indicative"]]]["minDigits"]) {
                if (isset($record[$fieldsmap["indicative"]])) {
                  $record[$fieldsmap["indicative"]] = "";
                }
                if (isset($record[$fieldsmap["phone"]])) {
                  $record[$fieldsmap["phone"]] = "";
                }
              }
            } else {
              if (isset($record[$fieldsmap["indicative"]])) {
                $record[$fieldsmap["indicative"]] = "";
              }
              if (isset($record[$fieldsmap["phone"]])) {
                $record[$fieldsmap["phone"]] = "";
              }
            }
            if (!empty($record[$fieldsmap['birthdate']]) && !empty($dateFormat)) {
              $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
              if ($date != false) {
                $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
              }
            } else {
              $record[$fieldsmap['birthdate']] = "";
            }
            if (!$this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']]) && ($importRepeatedFile == 0)) {

              $record['causa'] = "El contacto se encuentra repetido en el archivo.";
              $arrayRepeat[] = $record;
              continue;
            }
            $count++;
            $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
          } else {
            if (!empty($record[$fieldsmap["phone"]]) && is_numeric($record[$fieldsmap["phone"]])) {
              $record[$fieldsmap['phone']] = str_replace(" ", "", $record[$fieldsmap['phone']]);
              if (strlen($record[$fieldsmap["phone"]]) != $arrayCountry[$record[$fieldsmap["indicative"]]]["minDigits"]) {
                $record['causa'] = "El indicativo o el numero celular no cumple con las condiciones establecidas.";
                $arrayErrors[] = $record;
              } else {
                if (!empty($record[$fieldsmap["email"]])) {
                  $record[$fieldsmap["email"]] = "";
                }
                if (!empty($record[$fieldsmap['birthdate']]) && !empty($dateFormat)) {
                  $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
                  if ($date != false) {
                    $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
                  }
                } else {
                  $record[$fieldsmap['birthdate']] = "";
                }
                if (!$this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']]) && ($importRepeatedFile == 0)) {
                  $record['causa'] = "El contacto se encuentra repetido en el archivo.";
                  $arrayRepeat[] = $record;
                  continue;
                }
                $count++;
                $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
              }
            } else {
              $record['causa'] = "El indicativo o el numero celular no cumple con las condiciones establecidas.";
              $arrayErrors[] = $record;
            }
          }
        }
        if (isset($fieldsmap["email"]) && (!isset($fieldsmap["indicative"]) || !isset($fieldsmap["phone"]))) {
          if (filter_var($record[$fieldsmap["email"]], FILTER_VALIDATE_EMAIL)) {
            if (!empty($record[$fieldsmap["phone"]]) || is_numeric($record[$fieldsmap["phone"]])) {
              $record[$fieldsmap['phone']] = str_replace(" ", "", $record[$fieldsmap['phone']]);
              if (strlen($record[$fieldsmap["phone"]]) != $arrayCountry[$record[$fieldsmap["indicative"]]]["minDigits"]) {
                if (isset($record[$fieldsmap["indicative"]])) {
                  $record[$fieldsmap["indicative"]] = "";
                }
                if (isset($record[$fieldsmap["phone"]])) {
                  $record[$fieldsmap["phone"]] = "";
                }
              }
            }
            if (!empty($record[$fieldsmap['birthdate']]) && !empty($dateFormat)) {
              $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
              if ($date != false) {
                $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
              }
            } else {
              $record[$fieldsmap['birthdate']] = "";
            }
            if (!$this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']]) && ($importRepeatedFile == 0)) {
              $record['causa'] = "El contacto se encuentra repetido en el archivo.";
              $arrayRepeat[] = $record;
              continue;
            }
            $count++;
            $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
          } else {
            $record['causa'] = "El email no es valido.";
            $arrayErrors[] = $record;
          }
        }
        if (!isset($fieldsmap["email"]) && isset($fieldsmap["indicative"]) && isset($fieldsmap["phone"])) {
          if (!empty($record[$fieldsmap["phone"]])) {
            $record[$fieldsmap['phone']] = str_replace(" ", "", $record[$fieldsmap['phone']]);
            if (strlen($record[$fieldsmap["phone"]]) == $arrayCountry[$record[$fieldsmap["indicative"]]]["minDigits"]) {
              if (!empty($record[$fieldsmap['birthdate']]) && !empty($dateFormat)) {
                $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
                if ($date != false) {
                  $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
                }
              } else {
                $record[$fieldsmap['birthdate']] = "";
              }
              if (!$this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']]) && ($importRepeatedFile == 0)) {
                $record['causa'] = "El contacto se encuentra repetido en el archivo.";
                $arrayRepeat[] = $record;
                continue;
              }
              $count++;
              $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
            } else {
              $record['causa'] = "Telefono o indicativo no validos";
              $arrayErrors[] = $record;
            }
          } else {
            $record['causa'] = "Telefono o indicativo no validos";
            $arrayErrors[] = $record;
          }
        }
      }
      fclose($handle);
      if ($count % $count == 0) {
        $importcontactfile->processed = $count;
        $importcontactfile->update();
      }
      $manager->executeBulkWrite('aio.tmp' . $id, $bulkTmpCont);
      unset($this->emailbuffers);
      unset($bulkTmpCont);
      $arrayResults = array(
          //"arraySuccess" => $arraySuccess,
          "arrayRepeat" => $arrayRepeat,
          "arrayDisabled" => $arrayDisabled,
          "arrayErrors" => $arrayErrors
      );
      return $arrayResults;
    }

    public function repeatedCheck($email, $phone) {
      $email = strtolower(trim($email));
      $phone = strtolower(trim($phone));

      if (empty($phone) && !empty($email)) {
        if (empty($this->emailbuffers) || !isset($this->emailbuffers[$email])) {
          $this->emailbuffers[$email] = true;
          return true;
        } else {
          return false;
        }
      } 
      else if (empty($email) && !empty($phone)) {

        if (empty($this->emailbuffers) || !isset($this->emailbuffers[$phone])) {
          $this->emailbuffers[$phone] = true;
          return true;
        } else {
          //El email y el telefono estan repetidos en el archivo.
          return false;
        }
      } 
      else if (!empty($email) && !empty($phone)) {
        if (empty($this->emailbuffers) || !isset($this->emailbuffers[$email][$phone])) {
          $this->emailbuffers[$email][$phone] = true;
          return true;
        } else {
          //El email y el telefono estan repetidos en el archivo.
          return false;
        }
      }
    }

    public function download($array) {
      $route = __DIR__ . "/../../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../repeated/prueba.csv";
      $file = fopen($route, "w");
      foreach ($array as $value) {
        for ($i = 0; $i < count($value); $i++) {
          fwrite($file, $value[$i] . (($i == count($value) - 1) ? "" : ","));
        }
        fwrite($file, "\r\n");
      }
      fclose($file);
    }

    public function generateCsvCxcl($manager, $id, $status, $idAccount) {

      $query = new MongoDB\Driver\Query([]);
      $cursortmpid = $manager->executeQuery("aio.tmpid{$id}", $query);
      $cxclmongo = $cursortmpid->toArray();
      $idContact = "";
      $unsubscribed = 0;
      $spam = 0;
      $bounced = 0;
      $blocked = 0;
      $active = 0;
      switch ($status) {
        case 'active' :
          $active = (int) time();
          break;
        case 'unsubscribed' :
          $unsubscribed = (int) time();
          break;
        case 'spam' :
          $spam = (int) time();
          break;
        case 'bounced' :
          $bounced = (int) time();
          break;
        case 'blocked' :
          $blocked = (int) time();
          break;
      }

      $route = __DIR__ . "/../../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../cxcl/cxcl{$id}.csv";
      $file = fopen($route, "w");
      foreach ($cxclmongo as $key => $value) { 
        if($status == 'blocked'){
          $this->blockedOneContact($value, $idAccount, $value->createdBy);
        }
        if ($value->idContact != $idContact) {
          fwrite($file, (int) $value->idContactlist . ",");
          fwrite($file, (int) $value->idContact . ",");
          fwrite($file, (int) $value->updated . ",");
          fwrite($file, (int) $value->updated . ",");
          fwrite($file, $value->createdBy . ",");
          fwrite($file, $value->updatedBy . ",");
          fwrite($file, $unsubscribed . ",");
          fwrite($file, (int) $value->deleted . ",");
          fwrite($file, $value->singlePhone . ",");
          if($status == 'respectiveState'){
            $active = (int) time();
            $status1 = 'active';
            $bounced = 0;
            $blocked = 0;
            if($value->email != ""){
              if(in_array($value->email, $this->arrayDataBlockedEmail)){
                $active = 0;
                $bounced = 0;
                $blocked = (int) time();
                $status1 = 'blocked';
              } else {
                if(in_array($value->email, $this->arrayDataBounced)){
                  $active = 0;
                  $bounced = (int) time();
                  $blocked = 0;
                  $status1 = 'bounced';
                }
              }              
            }
            if($value->phone != ""){
              if(in_array($value->phone, $this->arrayDataBlockedPhone)){
                $active = 0;
                $bounced = 0;
                $blocked = (int) time();
                $status1 = 'blocked';
              }
            }
            fwrite($file, $status1 . ",");
          } else {
            fwrite($file, $status . ",");
          }
          fwrite($file, $spam . ",");
          fwrite($file, $bounced . ",");
          fwrite($file, $blocked . ",");
          fwrite($file, $active);
          fwrite($file, "\r\n");
          $idContact = $value->idContact;
        }
      }
      fclose($file);
      unset($cxclmongo);
    }

    public function addContactSegment($manager, $id, $idContactlist){
        
        $query = new MongoDB\Driver\Query([]);
        $cursortmpid = $manager->executeQuery("aio.tmpid{$id}", $query);
        $cxclmongotmpid = $cursortmpid->toArray(); 
        
        $query2 = new MongoDB\Driver\Query([]);
        $cursortmpid2 = $manager->executeQuery("aio.tmp{$id}", $query2);
        $cxclmongotmp = $cursortmpid2->toArray();        
        
        $idContacts = array ();
        
        foreach ($cxclmongotmpid as $key => $value) { 
            if ($value->idContact != "") {
                $idContacts[] = (int) $value->idContact;
            }   
        }
        
        foreach ($cxclmongotmp as $key => $value) { 
            if ($value->idContact != false) {
                $idContacts[] = (int) $value->idContact;
            }   
        }
        unset($cxclmongotmpid);
        unset($cxclmongotmpid);
        
        foreach($idContacts as $value){
            $segmentmanager = new Sigmamovil\General\Misc\SegmentManager();
            $segmentmanager->addOneContact($value, $idContactlist);
        }                  
    }
    
    public function generateCsvErrors($array, $id) {

      $route = __DIR__ . "/../../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../errors/errors{$id}.csv";
      $file = fopen($route, "w");

      foreach ($array as $value) {
        fputcsv($file, $value);
      }
      fclose($file);
    }

    public function generateCsvSuccess($array, $id) {
      $route = __DIR__ . "/../../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../success/success{$id}.csv";
      $file = fopen($route, "w");

      foreach ($array as $value) {
        unset($value["updatedBy"]);
        unset($value["createdBy"]);
        unset($value["phoneexists"]);
        unset($value["emailexists"]);
        unset($value["idSubaccount"]);
        unset($value["idAccount"]);
        unset($value["idContact"]);
        unset($value["idContactlist"]);
        unset($value["_id"]);
        fputcsv($file, $value);
      }
      fclose($file);
    }

    public function fixArrayEmail($array) {
      $arrayFix = array();
      foreach ($array as $value) {
        $arrayFix[] = $value->email;
      }
      return $arrayFix;
    }

    public function fixArrayPhone($array) {
      $arrayFix = array();
      foreach ($array as $value) {
        $arrayFix[] = $value->phone;
      }
      return $arrayFix;
    }

    public function fixArrayContact($array) {

      foreach ($array as $value) {
        if ((isset($value->phone) && isset($value->email)) && (!empty($value->phone) && !empty($value->email))) {
          $this->arrayContacts[$value->email][$value->phone][] = $value->idContact;
        }
      }
    }

    public function changeStatusToCanceled($importcontactfile) {
      $importcontactfile->status = "canceled";
      $importcontactfile->update();
    }

    public function fixArrayDomain($array) {
      $arrayFix = array();
      foreach ($array as $value) {
        $arrayFix[$value->domain] = $value->idDomain;
      }
      return $arrayFix;
    }

    public function createTmpDomain($manager, $idAccount) {
      $optionsDomain = array(
          'projection' => array('_id' => 0, 'domain' => 1, 'idDomain' => 1),
      );
      $queryIn = array('idAccount' => $idAccount);
      $queryDomain = new MongoDB\Driver\Query($queryIn, $optionsDomain);
      $arrayDomain = $manager->executeQuery("aio.domain", $queryDomain)->toArray();
      unset($queryIn);
      unset($queryDomain);
      $this->arrayTmpDomain = $this->fixArrayDomain($arrayDomain);
      unset($arrayDomain);
    }

    public function extractDomainEmail($email) {
      $arroba = strpos($email, "@");
      $domainEmail = substr($email, $arroba + 1, 50);
      return $domainEmail;
    }

    public function createDomain($domainEmail, $idAccount) {
      $contactManger = new \Sigmamovil\General\Misc\ContactManager();
      $nextIdContact = $contactManger->autoIncrementCollection("id_domain");
  //    $domain = new \Domain();
  //    $domain->idDomain = $nextIdContact;
  //    $domain->idAccount = $idAccount;
  //    $domain->domain = $domainEmail;
  //    $domain->deleted = 0;
  //    $domain->status = 1;
  //    if (!$domain->save()) {
  //      throw new \InvalidArgumentException("No se ha podido crear el dominio");
  //    }
      return $nextIdContact;
  //    return $domain->idDomain;
    }
    
  public function findBlocked($idAccount){
    $blocked = Blocked::find([array(
      "idAccount" => (int) $idAccount,
      "deleted" => 0
    )]);
    if($blocked != false){
      foreach ($blocked as $value){
        if($value->email != ""){
          if(!in_array($value->email, $this->arrayDataBlockedEmail)){
            $this->arrayDataBlockedEmail[] = (string) $value->email;
          }
        }
        if($value->phone != ""){
          if(!in_array($value->phone, $this->arrayDataBlockedPhone)){
            $this->arrayDataBlockedPhone[] = (string) $value->phone;
          }
        }
      }
    }
    unset($blocked);
  }
  
  public function findBounced($idAccount){
    $bounced = Bouncedmail::find([array(
      "idAccount" => ['$in' => [(int) $idAccount]],
      "deleted" => 0,
      "code" => ['$in' => ["10", "90", "200"]]
    )]);
    if($bounced != false){
      foreach ($bounced as $value){
        if(!in_array($value->email, $this->arrayDataBounced)){
          $this->arrayDataBounced[] = (string) $value->email;
        }
      }
    }
    unset($bounced);
    return $this->arrayDataBounced;
  }
  
  public function blockedOneContact($value, $idAccount, $createBy){
    $contactmanager = new \Sigmamovil\General\Misc\ContactManager();
    $email      = (string) $value->email != "" ? $value->email : "";
    $phone      = (string) $value->phone != "" ? $value->phone : "";
    $indicative = (string) $value->indicative != "" ? $value->indicative : "";
    //INICIO Para indicar el email bloqueado en coleccion Blocked (exista o no)
    $blocked = \Blocked::findFirst([["idAccount" => (int) $idAccount, "email" => $email, "phone" => $phone, "indicative" => $indicative, "deleted" => 0]]);
    if ($blocked == false) {
      $nextIdBlocked = $contactmanager->autoIncrementCollection("id_blocked");
      $blocked = new \Blocked();
      $blocked->idBlocked = $nextIdBlocked;
      $blocked->email = $email;
      $blocked->phone = $phone;
      $blocked->indicative = $indicative;
      $blocked->idAccount = (int) $idAccount;
      $blocked->idContacts[] = (int) $value->idContact;
      $blocked->motive = "bloqueado por importación de archivo csv";
      $blocked->blocked = time();
      if (!$blocked->save()) {
        throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
      }
      //Se llama el idBlocked del registro anterior para actualizar el createdBy y updatedBy
      $updateblocked = \Blocked::findFirst([["idBlocked" => $blocked->idBlocked]]);
      $updateblocked->createdBy = $createBy;
      $updateblocked->updatedBy = $createBy;
      if (!$updateblocked->save()) {
        foreach ($updateblocked->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    } else {
      //INICIO Para indicar el email bloqueado en coleccion Blocked (exista o no)
      if(!in_array($value->idContact, $blocked->idContacts)){
        $blocked->idContacts[] = (int) $value->idContact;
      }
      $blocked->motive = "bloqueado por importación de archivo csv";
      $blocked->blocked = time();
      $blocked->createdBy = $createBy;
      $blocked->updatedBy = $createBy;
      if (!$blocked->save()) {
        throw new \InvalidArgumentException("Se ha producido un error inténtelo más tarde");
      }
    }
    unset($blocked);
    unset($updateblocked); 
  }
  
}