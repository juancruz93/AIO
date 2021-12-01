<?php

require_once(__DIR__ . "/../bootstrap/index.php");

if (isset($argv[1])) {
  $idAccount = $argv[1];
}
$scriptSms = new ScriptSms();
//$scriptSms->start();
$scriptSms->index($idAccount);

/**
 * Description of ScriptSms
 *
 * @author jose.quinones
 */
class ScriptSms {
  
  protected $db;
  
  public function __construct() {
    $di = \Phalcon\DI::getDefault();
    $this->db = $di->get('db');
  }

  public function start(){
    //
    $flag = true;
    $limit = 8000;
    $offset = 0;
    $arrayData = array();
    //
    while ($flag){
      // Hacemos la consulta.
      $findSmslote = \Smslote::query()
        ->columns(['Account.idAccount AS idAccount, Account.name AS nameAccount, Subaccount.idSubaccount AS idSubaccount, Subaccount.name AS nameSubaccount, COUNT(Smslote.idSmslote) AS total'])
        ->leftJoin('Sms','Sms.idSms = Smslote.idSms')
        ->leftJoin('Subaccount','Subaccount.idSubaccount = Sms.idSubaccount')   
        ->leftJoin('Account','Account.idAccount = Subaccount.idAccount')       
        ->where("Sms.type = 'single' AND Smslote.messageCount IS NULL AND Sms.status = 'sent'")
        ->limit($limit, $offset)
        ->execute();
      // Recorremos los 8000 registros.
      foreach ($findSmslote as $smslote){
        // Llenamos  el array con la data
        $arrayData[] = [
          'idAccount' => $smslote->idAccount,
          'nameAccount' => $smslote->nameAccount, 
          'idSubaccount' => $smslote->idSubaccount, 
          'nameSubaccount' => $smslote->nameSubaccount, 
          'total' => $smslote->total, 
        ];
      }
      // Al offset le asignamos el mismo offset mas el limit
      $offset = $offset + $limit;
      // Si el array np esta definido o la cantidad de registros es menos o igual al limit se cierra la iteraccion
      if (!isset($arrayData) || count($arrayData) <= $limit) {
        $flag = false;
      }
    }
    // Imprimimos el array con la data
    var_dump($arrayData); exit;
  }
  
  public function index($id){
    //
    $flag = true;
    $limit = 8000;
    $offset = 0;
    $i = 0;
    //
    while ($flag){
      $findSmslote = Smslote::query()
        ->columns(['Smslote.idSmslote, Sms.status'])       
        ->leftJoin('Sms','Sms.idSms = Smslote.idSms')   
        ->where("Sms.idSubaccount = {$id} AND Sms.status = 'sent' AND Smslote.status = 'sent' AND Smslote.messageCount IS NULL")  
        ->limit($limit, $offset)
        ->execute();
      // Recorremos los 8000 registros.
      foreach ($findSmslote as $smslote){
        // Llenamos  el array con la data
        if($smslote->status == 'sent'){
          $messageCount = 1;
        } else {
          $messageCount = 0;
        }
        $sql = "UPDATE smslote SET messageCount = {$messageCount} WHERE idSmslote = {$smslote->idSmslote}";
        $this->db->execute($sql);
      }
      //
      $i++;
      // Al offset le asignamos el mismo offset mas el limit
      $offset = $offset + $limit;
      // Si el array np esta definido o la cantidad de registros es menos o igual al limit se cierra la iteraccion
      if (!isset($i) || count($i) <= $limit) {
        $flag = false;
      }
    }
  }
}
