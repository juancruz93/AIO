<?php

/**
 * Description of UpdateBlockedInIdContact
 *
 * @author jose.quinones
 */
require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$updateUpdateBounced = new UpdateBounced();
$updateUpdateBounced->index($id);

class UpdateBounced {
  
  public function index($idMail){
    var_export("Pasa por aqui 1");
    $query = array(
      'conditions' => array(
        "idMail" => (string) $idMail,
        "status" => "sent",
        "bounced" => 0,
        "spam"  => 0
      ),
      'fields' => array(
        'idContact' => 1,
      )
    );
    var_export("Pasa por aqui 2");
    var_dump($query);
    $findMxc = \Mxc::find($query);
    var_export("Pasa por aqui 3");
    var_export(count($findMxc));
    var_export("Pasa por aqui 4");
    $arrayInidContact1 = array();
    $arrayInidContact2 = array();
    $arrayNotInidContact = array();
    foreach($findMxc as $key => $mxc){
      /*$mxc = \Mxc::findFirst([["_id" => new \MongoId((string) $mxc->_id)]]);
      var_export("\n");
      var_export("+++++++++++++++++++++++++++++");
      var_export($key);
      var_export("+++++++++++++++++++++++++++++");
      var_export("\n");
      $mxc->status = 'scheduled';
      $mxc->save();
      var_export("\n");
      var_export("+++++++++++++++++++++++++++++");
      var_export($mxc->status);
      var_export("+++++++++++++++++++++++++++++");
      var_export("\n");
      if($key >= 8294){
        break;
      }*/
      if(!in_array($mxc->idContact, $arrayInidContact1)){
        $arrayInidContact1[] = (int) $mxc->idContact;
        $arrayInidContact2[] = [
            "id"  => (string) $mxc->_id,
            "idContact" => (int) $mxc->idContact
        ];
      } else {
        $arrayNotInidContact[] = [
            "id"  => (string) $mxc->_id,
            "idContact" => (int) $mxc->idContact
        ];
        $mxc = \Mxc::findFirst([["_id" => new \MongoId((string) $mxc->_id)]]);
        var_export("\n");
        var_export("+++++++++++++++++++++++++++++");
        var_export($mxc->status);
        var_export("+++++++++++++++++++++++++++++");
        var_export("\n");
        $mxc->status = 'scheduled';
        $mxc->save();
        var_export("\n");
        var_export("+++++++++++++++++++++++++++++");
        var_export($mxc->status);
        var_export("+++++++++++++++++++++++++++++");
        var_export("\n");
        //if($key >= 31){
        //  break;
        //}
        
      }
    }
    
    var_export("Pasa por aqui 5");
    var_export(count($arrayInidContact2));
    var_export("Pasa por aqui 6");
    var_export(count($arrayNotInidContact));
    var_export("Pasa por aqui 7");
    unset($findMxc);
    exit;
  }
}