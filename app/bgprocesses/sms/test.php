<?php

include_once('http_load.php');

$s = new Sender();
$s->sendSms();

class sender {

  public function sendSms() {
    echo 'Inicio del proceso de envío' . PHP_EOL;

    $urlRequest = "http://localhost:13014/cgi-bin/sendsms?username=sigm4&password=S1gm4S3nder&smsc=AVANTEL&coding=0&from=87416&to=3153495200&text=Hola+hay+chicas+solteras+en+tu+zona+dispuestas+a+conocerte+2280+bebesotes";

    echo 'URL: ' . $urlRequest . PHP_EOL;

    /*     * * DO THE ACTUAL SEND ** */
    $resultXML = $this->doHTTPrequest($urlRequest, array());
  }

  /**
   * This method sends the request to Kannel, and captures the results
   * @param $urlReq string
   * @param $urlPar array
   * @return string
   */
  private function doHTTPrequest($urlReq, $urlPar) {

    $response = http_load($urlReq, array('return_info' => true));

    echo 'Obteniendo respuesta' . PHP_EOL;

    print_r($response);

    if (key_exists('body', $response))
      return $response['body'];

    return null;
  }

}
