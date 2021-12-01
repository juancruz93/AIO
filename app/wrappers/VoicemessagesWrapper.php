<?php

namespace Sigmamovil\Wrapper;
/**
 * Description of VoicemessagesWrapper
 *
 * @author jose.quinones
 */
class VoicemessagesWrapper extends \BaseWrapper {

  public function createlote($data){
    $data = $this->validateData($data);
    if(isset($data["requestError"])){
      return json_encode($data);
    }
    $data = json_encode($data);
    $adapter = \Adapter::findFirst(array(
      "conditions" => "idAdapter = ?0",
      "bind" => array(10)//Este es el adaptador de voice-messages
    ));

    $key = "U2lnbWFNbzIyOlhsczdzbXM3MQ==";

    $curl = curl_init();
    //$key = base64_encode($adapter->uname . ":" . $adapter->passw);
    curl_setopt_array($curl, array(
      CURLOPT_URL => $adapter->urlIp,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{$data}",
      CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Authorization: Basic {$key}",
        "Content-Type: application/json"
      )
    ));
 
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    $res = json_decode($response);

    if (!$res->requestError) {
      return ["message" => "Se ha realizado el envió de mensaje de voz correctamente."];
    } else {
      return $res;
    }
  }
  
  public function validateData($data){
    $flag = true;
    $indicative = "";
    $phone = "";
    $messages = "";
    $destinations = [];
    $error = [];

    foreach ($data['receiver'] as $value){
      if (strstr($value["indicative"], "+") || !is_numeric($value["indicative"])) {
        $flag = false;
        $indicative .= " -Recuerde que el indicativo solo debe contener números";
        $error['indicative'] = $indicative;
      }
      if (!is_numeric($value["phone"])) {
        $flag = false;
        $phone .= " -Recuerde que el telefono solo debe contener números";
        $error['phone'] = $phone;
      }
      if (trim($value["indicative"]) == 57 && strlen(trim($value["phone"])) != 10) {
        $flag = false;
        $phone .= " -Recuerde que el movil con indicativo 57 debe contener 10 digitos";
        $error['phone'] = $phone;
      }
      $destinations[] = ["to" => "{$value['indicative']}{$value['phone']}"];
    }
    //
    if (preg_match("/[ñÑáéíóúÁÉÍÓÚ¿¡´]/", $data["message"])) {
      $flag = false;
      $messages .= " -Recuerde que el contenido del mensaje no debe contener ninguno de estos caracteres: ñ Ñ ¡ ¿ á é í ó ú Á É Í Ó Ú ´";
      $error['messages'] = $messages;
    }
    //Texto del mensaje que se enviará. La pausa entre palabras es posible. El texto del mensaje puede tener hasta 1400 caracteres de longitud.
    if (mb_strlen($data["message"], 'UTF-8') > 1400 ) {
      $flag = false;
      $messages .= " -Recuerde que el contenido del mesaje solo debe contener 1400 carateres";
      $error['messages'] = $messages;
    }
    if(!$flag){
      return ["requestError" => $error];
    }
    //
    //$year = new \DateTime($data["datesend"], new \DateTimeZone("Europe/Amsterdam"));
    $year = new \DateTime('NOW', new \DateTimeZone("Europe/Amsterdam"));
    $year = $year->format('c');
    //
    $infobip['bulkId'] = "AIO-SIGMA-SMS-";
    $infobip['messages'] = [
      [
        "from" => "SIGMA-MOVIL",
        "destinations" => $destinations,
        "text" => $data["message"],
        "language" => "es",
        "voice" => [
          "name" => 'Laura',
          "gender" => 'female',
        ],
        "sendAt" => $year
      ]
    ];
    return $infobip;
  }
}
