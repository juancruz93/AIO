<?php

//use Phalcon\Di;
//use Phalcon\Mvc\Model\Manager as ModelsManager;

require_once(__DIR__ . "/../bootstrap/index.php");

$deleteContacts = new DeleteContacts();
$deleteContacts->index();

class DeleteContacts {
  
  public $idAccount = 663; //id de la cuenta camara de comercio
  //public $idAccount = 33; //id de la cuenta Pruebas De Reglas V1 Prueba
  public $ArrayidCxcl = array();
  public $ArrayRepeat = array();
  
  public function __construct() {
    $di = \Phalcon\DI::getDefault();
    $this->logger = $di->get("logger");
    $this->db = $di->get('db');
  }
  
  public function index(){
    echo "Estas entrando en el index";
    
    // 1 = Obtener los contactos
    // 2 = Sacar repetidos en una cadena de texto
    // 3 = Convertirlos los repetidos en un Array
    // 4 = Verificacion o agregacion de un deleted logico
    // 5 = Eliminacion de los contactos repetidos
    
    //$sql = "SELECT COUNT(*) FROM tmp_email_ccc";
//    $sql = "SELECT * FROM tmp_email_ccc";
    $sql = "SELECT * FROM tmp_email_ccc LIMIT 10000 OFFSET 0";  
    $arrayDataTmpEmailCcc = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);

    foreach ($arrayDataTmpEmailCcc as $value) {      
      $this->findRepeatContact($value['email']);
    }
    exit; 
  }

  public function findRepeatContact($email){
    $count = 0;
    $where["idAccount"] = ['$ne' => $this->idAccount];
    $where["email"] = $email;
    $emailContact = \Contact::find([$where]);
    foreach ($emailContact as $value) {
      if($value->idContact != NULL || $value->email != ""){
        $query = array(
          'conditions' => array(
            'idAccount' => ['$ne' => (String) $this->idAccount],
            'email' => (string) $value->email ,
          ),
          'fields' => array(
            'idContact' => 1,
            'idAccount' => 1
          )
        );
        $count = \Contact::count($query);
        $this->findIdCxcl($value->idContact,$count);
      }
    }
  }
  
  public function findIdCxcl($idContact,$count){
    $where["idContact"] = (int) $idContact;
    $contact = \Contact::findFirst([$where]);
    $arrayDataCxcl = \Cxcl::findFirst(array(
      "conditions" => "idContact = ?0",
      "bind" => array(0 => (int) $idContact)
    ));
    if($arrayDataCxcl != NULL){
      if($arrayDataCxcl->created == (int)$contact->created || $arrayDataCxcl->createdBy == (int)$contact->createdBy){
        $insertSQL1 = "INSERT INTO `tmp_unique_ccc` (`idCxcl`,`idContact`, `email`, `name`, `lastname`, `birthdate`, `indicative`, `phone`, `idContactlist`, `namecontactlsit`, `description`,`idAccount`,`count`, `created`, `createdBy`) "
                . "VALUES "
                . "('{$arrayDataCxcl->idCxcl}','{$arrayDataCxcl->idContact}','{$contact->email}','{$contact->name}','{$contact->lastname}','{$contact->birthdate}','{$contact->indicative}','{$contact->phone}','{$arrayDataCxcl->Contactlist->idContactlist}','{$arrayDataCxcl->Contactlist->name}','{$arrayDataCxcl->Contactlist->description}','{$contact->idAccount}','{$count}','{$contact->created}','{$contact->createdBy}') ";
        $this->logger->log($insertSQL1);
        $this->db->execute($insertSQL1);
      }
    } else {
      $insertSQL2 = "INSERT INTO `tmp_trash_ccc` (`idAccount`,`idContact`, `email`, `name`, `lastname`, `birthdate`, `indicative`, `phone`, `created`, `createdBy`) "
              . "VALUES "
              . "('{$contact->idAccount}','{$contact->idContact}','{$contact->email}','{$contact->name}','{$contact->lastname}','{$contact->birthdate}','{$contact->indicative}','{$contact->phone}','{$contact->created}','{$contact->createdBy}') ";
      $this->logger->log($insertSQL2);
      $this->db->execute($insertSQL2);
    }
  }
}