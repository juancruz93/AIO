<?php

require_once __DIR__ . '/../../../public/library/php-jwt-master/src/JWT.php';
require_once 'http_load.php';

/**
 * Objeto encargado de preparar el envio sms y conexion a Kannel
 *
 * @author desarrollo3
 */
class PrepareSms {

  private $url = array();
  public $prefix,
          $text,
          $movil,
          $movilBatch,
          $idJobBatch,
          $sms,
          $adapter;

  /**
   * Contructor
   */
  function __construct() {
    $this->kannelProperties = \Phalcon\DI::getDefault()->get('kannelProperties');
  }

  public function startSend() {
//    $this->setAdapter();
    $this->prepareUrl();
    $this->createURL();
    //\Phalcon\DI::getDefault()->get('logger')->log(print_r($this->url['pre'], true));
//    if ($this->movil) {
    return $this->sendSms($this->url['pre']);
//    }
  }

  public function prepareUrl() {
    $pass = $this->jwtDecode($this->adapter->passw);
    $this->url['pre'] = $this->kannelProperties->baseUrl .
            'username=' . $this->adapter->uname .
            '&password=' . $pass .
            '&smsc=' . $this->adapter->fname .
            '&coding=' . $this->adapter->coding;
    if ($this->adapter->usedlr) {
      $this->url['pos'] = $this->kannelProperties->dlrURL;
    }
  }

  public function createURL() {
    $url = "";
    $url .= '&from=' . urlencode($this->adapter->smscid);
//    $to = $this->prefix . str_replace(',', ' ' . $this->prefix, $this->movilBatch);
    $url .= '&to=' . urlencode($this->movil);
//    $url .= '&to=' . urlencode($this->prefix . $this->movil);
    $url .= '&text=' . urlencode($this->text);
//    if (isset($this->url['pos'])) {
//      $url .= '&dlr-mask=7&dlr-url=' . urlencode($this->url['pos'] . $this->idJobBatch);
//    }
    $this->url['pre'] .= $url;
  }

  public function sendSms($url) {
//    echo 'Inicio del proceso de envío' . PHP_EOL;

    $urlRequest = $url;

//    echo 'URL: ' . $urlRequest . PHP_EOL;

    /*     * * DO THE ACTUAL SEND ** */
    //$resultXML = "0: Accepted for delivery";
    $resultXML = $this->doHTTPrequest($urlRequest, array());
    //\Phalcon\DI::getDefault()->get('logger')->log("resultXML " . $resultXML);
    return $resultXML;
  }

  /**
   * This method sends the request to Kannel, and captures the results
   * @param $urlReq string
   * @param $urlPar array
   * @return string
   */
  private function doHTTPrequest($urlReq, $urlPar) {
    $response = http_load($urlReq, array('return_info' => true));

    //echo 'Obteniendo respuesta' . PHP_EOL;

    //print_r($response);
    if($response['body'] != "0: Accepted for delivery"){
      \Phalcon\DI::getDefault()->get('logger')->log("response " . print_r($response, true));
    }
    if (key_exists('body', $response))
      return $response['body'];
    return null;
  }

  /**
   * Metodo encargado de desencriptar el password
   * 
   * @param String $payload
   * @return String
   */
  public function jwtDecode($payload) {
    $pass = Firebase\JWT\JWT::decode($payload, $this->kannelProperties->keyjwt, ["HS256"]);
    return $pass;
  }

  function setPrefix($prefix) {
    $this->prefix = $prefix;
  }

  function setText($text) {
    $this->text = $text;
  }

  function setMovil($movil) {
    $this->movil = $movil;
  }

  function setMovilBatch($movilBatch) {
    $this->movilBatch = $movilBatch;
  }

  function setIdJobBatch($idJobBatch) {
    $this->idJobBatch = $idJobBatch;
  }

  function setSms($sms) {
    $this->sms = $sms;
  }

  function setAdapter($adapter) {
    $this->adapter = $adapter;
  }

}
