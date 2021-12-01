<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\Wrapper;

class SmstwowayWrapper extends \BaseWrapper {

  protected $hoursms;

  public function initialize() {
    $this->hoursms = new \stdClass();
    $this->hoursms->startHour = $this->user->Usertype->Subaccount->Account->hourInit;
    $this->hoursms->endHour = $this->user->Usertype->Subaccount->Account->hourEnd;
  }

  public function findOneSms($idSms) {
    $this->sms = \Sms::findFirst(["conditions" => "idSms = ?0", "bind" => [0 => $idSms]]);
    $this->modelOneSms();
  }

  public function getallsmstwoway($page, $data) {
    $where = " ";


    if (isset($data['name']) && $data['name'] != "") {
      $where .= " AND smstwoway.name LIKE '%{$data['name']}%' ";
    }

    if (isset($data['category']) && count($data['category']) >= 1) {
      $arr = implode(",", $data['category']);
      $where .= "  AND smstwoway.idSmsCategory IN ({$arr})";
    }

    if (isset($data['dateinitial']) && isset($data['dateend'])) {
      if ($data['dateinitial'] != "" && $data['dateend'] != "") {
        if (strtotime($data['dateinitial']) > strtotime($data['dateend'])) {
          throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la final. ');
        }
        $where .= " AND startdate BETWEEN '{$data['dateinitial']}' AND '{$data['dateend']}'";
      }
    }

    if ((isset($data['dateinitial']) && $data['dateinitial'] != "") && (!isset($data['dateend']) && $data['dateend'] == "")) {
      if ($data['dateinitial'] > date('Y-m-d')) {
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la actual.');
      }
      $where .= " AND startdate BETWEEN '{$data['dateinitial']}' AND Date_format(now(),'%Y/%m/%d')";
    }



    if ((isset($data['dateinitial']) && !empty($data['dateinitial'])) && (isset($data['dateend']) && !empty($data['dateend']))) {
      if ($data['dateinitial'] > $data['dateend']) {
        return \InvalidArgumentException('La fecha inicial es mayor por favor cambie el rango.');
      }
      $startDate = strtotime($data['dateinitial']);
      $finalDate = strtotime($data['dateend']);
      $where .= " AND created  BETWEEN '{$startDate}' AND '{$finalDate}'";
    }


    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");
    $idSubaccount = \Phalcon\DI::getDefault()->get('user')->Usertype->idSubaccount;
    $limit = \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    $sql = "SELECT * from smstwoway "
            . " WHERE smstwoway.idSubaccount = {$idSubaccount} AND smstwoway.logicodeleted = 0 {$where}"
            . " ORDER BY idSmsTwoway DESC "
            . "  LIMIT {$limit} "
            . " OFFSET {$page}";
    $sql2 = "SELECT smstwoway.idSmsTwoway FROM smstwoway "
            . " WHERE smstwoway.idSubaccount = {$idSubaccount} AND smstwoway.logicodeleted = 0 {$where}";
    //. " GROUP BY smstwoway.idSmsTwoway ";

    $data = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    $totals = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql2);

