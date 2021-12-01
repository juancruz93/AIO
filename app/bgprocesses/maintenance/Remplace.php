<?php

require_once(__DIR__ . "/../bootstrap/index.php");

$Remplace = new Remplace();
$Remplace->remplace();

class Remplace {

  public function remplace() {

    try {

      $manager = \Phalcon\DI::getDefault()->get('mongomanager');
      $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
     // $name = \Phalcon\DI::getDefault()->get('path')->path . "tmp/email.csv";

     // $handle = fopen($name, "r");

      $contador = 0;
    //  \Phalcon\DI::getDefault()->get('logger')->log("CodeRemplace:29/11/2017::Se inicio el proceso de remplazar los correos repetidos");
    //  while ((($record = fgets($handle)) !== false)) {

       

        $contador++;
        //$email = str_replace("﻿", "", ltrim(rtrim($record)));
        $contion2 = array(''
            . 'conditions' => array(
                'email' => "valle@fenavi.org"
            ),
            'fields' => array(
                'idContact' => 1,
                'idAccount'=>1,
                'email'=>1
        ));
        $queryContact = \Contact::find($contion2);

        $idContactRepeat;
        
        foreach ($queryContact as $key => $value) {

        $cxcl = \Cxcl::find(array(
                    "conditions" => "idContact = ?0 ",
                    "bind" => array(0 => $value->idContact)
        ))->toArray();
        
        if (count($cxcl) > 0) {
        	$idContactRepeat =$value->idContact;
            break;
        }
        }

        ////corregir los contactos en cxcl
        $contadorFinal =0;
        $contion = array(''
            . 'conditions' => array(
                'email' => "valle@fenavi.org",
                'idAccount'=>array(
                    '$ne'=>"663"
                )
            ),
            'fields' => array(
                'idContact' => 1,
                'idAccount'=>1,
                'email'=>1
        ));
        $queryEmailContact = \Contact::find($contion);


        foreach ($queryEmailContact as $key => $value) {
        		//var_dump($value->idContact);
        	
        	 $condition2 = array(
            'conditions' => array(
                'idContact' => $value->idContact
            ),
            'fields' => array(
            )
        );
        $cxc = \Cxc::find($condition2);

        foreach ($cxc[0]->idContactlist as $id => $data) {
            var_dump($id);

        $cxcl = \Cxcl::find(array(
                    "conditions" => "idContact = ?0 and idContactlist = ?1",
                    "bind" => array(0 => $idContactRepeat, 1 => $id)
        ))->toArray();
        

        if (count($cxcl) > 0) {
            $sql = "UPDATE cxcl SET idContact = {$value->idContact} WHERE idContact={$idContactRepeat} AND idContactlist = {$id}";
            $result = \Phalcon\DI::getDefault()->get("db")->query($sql);
            var_dump("update true");
        }
        }
        }
   //   }
      //\Phalcon\DI::getDefault()->get('logger')->log("CodeRemplace:29/11/2017::Se finalizo el proceso de remplazar los correos repetidos, total de correos procesados" . $contador );
    } catch (Exception $exc) {
      \Phalcon\DI::getDefault()->get('logger')->log("CodeRemplace:29/11/2017::Error procesando los correos repetidos total procesados" . $contador . "id de Contacto ::" . $idContactoSet);
      \Phalcon\DI::getDefault()->get('logger')->log("CodeRemplace:29/11/2017::Error procesando los correos repetidos" . $exc);
    }
  }

}
