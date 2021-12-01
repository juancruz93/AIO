<?php

class Flashmessage extends Modelbase
{

  public $idFlashmessage,
      $start,
      $end,
      $created,
      $updated,
      $name,
      $message,
      $allied,
      $accounts,
      $type,
      $target,
      $category;

  public function initialize() {
    
  }

//    public function validation()
//    {
////        $this->validate(new PresenceOf(array(
////            'field' => 'name',
////            'message' => 'El nombre del mensaje es obligatorio, por favor valide la información'
////        )));
////        
////        $this->validate(new SpaceValidator(array(
////            'field' => 'name',
////            'message' => 'El campo nombre esta vacío, por favor valide la información'
////        )));
////        
////        $this->validate(new PresenceOf(array(
////            'field' => 'message',
////            'message' => 'El campo mensaje es obligatorio, por favor valide la información'
////        )));
////        
////        $this->validate(new SpaceValidator(array(
////            'field' => 'message',
////            'message' => 'El campo mensaje esta vacío, por favor valide la información'
////        )));
////        
////        $this->validate(new PresenceOf(array(
////            'field' => 'accounts',
////            'message' => 'El campo mostrar en es obligatorio, por favor valide la información'
////        )));
////        
////        $this->validate(new PresenceOf(array(
////            'field' => 'type',
////            'message' => 'El campo tipo de mensaje en es obligatorio, por favor valide la información'
////        )));
////        
////        $this->validate(new PresenceOf(array(
////            'field' => 'start',
////            'message' => 'El campo fecha y hora de inicio en es obligatorio, por favor valide la información'
////        )));
////        
////        $this->validate(new PresenceOf(array(
////            'field' => 'start',
////            'message' => 'El campo fecha y hora de inicio en es obligatorio, por favor valide la información'
////        )));
////        
////        $this->validate(new PresenceOf(array(
////            'field' => 'category',
////            'message' => 'El campo categoria en es obligatorio, por favor valide la información'
////        )));
//    }
}
