<?php

ini_set('memory_limit', '768M');
require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$import = new ExportContactList();
$import->start($id);

class ExportContactList {

  protected $logger;
  protected $path;
  protected $route;
  protected $fileManager;
  protected $idUser;
  protected $db;
  protected $fields;
  protected $arrIdImportContact;

  public function __construct() {
    $di = \Phalcon\DI::getDefault();
    $this->logger = $di->get("logger");
    $this->path = $di->get("tmpPath")->dir;
    $this->route = "//DS-PINZON/Users/juan.pinzon/Documents/NetBeansProjects/aio/tmpcontactlist.csv";

    //$routecsv = "C:/Users/juan.pinzon/Documents/NetBeansProjects/aio/tmp/cxcl/cxcl{$id}.csv";
    $this->fileManager = new \Sigmamovil\General\Misc\FileManager();
    $this->db = $di->get('db');
  }

  public function start($idContactList) {

    $cxcl = \Cxcl::find(array(
                //"columns"=>"idMail",
                "conditions" => "idContactlist = ?0 and deleted = 0",
                "bind" => array(0 => $idContactList)
            ))->toArray();



    $route2 = __DIR__ . "/../../" . \Phalcon\DI::getDefault()->get('tmpPath')->dir . "/../contactlist/Contactlist{$idContactList}.csv";
    $file = fopen($route2, "w");

    $titulos = 1;

    foreach ($cxcl as $val) {

      $contion = array(
          'conditions' => array(
              'idContact' => (int) $val["idContact"]
          )
      );

      $Contact = \Contact::findfirst($contion);
      unset($contion);
      $contion = array(
          'conditions' => array(
              'idContact' => (int) $val["idContact"],
              'idContactlist.' . $idContactList => array(
                  '$exists' => true
              )
          ),
          'fields' => array(
              'idContactlist.' . $idContactList => 1
          )
      );
      $cxc = \Cxc::find($contion);


      if ($titulos == 1) {
        fwrite($file, "IdContacto" . ",");
        fwrite($file, "Email" . ",");
        fwrite($file, "Nombre" . ",");
        fwrite($file, "Apellido" . ",");
        fwrite($file, "Telefono" . ",");
        fwrite($file, "FechaCumpleaños" . ",");
        fwrite($file, (string) $this->findCustomtitle($Contact->idContact, (int) $idContactList));
        fwrite($file, "\r\n");

        $titulos = 0;
      }



      fwrite($file, (int) $Contact->idContact . ",");
      fwrite($file, (string) $Contact->email . ",");
      fwrite($file, (string) $Contact->name . ",");
      fwrite($file, (string) $Contact->lastname . ",");
      fwrite($file, (string) $Contact->phone . ",");
      fwrite($file, (string) $Contact->birthdate . ",");
      fwrite($file, (string) $this->findCustomfield($Contact->idContact, (int) $idContactList));
      fwrite($file, "\r\n");
    }
    fclose($file);
    exit;
  }

  public function findCustomfield($idContact, $idContactList) {
    //declaramos una variable Como Array.
    $array = array();
    $string = "";

    //Asignamos la informacion del Model y la consulta de todos los Contactos a la variable.
    $cxc = \Cxc::findFirst([["idContact" => (Int) $idContact]]);

    //Por cada Contacto varificamos que tenga Campos Personalizados.
    if (isset($cxc->idContactlist[$idContactList]) != NULL) {
      //Recorremos el Customfield por cada Contacto.
      $prueba = array_filter($cxc->idContactlist[$idContactList]);
      unset($cxc);
      foreach ($prueba as $key => $value) {
        //verificamos que no sea Null
        if ($value != null) {
          //Al Array vacio le asignamos el array que creamos con la data de los Campos Personalizados.
          //$array[$key] = ["value" => $value["value"]];
          $string .= $value["value"] . ",";
        }
        unset($prueba);
      }
      $string1 = substr($string, 0, -1);
    }

    unset($cxc);
    //Retornamos el Array que contiene toda la data de los Campos Personalizados .
    return $string1;
  }

  public function findCustomtitle($idContact, $idContactList) {
    //declaramos una variable Como Array.
    $array = array();
    $string = "";

    //Asignamos la informacion del Model y la consulta de todos los Contactos a la variable.
    $cxc = \Cxc::findFirst([["idContact" => (Int) $idContact]]);

    //Por cada Contacto varificamos que tenga Campos Personalizados.
    if (isset($cxc->idContactlist[$idContactList]) != NULL) {
      //Recorremos el Customfield por cada Contacto.
      $prueba = array_filter($cxc->idContactlist[$idContactList]);
      unset($cxc);
      foreach ($prueba as $key => $value) {
        //verificamos que no sea Null
        if ($value != null) {
          //Al Array vacio le asignamos el array que creamos con la data de los Campos Personalizados.
          //$array[$key] = ["value" => $value["value"]];
          $string .= $value["name"] . ",";
        }
        unset($prueba);
      }
      $string1 = substr($string, 0, -1);
    }

    unset($cxc);
    //Retornamos el Array que contiene toda la data de los Campos Personalizados .
    return $string1;
  }

}

?>