<?php

class Apielastic {

  protected $apikey,
          $secret,
          $urlBase,
          $urlCreateList,
          $urlCreateContacts,
          $urlCreateMail,
          $urlCreateCustomField;
  
  const POST = "POST",GET = "GET",idBD = 831;

  public function __construct() {
    $this->apikey = "1-626-5a15d2ad005344.73198162";
    $this->secret = "92b892a1628369d2c7a6d000b253722d1e77d0d8";
    $this->urlBase = "http://elasticmail.sigmamovil.com/";
    $this->urlCreateCustomField = $this->urlBase . "api/dbase/".self::idBD."/fields";
    $this->urlCreateList = $this->urlBase . "api/lists";
  }

  public function createList() {
    $data = json_encode(array('list'=>array("name"=>$name,"dbase"=>self::idBD)));
    $list = $this->send_http_request($this->urlCreateList,self::POST, $data);
    return $list->id;
  }

  public function createContacts() {
    
  }

  public function createMail() {
    
  }

  public function createCustomField($name) {
    $data = json_encode(array(
        "field" => array(
            "name" => $name,
            "defaultValue" => "",
            "required" => "false",
            "type" => "Text",
            "values" => ""
        )
    ));
    $this->send_http_request($this->urlCreateCustomField,self::POST, $data);
  }

  protected function send_http_request($uri,$method, $data) {
    $pwd = hash_hmac('sha1', $method . "|" . $uri . "|" . $data, $this->secret);
    $options = array(
        'http' => array(
            'header' => "Authorization: Hmac " . base64_encode($this->apikey . ":" . $pwd),
            'method' => $method,
            'content' => $data
        )
    );
    $context = stream_context_create($options);
    //var_dump($context);
    //exit();
    return file_get_contents($uri, false, $context);
  }

}
