<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiDataValidation
 *
 * @author juan.pinzon
 */
class ApiDataValidation {

  private $apiRoot;
  private $apiKey;

  public function __construct($apiRoot, $apiKey) {
    $this->apiRoot = $apiRoot;
    $this->apiKey = $apiKey;
  }

  private function curlHandle($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "{$this->apiRoot}{$url}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: bearer ' . $this->apiKey]);

    return $ch;
  }

  private function parsedResult($ch) {
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    assert($status_code == 200);

    return json_decode($response);
  }

  public function realTimeCheck($email) {
    $ch = $this->curlHandle("realtime/?email=" . urlencode($email));
    $result = $this->parsedResult($ch);
    
    assert($result->{'status'} == 'ok');
    
    return $result->{'grade'};
  }

}
