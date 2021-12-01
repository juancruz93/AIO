<?php

$idAccount = $argv[1];
$idMail = $argv[2];

echo $idMail . PHP_EOL;
echo $idAccount . PHP_EOL;

require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../general/misc/SQLExecuter.php");

$pdf = new PdfMailManager();
$pdf->process($idAccount, $idMail);

/**
 * Description of PdfMailManager
 *
 * @author jose.quinones
 */
class PdfMailManager {

  public function process($idAccount, $idMail) {

    $files = glob("/websites/aio/public/assets/{$idAccount}/pdf/{$idMail}/" . "{*.pdf}", GLOB_BRACE);

    $arrayPdfmail = array();
    $basename = array();
    //
    $inIdcontact = $this->searchIdContacts($idMail);
    //
    $arrayInfoField = array();
    $arrayInfoField = $this->findCustomfield($inIdcontact, $files);
    unset($inIdcontact);
    //
    foreach ($files as $file) {
      $path_parts = pathinfo($file);
      $size = filesize($file);
      $size = $size / 1024;
      //$arrayPdfmail[] = "(null, {$idMail}, 0, '{$path_parts['basename']}', {$size}, '{$path_parts['extension']}', " . time() . ")";
      if ($path_parts['extension'] == "pdf") {
        //funcion en la cual por nombre
        $namefield = explode("_", $path_parts['basename']);
        //$namefield[0] = CC
        //$namefield[1] = nombre
        $namefield = explode(".", $namefield[1]);
        //$namefield[0] = nombre
        //$namefield[1] = pdf
        foreach ($arrayInfoField as $key => $value){
          if($value['value'] == $namefield[0]){
            //$arrayPdfmail[] = ["idMail" => $idMail, "idContact" => (int) $value['idContact'], "name" => $path_parts['basename'], "size" => $size, "type" => $path_parts['extension'], "createdon" => time()];
            $arrayPdfmail[] = "(null, {$idMail}, {$value['idContact']}, '{$path_parts['basename']}', {$size}, '{$path_parts['extension']}', " . time() . ")";
            unset($arrayInfoField[$key]);
            array_values($arrayInfoField);
            break;
          }
        }
        unset($namefield);
      }
    }
    unset($files);
    if (count($arrayPdfmail) > 0) {
      $values = implode(',', $arrayPdfmail);
      //error_reporting(E_ALL);
      //ini_set('display_errors', 1);
      $sql = "INSERT IGNORE INTO pdfmail (idPdfmail, idMail, idContact, name, size, type, createdon) VALUES {$values}";
      try {
        $executer = new SQLExecuter();
        $executer->instanceDbAbstractLayer();
        $executer->setSQL($sql);
        $executer->executeAbstractLayer();
        $updateMail = \Mail::findFirst(array(
          'conditions' => 'idMail = ?0 AND deleted = 0',
          'bind' => array(0 => $idMail)
        ));
        $updateMail->attachment = 0;
        $updateMail->pdf = 1;
        $updateMail->update();
      } catch (\Exception $ex) {
        $this->logger->log("Exception: {$ex}");
        throw new \Exception("Exception: {$ex}");
      }
    }
  }
  
  public function searchIdContacts($idMail) {
    $findMail = \Mail::findFirst(array(
      'conditions' => 'idMail = ?0 AND deleted = 0',
      'bind' => array(0 => $idMail)
    ));
    $inIdcontact = array();
    $target = json_decode($findMail->target);
    unset($findMail);
    switch ($target->type) {
      case "contactlist":
        $inIdcontact = $this->getAllCxcl($this->getIdContaclist($target));
        break;
      case "segment":
        $inIdcontact = $this->getAllIdContactSegment($this->getIdSegment($target));
        break;
      default:
    }
    unset($target);
    return $inIdcontact;
  }

  public function getIdContaclist($target) {
    $idContactlist = array();
    if (isset($target->contactlists)) {
      foreach ($target->contactlists as $key) {
        $idContactlist[] = (int) $key->idContactlist;
      }
    }
    unset($target);
    return $idContactlist;
  }

  public function getAllCxcl($idContactlist) {
    $idContactlist = implode(",", $idContactlist);
    $sql = "SELECT DISTINCT idContact, idContactlist FROM cxcl"
            . " WHERE idContactlist IN ({$idContactlist})"
            . " AND unsubscribed = 0 "
            . " AND deleted = 0 "
            . " AND spam = 0 "
            . " AND bounced = 0 "
            . " AND blocked = 0 "
            . " AND singlePhone = 0";
    unset($idContactlist);
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    $inIdcontact = array();
    for ($i = 0; $i < count($cxcl); $i++) {
      $inIdcontact[$i] = ["idContact" => (int) $cxcl[$i]['idContact'], "idContactlist" => (int) $cxcl[$i]['idContactlist']];
    };
    unset($sql);
    unset($cxcl);
    return $inIdcontact;
  }

  public function getIdSegment($target) {
    $idSegment = array();
    if (isset($target->segment)) {
      foreach ($target->segment as $key) {
        $idSegment[] = (int) $key->idSegment;
      }
    }
    unset($target);
    return $idSegment;
  }

  public function getAllIdContactSegment($idSegment) {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $command = new MongoDB\Driver\Command([
        'aggregate' => 'sxc',
        'pipeline' => [
            ['$match' => ['idSegment' => ['$in' => $idSegment], 'email' => ['$nin' => ["", null, "null"]]]],
            ['$group' => ['_id' => '$idContact', 'data' => ['$first' => '$$ROOT']]],
        ],
        'allowDiskUse' => true,
    ]);
    $segment = $manager->executeCommand('aio', $command)->toArray();
    $inIdcontact = array();
    for ($i = 0; $i < count($segment[0]->result); $i++) {
      //hacer funcion para traer la lista de contactos de acuerdo al idContact
      $inIdcontact[$i] = $segment[0]->result[$i]->_id;
    }
    unset($command);
    unset($segment);
    return $inIdcontact;
  }

  public function findCustomfield($inIdcontact, $files) {
    $path_parts = pathinfo($files[0]);
    if ($path_parts['extension'] == 'pdf') {
      $field = explode("_", $path_parts['basename']);
      //$field[0] = CC
      //$field[1] = nombre
    }
    $arrayInfoField = array();
    foreach ($inIdcontact as $value) {
      $cxc = Cxc::findFirst(["conditions" => ['idContact' => $value["idContact"]]]);
      if ($cxc != FALSE) {
        foreach ($cxc->idContactlist[$value["idContactlist"]] as $key => $item) {
          if (strtoupper($item['name']) == strtoupper($field[0])) {
            $arrayInfoField[] = [
              "idContact" => $cxc->idContact,
              "idContactlist" => $value["idContactlist"],
              "idCustomfield" => $key,
              "name" => $item['name'],
              "value" => $item['value'],
              "type" => $item['type']
            ];
          }
        }
      }
      unset($cxc);
    }
    unset($inIdcontact);
    return $arrayInfoField;
  }

}
