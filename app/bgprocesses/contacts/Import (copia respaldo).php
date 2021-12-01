<?php

/**
 * Created by PhpStorm.
 * User: juan.dorado
 * Date: 14/07/2016
 * Time: 09:41
 */
ini_set('memory_limit', '768M');
require_once(__DIR__ . "/../bootstrap/index.php");
$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$import = new Import();
$import->importStart($id);

class Import {

  public $emailbuffers = array();
  public $countIdEmail;
  public $countIdPhone;
  public $arrayTmp;
  public $newArrayEmail;
  public $newArrayPhone;
  public $emailInsert;
  public $phoneInsert;
  public $tmpInsert;
  public $bulkEmail;
  public $bulkPhone;
  public $bulkTmpCont;
  public $bulkTmpId;
  public $contactEmail;
  public $contactPhone;
  public $contactId;
  public $arrayPhones = array();
  public $arrayEmails = array();

  public function importStart($id) {
    try {
      $importcontactfile = \Importcontactfile::findFirst(array(
          "conditions" => "idImportcontactfile = ?0",
          "bind" => array(0 => $id)
      ));
      if (!$importcontactfile) {
        throw new InvalidArgumentException("No se encontró el proceso a importar.");
      }
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
        largo del script */
      $emailUser = $importfile->User->email;
      $idAccount = $importfile->User->Usertype->Subaccount->idAccount;
      $idContactlist = $importfile->idContactlist;
      $delimiter = $importcontactfile->delimiter;
      $dateFormat = $importcontactfile->dateformat;

      /**
       * Si las colecciones temporales en mongo son creadas se continua con el proceso
       */
      if ($cursor->toArray()[0]->retval->ok) {
        unset($cursor);
        /* Funcion que limpia el archivo csv de contactos repetidos e invalidos y los guarda en la tabla temporal */
        $csv = $this->csvCleaner($route, $delimiter, $fieldsmap, $dateFormat, $customfield, $importcontactfile, $emailUser, $idAccount, $manager, $id, $idContactlist);

        if ($importcontactfile->header) {
          unset($csv['arrayDisabled'][0]);
        }
        $this->bulkEmail = new MongoDB\Driver\BulkWrite;
        $this->bulkPhone = new MongoDB\Driver\BulkWrite;
        $this->bulkTmpCont = new MongoDB\Driver\BulkWrite;
        $this->bulkTmpId = new MongoDB\Driver\BulkWrite;
        $bulkAutoIncrement = new MongoDB\Driver\BulkWrite;
        $date = time();

        /**
         * Funcion que consulta las colecciones en mogo y crea arrays temporales de los emails, telefonos
         */
        $this->createArraysTmp($manager);

        $importcontactfile->status = "processing";
        $importcontactfile->update();

        /**
         * Funcion que se encarga de pasar los contactos de la coleccion temporal a la de contactos en mongo
         */
        $this->bulkData($manager, $idAccount, $emailUser, $date, $idContactlist, $id);

        /**
         * Atualiza los autoincrementales en mogo
         */
        $bulkAutoIncrement->update(array('field_id' => 'id_email'), array('$set' => array('nextId' => $this->countIdEmail)));
        $bulkAutoIncrement->update(array('field_id' => 'id_phone'), array('$set' => array('nextId' => $this->countIdPhone)));
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $manager->executeBulkWrite('aio.autoincrementcollection', $bulkAutoIncrement, $writeConcern);
        unset($bulkAutoIncrement);

        /**
         * Funcion que se encarga de guardar lo procesado anteriormente en mongo
         */
        $this->executeBulk($manager, $writeConcern, $id);
        $this->clearVarTmp();

        $importstart = new MongoDB\Driver\Command(array('eval' => "importstart({$id}, {$idContactlist})", 'nolock' => true));
        $cursorimport = $manager->executeCommand('aio', $importstart);

        if ($cursorimport->toArray()[0]->retval) {
          unset($cursorimport);
          $importcontactfile->status = "saving";
          $importcontactfile->update();
          $this->generateCsvCxcl($manager, $id);

          $dropCollectionTmp = new MongoDB\Driver\Command(['eval' => "dropCollectionTmp({$id})"]);
          $cursorTmp = $manager->executeCommand('aio', $dropCollectionTmp);
          $arrTmp = $cursorTmp->toArray();
          unset($cursorTmp);
          $routecsv = \Phalcon\DI::getDefault()->get('path')->path . "tmp/cxcl/cxcl{$id}.csv";
          $res = \Phalcon\DI::getDefault()->get('db')->query("LOAD DATA INFILE '{$routecsv}' IGNORE INTO TABLE cxcl FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'");

          $importcontactfile->status = "finished";
          $importcontactfile->imported = $arrTmp[0]->retval[0]->totalTmp;
          $importcontactfile->exists = $arrTmp[0]->retval[1]->totalTmpExist;
          $importcontactfile->repeated = count($csv['arrayRepeat']);
          $importcontactfile->invalids = count($csv['arrayDisabled']);
          $importcontactfile->update();
          //unset($arrTmp);
          $arr = array_merge($csv['arrayRepeat'], $csv['arrayDisabled']);
          $this->generateCsvErrors($arr, $id);
          unset($arr);

          var_dump($arrTmp[0]->retval);
          return;
        }
        var_dump("aquiiiiiiii");
        exit;
      } else {
        throw new InvalidArgumentException("Ha ocurrido un error, por favor contacte al administrador.");
      }
    } catch (InvalidArgumentException $ex) {
      echo $ex->getMessage();
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (\Exception $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }

  public function executeBulk($manager, $writeConcern, $id) {
    if ($this->emailInsert) {
      $manager->executeBulkWrite('aio.email', $this->bulkEmail, $writeConcern);
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
        if (isset($this->newArrayEmail[$values->email]) && !empty($values->email)) {
          $array = array('idAccount' => $idAccount, 'idEmail' => $this->countIdEmail, 'email' => $values->email,
              'created' => $date, 'updated' => $date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser);
          $this->bulkEmail->insert($array);
          if ($count % 20000 == 0) {
            $manager->executeBulkWrite('aio.email', $this->bulkEmail);
            unset($this->bulkEmail);
            $this->bulkEmail = new MongoDB\Driver\BulkWrite;
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

        $queryEmailContact = new MongoDB\Driver\Query(array('email' => $values->email, 'phone' => $values->phone));
        $contactExists = $manager->executeQuery("aio.contact", $queryEmailContact)->toArray();

        if (count($contactExists) == 1) {
          $this->bulkTmpCont->update(array('_id' => $values->_id), array('$set' => array('idContact' => $contactExists[0]->idContact)));
          $this->tmpInsert = true;
          $array = array('idContactlist' => $idContactlist, 'idContact' => $contactExists[0]->idContact, 'created' => $date,
              'updated' => $date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser, 'unsubscribed' => 0, 'deleted' => 0);
          $this->bulkTmpId->insert($array);
        }

        if (!empty($values->email) && empty($values->phone)) {
          $queryEmailContact = new MongoDB\Driver\Query(array('email' => $values->email, 'phone' => ""));
          $contactExists = $manager->executeQuery("aio.contact", $queryEmailContact)->toArray();
          if (count($contactExists) == 1) {
            $this->bulkTmpCont->update(array('_id' => $values->_id), array('$set' => array('idContact' => $contactExists[0]->idContact)));
            $this->tmpInsert = true;
            $array = array('idContactlist' => $idContactlist, 'idContact' => $contactExists[0]->idContact, 'created' => $date,
                'updated' => $date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser, 'unsubscribed' => 0, 'deleted' => 0);
            $this->bulkTmpId->insert($array);
          }
        }
        if (!empty($values->phone) && empty($values->email)) {
          $queryEmailContact = new MongoDB\Driver\Query(array('email' => '', 'phone' => $values->phone));
          $contactExists = $manager->executeQuery("aio.contact", $queryEmailContact)->toArray();
          if (count($contactExists) == 1) {
            $this->bulkTmpCont->update(array('_id' => $values->_id), array('$set' => array('idContact' => $contactExists[0]->idContact)));
            $this->tmpInsert = true;
            $array = array('idContactlist' => $idContactlist, 'idContact' => $contactExists[0]->idContact, 'created' => $date,
                'updated' => $date, 'createdBy' => $emailUser, 'updatedBy' => $emailUser, 'unsubscribed' => 0, 'deleted' => 0);
            $this->bulkTmpId->insert($array);
          }
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
    unset($this->bulkPhone);
    unset($this->bulkTmpCont);
    unset($this->countIdEmail);
    unset($this->countIdPhone);
  }

  public function createArraysTmp($manager) {
    $queryIdEmail = new MongoDB\Driver\Query(array('field_id' => 'id_email'));
    $nextIdEmail = $manager->executeQuery("aio.autoincrementcollection", $queryIdEmail)->toArray();
    $this->countIdEmail = $nextIdEmail[0]->nextId;
    unset($nextIdEmail);

    $queryIdPhone = new MongoDB\Driver\Query(array('field_id' => 'id_email'));
    $nextIdPhone = $manager->executeQuery("aio.autoincrementcollection", $queryIdPhone)->toArray();
    $this->countIdPhone = $nextIdPhone[0]->nextId;
    unset($nextIdPhone);

    $optionsEmail = array(
        'projection' => array('_id' => 0, 'email' => 1),
    );
    $queryIn = array('email' => ['$in' => $this->arrayEmails]);

    $queryEmail = new MongoDB\Driver\Query($queryIn, $optionsEmail);
    $arrayEmail = $manager->executeQuery("aio.email", $queryEmail)->toArray();
    unset($optionsEmail);
    unset($queryIn);

    $emailsDiff = array_diff($this->arrayEmails, $this->fixArrayEmail($arrayEmail));

    unset($this->arrayEmails);
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

    $phonesDiff = array_diff($this->arrayPhones, $this->fixArrayPhone($arrayPhone));

    unset($this->arrayPhones);
    unset($arrayPhone);

    $this->newArrayPhone = array_fill_keys($phonesDiff, true);
    unset($phonesDiff);

    $this->emailInsert = false;
    $this->phoneInsert = false;
    $this->tmpInsert = false;
  }

  public function createArrayInsert($bulk, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist) {
    $tmpcontacts = array();
    $obj = array();
    $tmpcontacts['idContact'] = false;
    $tmpcontacts['idAccount'] = $idAccount;
    $tmpcontacts['idSubaccount'] = $idSubaccount;
    $tmpcontacts['email'] = trim($record[$fieldsmap['email']]);
    $this->arrayEmails[] = trim($record[$fieldsmap['email']]);
    $tmpcontacts['name'] = trim($record[$fieldsmap['name']]);
    $tmpcontacts['lastname'] = trim($record[$fieldsmap['lastname']]);
    $tmpcontacts['birthdate'] = trim($record[$fieldsmap['birthdate']]);
    $tmpcontacts['indicative'] = trim($record[$fieldsmap['indicative']]);
    $tmpcontacts['phone'] = trim($record[$fieldsmap['phone']]);
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

    $bulk->insert($tmpcontacts);
    /* if ($count % 100 == 0) {
      $importcontactfile->processed = $count;
      $importcontactfile->update();
      } */
    unset($tmpcontacts);
  }

  public function csvCleaner($route, $delimiter, $fieldsmap, $dateFormat, $customfield, $importcontactfile, $createBy, $idAccount, $manager, $id, $idContactlist) {
    $handle = fopen($route, "r");
    $arrayDisabled = array();
    $arrayRepeat = array();
    $count = 0;
    $idSubaccount = $importcontactfile->idSubaccount;
    $importcontactfile->status = "preprocessing";
    $importcontactfile->update();
    $bulkTmpCont = new MongoDB\Driver\BulkWrite;

    if (!$handle) {
      \Phalcon\DI::getDefault()->get('logger')->log("Error al abrir el archivo original");
      throw new \InvalidArgumentException('Error al procesar el archivo. Contacte a su administrador!');
    }

    while ((($record = fgetcsv($handle, 0, $delimiter)) !== false)) {
      if (!filter_var($record[$fieldsmap['email']], FILTER_VALIDATE_EMAIL)) {
        $record[$fieldsmap['email']] = "";
        if ((!empty($record[$fieldsmap['indicative']]) && is_numeric($record[$fieldsmap['indicative']])) && (!empty($record[$fieldsmap['phone']]) && is_numeric($record[$fieldsmap['phone']]))) {
          if ($this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']])) {
            $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
            if ($date != false) {
              $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
            } else {
              $record[$fieldsmap['birthdate']] = "";
            }
            $count++;
            $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
          } else {
            $record['causa'] = "El contacto se encuentra repetido en el archivo.";
            $arrayRepeat[] = $record;
          }
        } else {
          $record['causa'] = "El correo, indicativo o teléfono son inválidos.";
          $arrayDisabled[] = $record;
        }
      } else {
        if ((!empty($record[$fieldsmap['indicative']]) && is_numeric($record[$fieldsmap['indicative']])) && (!empty($record[$fieldsmap['phone']]) && is_numeric($record[$fieldsmap['phone']]))) {
          $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
          if ($date != false) {
            $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
          } else {
            $record[$fieldsmap['birthdate']] = "";
          }
          if ($this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']])) {
            $count++;
            $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
          } else {
            $record['causa'] = "El contacto se encuentra repetido en el archivo.";
            $arrayRepeat[] = $record;
          }
        } else {
          if ($this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']])) {
            $record[$fieldsmap['indicative']] = "";
            $record[$fieldsmap['phone']] = "";
            $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
            if ($date != false) {
              $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
            } else {
              $record[$fieldsmap['birthdate']] = "";
            }
            $count++;
            $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
          } else {
            $record['causa'] = "El contacto se encuentra repetido en el archivo.";
            $arrayRepeat[] = $record;
          }
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
        "arrayDisabled" => $arrayDisabled
    );
    return $arrayResults;
  }

  public function repeatedCheck($email, $phone) {
    $email = strtolower(trim($email));
    $phone = strtolower(trim($phone));

    if (empty($phone)) {
      if (empty($this->emailbuffers) || !isset($this->emailbuffers[$email])) {
        $this->emailbuffers[$email] = true;
        return true;
      }
    } else if (empty($email)) {
      if (empty($this->emailbuffers) || !isset($this->emailbuffers[$phone])) {
        $this->emailbuffers[$phone] = true;
        return true;
      }
    } else if (empty($this->emailbuffers) || !isset($this->emailbuffers[$email][$phone])) {
      $this->emailbuffers[$email][$phone] = true;
      return true;
    } else {
      //El email y el telefono estan repetidos en el archivo.
      return false;
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

  public function generateCsvCxcl($manager, $id) {
    $query = new MongoDB\Driver\Query([]);
    $cursortmpid = $manager->executeQuery("aio.tmpid{$id}", $query);
    $cxclmongo = $cursortmpid->toArray();

    $route = __DIR__ . "/../../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../cxcl/cxcl{$id}.csv";
    $file = fopen($route, "w");
    foreach ($cxclmongo as $value) {
      fwrite($file, (int)$value->idContactlist . ",");
      fwrite($file, (int)$value->idContact . ",");
      fwrite($file, (int)$value->created . ",");
      fwrite($file, (int)$value->updated . ",");
      fwrite($file, $value->createdBy . ",");
      fwrite($file, $value->updatedBy . ",");
      fwrite($file, (int)$value->unsubscribed . ",");
      fwrite($file, (int)$value->deleted);
      fwrite($file, "\r\n");
    }
    fclose($file);
    unset($cxclmongo);
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
}
