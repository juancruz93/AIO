<?php

namespace Sigmamovil\Wrapper;

class KnowledgebaseWrapper extends \BaseWrapper {

  private $knowledgebase;

  public function setCSV($csv) {
    $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
    if (!in_array($csv['type'], $mimes)) {
      throw new \InvalidArgumentException("Debe subir un archivo de tipo CSV");
    }
    $this->csv = (object) $csv;
  }

  public function importcsv() {
    $this->setCsvRoute();
    $this->allRegister = array();
    $csv = file($this->csvRoute);
    $bagQuantity = 5000;
    $bags = ceil(count($csv) / $bagQuantity);
    $this->total = count($csv);
    $this->totalImported = 0;
    $this->totalNotImported = 0;
//    var_dump(count($csv));
//    var_dump($csv[3246]);
//    exit;
    //Se recorren cada una de las bolsas de $bagQuantity registros
    for ($c = 0; $c < $bags; $c++) {
      $csvEmails = array();
      $row = $c * $bagQuantity;
      //Se recorre cada una de las líneas de CSV
      for ($i = 0; $i < $bagQuantity; $i++) {
        $data = explode(";", $csv[$row]);
        if (!isset($data[1])) {
          throw new \InvalidArgumentException("El archivo no cumple con el formato establecido");
        }
        $csvEmails[$i] = $data[0];
        $row++;
        if ($row == count($csv)) {
          break;
        }
      }
//      $csvEmails = array('crystal@example.com', 'crystal2@example.com', 'ricardo.mayorga@sigmamovil.com');
      $this->insertBag($csvEmails);
    }
    unlink($this->csvRoute);
//    $this->createCsvResult();

    $import = new \KnowledgebaseImports();
    $import->idUser = $this->user->idUser;
    $import->idAllied = $this->user->userType->idAllied;
    $import->fileName = $this->csv->name;
    $import->fileType = "csv";
    $import->total = $this->total;
    $import->totalImported = $this->totalImported;
    $import->totalNotImported = $this->totalNotImported;
    if (!$import->save()) {
      foreach ($import->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
//    $command = "mongoimport /d aio /c bounced_mail /jsonArray /file: " . \Phalcon\DI::getDefault()->get('path')->path . "tmp/jsonImport/contacts.json";
  }

  public function validatecsv() {
    $this->setCsvRoute();
    $this->allRegister = array();
    $csv = file($this->csvRoute);
    $bagQuantity = 5000;
    $bags = ceil(count($csv) / $bagQuantity);
    $this->total = count($csv);
    $this->totalImported = 0;
    $this->totalNotImported = 0;
//    var_dump(count($csv));
//    var_dump($csv[3246]);
//    exit;
    //Se recorren cada una de las bolsas de $bagQuantity registros
    for ($c = 0; $c < $bags; $c++) {
      $csvEmails = array();
      $row = $c * $bagQuantity;
      //Se recorre cada una de las líneas de CSV
      for ($i = 0; $i < $bagQuantity; $i++) {
        $data = explode(";", $csv[$row]);
         if (!isset($data[1])) {
          throw new \InvalidArgumentException("El archivo no cumple con el formato establecido");
        }
        $csvEmails[$i] = $data[0];
        $row++;
        if ($row == count($csv)) {
          break;
        }
      }
//      $csvEmails = array('crystal@example.com', 'crystal2@example.com', 'ricardo.mayorga@sigmamovil.com');
      $this->validateBag($csvEmails);
    }
    unlink($this->csvRoute);
    $this->createCsvResult();
  }

  public function createCsvResult() {
    $list = array();

    foreach ($this->allRegister as $register) {
//      $list[] = implode(";",$register);
      $list[] = $register['email'] . ";" . $register['imported'];
    }

    $file = fopen(\Phalcon\DI::getDefault()->get('path')->path . "/tmp/csv/" . $this->csv->name, "w");

    foreach ($list as $line) {
      fputcsv($file, explode('","', $line));
    }

    fclose($file);
  }

  public function setCsvRoute() {
    $location = \Phalcon\DI::getDefault()->get('path')->path . '/tmp/csv';
    if (!file_exists($location)) {
      mkdir($location, 0777, true);
    }
    $filename = "csvtemp_" . time() . ".csv";
    if (move_uploaded_file($this->csv->tmp_name, $location . '/' . $filename)) {
      $this->csvRoute = $location . '/' . $filename;
    } else {
      throw new \InvalidArgumentException("No se ha podido subir el archivo");
    }
  }

  public function validateBag($csvEmails) {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');

    //Limpiamos el $csvEmails de email que no cumplen el UTF-8
    $new = array();

    foreach ($csvEmails as $email) {
      if (preg_match('!!u', $email)) {
        $new[] = $email;
      } else {
        throw new \InvalidArgumentException("No se puede validar el archivo porque tiene registros corruptos (UTF-8. Ej: " . utf8_encode($email) . ")");

//        $register["email"] = $email;
//        $register["imported"] = "NO";
//        $register["observation"] = "No cumple UTF-8";
//        $this->allRegister[] = $register;
//        $decoded_mail = utf8_decode($email);
//        $new[] = str_replace("?", "", $decoded_mail);
      }
    }
    unset($csvEmails);
    $csvEmails = $new;


    //Buscamos en bbdd si se encuentran los emails registrados, y en $emailsDiff están los emails que no están en base de datos (sin repetirse) 
    $optionsEmail = array(
        'projection' => array('_id' => 0, 'email' => 1),
    );
    $queryIn = array('email' => ['$in' => $csvEmails]);
    $queryEmail = new \MongoDB\Driver\Query($queryIn, $optionsEmail);
    $arrayEmail = $manager->executeQuery("aio.bounced_mail", $queryEmail)->toArray();
    unset($optionsEmail);
    unset($queryIn);
    unset($queryEmail);

    $emailsDiff = array_diff($csvEmails, $this->fixArrayEmail($arrayEmail));

    //Se agregan los emails repetido en BBDD dentro del array completo
    $repeatedEmails = array_diff($csvEmails, $emailsDiff);
    foreach ($repeatedEmails as $email) {
      $register["email"] = $email;
      $register["imported"] = "Rebotado";
//      $register["observation"] = "Repetido en BBDD";
      $this->allRegister[] = $register;
    }
    $this->totalNotImported += count($repeatedEmails);


    //Se agregan los emails que se van a agregar en BBDD dentro del array completo
    foreach ($emailsDiff as $email) {
      $register["email"] = $email;
      $register["imported"] = "";
//      $register["observation"] = "";
      $this->allRegister[] = $register;
    }
    $this->totalImported += count($emailsDiff);
  }

  public function insertBag($csvEmails) {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');

    //Limpiamos el $csvEmails de email que no cumplen el UTF-8
    $new = array();
    foreach ($csvEmails as $email) {
      if (preg_match('!!u', $email)) {
        $new[] = $email;
      } else {
//        $register["email"] = $email;
//        $register["imported"] = "NO";
//        $register["observation"] = "No cumple UTF-8";
//        $this->allRegister[] = $register;
//        $new[] = utf8_encode($email);
      }
    }
    unset($csvEmails);
    $csvEmails = $new;


    //Buscamos en bbdd si se encuentran los emails registrados, y en $emailsDiff están los emails que no están en base de datos (sin repetirse) 
    $optionsEmail = array(
        'projection' => array('_id' => 0, 'email' => 1),
    );
    $queryIn = array('email' => ['$in' => $csvEmails]);
    $queryEmail = new \MongoDB\Driver\Query($queryIn, $optionsEmail);
    $arrayEmail = $manager->executeQuery("aio.bounced_mail", $queryEmail)->toArray();
    unset($optionsEmail);
    unset($queryIn);
    unset($queryEmail);

    $emailsDiff = array_diff($csvEmails, $this->fixArrayEmail($arrayEmail));

    //Se agregan los emails repetido en BBDD dentro del array completo
    $repeatedEmails = array_diff($csvEmails, $emailsDiff);
//    foreach ($repeatedEmails as $email) {
//      $register["email"] = $email;
//      $register["imported"] = "NO";
////      $register["observation"] = "Repetido en BBDD";
//      $this->allRegister[] = $register;
//    }
    $this->totalNotImported += count($repeatedEmails);

    if (count($emailsDiff) > 0) {

      //Se obtiene el número de lote en $lot
      $optionsEmail = array(
          'projection' => array('_id' => 0, 'idBouncedMail' => 1, 'lote' => 1),
          'sort' => array('lote' => -1),
          'limit' => 1
      );
      $queryIn = array();
      $queryEmail = new \MongoDB\Driver\Query($queryIn, $optionsEmail);
      $arrayEmail = $manager->executeQuery("aio.bounced_mail", $queryEmail)->toArray();

      if (count($arrayEmail) == 0) {
        $lot = 1;
      } else {
        $lot = $arrayEmail[0]->lote + 1;
      }
      unset($optionsEmail);
      unset($queryIn);
      unset($queryEmail);

      //Se obtiene el mayor idBouncedMail en $idBouncedMail
      $optionsEmail = array(
          'projection' => array('_id' => 0, 'idBouncedMail' => 1, 'lot' => 1),
          'sort' => array('idBouncedMail' => -1),
          'limit' => 1
      );
      $queryIn = array();
      $queryEmail = new \MongoDB\Driver\Query($queryIn, $optionsEmail);
      $arrayEmail = $manager->executeQuery("aio.bounced_mail", $queryEmail)->toArray();
      if (count($arrayEmail) == 0) {
        $idBouncedMail = 1;
      } else {
        $idBouncedMail = $arrayEmail[0]->idBouncedMail + 1;
      }
      unset($optionsEmail);
      unset($queryIn);
      unset($queryEmail);


      //Se crea el bulk
      $bulk = new \MongoDB\Driver\BulkWrite;
      $count = $idBouncedMail;
      foreach ($emailsDiff as $email) {
        $bulk->insert([
            'idBouncedMail' => $count,
            'email' => $email,
            'type' => "aio",
            'lote' => $lot,
            'created' => time(),
            'updated' => time(),
            'createdBy' => $this->user->email,
            'updatedBy' => $this->user->email
        ]);
        $count++;
      }

      $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
      $result = $manager->executeBulkWrite('aio.bounced_mail', $bulk, $writeConcern);
    }
    //Se agregan los emails que se van a agregar en BBDD dentro del array completo
//    foreach ($emailsDiff as $email) {
//      $register["email"] = $email;
//      $register["imported"] = "SI";
////      $register["observation"] = "";
//      $this->allRegister[] = $register;
//    }
    $this->totalImported += count($emailsDiff);
  }

  public function fixArrayEmail($array) {
    $arrayFix = array();
    foreach ($array as $value) {
      $arrayFix[] = $value->email;
    }
    return $arrayFix;
  }

  public function findImports($page) {

    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");

    $idAllied = $this->user->userType->idAllied;
    $this->data = $this->modelsManager->createBuilder()
            ->from('KnowledgebaseImports')
            ->where("idAllied = " . $idAllied)
            ->orderBy("KnowledgebaseImports.created DESC")
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)
            ->offset($page)
            ->getQuery()
            ->execute();
    $this->totals = $this->modelsManager->createBuilder()
            ->from('KnowledgebaseImports')
            ->where("idAllied = " . $idAllied)
            ->getQuery()
            ->execute();
    $this->modelData();
  }

  public function modelData() {
    $this->imports = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $data) {
      $import = new \stdClass();
      $import->idKnowledgeBaseImports = $data->idKnowledgeBaseImports;
      $import->idUser = $data->idUser;
      $import->idAllied = $data->idAllied;
      $import->totalImported = $data->totalImported;
      $import->totalNotImported = $data->totalNotImported;
      $import->total = $data->total;
      $import->fileName = $data->fileName;
      $import->fileType = $data->fileType;
      $import->created = date("d/m/Y  H:ia", $data->created);
      $import->updated = date("d/m/Y  H:ia", $data->updated);
      $import->createdBy = $data->createdBy;
      $import->updatedBy = $data->updatedBy;

      $arr[] = $import;
    }
    $this->imports['items'] = $arr;
  }

  public function getImports() {
    return $this->imports;
  }

}
