<?php

ini_set('memory_limit', '768M');
require_once(__DIR__ . "/../bootstrap/index.php");

$idAccount = 0;
//$argv es el array que contiene los parametros recibidos por consola. La posicion cero siempre es el nombre del archivo
// De la posicion 1 en adelante son todos los posibles parametros que se envian desde consola 
if (isset($argv[1])) {
  $idAccount = $argv[1];
}

$lcc = new UpdateCxclEmailPhone();
$lcc->start($idAccount);

class UpdateCxclEmailPhone {

  protected $logger;
  protected $db;

  public function __construct() {
    $di = \Phalcon\DI::getDefault();
    $this->logger = $di->get("logger");
    $this->db = $di->get('db');
  }

  public function start($idAccount) {
    //Con id de cuenta vamos al account config
    $accountConfig = \AccountConfig::find(
                    array(
                        "conditions" => "idAccount = ?0",
                        "bind" => array(0 => $idAccount)
                    )
            )->toArray();

    //Con idAccountConfig vamos al detail config
    $detailConfig = \DetailConfig::find(
                    array(
                        "conditions" => "idAccountConfig = ?0",
                        "bind" => array(0 => $accountConfig[0]["idAccountConfig"])
                    )
    );

    //Recorremos los servicios y extraemos el servicio 2 de
    // email marketing contactos
    foreach ($detailConfig as $keydt) {
      if ($keydt->idServices == 2) {
        $detailConfigEmailService = $keydt;
      }
    }

    //Busco las subcuentas de esa esa cuenta
    $subaccount = \Subaccount::find(
                    array(
                        "conditions" => "idAccount = ?0",
                        "bind" => array(0 => $idAccount)
                    )
            )->toArray();

    //Recorro las subcuentas
    foreach ($subaccount as $key) {
      //Busco en saxs si la subcuenta tiene asignado el servicio
      $saxs = \Saxs::find(
                      array(
                          "conditions" => "idSubaccount = ?0",
                          "bind" => array(0 => $key["idSubaccount"])
                      )
              )->toArray();

      $arrayIdSubaccount = array();
      foreach ($saxs as $sxs) {
        if ($sxs["idServices"] == 2) {
          array_push($arrayIdSubaccount, intval($sxs["idSubaccount"]));
          //echo "La subcuenta " . $sxs["idSubaccount"] . " tiene el servicio \n";
        }
      }
      //Busco las Contactlist de cada subcuenta
      foreach ($arrayIdSubaccount as $value) {
        $contactlist = \Contactlist::find(
                        array(
                            "conditions" => "idSubaccount = ?0 and deleted=0",
                            "columns" => "idContactlist",
                            "bind" => array(0 => $value)
                        )
                )->toArray();
        //Extraigo los idContactlis
        foreach ($contactlist as $key => $value) {
          $arrayIdCl[] = intval($value["idContactlist"]);
        }
      }
    }
    //Separo por coma los idContactlist
    $commaSeparatedIdCl = implode(",", $arrayIdCl);

    //Busco en Cxcl con esos idContactlist
    $cxcl = \Cxcl::find(
                    ["conditions" => "idContactlist IN ($commaSeparatedIdCl) and deleted = 0",
                        "columns" => "Distinct(idContact)"
                    ]
    );

    //Incluyo en un array dichos Id's
    foreach ($cxcl as $key => $value) {
      foreach ($value as $val) {
        $arrayIdContactFiltered[] = (int) $val;
      }
    }

    //Conditions de consulta a modelo de contact
    $arrayConditionsContact = array(
        "conditions" => array(
            "idContact" => ['$in' =>
                $arrayIdContactFiltered
            ],
            "deleted" => 0
        )
    );

    //Consulto en coleccion de contact con los id de contacto
    $contact = \Contact::find($arrayConditionsContact);

    //Recorro la data resultante e incluyo en un array general cada array de data
    $arrayGeneral = array();
    $arrayData = array();
    foreach ($contact as $key => $value) {
      $arrayData = array(
          "idContact" => $value->idContact,
          "phone" => $value->phone,
          "email" => $value->email,
      );
      array_push($arrayGeneral, $arrayData);
    }

    //Teniendo la informacion desde coleccion Contact,
    //Procedo a validar los numeros y los telefonos
    //Si y solo si tiene telefono, lo pongo en un array. 
    //Igualmente Si y solo si tiene email
    $arrayIdContactPhone = array();
    foreach ($arrayGeneral as $key => $value) {
      //Si solamente tiene phone (no email)
      if (($value["phone"] != null || $value["phone"] != "") && ($value["email"] == null || $value["email"] == "")) {
        array_push($arrayIdContactPhone, (int) $value["idContact"]);
      }
    }
    
    //Separo por commas los idContact convirtiendo el array en string separado por comas
    $commaSeparatedIdContact = implode(",", $arrayIdContactPhone);
    //Teniendo los idContact de los registros que tienen si y solo si telefono
    //los marcamos en cxcl en la columna singlephone
    //Realizamos sentencia update
    $sqlUpdateCxcl = "UPDATE cxcl "
            . " SET singlePhone =" . time()
            . " WHERE idContact IN ({$commaSeparatedIdContact})";
    $this->db->execute($sqlUpdateCxcl);

  }

}
