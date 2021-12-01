<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Respaldo
 *
 * @author jordan.zapata
 */
class Respaldo {

  //put your code here

  public function respaldo($param) {

    while ((($record = fgetcsv($handle, 0, $delimiter)) !== false)) {
      //////////inicio del while///////////////////////////
      if (isset($amount) && ($count + 1) > $amount) {
        break;
      }
      if (!isset($fieldsmap['email']) && !isset($fieldsmap['phone'])) {
        $this->changeStatusToCanceled($importcontactfile);
        $dropCollectionTmp = new MongoDB\Driver\Command(['eval' => "dropCollectionTmp({$id})"]);
        $manager->executeCommand('aio', $dropCollectionTmp);

        throw new InvalidArgumentException("Error al procesar el archivo csv.");
      } 
      
      
      
      else if (!filter_var($record[$fieldsmap['email']], FILTER_VALIDATE_EMAIL)) {

        \Phalcon\DI::getDefault()->get('logger')->log("El csv no contiene email");
        $record[$fieldsmap['email']] = "";
        if ((!empty($record[$fieldsmap['indicative']]) && is_numeric($record[$fieldsmap['indicative']])) && (!empty($record[$fieldsmap['phone']]) && is_numeric($record[$fieldsmap['phone']]))) {
          if ($this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']])) {
            if (isset($record[$fieldsmap['birthdate']]) && !empty($dateFormat)) {
              $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
              if ($date != false) {
                $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
              }
            } else {
              $record[$fieldsmap['birthdate']] = "";
            }
            if ($arrayCountry[$record[$fieldsmap['indicative']]] && ($arrayCountry[$record[$fieldsmap['indicative']]]["maxDigits"] == strlen($record[$fieldsmap['phone']]))) {
              $count++;
              $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
            } else {
              $record['causa'] = "El indicativo o el numero celular no cumple con las condiciones establecidad.";
              $arrayErrors[] = $record;
            }
          } else {
            $record['causa'] = "El contacto se encuentra repetido en el archivo.";
            $arrayRepeat[] = $record;
          }
        } else {
          $record['causa'] = "El correo, indicativo o teléfono son inválidos.";
          $arrayDisabled[] = $record;
        }
      } 
      else {
        if ((!empty($record[$fieldsmap['indicative']]) && is_numeric($record[$fieldsmap['indicative']])) && (!empty($record[$fieldsmap['phone']]) && is_numeric($record[$fieldsmap['phone']]))) {
          if (isset($record[$fieldsmap['birthdate']]) && !empty($dateFormat)) {
            $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
            if ($date != false) {
              $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
            }
          } else {
            $record[$fieldsmap['birthdate']] = "";
          }
          if ($this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']])) {
            if ($arrayCountry[$record[$fieldsmap['indicative']]] && ($arrayCountry[$record[$fieldsmap['indicative']]]["maxDigits"] == strlen($record[$fieldsmap['phone']]))) {
              $count++;
              $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
            } else {
              $record['causa'] = "El indicativo o el numero celular no cumple con las condiciones establecidad.";
              $arrayErrors[] = $record;
            }
          } else {
            $record['causa'] = "El contacto se encuentra repetido en el archivo.";
            $arrayRepeat[] = $record;
          }
        } 
        else {
          if ($this->repeatedCheck($record[$fieldsmap['email']], $record[$fieldsmap['phone']])) {
            $record[$fieldsmap['indicative']] = "";
            $record[$fieldsmap['phone']] = "";
            if (isset($record[$fieldsmap['birthdate']]) && !empty($dateFormat)) {
              $date = date_create_from_format($dateFormat, trim($record[$fieldsmap['birthdate']]));
              if ($date != false) {
                $record[$fieldsmap['birthdate']] = date_format($date, "Y-m-d");
              }
            } else {
              $record[$fieldsmap['birthdate']] = "";
            }
            if ($arrayCountry[$record[$fieldsmap['indicative']]] && ($arrayCountry[$record[$fieldsmap['indicative']]]["maxDigits"] == strlen($record[$fieldsmap['phone']]))) {
              $count++;
              $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
            } else if (!$arrayCountry[$record[$fieldsmap['indicative']]] && !$record[$fieldsmap['phone']]) {
              $count++;
              $this->createArrayInsert($bulkTmpCont, $record, $fieldsmap, $createBy, $idAccount, $idSubaccount, $customfield, $importcontactfile, $count, $idContactlist);
            } else {
              $record['causa'] = "El indicativo o el numero celular no cumple con las condiciones establecidad.";
              $arrayErrors[] = $record;
            }
          } else {
            $record['causa'] = "El contacto se encuentra repetido en el archivo.";
            $arrayRepeat[] = $record;
          }
        }
      }
      ///////////final del while///////////////////////////
    }
  }

}