    return $this->modelData($data, $totals);
  }

  public function createSmsLotetwoway($data) {

    $smstwowaycontroller = new \SmstwowayController();
    $flag = false;
    $amount = 0;
    /*
     * Validacion de ammount
     */
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms_two_way && ($key->status == 1 || $key->status == '1')) {
        $flag = true;
        $amount = $key->amount;
      }
    }
    if ($flag == false) {
      throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
    }
    if (($amount == 0 || $amount < 0) && $flag == true) {
      throw new \InvalidArgumentException("No tienes capacidad para enviar más SMS doble via");
    }

    /*
     * Set Fields 
     */
    $idSmsTwoway = ((isset($data["idSmsTwoway"])) ? $data["idSmsTwoway"] : NULL);
    $idSmsCategory = $data["category"];

    $dataname = $data["name"];
    $typeResponse = $data["response"];
    $notification = ((isset($data["sendNotification"])) ? $data["sendNotification"] : 0);
    $advancedOptions = ((isset($data["optionsAvanced"])) ? $data["optionsAvanced"] : 0);
    $dateNow = ((isset($data["sentNow"])) ? $data["sentNow"] : 0);
    $divide = ((isset($data["divideSending"])) ? $data["divideSending"] : 0);

    /**
     * Validation Fields
     */
    if (!$dateNow) {
      $gmt = $data["gmt"];
      $originalDate = $data['dtpicker'];
    }

    if ($advancedOptions) {
      if ($notification == true) {

        $email = $data["emailNotification"];
      }
      if ($divide) {
        $sendingTime = (isset($data["sendingTime"]) && !empty($data["sendingTime"])) ? $data["sendingTime"] : null;
        $quantity = (isset($data["quantity"]) && !empty($data["quantity"])) ? $data["quantity"] : null;
        $timeFormat = (isset($data["timeFormat"]) && !empty($data["timeFormat"])) ? $data["timeFormat"] : null;
      }
    }

    $receiver = explode("\n", trim($data["receiver"]));

    /*
     * Validation ammount to receiver
     */
    if (empty($receiver[0])) {
      throw new \InvalidArgumentException("Debes agregar al menos un destinatario");
    }

    if ((count($receiver) > $amount)&& $flag == true) {
      if(abs($amount)){
        $tAvailable = (object) ["totalAvailable" => 0];
      } else {
        $tAvailable = (object) ["totalAvailable" => $amount];
      }
      $this->sendmailnotsmsbalance($tAvailable);
      throw new \InvalidArgumentException("Solo puedes hacer " . $tAvailable->totalAvailable . " envío(s) de sms. Si nesesitas más saldo contacta al administrador");
    }
    
    //Se realiza validaciones de los sms programados
    $balance = $this->validateBalance();
    $target = 0;
    if($balance['smsFindPending']){
      foreach ($balance['smsFindPending'] as $value){
        $target = $target + $value['target'];
      }
    }
    $scheduled = $target;
    $amountSms = $balance['balanceConsumedFind'][0]['amount'];
    unset($balance);
    $totalTarget =  $amountSms - $target;
    $target = $target + count($data["receiver"]);
    if(($target>$amount) && $flag == true){
      $target = $target - $amountSms;
      if(abs($totalTarget)){
        $tAvailable = (object) ["totalAvailable" => 0];
      } else {
        $tAvailable = (object) ["totalAvailable" => $totalTarget];
      }
      $this->sendmailnotsmsbalance($tAvailable);
      throw new \InvalidArgumentException("No tiene saldo disponible para realizar este Sms!, {'amount':".$tAvailable->totalAvailable.", 'missing':" .$target.", 'scheduled':" .$scheduled.", 'totalAmount':".$this->arraySaxs['totalAmount'].",'subaccountName':".$this->arraySaxs['subaccountName'].", 'accountName':".$this->arraySaxs['accountName']."}");
    }
    unset($target);
    unset($scheduled);
    unset($amountSms);
    unset($totalTarget);
    unset($tAvailable);

    /*
     * Set Model SmsTwoWay
     */

    $smsTwoWay = new \Smstwoway();
    if ($idSmsTwoway) {
      $smsTwoWay = \Smstwoway::findFirst(array(
                  "conditions" => "idSmsTwoway = ?0",
                  "bind" => array($idSmsTwoway)
      ));
    }

    //OBLIGATORIOS
    if ($idSmsTwoway) {
      $smsTwoWay->idSmsTwoway = $idSmsTwoway;
    }


    $smsTwoWay->target = count($receiver);
    $smsTwoWay->idSmsCategory = $idSmsCategory;
    $smsTwoWay->idSubaccount = $this->user->Usertype->idSubaccount;
    $smsTwoWay->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $smsTwoWay->name = $dataname;
    //SENTNOW
    $smsTwoWay->dateNow = $dateNow;
    if (!$dateNow) {
      if (isset($originalDate) && isset($gmt)) {
        if (strtotime($originalDate) < strtotime("now") && !$datenow) {
          throw new \InvalidArgumentException("No se debe asignar fecha y hora del pasado.");
        }
        $dateStart = $smstwowaycontroller->validateDate($originalDate, $gmt);
        $smsTwoWay->originalDate = $originalDate;
        $smsTwoWay->startdate = $dateStart;
        $smsTwoWay->gmt = $gmt;
      } else {
        throw new \InvalidArgumentException("La fecha de envío o la zona horaria no aparecen registrados.");
      }
    } else {
      $dateStart = date('Y-m-d G:i:s', time());
      $smsTwoWay->originalDate = $dateStart;
      $smsTwoWay->startdate = $dateStart;
    }

    $smsTwoWay->notification = $notification;
    $smsTwoWay->divide = $divide;
    $smsTwoWay->advancedoptions = $advancedOptions;

    //ADVANCED

    if ($advancedOptions) {
      if ($notification == true) {
        $smsTwoWay->email = $email;
      }
      if ($divide) {
        $smsTwoWay->sendingTime = $sendingTime;
        $smsTwoWay->quantity = $quantity;
        $smsTwoWay->timeFormat = $timeFormat;
      }
    }

    //NOTIFICATION
    $smsTwoWay->typeResponse = $typeResponse;
    $smsTwoWay->receiver = $data["receiver"];
    $smsTwoWay->confirm = 1;
    $smsTwoWay->logicodeleted = 0;
    $smsTwoWay->type = \Phalcon\DI::getDefault()->get("typeSms")->lote;
    $smsTwoWay->total = count($receiver);


    if (!$smsTwoWay->save()) {
      foreach ($smsTwoWay->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $count = 0;

    if ($idSmsTwoway) {
      $response = $this->db->query("DELETE FROM smslotetwoway WHERE idSmsTwoway = {$idSmsTwoway}");
    }
    foreach ($receiver as $key) {
      $arr = explode(";", $key);
      $flag = true;

      if (strstr($arr[0], "+")) {
        $flag = false;
      }
      if (strlen(trim($arr[1])) != 10 || !is_numeric($arr[1])) {
        $flag = false;
      }
      if (strlen(str_replace(" ", "", trim($arr[2]))) > 160) {
        $flag = false;
      }

      if (count($arr) == 3 && $flag) {
        $smsLoteTwoWay = new \Smslotetwoway();
        $smsLoteTwoWay->idSmsTwoway = $smsTwoWay->idSmsTwoway;
        $smsLoteTwoWay->idAdapter = 3;
        $smsLoteTwoWay->indicative = $arr[0];
        $smsLoteTwoWay->phone = trim($arr[1]);
        $smsLoteTwoWay->message = trim($arr[2]);
        $smsLoteTwoWay->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
        if (!$smsLoteTwoWay->save()) {
          foreach ($smsLoteTwoWay->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        $count++;
      }
    }

    if ($count == 0) {
      throw new \InvalidArgumentException("El envío debe contener al menos un destinatario valido");
    }
    if (($count * 2) > (int) $amount) {
      throw new \InvalidArgumentException("Debe contar con el doble de saldo disponible para este envio.");
    }

    return ["message" => "Se ha creado el lote de sms!", "sms" => $smsTwoWay];
  }

  public function editSmstwowaySend($data) {

    $idSmsTwoway = $data['idSmsTwoway']; //entregando el id;

    $Smstwoway = \Smstwoway::findFirst(array(
                "conditions" => "idSmsTwoway = ?0",
                "bind" => array($idSmsTwoway)));

    $flag = false;
    $amount = 10; //puesto para pruebas

    /*
     * Validacion de ammount
     */
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms_two_way) {
        $flag = true;
        $amount = $key->amount;
      }
    }

    if ($flag == false) {
      throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
    }
    if ($amount == 0) {
      throw new \InvalidArgumentException("No tienes capacidad para enviar más sms");
    }

    /*
     * Set Fields 
     */
    $idSmsCategory = $data["idSmsCategory"];
    $dataname = $data["name"];
    $typeResponse = $data["typeResponse"];
    $notification = $data["notification"];
    $advancedOptions = $data["advancedoptions"];
    //$dateNow = date('Y-m-d G:i:s', time()); $data["sentNow"];
    $dateNow = $data["sent"];

    //$data["datesend"] = date('Y-m-d G:i:s', time());

    $divide = $data["divide"];

    /**
     * Validation Fields
     */
    if (!$dateNow) {
      $gmt = $data["gmt"];
      $originalDate = $data['originalDate'];
    }

    if ($advancedOptions) {
      if ($notification) {
        $email = $data["emailNotification"];
      }
      if ($divide) {
        $sendingTime = (isset($data["sendingTime"]) && !empty($data["sendingTime"])) ? $data["sendingTime"] : null;
        $quantity = (isset($data["quantity"]) && !empty($data["quantity"])) ? $data["quantity"] : null;
        $timeFormat = (isset($data["timeFormat"]) && !empty($data["timeFormat"])) ? $data["timeFormat"] : null;
      }
    }

    //$receiver = json_encode($data['receiver']);

    $receiver = explode(";", trim($data["receiver"]));
//    $receiver = json_encode($data['receiver']);
//    $receiver = json_decode($receiver);

    /**
     * Validation Fields of a speedsent message...
     */
    if (empty($receiver[0])) {
      throw new \InvalidArgumentException("Debes agregar al menos un indicativo de Pais");
    }
    if (empty($receiver[1])) {
      throw new \InvalidArgumentException("Debes agregar al menos un destinatario");
    }
    if (empty($receiver[2])) {
      throw new \InvalidArgumentException("Debes agregar al menos un mensaje");
    }

    //validation on the amount of messages
    if (count($receiver) > $amount) {
      throw new \InvalidArgumentException("Solo puedes hacer " . $amount . " envío(s) de sms. Si nesesitas más saldo contacta al administrador");
    }

    /*
     * Set Model SmsTwoWay
     */

    //$smsTwoWay = new \Smstwoway();
    //OBLIGATORIOS
    $Smstwoway->target = count($receiver);
    $Smstwoway->idSmsCategory = $idSmsCategory;
    $Smstwoway->idSubaccount = $this->user->Usertype->idSubaccount;
    $Smstwoway->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $Smstwoway->name = $dataname;

    //SENTNOW
    $Smstwoway->dateNow = $dateNow;

    if (!$dateNow) {
      if (isset($originalDate) && isset($gmt)) {

        $smsTwoWayController = new \SmstwowayController();

        $dateStart = $smsTwoWayController->validateDate($originalDate, $gmt);

        $Smstwoway->originalDate = $originalDate;
        $Smstwoway->startdate = $dateStart;
        $Smstwoway->gmt = $gmt;
      } else {
        throw new \InvalidArgumentException("La fecha de envio o el gmt parecen no registrados.");
      }
    } else {
      $dateStart = date('Y-m-d G:i:s', time());

      $Smstwoway->originalDate = $dateStart;
      $Smstwoway->startdate = $dateStart;
    }

    $Smstwoway->notification = $notification;
    $Smstwoway->divide = $divide;
    $Smstwoway->advancedoptions = $advancedOptions;

    //ADVANCED
    if (!$advancedOptions) {
      if ($notification) {
        /*
         * emails notificacion Set format
         */
//        if (isset($email)) {
//          $emailNotification = explode(",", trim($email));
//          if (count($emailNotification) > 8) {
//            throw new \InvalidArgumentException("El tope maximo de correos es 8");
//          }
//          for ($i = 0; $i < count($emailNotification); $i++) {
//            if (!filter_var($emailNotification[$i], FILTER_VALIDATE_EMAIL)) {
//              throw new \InvalidArgumentException("El formato del correo {$emailNotification[$i]} no es valido.");
//            }
//          }
//        }

        $smsTwoWay->email = $emailNotification;
      }
      if ($divide) {
        $Smstwoway->sendingTime = $sendingTime;
        $Smstwoway->quantity = $quantity;
        $Smstwoway->timeFormat = $timeFormat;
      }
    }

    //NOTIFICATION
    $Smstwoway->typeResponse = $typeResponse;
    $Smstwoway->receiver = $data["receiver"];

    $Smstwoway->confirm = 1;
    $Smstwoway->logicodelete = 0;
    $Smstwoway->type = \Phalcon\DI::getDefault()->get("typeSms")->lote;
    $Smstwoway->total = count($receiver);

    \Phalcon\DI::getDefault()->get("db")->begin();

    if (!$Smstwoway->save()) {

      \Phalcon\DI::getDefault()->get("db")->rollback();
      foreach ($Smstwoway->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $count = 1;

    foreach ($receiver as $key) {

      $arr = explode(";", $key);
      $flag = true;

      if (strstr($arr[0], "+")) {
        $flag = false;
      }
      if (strlen(trim($arr[1])) != 10 || !is_numeric($arr[1])) {
        $flag = false;
      }
      if (strlen(trim($arr[2])) > 160) {
        $flag = false;
      }
      if (preg_match("/[ñÑáéíóúÁÉÍÓÚ¿¡´]/", $arr[2])) {
        $flag = false;
      }

      if (count($arr) == 3 && $flag) {
        $smsLoteTwoWay = new \Smslotetwoway();
        $smsLoteTwoWay->idSmsTwoway = $smsTwoWay->idSmsTwoway;
        $smsLoteTwoWay->idAdapter = 3;
        $smsLoteTwoWay->indicative = $arr[0];
        $smsLoteTwoWay->phone = trim($arr[1]);
        $smsLoteTwoWay->message = trim($arr[2]);
        $smsLoteTwoWay->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
        if (!$smsLoteTwoWay->save()) {
          \Phalcon\DI::getDefault()->get("db")->rollback();
          foreach ($smsLoteTwoWay->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
        $count++;
      }
    }
    if ($count == 0) {
      \Phalcon\DI::getDefault()->get("db")->rollback();
      throw new \InvalidArgumentException("El envío debe contener al menos un destinatario valido");
    }

    \Phalcon\DI::getDefault()->get("db")->commit();
    return ["message" => "Se ha creado el lote de sms!", "sms" => $smsTwoWay];
  }

  public function getInfo($idSmsTwoway) {
    $arrLote = array();
    $limit = 1000;
    $offset = 0;
    $flag = true;
    $smstwoway = \Smstwoway::findFirst(array("conditions" => "idSmsTwoway = ?0 ", "bind" => array($idSmsTwoway)));
    if (!$smstwoway) {
      throw new \InvalidArgumentException("El sms de doble via que intenta consultar no existe");
    }

    $arrLote = \Smslotetwoway::find(array("conditions" => " idSmsTwoway = ?0 ", "bind" => array($idSmsTwoway), "columns" => "count(IFNULL(userResponseGroup,0)) as count,userResponseGroup", "group" => "userResponseGroup"));

    return $arrLote->toArray();
  }

  public function getDetail($idSmsTwoway, $page, $filter) {
    $arrReturn = array();
    $smstwoway = \Smstwoway::findFirst(array("conditions" => "idSmsTwoway = ?0 ", "bind" => array($idSmsTwoway)));
    if (!$smstwoway) {
      throw new \InvalidArgumentException("El sms de doble via que intenta consultar no existe");
    }

    $where = "";

    if (isset($filter) && $filter != "") {
      $where = "AND phone LIKE '%" . $filter . "%'";
    }

    $smsLoteCount = \Smslotetwoway::count(array("conditions" => "idSmsTwoway = ?0 ", "bind" => array($idSmsTwoway)));
    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");

    $conditions = array(
        "conditions" => "idSmsTwoway = ?0 " . $where,
        "bind" => array($idSmsTwoway),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC",
    );

    $smsLote = \Smslotetwoway::find($conditions);
    $data = array();
    if (count($smsLote) > 0) {
      foreach ($smsLote as $key => $value) {
        $data[$key] = array(
            "idSmsLoteTwoway" => $value->idSmsLoteTwoway,
            "indicative" => $value->indicative,
            "message" => $value->message,
            "phone" => $value->phone,
            "response" => $value->response,
            "userResponse" => $value->userResponse,
            "group" => $value->userResponseGroup,
            "status" => $value->status,
            $date = new \DateTime("@$value->updated"),
            "date" => $date->format('Y-m-d H:i:s'),
            "response" => $this->getResponses($value->idSmsLoteTwoway)
        );
      }
    }

    $arrReturn["data"] = $data;
    $arrReturn["detail"] = array("total" => $smsLoteCount, "total_pages" => ceil($smsLoteCount / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));

    return $arrReturn;
  }

  public function getResponses($id) {

    $smsLoteTwoway = \Smslotetwoway::findFirst(array("conditions" => "idSmsLoteTwoway = ?0", "bind" => array($id)));
    //var_dump($smsLoteTwoway);
    $receiverTable = \Receiversms::findFirst(array(
                "conditions" => array(
                    "idSmslote" => (string) $smsLoteTwoway->idSmsLoteTwoway
                )
    ));

    $arrayInfo = array();

    //for para que contruya el arreglo de valores en posiciones invertidas
    foreach (array_reverse($receiverTable->dataReceiver) as $key) {
      $arrayInfo[] = [
          "dateRegister" => $key['dateRegister'],
          "receiver" => $key['receiver'],
          "group" => $key['group']
      ];
    }

    /* para borrar la primera respuesta 
     * que actualmente ya se muestra 
     * en la fila uno de la tabla */
    unset($arrayInfo[0]);

    //for para que contruya el arreglo de valores 
    //en posiciones invertidas sin la primera posicion
    foreach ($arrayInfo as $key) {
      $arrayInfo2[] = [
          "dateRegister" => $key['dateRegister'],
          "receiver" => $key['receiver'],
          "group" => $key['group']
      ];
    }

    unset($arrayInfo); //destryendo el primer arreglo;
    return $arrayInfo2;
  }

  public function homologate($homo, $response) {
    $homoJson = json_decode($homo);
    $stringReturn = "Otro";
    for ($i = 0; $i < count($homoJson->typeResponse); $i++) {
      $selection = $homoJson->typeResponse[$i]->response;
      $homologate = array_map('strtolower', explode(",", $homoJson->typeResponse[$i]->homologate));
      if (strtolower($selection) == strtolower($response) || in_array(strtolower($response), $homologate)) {
        return $selection;
      }
    }
    return $stringReturn;
  }

  public function getSms($idSmsTwoway) {
    $smsTwoWay = \Smstwoway::findFirst(array("conditions" => "idSmsTwoway = ?0", "bind" => array($idSmsTwoway)));
    if (!$smsTwoWay) {
      return false;
    }
    //SE VALIDA QUE LA COMPARACIÓN DE FECHA SEA CORRECTA, NO SE PUEDE EDITAR SMS QUE LE QUEDAN MENOS DE 10 MINUTOS PARA ENVIARSE
    $now = new \DateTime("now");
    $startDate = new \DateTime($smsTwoWay->startdate);
    $conditions = new \stdClass();
    $conditions->i = 11;
    $conditions->y = 0;
    $conditions->m = 0;
    $conditions->d = 0;
    $isValid = $this->validateDiffDate($now, $startDate, $conditions);
//    if(!$isValid){
//      throw new \InvalidArgumentException("La fecha de programación de envio debe ser mayor a 10 minutos.");
//    }
    $data = get_object_vars($smsTwoWay);
    $data["category"] = $smsTwoWay->Smscategory->name;
    return $data;
  }

  public function getSmsUnique($data) {
    $smsTwoWay = \Smstwoway::findFirst(array("conditions" => "idSmsTwoway = ?0", "bind" => array($idSmsTwoway)));
    if (!$smsTwoWay) {
      return false;
    }

    //return ["name" => $smsTwoWay->name, "category" => $smsTwoWay->Smscategory->name, "startdate" => $smsTwoWay->startdate, "target" => $smsTwoWay->total];
    //return $this->modelData($smsTwoWay);
    //return array("prueba"=>"pruebita");
    //$arr = array();s
    foreach ($smsTwoWay as $key => $value) {
      $smsTwoWay[$key] = array("idSmsTwoway" => $value['idSmsTwoway'],
          "idSmsCategory" => $value['idSmsCategory'],
          "idSubaccount" => $value['idSubaccount'],
          "logicodeleted" => $value['logicodeleted'],
          "notification" => $value['notification'],
          "status" => $value['status'],
          "type" => $value['type'],
          "created" => date('d/m/Y g:i a', $value['created']),
          "updated" => date('d/m/Y g:i a', $value['updated']),
          "createdBy" => $value['createdBy'],
          "updatedBy" => $value['updatedBy'],
          "sent" => $value['sent'],
          "total" => $value['total'],
          "target" => $value['target'],
          "startdate" => $value['startdate'],
          "email" => $value['email'],
          "name" => $value['name']);
    }
    $arrReturn = json_encode($smsTwoWay);
    return $arrReturn;
  }

  public function modelData($data, $totals) {
    $arrReturn = array("total" => count($totals), "total_pages" => ceil(count($totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($data as $key => $value) {
      $arr[$key] = array("idSmsTwoway" => $value['idSmsTwoway'],
          "idSmsCategory" => $value['idSmsCategory'],
          "idSubaccount" => $value['idSubaccount'],
          "logicodeleted" => $value['logicodeleted'],
          "notification" => $value['notification'],
          "status" => $value['status'],
          "type" => $value['type'],
          "created" => date('d/m/Y g:i a', $value['created']),
          "updated" => date('d/m/Y g:i a', $value['updated']),
          "createdBy" => $value['createdBy'],
          "updatedBy" => $value['updatedBy'],
          "sent" => $value['sent'],
          "total" => $value['total'],
          "target" => $value['target'],
          "startdate" => $value['startdate'],
          "email" => (empty($value['email'])) ? false : $value['email'],
          "name" => $value['name']);
    }
    $arrReturn["items"] = $arr;
    return $arrReturn;
  }

  public function sanetizePost($post) {
    foreach ($post as $key => $value) {
      if ($value == 'true' || $value == 'false') {
        $post[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
      }
    }
    return $post;
  }

  public function changeStatus($idSmsTwoway, $status) {
    $Smstwoway = \Smstwoway::findFirst(array(
                "conditions" => "idSmsTwoway = ?0",
                "bind" => array(0 => $idSmsTwoway)
    ));
    if (!$Smstwoway) {
      throw new \InvalidArgumentException("El envio de sms no se encontró, por favor valida la información.");
    }
    if ($status == 'scheduled') {
      $Smstwoway->confirm = 1;
    }
    $Smstwoway->status = $status;
    if (!$Smstwoway->save()) {
      foreach ($smstwoway->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
      $this->trace("fail", "No se logro crear el smslote {$message}");
    }
    return array("message" => "Se cambio el estado correctamente.");
  }

  /**
   * 
   * @author Jordan Zapata Mora
   * @param type $data recibe la data que se envia desde el formulario de smstwowaycontac
   */
  public function getDataSaveContact($data) {
    $this->SmstwowayData = $data;
  }

  public function validateDatasmstwoway($data) {

//    if ($data['name'] == "") {
//      $this->notification->info("Es obligatorio el nombre del envío");
//      return $this->response->redirect("");
//    }
//    if ($data['idSmsCategory'] == "") {
//      $this->notification->info("Debe elegir una categoría");
//      return $this->response->redirect("");
//    }
//    if ($data['notification'] and $data['email'] == "") {
//      $this->notification->info("Debe indicar al menos una dirección de notificación");
//      return $this->response->redirect("");
//    }
//    if ($data['divide']) {
//      if (!isset($data['quantity']) or $data['quantity'] < 1) {
//        $this->notification->info("Debe indicar una cantidad de envíos por intervalo mayor a 1");
//        return $this->response->redirect("");
//      }
//      if (!isset($data['sendingTime'])) {
//        $this->notification->info("Debe elegir un tiempo de envio correcto");
//        return $this->response->redirect("");
//      }
//      if (!isset($data['timeFormat'])) {
//        $this->notification->info("Debe elegir un formato de tiempo correcto");
//        return $this->response->redirect("");
//      }
//    }
//    }
  }

  public function validateDateSending($date, $timezone) {
    if (!isset($this->hoursms) || empty($this->hoursms)) {
      $this->hoursms = new \stdClass();
      $this->hoursms->startHour = $this->user->Usertype->Subaccount->Account->hourInit;
      $this->hoursms->endHour = $this->user->Usertype->Subaccount->Account->hourEnd;
    }
    $timezone = substr($timezone, 0, 3);
    if ($timezone[1] == 0) {
      $typeGmt = substr($timezone, 0, 1);
      $timezone = substr($timezone, 2, 2);
    }
    if ($typeGmt == "-") {
      if ($timezone > 5) {
        $timezone = $timezone - 5;
      } else {
        $typeGmt = "+";
        $timezone = 5 - $timezone;
      }
    } else if ($typeGmt == "+") {
      $timezone = 5 + $timezone;
    }
    $datenowstr = strtotime("{$typeGmt}{$timezone} hour", strtotime($date));
    $dateStart = date("Y-m-d H:i:s", $datenowstr);
    $hour = date("H", $datenowstr);

//    var_dump($hour,$this->hoursms->startHour,$this->hoursms->endHour);
//    exit();
    if ($hour < $this->hoursms->startHour || $hour >= $this->hoursms->endHour) {
      throw new InvalidArgumentException("La hora de envio debe de ser entre las " . $this->hoursms->startHour . ":00  y las " . $this->hoursms->endHour . ":00 de acuerdo al GMT seleccionado");
    }
    return $dateStart;
  }

  /**
   * 
   * @author Jordan Zapata Mora
   * @return retorna el mensaje de confirmacion del proceso de creacion de smstwowaycontact
   */
  public function saveSmstwowayContact() {
    $data = $this->SmstwowayData;

    $flag = false;
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms_two_way) {
        $flag = true;
        $amount = $key->amount;
      }
    }
    if ($flag == false) {
      $this->notification->info("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
      return $this->response->redirect("");
    }
    if ($amount == 0) {
      $this->notification->info("No tienes capacidad para enviar más sms");
      return $this->response->redirect("sms");
    }

    if (!empty($this->SmstwowayData)) {

      $this->validateDatasmstwoway($this->SmstwowayData);
      $smstwoway = new \Smstwoway();

      if (isset($this->SmstwowayData["idSmsTwoway"])) {
        $smstwoway = \Smstwoway::findFirst(array(
                    "conditions" => "idSmsTwoway = ?0", "bind" => array(
                        $this->SmstwowayData["idSmsTwoway"]
                    )
        ));

        if ($smstwoway) {
          $smstwoway->idSmsCategory = $this->SmstwowayData["idSmsCategory"];
        }
      }

      //datos basicos
      $smstwoway->name = $this->SmstwowayData["name"];
      $smstwoway->type = "contact";
      $smstwoway->confirm = 1;
      $smstwoway->receiver = json_encode($this->SmstwowayData["receiver"]);
      $smstwoway->message = $this->SmstwowayData["message"];
      $smstwoway->typeResponse = json_encode($this->SmstwowayData["typeResponse"]);
      $smstwoway->idSubaccount = $this->user->Usertype->idSubaccount;
      $smstwoway->status = "scheduled";
      $smstwoway->idSmsCategory = $this->SmstwowayData["idSmsCategory"];
      $smstwoway->target = $this->SmstwowayData["target"];


//      //enviar ahora mismo
      if ($this->SmstwowayData["sentNow"] == false) {

        $smstwoway->dateNow = 0;
        $smstwoway->gmt = $this->SmstwowayData["timezone"];
        $smstwoway->startdate = $this->SmstwowayData["originalDate"];
      } else {
        $smstwoway->dateNow = 1;
        $smstwoway->startdate = date('Y-m-d G:i:s', time());
        $smstwoway->gmt = null;
      }
//      //opciones avanzadas
      if (isset($this->SmstwowayData["advancedoptions"])) {
        if ($this->SmstwowayData["advancedoptions"] == true) {
          $smstwoway->advancedoptions = 1;
          //Enviar notificacion
          if ($this->SmstwowayData["notification"] == true) {

            $smstwoway->notification = 1;
            $smstwoway->email = $this->SmstwowayData["email"];
          } else {
            $smstwoway->notification = 0;
          }
          //Particionar el envío
          if ($this->SmstwowayData["divide"] == true) {
            $smstwoway->divide = 1;
            $smstwoway->quantity = $this->SmstwowayData["quantity"];
            $smstwoway->sendingTime = $this->SmstwowayData["sendingTime"];
            $smstwoway->timeFormat = $this->SmstwowayData["timeFormat"];
          } else {
            $smstwoway->divide = 0;
          }
        } else {
          $smstwoway->advancedoptions = 0;
          $smstwoway->notification = 0;
          $smstwoway->divide = 0;
          $smstwoway->email = null;
          $smstwoway->quantity = null;
          $smstwoway->sendingTime = null;
          $smstwoway->timeFormat = null;
        }
      } else {
        $smstwoway->advancedoptions = 0;
        $smstwoway->notification = 0;
        $smstwoway->divide = 0;
        $smstwoway->email = null;
        $smstwoway->quantity = null;
        $smstwoway->sendingTime = null;
        $smstwoway->timeFormat = null;
      }
//      $datenow = $data['sentNow'];
//      $timezone = $data['timezone'];
//      if ($datenow) {
//        $data["originalDate"] = date('Y-m-d G:i:s', time());
//        $timezone = "-0500";
//      }
//      if (strtotime($data["originalDate"]) < strtotime("now") && !$datenow) {
////        throw new InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
//      }
//      $dateStart = $this->validateDateSending($data["originalDate"], $timezone);
//      if (isset($data["email"])) {
//        $emailNotification = explode(",", trim($data["email"]));
//        if (count($emailNotification) > 8) {
//          throw new \InvalidArgumentException("El tope maximo de correos es 8");
//        }
//        for ($i = 0; $i < count($emailNotification); $i++) {
//          if (!filter_var($emailNotification[$i], FILTER_VALIDATE_EMAIL)) {
//            throw new \InvalidArgumentException("El formato del correo {$emailNotification[$i]} no es valido.");
//          }
//        }
//      }
//      $smstwoway->email = $data["email"];
//      if ($data["advancedoptions"]) {
//        if ($data["divide"]) {
//          if (!isset($data["quantity"]) or $data["quantity"] == 0) {
//            throw new InvalidArgumentException("Debes indicar una cantidad correcta");
//          }
//          if (!isset($data["sendingTime"]) or $data["sendingTime"] == "") {
//            throw new InvalidArgumentException("Debes elegir un tiempo de envío");
//          }
//          if (!isset($data["timeFormat"]) or $data["timeFormat"] == "") {
//            throw new InvalidArgumentException("Debes elegir un formato de tiempo");
//          }
//          $smstwoway->divide = 1;
//          $smstwoway->sendingTime = $data["sendingTime"];
//          $smstwoway->timeFormat = $data["timeFormat"];
//        }
//      }
//
//      if (!isset($data['receiver'])) {
//        throw new InvalidArgumentException("Debes agregar al menos un destinatario");
//      }
//      if ($data['target'] > $amount) {
//        throw new InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de sms si nesesitas más saldo contacta al administrador");
//      }
//
////      $smsloteform->bind($data, $sms);
////        if (!$smsloteform->isValid()) {
////          foreach ($smsloteform->getMessages() as $msg) {
////            throw new \InvalidArgumentException($msg);
////          }
////        }
//
//      $smstwoway->advancedoptions = 0;
//      if ($data["advancedoptions"] AND ( $data["notification"] || $data["divide"])) {
//        $smstwoway->advancedoptions = 1;
//      }
//      (($datenow) ? $smstwoway->startdate = date('Y-m-d H:i:s', time()) : "");
//      
//      $smstwoway->name = $data["name"];
//      $smstwoway->target = $data['target'];
//      $smstwoway->idSubaccount = $this->user->Usertype->idSubaccount;
//      $smstwoway->idSmsCategory = $data['idSmsCategory'];
//      $smstwoway->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
//      $smstwoway->confirm = 1;
//      $smstwoway->logicodeleted = 0;
//      $smstwoway->type = \Phalcon\DI::getDefault()->get("typeSms")->contact;
//      $smstwoway->startdate = $dateStart;
//      $smstwoway->receiver = json_encode($data['receiver']);
//      $smstwoway->message = $data['message'];
//      $smstwoway->typeResponse = json_encode($data["typeResponse"]);
////        $sms->sent = $data["AproximateSendings"];
//      $smstwoway->sent = 0;
//      $smstwoway->notification = 0;
//      if ($data["notification"]) {
//        $smstwoway->notification = 1;
//      }
//
//      if ($smstwoway->advancedoptions == 0) {
//        $smstwoway->notification = 0;
//        $smstwoway->email = "";
//        $smstwoway->divide = 0;
//        $smstwoway->sendingTime = null;
//        $smstwoway->quantity = null;
//        $smstwoway->timeFormat = null;
//      }
//
//      $smstwoway->dateNow = 1;
//      $smstwoway->gmt = null;
//      $smstwoway->originalDate = null;
//      if ($data["sentNow"] == false) {
//        $smstwoway->dateNow = 0;
//        $smstwoway->gmt = $data["timezone"];
//        $smstwoway->originalDate = $data["originalDate"];
//      }
//      var_dump($smstwoway);
//      exit;
      if (!$smstwoway->save()) {
        $this->db->rollback();
        foreach ($sms->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
        $this->trace("fail", "No se logro crear una cuenta");
      }
    }

    return ["message" => "Se ha creado el Smstwoway por contactos."];
  }

  public function getDataEdit($idSmsTwoway) {
    $smsTwoWay = \Smstwoway::findFirst(array("conditions" => "idSmsTwoway = ?0", "bind" => array($idSmsTwoway)));
    if (!$smsTwoWay) {
      return false;
    }
    if ($smsTwoWay->type == "csv") {
      return get_object_vars($smsTwoWay);
    } else if ($smsTwoWay->type == "lote") {
      $arr = array();
      $arr["name"] = $smsTwoWay->name;
      $arr["category"] = $smsTwoWay->idSmsCategory;
      $dateNow = false;
      if ($smsTwoWay->dateNow == 1) {
        $dateNow = true;
      }
      $arr["sentNow"] = $dateNow;
      $arr["gmt"] = $smsTwoWay->gmt;
      $arr["startdate"] = $smsTwoWay->startdate;
      $arr["receiver"] = $smsTwoWay->receiver;
      $arr["typeResponse"] = json_decode($smsTwoWay->typeResponse);
      $optionsAvanced = false;
      if ($smsTwoWay->advancedoptions == 1) {
        $optionsAvanced = true;
      }
      $arr["optionsAvanced"] = $optionsAvanced;
      $sendNotification = false;
      if ($smsTwoWay->notification == 1) {
        $sendNotification = true;
      }
      $arr["sendNotification"] = $sendNotification;
      $arr["emailNotification"] = $smsTwoWay->email;
      $divideSending = false;
      if ($smsTwoWay->divide == 1) {
        $divideSending = true;
      }
      $arr["divideSending"] = $divideSending;
      $arr["quantity"] = (int) $smsTwoWay->quantity;
      $arr["sendingTime"] = $smsTwoWay->sendingTime;
      $arr["timeFormat"] = $smsTwoWay->timeFormat;

      return $arr;
    }
  }

  public function createCsv($file, $post) {
    

    $post = $this->sanetizePost($post);
    
//    var_dump($post, $file);
//    exit();
    $x= json_decode($post['category'],true);
    $post['idSmsCategory'] = $x['idSmsCategory'];
    $y = json_decode($post['gmt'],true);
    if(isset($y['gmt']['gmt'])){
     $post['gmt'] = $y['gmt']['gmt'];   
    }else{
        $post['gmt'] = '-0500';
    }
    
    $this->validateFastSending($post);
    $datenow = $post["dateNow"];
    if ($file['csv']["error"] == 4) {
      throw new \InvalidArgumentException("No has seleccionado un archivo CSV");
    }
//    if (!isset($post["typeResponse"]) || empty($post["typeResponse"])) {
//      throw new InvalidArgumentException("Hace falta los tipos de respuesta.");
//    }
    if ($post["notification"]) {
      $email = $post["email"];
//      $email = explode(",", $email);
//      if (count($email) > 8) {
//        throw new InvalidArgumentException("No se puede ingresar más 8 correos electrónicos");
//      }
//      if (count($email) > $amount) {
//        throw new InvalidArgumentException("Solo puedes hacer " . $amount . " envíos de sms. Si nesesitas más saldo contacta al administrador");
//      }
    }
    if ($file['csv']['size'] > 2097152) {
      throw new InvalidArgumentException("El archivo CSV excede el tamaño las 2 megabytes aceptadas");
    }

    if ($datenow == 1) {
      $startdate = date('Y-m-d G:i:s', time());
    } else {
      $startdate = $post["dtpicker"];
    }

    if (strtotime($startdate) < strtotime("now") && !$datenow) {
      throw new \InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
    }

    $subaccount = \Subaccount::findFirst(array(
                "conditions" => "idSubaccount = ?0",
                "bind" => array(0 => $this->user->Usertype->idSubaccount)
    ));

    $smstwoway = new \Smstwoway();

    $smsloteform = new \SmsloteForm();
    $smsloteform->bind($post, $smstwoway);
    if (!$smsloteform->isValid()) {
      foreach ($smsloteform->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    if(isset($post["optionsAvanced"])){
        $advancedoptions = $post["optionsAvanced"];
        $notification = $post["notification"];
        $divide = $post["divide"];   
    }else{
        $advancedoptions = 0;
        $notification = 0;
        $divide = 0;
    }


    if ($advancedoptions == 1) {
      $smstwoway->notification = 1;
      $smstwoway->email = $email;
      if ($divide == 1) {

        if (!isset($quantity) or $quantity == 0) {
          throw new InvalidArgumentException("Debes indicar una cantidad correcta");
        }
        if (!isset($sendingTime) or $sendingTime == "") {
          throw new InvalidArgumentException("Debes elegir un tiempo de envío");
        }
        if (!isset($timeFormat) or $timeFormat == "") {
          throw new InvalidArgumentException("Debes elegir un formato de tiempo");
        }
        $smstwoway->divide = 1;
        $smstwoway->sendingTime = $post["sendingTime"];
        $smstwoway->timeFormat = $post["timeFormat"];
        $smstwoway->quantity = $post["quantity"];
      }
    } else {
      $smstwoway->notification = 0;
      $smstwoway->email = null;
      $smstwoway->divide = 0;
      $smstwoway->sendingTime = null;
      $smstwoway->quantity = null;
      $smstwoway->timeFormat = null;
    }

    $smstwoway->advancedoptions = 0;
    if ($advancedoptions == 1 AND ( $notification == 1 || $divide == 1)) {
      $smstwoway->advancedoptions = 1;
    }

    (($datenow) ? $smstwoway->startdate = date('Y-m-d H:i:s', time()) : "");
    $smstwoway->idSubaccount = $this->user->Usertype->idSubaccount;
    $smstwoway->status = "draft";
    $smstwoway->sent = 0;
    $smstwoway->logicodeleted = 0;
    $smstwoway->type = "csv";
    $smstwoway->dateNow = 1;
    $smstwoway->gmt = null;
    $smstwoway->originalDate = null;
    $smstwoway->typeResponse = $post["response"];
    if(isset($post["international"])){
        $smstwoway->international = $post["international"];
        if(isset($post["idcountry"])){         
         $x = json_decode($post['idcountry'],true);
         $smstwoway->idcountry = $x['idcountry'];  
        }else{
           $smstwoway->idcountry = 0; 
        }
    }else{
        $smstwoway->international = 0;
    }
   
    if ($post["dateNow"] != 1) {
      $smstwoway->dateNow = 0;
      $smstwoway->gmt = $post["gmt"];
      $smstwoway->originalDate = $post["dtpicker"];
      $smstwoway->startdate = $post["dtpicker"];
    }
    if (!$smstwoway->save()) {
      foreach ($smstwoway->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
      $this->trace("fail", "No se logro crear el smslotetwoway por medio de un archivo csv {$message}");
    }
    /* if($smstwoway->international && $smstwoway->idcountry != 0 ){
        $file = new \Sigmamovil\General\Misc\FileManager();
        $resul = $file->csvsms($_FILES['csv'], $smstwoway->idSmsTwoway, $this->user->Usertype->idSubaccount, false);        
        $smstwoway->target = $resul["success"];
        if (!$smstwoway->save()) {
          foreach ($smstwoway->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se logro crear el smslote {$message}");
        }
    }else{ */
        $file = new \Sigmamovil\General\Misc\FileManager();
        $resul = $file->csvsms($_FILES['csv'], $smstwoway->idSmsTwoway, $this->user->Usertype->idSubaccount, false);
        $smstwoway->target = $resul["success"];
        if (!$smstwoway->save()) {
          foreach ($smstwoway->getMessages() as $message) {
            throw new InvalidArgumentException($message);
          }
          $this->trace("fail", "No se logro crear el smslote {$message}");
        }
    //}


    foreach ($smstwoway->Subaccount->Saxs as $key) {
      if ($key->idServices == 1) {
        $saxs = $key;
      }
    }

    // if ($resul["success"] > $saxs->amount) {
    //   throw new \InvalidArgumentException("Solo puedes hacer " . $saxs->amount . " envíos de sms si nesesitas más saldo contacta al administrador");
    // }
  }

  public function validateFastSending($data) {

    if ($data['name'] == "") {
      $this->notification->info("Es obligatorio el nombre del envío");
      return $this->response->redirect("");
    }
    if ($data['idSmsCategory'] == "") {
      $this->notification->info("Debe elegir una categoría");
      return $this->response->redirect("");
    }
    if ($data['notification'] && (isset($data['email']) && empty($data['email']))) {
      $this->notification->info("Debe indicar al menos una dirección de notificación");
      return $this->response->redirect("");
    }
    if (isset($data['divide']) && $data['divide']) {
      if (!isset($data['quantity']) or $data['quantity'] < 1) {
        $this->notification->info("Debe indicar una cantidad de envíos por intervalo mayor a 1");
        return $this->response->redirect("");
      }
      if (!isset($data['sendingTime'])) {
        $this->notification->info("Debe elegir un tiempo de envio correcto");
        return $this->response->redirect("");
      }
      if (!isset($data['timeFormat'])) {
        $this->notification->info("Debe elegir un formato de tiempo correcto");
        return $this->response->redirect("");
      }
    }
  }

  /**
   * @description: una funcion para comparar dos fechas 
   * @return Boolean si es valida la fecha
   * @param Date $date1 La fecha mayor a $date2, $params son las condiciones sujetas a un objetos Leer la documentacion http://www.php.net/manual/en/datetime.diff.php.
   */
  public function validateDiffDate($date1, $date2, $params) {
    $returnBool = false;
    $arrConditions = array();
    if ($date1 > $date2) {
      return $returnBool;
    }

    $diff = $date1->diff($date2);
    $conditions->i = 11;
    $conditions->y = 0;
    $conditions->m = 0;
    $conditions->d = 0;
    if ($diff->i > $params->i && $diff->y >= $params->y && $diff->m >= $params->m && $diff->d >= $params->d) {
      return true;
    } else {
      return false;
    }
  }

  public function getAllEdit($idSmsTwoway) {

    $SmsTwoway = \Smstwoway::findFirst(array(
                "conditions" => "idSmsTwoway = ?0 ",
                "bind" => array(0 => $idSmsTwoway)
    ));


    /*
     * Validacion de ammount
     */
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == $this->services->sms_two_way) {
        $flag = true;
        $amount = $key->amount;
      }
    }

    if ($flag == false) {
      throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
    }
    if ($amount == 0) {
      throw new \InvalidArgumentException("No tienes capacidad para enviar más sms");
    }

    $arr = array();
    $arr["name"] = $SmsTwoway->name;
    $arr["idSmsCategory"] = $SmsTwoway->idSmsCategory;
    $dateNow = false;
    if ($SmsTwoway->dateNow != 0) {
      $dateNow = true;
    }
    $arr["sentNow"] = $dateNow;
    $arr["timezone"] = $SmsTwoway->gmt;
    $arr["startdate"] = $SmsTwoway->startdate;
    $listSelected = "";
    if (json_decode($SmsTwoway->receiver)->type == "contactlist") {
      $listSelected = 1;
    } else {
      $listSelected = 2;
    }
    $arr["listSelected"] = $listSelected;
    $arr["arrAddressee"] = json_decode($SmsTwoway->receiver)->contactlists;
    $arr["message"] = $SmsTwoway->message;
    $arr["typeResponse"] = json_decode($SmsTwoway->typeResponse);
    $advancedoptions = false;
    if ($SmsTwoway->advancedoptions != 0) {
      $advancedoptions = true;
    }
    $arr["advancedoptions"] = $advancedoptions;
    $notification = false;
    if ($SmsTwoway->notification != 0) {
      $notification = true;
    }
    $arr["notification"] = $notification;
    $arr["email"] = $SmsTwoway->email;
    $divide = false;
    if ($SmsTwoway->divide) {
      $divide = true;
    }
    $arr["divide"] = $divide;
    $arr["quantity"] = (int) $SmsTwoway->quantity;
    $arr["sendingTime"] = $SmsTwoway->sendingTime;
    $arr["timeFormat"] = $SmsTwoway->timeFormat;

    return $arr;
  }

  /**
   * @author Desconocido
   * @Description Valida el nuevo texto envia desde el usuario final.
   * @param Object $data 
   * @throws InvalidArgumentException
   * @Return Boolean
   */
  public function registerReceiverLote($data) {

    $smsLoteTwoway = \Smslotetwoway::findFirst(array("conditions" => "messageId = ?0", "bind" => array($data->results[0]->pairedMessageId)));

    if ($smsLoteTwoway) {
      $smsLoteTwoway->userResponse = $data->results[0]->text;
      $smsLoteTwoway->userResponseGroup = $this->homologate($smsLoteTwoway->Smstwoway->typeResponse, $data->results[0]->text);
      $smsLoteTwoway->totalUserResponse = $smsLoteTwoway->totalUserResponse + 1;

      if (!$smsLoteTwoway->save()) {
        foreach ($smsLoteTwoway->getMessages() as $message) {
          throw new InvalidArgumentException($message);
        }
      }
      $Smstwoway = \Smstwoway::findFirst(array(
                  "conditions" => "idSmsTwoway = ?0", "bind" => array(
                      $smsLoteTwoway->idSmsTwoway
                  )
      ));

      $this->recalculateSaxsBySms($Smstwoway->idSubaccount, $smsLoteTwoway->totalUserResponse);
      $this->newReceiver($smsLoteTwoway);
    }

    //return true;
    return array(
        "true" => true,
        "dataTwoWay" => $smsLoteTwoway
    );
  }

  /**
   * 
   * @author Desconocido
   * @param Object $smsLoteTwoway
   * @return boolean
   * @throws InvalidArgumentException
   */
  public function newReceiver($smsLoteTwoway) {

    $receiverTable = \Receiversms::findFirst(array(
                "conditions" => array(
                    "idSmslote" => (string) $smsLoteTwoway->idSmsLoteTwoway
                )
    ));
    if (!$receiverTable) {
      $receiverTable = new \Receiversms();
    }
    $receiverTable->idSmslote = $smsLoteTwoway->idSmsLoteTwoway;
    $data = array();
    $data["group"] = $smsLoteTwoway->userResponseGroup;
    $data["dateRegister"] = date('Y-m-d G:i:s', time());
    $data["receiver"] = $smsLoteTwoway->userResponse;
    $receiverTable->dataReceiver[] = $data;
    if (!$receiverTable->save()) {
      foreach ($receiverTable->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
    }

    return true;
  }

  /**
   * @author jordan.zapata@sigmamovil.com
   * @param type $idSubaccount
   * @param type $totaluserresponse
   * @return boolean
   */
  public function recalculateSaxsBySms($idSubaccount, $totaluserresponse) {
    //descomentar cuando se agrege el envio por contactos
//    $count = Smsxc::count(array(
//                "conditions" => array(
//                    "idSubaccount" => (string) $idSubaccount,
//                    "status" => "sent"
//                )
//    ));

    $cero = 0;

    $sql = "CALL updateCountersSmstwowaySaxs({$idSubaccount},{$cero},{$totaluserresponse})";
    $this->db->execute($sql);
    return true;
  }

  /**
   * @author jordan.zapata@sigmamovil.com
   * @param type $id
   * @return boolean
   */
  public function changeCancelEdit($id) {

    $smstwoway = \Smstwoway::findFirst(array(
                "conditions" => "idSmsTwoway = ?0 ",
                "bind" => array(0 => $id)
    ));


    if (!$smstwoway) {
      throw new \InvalidArgumentException("No se encontro el Sms doble via");
    }


    if ($smstwoway->status != "scheduled" && $smstwoway->status != "draft") {
      return true;
    }

    $smstwoway->status = \Phalcon\DI::getDefault()->get('statusSms')->draft;

    if (!$smstwoway->save()) {
      foreach ($smstwoway->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
      $this->trace("fail", "No se logro cambiar el estado a draf {$message}");
    }

    return true;
  }

  public function editCsvTwoway($data) {

    $datenow = $data["dateNow"];

    if ($data["notification"]) {
      $email = $data["email"];
    }

    if ($datenow == true) {
      $startdate = date('Y-m-d G:i:s', time());
    } else {
      $startdate = $data["dtpicker"];
    }

    if (strtotime($startdate) < strtotime("now") && !$datenow) {
      throw new \InvalidArgumentException("No puedes asignar un envio con una fecha y hora del pasado");
    }

    $subaccount = \Subaccount::findFirst(array(
                "conditions" => "idSubaccount = ?0",
                "bind" => array(0 => $this->user->Usertype->idSubaccount)
    ));

    $idSmsTwoway = $data["idSmsTwoway"];
    $smstwoway = \Smstwoway::findFirst(array(
                "conditions" => "idSmsTwoway = ?0", "bind" => array(
                    $idSmsTwoway
                )
    ));
    $smstwoway->name = $data["name"];
    $smstwoway->idSmsCategory = $data["idSmsCategory"];

    if ($data["dateNow"] == true) {
      $smstwoway->dateNow = 1;
      $smstwoway->startdate = date('Y-m-d G:i:s', time());
      $smstwoway->originalDate = date('Y-m-d G:i:s', time());
    } else {
      $smstwoway->dateNow = 0;
      $smstwoway->gmt = $data["gmt"];
      $smstwoway->startdate = $data["dtpicker"];
      $smstwoway->originalDate = $data["dtpicker"];
    }

    $smstwoway->typeResponse = $data["response"];

    if ($data["optionsAvanced"] == true) {
      $smstwoway->advancedoptions = 1;
    }
    if ($data["notification"] == true) {
      $smstwoway->notification = 1;
    }
    if ($data["email"]) {
      $smstwoway->email = $data["email"];
    }

    if ($data["divideSending"] == true) {
      $smstwoway->divide = 1;
    }
    $smstwoway->sendingTime = $data["sendingTime"];
    $smstwoway->quantity = $data["quantity"];
    $smstwoway->timeFormat = $data["timeFormat"];
    $smstwoway->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;

    if (!$smstwoway->save()) {
      foreach ($smstwoway->getMessages() as $message) {
        throw new InvalidArgumentException($message);
      }
      $this->trace("fail", "No se logro crear el smslotetwoway por medio de un archivo csv {$message}");
    }

    foreach ($smstwoway->Subaccount->Saxs as $key) {
      if ($key->idServices == 1) {
        $saxs = $key;
      }
    }
  }

  public function createSingleSmstwoway($data) {
    $saxs = null;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 7 && $key->status==1) {
        $saxs = $key;
      }
    }

    if (!isset($saxs)) {
      throw new \InvalidArgumentException("No tienes asignado este servicio, si lo deseas adquirir comunicate con soporte");
    }
    if ($saxs->amount == 0) {
      throw new \InvalidArgumentException("No tienes capacidad para enviar más SMS");
    }
    //verificamos que un single sea mayor a el amount disponible
    if (count($data["receiver"]["phone"]) > $saxs->amount) {
      if(abs($saxs->amount)){
        $tAvailable = (object) ["totalAvailable" => 0];
      } else {
        $tAvailable = (object) ["totalAvailable" => $saxs->amount];
      }
      $this->sendmailnotsmsbalance($tAvailable);
      throw new \InvalidArgumentException("Solo puedes hacer " . $tAvailable->totalAvailable . " envíos de sms si nesesitas más saldo contacta al administrador");
    }

    //Validamos Campos
    $validateResponse = $this->validateSingleSmstwoway($data);

    if ($validateResponse["response"] != 1) {
      return $validateResponse["response"];
      return;
    }
    
    //Se realiza validaciones de los sms programados
    $balance = $this->validateBalance();
    $target = 0;
    if($balance['smsFindPending']){
      foreach ($balance['smsFindPending'] as $value){
        $target = $target + $value['target'];
      }
    }
    $scheduled = $target;
    $amountSingle = $balance['balanceConsumedFind'][0]['amount'];
    unset($balance);
    $totalTarget =  $amountSingle - $target;
    $target = $target + 1;
    if($target>$amountSingle){
      $target = $target - $amountSingle;
      if(abs($totalTarget)){
        $tAvailable = (object) ["totalAvailable" => 0];
      } else {
        $tAvailable = (object) ["totalAvailable" => $totalTarget];
      }
      $this->sendmailnotsmsbalance($tAvailable);
      throw new \InvalidArgumentException("No tiene saldo disponible para realizar este Sms!, {'amount':".$tAvailable->totalAvailable.", 'missing':" .$target.", 'scheduled':" .$scheduled.", 'totalAmount':".$this->arraySaxs['totalAmount'].",'subaccountName':".$this->arraySaxs['subaccountName'].", 'accountName':".$this->arraySaxs['accountName']."}");
    }
    unset($target);
    unset($scheduled);
    unset($amountSingle);
    unset($totalTarget);
    unset($tAvailable);
    
    $typeResponse ="{'typeResponse':[{'response':'si','homologate':'confirmado,acepto,ok'},{'response':'no','homologate':'cancelado,negativo'}]}";
    //Instanciamos modelos en insertamos en BD
    $smstwoway = new \Smstwoway();
    $smstwoway->idSmsCategory = $data["idSmsCategory"];
    $smstwoway->idSubaccount = $this->user->Usertype->idSubaccount;
    $smstwoway->name = $data["name"];
    $smstwoway->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    $smstwoway->confirm = 1;
    $smstwoway->logicodeleted = 0;
    $smstwoway->type = "lote";
    $smstwoway->startdate = date('Y-m-d G:i:s', time());
    $smstwoway->dateNow = 1;
    $smstwoway->sent = 0;
    $smstwoway->total = 1;
    $smstwoway->target = 1;
    $smstwoway->typeResponse = $typeResponse;
    $smstwoway->receiver = json_encode($data["receiver"]);
    
    if($data["notification"]==1 && $data["email"]){
      $smstwoway->notification = $data["notification"];
      $smstwoway->email =$data["email"];
    }else{
      $smstwoway->notification =0;
      $smstwoway->email ="";
    }
    
    if (!$smstwoway->save()) {
      foreach ($smstwoway->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    $smslotetwoway = new \Smslotetwoway();
    $smslotetwoway->idSmsTwoway = $smstwoway->idSmsTwoway;
    $smsLoteTwoWay->idAdapter = 3;
    $smslotetwoway->indicative = $data["receiver"]["indicative"];
    $smslotetwoway->phone = trim($data["receiver"]["phone"]);
    $smslotetwoway->message = trim($data["receiver"]["message"]);
    $smslotetwoway->status = \Phalcon\DI::getDefault()->get("statusSms")->scheduled;
    if (!$smslotetwoway->save()) {
      foreach ($smslotetwoway->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    //Enviamos a Infobip
    $responseInfobip = $this->sendSingleMessage($this->validReceiver($smslotetwoway));
    $smslotetwoway->response = $responseInfobip->messages[0]->status->name;
    $smslotetwoway->messageId = $responseInfobip->messages[0]->messageId;
    if ($smslotetwoway->response == "PENDING_ENROUTE") {
      $smslotetwoway->status = \Phalcon\DI::getDefault()->get("statusSms")->sent;
      $smstwoway->sent = 1;
    } else {
      $smstwoway->status = "undelivered";
    }

    $smstwoway->status = \Phalcon\DI::getDefault()->get("statusSms")->sent;
    $smstwoway->update();
    $smslotetwoway->save();
    $saxs->amount = (Int) $saxs->amount - 1;
    $saxs->save();
    
    //Send Mail Notification
    if($smstwoway->email){
      $sendMailNot= new \Sigmamovil\General\Misc\SmstwowayEmailNotification();
      $sendMailNot->sendSmsNotification($smstwoway);
    }

    return array(
        "idSmstwoway" => $smstwoway->idSmsTwoway,
        "messageId" => $smslotetwoway->idSmsLoteTwoway,
        "to" => "{$smslotetwoway->indicative}{$smslotetwoway->phone}",
        "status" => $smslotetwoway->status,
        "smsCount" => $smstwoway->sent
    );
  }

  public function validateSingleSmstwoway($data) {
    $flag = true;
    $arrResponse = array();
    if (!isset($data["name"]) || $data["name"] == "") {
      $arrResponse["validateName"] = "Nombre de campaña es obligatiorio";
      $flag = false;
    }
    if (!isset($data["idSmsCategory"]) || $data["idSmsCategory"] == "") {
      $arrResponse["validateIdSmsCategory"] = "IdSmsCategory es obligatiorio";
      $flag = false;
    }
    if (isset($data["receiver"]) || $data["receiver"] != "") {
      if (!isset($data["receiver"]["indicative"]) ||
              $data["receiver"]["indicative"] == "" ||
              strstr($data["receiver"]["indicative"], "+") ||
              !is_numeric($data["receiver"]["indicative"])) {
        $arrResponse["validateReceiverIndicative"] = "Indicativo no esta definido y-o sintaxis es incorrecta.";
        $flag = false;
      }
      if (!isset($data["receiver"]["phone"]) ||
              $data["receiver"]["phone"] == "" ||
              !is_numeric($data["receiver"]["phone"]) ||
              strlen(trim($data["receiver"]["phone"])) != 10
      ) {
        $arrResponse["validateReceiverPhone"] = "Telefono no esta definido y-o sintaxis es incorrecta ";
        $flag = false;
      }
      if (!isset($data["receiver"]["message"]) ||
              $data["receiver"]["message"] == "" ||
              strlen($data["receiver"]["message"]) > 160) {
        $arrResponse["validateReceiverMessage"] = "Mensaje es obligatorio y-o sintaxis es incorrecta ";
        $flag = false;
      }
    } else {
      $arrResponse["validateReceiver"] = "Receiver es obligatorio ";
      $flag = false;
    }
    
     if($data["notification"] && (isset($data["email"]) || $data["email"] != "")){
      if(filter_var($data["email"], FILTER_VALIDATE_EMAIL)==false){
        $arrResponse["validateEmail"] = "Email tiene sintaxis es incorrecta ";
        $flag = false;
      } 
    }

    if ($flag) {
      return [
          "response" => true
      ];
    }
    return [
        "response" => $arrResponse
    ];
  }

  private function validReceiver($receiver) {
    if (strlen($receiver->message) > 160) {
      throw new \InvalidArgumentException("El mensaje excede los 160 caracteres");
    }
    return array(
        "from" => "AIO-SMS",
        "to" => "{$receiver->indicative}{$receiver->phone}",
        "text" => $receiver->message
    );
  }

  private function sendSingleMessage($receiver) {
    $adapter = \Adapter::findFirst(array(
                "conditions" => "fname = ?0",
                "bind" => array("TWOWAY(SINGLESMS)")
    ));

    $apiSms = new \Sigmamovil\General\Misc\ApisSms(\Phalcon\DI::getDefault()->get('general')->keyjwt);
    return $apiSms->apiInfobip($receiver, $adapter);
  }
  
  public function validateBalance(){
    $smsFindPending = \Smstwoway::find(array(
      'conditions'=> 'idSubaccount = ?0 and status = ?1 and startdate >= ?2',
      'bind'=> array(
        0 => $this->user->Usertype->subaccount->idSubaccount,
        1 => 'scheduled',
        2 => date('Y-m-d h:i:s')
      ),
      'columns' => 'idSmsTwoway,target',
      'order' => 'idSmsTwoway ASC'
    ));
    
    $balanceConsumedFind = \Saxs::find(array(
      'conditions' => 'idSubaccount = ?0 and idServices = ?1',
      'bind' => array(
        0 => $this->user->Usertype->subaccount->idSubaccount,
        1 => 7
      ),
      'columns' => 'idSubaccount, totalAmount-amount as consumed, amount, totalAmount'
    ));
    $answer = ['smsFindPending'=>$smsFindPending->toArray(), 'balanceConsumedFind'=>$balanceConsumedFind->toArray()];
    return $answer;
  }
  
  public function sendmailnotsmsbalance($data){
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 7) {
        $amount = $data->totalAvailable;
        $totalAmount = $key->totalAmount;
        $subaccountName = $this->user->Usertype->Subaccount->name;
        $accountName = $this->user->Usertype->Subaccount->Account->name;
        $this->arraySaxs = array(
            "amount" => $amount,
            "totalAmount" => $totalAmount,
            "subaccountName" => $subaccountName,
            "accountName" => $accountName
        );
      }
    }
    $sendMailNot= new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();      
    $sendMailNot->sendSmsNotification($this->arraySaxs);
    return true;
  }
  
}