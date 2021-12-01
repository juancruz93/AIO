<?php

require_once(__DIR__ . "/../bootstrap/index.php");
require_once 'ContactIteratorSegment.php';
require_once 'FieldsValidations.php';
require_once 'MailNotification.php';

/**
 * Description of segmentsHandler
 *
 * @author juan.pinzon
 */
class SegmentsHandler {

  private $idSegment;
  private $segment;
  private $limit;
  private $offset;
  private $dataIdContactlists;
  private $dataIdContacts;

  const ROWS_LIMIT = 8000;
  //const ROWS_LIMIT = 3;
  const ROWS_OFFSET = 0;

  public function __construct($idSegment) {
    $this->setLimit(self::ROWS_LIMIT);
    $this->setOffset(self::ROWS_OFFSET);
    $this->setDataIdContactlists([]);
    $this->consultSegment($idSegment);
    $this->setItemsDataIdContactlists();
  }

  public function getIdSegment() {
    return $this->idSegment;
  }

  public function getSegment() {
    return $this->segment;
  }

  public function getLimit() {
    return $this->limit;
  }

  public function getOffset() {
    return $this->offset;
  }

  public function getDataIdContactlists() {
    return $this->dataIdContactlists;
  }

  public function getDataIdContacts() {
    return $this->dataIdContacts;
  }

  public function setIdSegment($idSegment) {
    $this->idSegment = $idSegment;
  }

  public function setSegment($segment) {
    $this->segment = $segment;
  }

  public function setLimit($limit) {
    $this->limit = $limit;
  }

  public function setOffset($offset) {
    $this->offset = $offset;
  }

  public function setDataIdContactlists($dataIdContactlists) {
    $this->dataIdContactlists = $dataIdContactlists;
  }

  public function setDataIdContacts($dataIdContacts) {
    $this->dataIdContacts = $dataIdContacts;
  }

  public function consultSegment($idSegment) {
    $segment = Segment::findFirst(array(
                "conditions" => array(
                    "idSegment" => (int) $idSegment
                )
    ));

    if (!$segment) {
      throw new InvalidArgumentException("El segmento que intenta procesar no existe");
    }

    $this->setSegment($segment);
  }

  private function addItemDataIdContactlists($item) {
    array_push($this->dataIdContactlists, $item);
  }

  public function setItemsDataIdContactlists() {
    foreach ($this->getSegment()->contactlist as $value) {
      $this->addItemDataIdContactlists($value["idContactlist"]);
    }
  }

  private function consultIdContacts($limit, $offset) {
    $cxcl = Cxcl::find(array(
                "columns" => "idContact, idContactlist",
                "conditions" => "idContactlist IN (".implode(",", $this->getDataIdContactlists()).") AND status = :status: AND active > :active:",
                "bind" => array("status" => 'active', "active" => 0),
                "limit" => $limit,
                "offset" => $offset
    ));

    $this->setDataIdContacts(array(
        "idContacts" => [],
        "idContactlists" => []
    ));

    foreach ($cxcl as $c) {
      array_push($this->dataIdContacts["idContacts"], $c->idContact);
      array_push($this->dataIdContacts["idContactlists"], $c->idContactlist);
    }
  }

