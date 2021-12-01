<?php

namespace Sigmamovil\General\Misc;

use Phalcon\Db;
use \ElephantIO\Exception\ServerConnectionFailureException;


class FileManager {
  public $idsms;
  public function csvsms($file, $idSms,$idSubaccount = 0, $boolSms = true) {
    $this->setidSms($idSms); 
    $sms = \Sms::findFirst(array(
        "conditions" => "idSms = ?0",
        "bind" => array($idSms)
    ));
    //$elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
//    $elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('https://wstest.sigmamovil.com'));//tst
    //$elephant->initialize();
    //set Path
    $type = ($boolSms)? "sms" : "twoway";
    $smsTwoWay = null;
    $prefix = null;
    $validate = false;
    if($type =='twoway'){
        $smsTwoWay = \Smstwoway::findFirst(array(
                        "conditions" => "idSmsTwoway = ?0",
                        "bind" => array($idSms)
                    ));
        if($smsTwoWay->international == 1 && !empty($smsTwoWay->idcountry)){
         $validate = true;
         $country = \Country::findFirst(["conditions" => "idCountry = ?0", "bind" => [0 =>(int) $smsTwoWay->idcountry]]);
         if($country){
           $prefix = $country->phoneCode; 
         }  
        }                           
    }
    
    
    $dirTmpAio = \Phalcon\DI::getDefault()->get('path')->path . "/tmp/smscsv/";

    if (!file_exists($dirTmpAio)) {
      mkdir($dirTmpAio, 0777, TRUE);
    }

    $csv_mimetypes = array(
        'text/csv',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
        'application/octet-stream',
    );
    
    if (!in_array($file["type"], $csv_mimetypes)) {
      throw new \InvalidArgumentException("El archivo de datos debe ser de tipo CSV");
    }

    $file["name"] = "tmpsmscsv_{$idSms}{$type}.csv";
    $dircsv = $dirTmpAio . $file["name"];

    if (!\move_uploaded_file($file["tmp_name"], $dircsv)) {
      throw new \InvalidArgumentException("Ha ocurrido un error subiendo el archivo");
    }

    $handle = \fopen($dircsv, "r");
    $dirNewCsv = $dirTmpAio . "new_tmpsmscsv_{$idSms}{$type}.csv";
    $fpNewCsv = \fopen($dirNewCsv, "w");
    $contador = 0;
    $contadornum = 0;
    $contadorind = 0;
    $count = 0;
    $countcaracter160 = 0;
    $countcaracter300 = 0;
    $contadorIndicativoInternacional = 0;
    $contadorIndicativoNacional = 0;
    $countTotalErrors = 0;
    $arrayValudateNumbers = [];
    $countValidateNumbers = 0;
    $allDataSmsFailed = [];
    while (($data = fgetcsv($handle, 500, ";")) !== FALSE) {
        if($validate == false){
            $count = count($data);  
            if(empty($data[2])){
              $contador ++;
              $countTotalErrors++;  
            }else if(empty($data[1])){
                $contadornum ++;
                $countTotalErrors++;
            }else if(empty($data[0])){
                $contadorind ++;
                $countTotalErrors++;
            }else if($data[0] != "57" || $data[0] != 57){
              $contadorIndicativoNacional++;
              $countTotalErrors++;
            }else if($type == 'sms' && (strlen(trim($data[2])) > 160 && $sms->morecaracter == 0)){
               $countcaracter160 ++; 
               $countTotalErrors++;
            }else if($type == 'sms' && (strlen(trim($data[2])) > 300 && $sms->morecaracter == 1)){
               $countcaracter300 ++; 
               $countTotalErrors++;
            }else if($type != 'sms' && strlen(trim($data[2])) > 160){
               $countcaracter160 ++; 
               $countTotalErrors++;
            }else{
              if($type == 'sms'){
                $data[1] = str_replace(' ', '', $data[1]);
                if(!in_array($data[1],$arrayValudateNumbers)){//NO EXISTE EN EL ARREGLO, LO AGREGAMOS
                  array_push($arrayValudateNumbers, $data[1]);
                  fputcsv($fpNewCsv,$data,";",'"'); 
                }else{//SI EXISTE EN EL ARREGLO, NO LO ESCRIBIMOS EN CSV Y AGREGAMOS EL REGISTRO PARA INSERTARLO EN SMSFAILED
                  $countValidateNumbers++;
                  $array = array(
                    "idSms" => $sms->idSms,
                    "idContact" => null,
                    "indicative" => $data[0],
                    "message" => $data[2],
                    "phone" => $data[1],
                    "count" => 1,
                    "detail" => "El numero estaba repetido en el archivo",
                    "type" => "sms",
                  );
                  array_push($allDataSmsFailed, $array);
                }
              }else{
                fputcsv($fpNewCsv,$data,";",'"');
              }  
            }    
        }else{
           
            $count = count($data);  
            if(empty($data[2])){
              $contador ++;  
            }else if(empty($data[1])){
                $contadornum ++;
            }else if(empty($data[0])){
                $contadorind ++;
            }else if($validate == true && (int) $data[0]!= $prefix ){
                $contadorIndicativoInternacional ++;
            }else if(strlen(trim($data[2])) > 160){
               $countcaracter160 ++; 
            }else{
              fputcsv($fpNewCsv,$data,";",'"');  
            }  
            fputcsv($fpNewCsv,$data,";",'"');           
        }
                
    }
    
    \fclose($fpNewCsv);
    if($type == 'sms'){
        if($contador == $count){
            sleep(1);
            throw new \InvalidArgumentException("El mensaje del envío se encuentra vacío, valide nuevamente el archivo");  
        }else if($contador > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$contador} mensajes vacíos");
        }else if($contadornum == $count){
            sleep(1);
            throw new \InvalidArgumentException("Los números de teléfono del envío se encuentran vacío, valide nuevamente el archivo");  
        }else if($contadornum > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$contadornum} números de teléfonos vacíos");
        }else if($contadorind == $count){
            sleep(1);
            throw new \InvalidArgumentException("Los indicativos del envío se encuentran vacío, valide nuevamente el archivo");  
        }else if($contadorind > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$contadorind} indicativos vacíos");
        }elseif($countcaracter160 == $count){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$countcaracter160} mensajes con más de 160 carácteres");
        }elseif($countcaracter160 > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$countcaracter160} mensajes con más de 160 carácteres");
        }elseif($countcaracter300 == $count){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$countcaracter300} mensajes con más de 300 carácteres");
        }elseif($countcaracter300 > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$countcaracter300} mensajes con más de 300 carácteres");
        }
    }else{
        if($contador == $count){
            sleep(1);
            throw new \InvalidArgumentException("El mensaje del envío se encuentra vacío, valide nuevamente el archivo");  
        }else if($contador > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$contador} mensajes vacíos");
        }else if($contadornum == $count){
            sleep(1);
            throw new \InvalidArgumentException("Los números de teléfono del envío se encuentran vacío, valide nuevamente el archivo");  
        }else if($contadornum > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$contadornum} números de teléfonos vacíos");
        }else if($contadorind == $count){
            sleep(1);
            throw new \InvalidArgumentException("Los indicativos del envío se encuentran vacío, valide nuevamente el archivo");  
        }else if($contadorind > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$contadorind} indicativos vacíos");
        }elseif($countcaracter160 > 0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$countcaracter160} mensajes con más de 160 carácteres");
        }else if($contadorIndicativoInternacional >0){
            sleep(1);
            throw new \InvalidArgumentException("Ha ocurrido un error, el envío tiene {$contadorIndicativoInternacional} indicativos incorrectos del pais elegido");
        }
    }  
         
    $user = \Phalcon\DI::getDefault()->get('user');
    if($type == 'sms'){
      //Preload
      $elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
      $elephant->initialize();
      $elephant->emit('communication-php-node',['callback' =>'loading-csv-'.$type, 'data'=>['status' =>'preload','idSubaccount'=>$idSubaccount]]);

      $query = "LOAD DATA INFILE '{$dirNewCsv}' IGNORE INTO TABLE smslote FIELDS TERMINATED BY ';' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' STARTING BY '' (@value1, @value2, @value3) SET idSms = {$idSms}, idAdapter = null, indicative =@value1,phone = IF(@value2='',1595620341,REPLACE(@value2,' ','')),message = @value3,status = 'scheduled', created = ".time().", updated = ".time().", response = null, createdBy = '{$user->email}', updatedBy = '{$user->email}', messageCount = IF(CHAR_LENGTH(@value3) > 160,2,1)";

      $executeLoadData = \Phalcon\DI::getDefault()->get('db')->query($query);
      //VALIDACIONES
	  if ($executeLoadData->numRows() < 1) {
        throw new \InvalidArgumentException("Ha ocurrido un error cargando el archivo CSV");
      }
      $elephant->emit('communication-php-node',['callback' =>'loading-csv-'.$type, 'data'=>['status' =>'validations','idSubaccount'=>$idSubaccount,'data'=>['rowsCsv'=>$executeLoadData->numRows()]]]);
	  sleep(1);
	
      foreach ($allDataSmsFailed as $dataSmsFailed) {
        $SmsFailed = new \SmsFailed();
        $SmsFailed->idSms = $dataSmsFailed["idSms"];
        $SmsFailed->idContact = $dataSmsFailed["idContact"];
        $SmsFailed->indicative = $dataSmsFailed["indicative"];
        $SmsFailed->message = $dataSmsFailed["message"];
        $SmsFailed->phone = $dataSmsFailed["phone"];
        $SmsFailed->count = $dataSmsFailed["count"];
        $SmsFailed->detail = $dataSmsFailed["detail"];
        $SmsFailed->type = $dataSmsFailed["type"];

        if (!$SmsFailed->save()) {
          foreach ($SmsFailed->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
        }
      }
      $elephant->emit('communication-php-node',['callback' => 'loading-csv-'.$type, 'data'=> ['status' =>'load','idSubaccount'=>$idSubaccount,'data'=>['countTotal'=>$countTotalErrors,'countInvalid'=>$contadorIndicativoNacional,'countRepeat'=>$countValidateNumbers]]]); 

      //SE TERMINO MOSTRAR LOS DATOS
      if($type == "sms"){
        $smsMessage = \Smslote::findFirst(["conditions"=>"idSms = ?0","bind"=>[$idSms],"columns" => "message"]);
      }else{
        $smsMessage = \Smslotetwoway::findFirst(["conditions"=>"idSmsTwoway = ?0","bind"=>[$idSms],"columns" => "message"]);
      }
      $elephant->emit('communication-php-node',['callback' => 'loading-csv-'.$type,'data'=> ['status' =>'finish','idSubaccount'=>$idSubaccount,'id'=>$idSms,'message'=>$smsMessage->message,'data'=>['countSent'=>$executeLoadData->numRows()]]]);
      $this->deleteTmpCsv($idSms);
      $data = ["success" => $executeLoadData->numRows()];
      $elephant->close();
      return $data;

    }else{
    	//Preload
      $elephant = new \ElephantIO\Client(new \ElephantIO\Engine\SocketIO\Version1X('http://localhost:3000'));
      $elephant->initialize();
      $elephant->emit('communication-php-node',['callback' =>'loading-csv-'.$type, 'data'=>['status' =>'preload','idSubaccount'=>$idSubaccount]]);

      $query = "LOAD DATA INFILE '{$dirNewCsv}' IGNORE INTO TABLE tmp_sms_csv FIELDS TERMINATED BY ';' "
            . "OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' STARTING BY '' (@value1, @value2, @value3) "
            . "SET idSms = {$idSms}, indicative =@value1, message = @value3, status = '" . \Phalcon\DI::getDefault()->get('statusSms')->scheduled . "', "
            . "phone = IF(@value2='',".time().",REPLACE(@value2,' ','')), created = " . time() . ", updated = " . time() . ", createdBy = '{$user->email}', "
            . "updatedBy = '{$user->email}', type = '{$type}'";

      $executeLoadData = \Phalcon\DI::getDefault()->get('db')->query($query);
      if ($executeLoadData->numRows() < 1) {
        throw new \InvalidArgumentException("Ha ocurrido un error cargando el archivo CSV");
      }   
      
      sleep(1);
      //VALIDACIONES
      $elephant->emit('communication-php-node',['callback' =>'loading-csv-'.$type, 'data'=>['status' =>'validations','idSubaccount'=>$idSubaccount,'data'=>['rowsCsv'=>$executeLoadData->numRows()]]]);

      
      $smsFailed = $this->callSmsFailed($idSms);
      //SE ENVIA A NODE
      sleep(1);
      $elephant->emit('communication-php-node',['callback' => 'loading-csv-'.$type, 'data'=> ['status' =>'load','idSubaccount'=>$idSubaccount,'data'=>['countTotal'=>$smsFailed->countTotal,'countInvalid'=>$smsFailed->countInvalid,'countRepeat'=>$smsFailed->countRepeat]]]);
      //HACIENDO INSERCION EN SMS LOTE
      $executeQuery = \Phalcon\DI::getDefault()->get('db')->query($this->queryInsert($sms,$type));

      if($type == "sms"){
        $sms = \Smslote::findFirst(["conditions"=>"idSms = ?0","bind"=>[$idSms]]);
      }else{
        $sms = \Smslotetwoway::findFirst(["conditions"=>"idSmsTwoway = ?0","bind"=>[$idSms]]);
      }
      
      sleep(1);
      //SE TERMINO MOSTRAR LOS DATOS
      $elephant->emit('communication-php-node',['callback' => 'loading-csv-'.$type,'data'=> ['status' =>'finish','idSubaccount'=>$idSubaccount,'id'=>$idSms,'message'=>$sms->message,'data'=>['countSent'=>$executeQuery->numRows()]]]);
      $this->deleteTmpCsv($idSms);
      $data = ["success" => $executeQuery->numRows()];
      $elephant->close();
      return $data;
    }
  }

  
  public function callSmsFailed($idSms) {
    $query = \Phalcon\DI::getDefault()->get('db')->query("CALL smsfailed({$idSms})");
    /*if (!$query) {
      throw new \InvalidArgumentException("Ha ocurrido un error eliminando el espacio temporal del lote CSV");
    }*/
    
    return json_decode(json_encode($query->fetchArray()));
  }
  public function deleteTmpCsv($idSms) {
    if (!\Phalcon\DI::getDefault()->get('db')->query("DELETE FROM tmp_sms_csv WHERE idSms = {$idSms}")) {
      throw new \InvalidArgumentException("Ha ocurrido un error eliminando el espacio temporal del lote CSV");
    }
    return true;
  }

  public function queryInsert($sms,$type) {
    $user = \Phalcon\DI::getDefault()->get('user');
    //Campañas de SMS con mensaje menor o igual a 320 Caracteres (2sms por registro)
    /*$array = ['28','148','420','766','1270','1361','1367','1406'];
    if(in_array($user->Usertype->idSubaccount, $array)){ */
    //if($user->Usertype->idSubaccount == '420'){
    if($sms->morecaracter == 1){
      $query = "SELECT null, idSms, trim(indicative), trim(message), status, created, createdBy, trim(phone), updated, updatedBy, IF(CHAR_LENGTH(message) > 160,2,1) AS messageCount "
            . "FROM tmp_sms_csv "
            . "WHERE "
            . "indicative REGEXP '^[0-9]+$' "
            . "AND phone REGEXP '^[0-9]+$' "
            . "AND CHARACTER_LENGTH(message) <= 300 "
            . "AND idSms = {$sms->idSms}";
    }else{
      if($type == 'sms'){
        $query = "SELECT null, idSms, trim(indicative), SUBSTRING(message,1,160), status, created, createdBy, trim(phone), updated, updatedBy, 1 AS messageCount "
              . "FROM tmp_sms_csv "
              . "WHERE "
              . "indicative REGEXP '^[0-9]+$' "
              . "AND phone REGEXP '^[0-9]+$' "
              . "AND CHARACTER_LENGTH(message) <= 160 "  
              // . "AND SUBSTRING(message,1,160) "
              . "AND idSms = {$sms->idSms}";
      }else{
        
        $query = "SELECT null, idSms, trim(indicative), SUBSTRING(message,1,160), status, created, createdBy, trim(phone), updated, updatedBy "
              . "FROM tmp_sms_csv "
              . "WHERE "
              . "indicative REGEXP '^[0-9]+$' "
              . "AND phone REGEXP '^[0-9]+$' "
              . "AND CHARACTER_LENGTH(message) <= 160 "  
              // . "AND SUBSTRING(message,1,160) "
              . "AND idSms = {$this->idsms}";
      }        
    }
//    var_dump($query);
//    exit();
    if($type == 'sms'){
      $queryInsert = "INSERT INTO smslote (idSmslote, idSms, indicative, message, status, created, createdBy, phone, updated, updatedBy, messageCount) {$query}";
    }else{
      $queryInsert = "INSERT INTO smslotetwoway (idSmsLoteTwoway, idSmsTwoway, indicative, message, status, created, createdBy, phone, updated, updatedBy) {$query}";
    }
    
    return $queryInsert;
  }

  public function deleteFileCsv($route) {
    if (!unlink($route)) {
      throw new \InvalidArgumentException("Ha ocurrido un error eliminando el archivo CSV temporal");
    }
  }

  public function viewcsv($route, $destiny) {
    ini_set('auto_detect_line_endings', '1');
    $destiny = __DIR__. "/../../" . $destiny;
    $count = 0;
    $handle = fopen($route, "r");
    $tmp = fopen($destiny, "w");
    $line = array();
    $emails = array();
    //$line = array('', '', '', '', '');

    while ((($record = fgets($handle)) !== false)) {
      $l = trim($record);
        if (!empty($l)) {
          if (!mb_check_encoding($l, 'UTF-8')) {
            if (mb_check_encoding($l, 'ISO-8859-1')) {
              $l = mb_convert_encoding($l, 'UTF-8', 'ISO-8859-1');
              if ($count <= 4) {
                $line[$count] = str_replace('"', '\"', $l);
              }
            } else {
              throw new \InvalidArgumentException("Codificacion invalida en texto");
            }
          } else {
            if ($count <= 4) {
              $line[$count] = str_replace('"', '\"', $l);
            }
          }
          $explode = explode(";", $l);
          foreach ($explode as $value) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
              $emails[] = $value;
            }
          }
          unset($explode);
        }
      fwrite($tmp, $l . "\n");
      $count++;
    }
    fclose($handle);
    fclose($tmp);
    $result = array(
        "rows" => $count,
        "countContact" => count($emails),
        "arraytemp" => $line
    );
    return $result;
  }
  
  private function consultBalance($idSubaccount){
    $saxs = \Saxs::findFirst(array(
        "columns" => "amount",
        "conditions" => "idSubaccount = ?0 AND idServices = ?1",
        "bind" => array($idSubaccount, \Phalcon\DI::getDefault()->get('services')->sms)
    ));
    
    return $saxs->amount;
  }
  
  public function setidSms($idsms){
    $this->idsms = $idsms;
  }
}
