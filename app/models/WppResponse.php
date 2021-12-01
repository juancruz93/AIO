<?php

class WppResponse extends Modelbase {

    public $idWppresponse,
            $response,
            $created,
            $updated;

    public function initialize() {

    }

    public function getSource() {
        return "wpp_response";
      }

}