  public function createSegment() {
    $fieldValidation = new FieldsValidations(\Phalcon\DI::getDefault()->get('filtersSegment'));
    $flag = true;
    $totalContacts = 0;

    $mongoclient = new \MongoClient();
    $collection = $mongoclient->aio->sxc;

    while ($flag) {
      $countValid = 0;
      $this->consultIdContacts($this->getLimit(), $this->getOffset());

      if (count($this->getDataIdContacts()["idContacts"]) > 0) {
        $contactIterator = new ContactIteratorSegment();
        $contactIterator->setIdContacts($this->getDataIdContacts());
        $contactIterator->findContacts();
        
        $this->setOffset($this->getOffset() + self::ROWS_LIMIT);
        
        $bulk = [];
        foreach ($contactIterator as $key => $contact) {
          if (isset($contact["customfield"])) {
            $fieldValidation->setCustomFields($contact["customfield"]);
          } else {
            $fieldValidation->setCustomFields(NULL);
          }
          $valid = $fieldValidation->validateContact($this->getSegment()->filters, $this->getSegment()->conditions, $contact["contact"]);
          if ($valid) {
            $c = $contact["contact"];
            $bulk[] = array(
                "idSegment" => (int) $this->getSegment()->idSegment,
                "idContact" => (int) $c->idContact,
                "idAccount" => $c->idAccount,
                "idSubaccount" => (int) $c->idSubaccount,
                "email" => $c->email,
                "indicative" => $c->indicative,
                "phone" => $c->phone,
                "name" => $c->name,
                "lastname" => $c->lastname,
                "birthdate" => $c->birthdate,
                "blockedEmail" => ((isset($c->blockedEmail)) ? $c->blockedEmail : ""),
                "blockedPhone" => ((isset($c->blockedPhone)) ? $c->blockedPhone : ""),
                "created" => (int) time(),
                "unsubscribed" => (int) 0,
                "deleted" => (int) 0,
                "blocked" => (int) 0,
                "updated" => (int) time(),
                "unsubscribed" => (int) 0,
                "deleted" => (int) 0,
                "blocked" => (int) 0,
                "createdBy" => $this->getSegment()->createdBy,
                "updatedBy" => $this->getSegment()->updatedBy
            );
            $countValid++;
            unset($c, $contact);
          }
        }

        $totalContacts += $countValid;
        if ($countValid == 0) {
          continue;
        }
        $result = $collection->batchInsert($bulk);
        
        if ($result["ok"] == (float) 1) {
          unset($bulk);
        }
      } else {
        $flag = false;
        break;
      }
    }

    $segment = $this->getSegment();
    $segment->status = "processed";
    if (!$segment->save()) {
      foreach ($segment->getMessages() as $message) {
        throw new \InvalidArgumentException("No se logró crear el smslote {$message}");
      }
    }

    return ["total" => $totalContacts, "segment" => $segment];
  }
  
  }
  
try {
  $id = 0;
  if (isset($argv[1])) {
    $id = $argv[1];
  }
  $sh = new SegmentsHandler($id);
  $response = $sh->createSegment();
  $Segment = \Segment::findFirst([["idSegment" =>(int) $response['segment']->idSegment, "deleted" => 0]]);
  $subaccount = \Subaccount::findFirst(array(
                  'conditions' => 'idSubaccount = ?1',
                  'bind' => array(1 =>(int) $Segment->idSubaccount)
  ));
  $mailnotification = new MailNotification($response["segment"]->createdBy);
  $validateSub = [1528,636];
    
  if (!in_array($subaccount->idSubaccount,$validateSub)) {
      if ($response["total"] > 0) {
        $mailnotification->setSubject("Proceso de creación del segmento '{$response["segment"]->name}' exitoso");
        $mailnotification->setContent("El proceso de creación del segmento con nombre <b>'{$response['segment']->name}'</b> ha finalizado exitosamente, "
                . "<b>{$response["total"]}</b> contactos cumplieron con las condiciones establecidas en el segmento");
        $mailnotification->sendNotification();
        echo "El proceso de creación de segmento con id '{$response['segment']->idSegment}' y nombre '{$response['segment']->name}' ha sido exitoso";
      } else {
        $mailnotification->setSubject("Proceso de creación del segmento '{$response["segment"]->name}' exitoso, sin contactos");
        $mailnotification->setContent("El proceso de creación del segmento con nombre <b>'{$response['segment']->name}'</b> ha finalizado exitosamente, "
                . "ningún contacto cumplión con las condiciones establecidas en el segmento");
        $mailnotification->sendNotification();
        echo "El proceso de creación de segmento con id '{$response['segment']->idSegment}' y nombre '{$response['segment']->name}' no ha generado contactos que cumplan con las condiciones establecidas";
      }
  }
} catch (InvalidArgumentException $ex) {
  echo "Ha ocurrido el siguiente error en la creación de segmentos {$ex->getMessage()}"
  . "-----> {$ex->getTraceAsString()}";
  \Phalcon\DI\FactoryDefault::getDefault()->get("logger")->log("Ha ocurrido el siguiente error en la creación de segmentos {$ex->getMessage()}"
          . "-----> {$ex->getTraceAsString()}");
} catch (Exception $ex) {
  echo "Ha ocurrido un error grave en la creación de segmentos {$ex->getMessage()}"
  . "-----> {$ex->getTraceAsString()}";
  \Phalcon\DI\FactoryDefault::getDefault()->get("logger")->log("Ha ocurrido un error grave en la creación de segmentos {$ex->getMessage()}"
          . "-----> {$ex->getTraceAsString()}");
} 