<?php


namespace Sigmamovil\General\Misc;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApisSms
 *
 * @author juan.pinzon
 */
//require_once __DIR__ . '/../../public/library/php-jwt-master/src/JWT.php';

class ApisSms {

  private $keyjwt;
  

  function __construct($keyjwt) {
    $this->keyjwt = $keyjwt;
  }

  public function apiInfobip($batch, $adapter) {
    $curl = curl_init();
    
    $data = json_encode($batch);
    $key = base64_encode($adapter->uname . ":" . $adapter->passw);
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

    if (isset($res->messages)) {

      return $res;
    } else {
      return json_decode($error);
    }
  }

  private function jwtDecode($payload) {
    //$pass = Firebase\JWT\JWT::decode($payload, $this->getKeyjwt(), ["HS256"]);
    return $pass;
  }

  function getKeyjwt() {
    return $this->keyjwt;
  }
  
  public function apiInfobipVm($batch, $adapter) {
    $curl = curl_init();
    
    $data = json_encode($batch);
    $key = base64_encode($adapter->uname . ":" . $adapter->passw);
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

    if (isset($res->messages)) {

      return $res;
    } else {
      return json_decode($error);
    }
  }

}
