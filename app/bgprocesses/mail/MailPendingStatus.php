<?php
/**
 * Description of MailPendingStatus
 *
 * @author jose.quinones
 */
require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$sending = new MailPendingStatus();

$sending->sendingStart($id);

class MailPendingStatus {
  public function __construct() {
    $this->db = \Phalcon\DI::getDefault()->get('db');
  }
  
  public function sendingStart($idMail) {
    try {
      \Phalcon\DI::getDefault()->get('logger')->log("Inicio sendingStart Mail{$idMail}");
      /* Se busca el mail */
      $mail = \Mail::findFirst(array(
        "conditions" => "idMail = ?0 AND status = 'sending' AND sentprocessstatus = 'finished' ",
        "bind" => array(0 => $idMail)
      ));
      /* Se busca el contenido del mail */
      $mailContent = \MailContent::findFirst(array(
                  'conditions' => 'idMail = ?0',
                  'bind' => array(0 => $idMail)
      ));

      /* Se valida que el mail y el contenido del mail exista */
      if (!$mail || !$mailContent) {
        throw new InvalidArgumentException('El Mail no existe o ya se esta entregando los correos');
      }
      unset($mailContent);
      $mxcCount = Mxc::count([["idMail" => $idMail, "status" => "sent"]]);
      //Se coloca el valor de la consulta
      $mail->messagesSent = $mxcCount;
      $mail->quantitytarget = $mxcCount;
      $mail->status = 'sent';
      $mail->update();
      \Phalcon\DI::getDefault()->get('logger')->log("Se Actualizo la campaña con messagesSent{$mail->messagesSent} Mail{$idMail}");
      \Phalcon\DI::getDefault()->get('logger')->log("Se Actualizo la campaña con quantitytarget{$mail->quantitytarget} Mail{$idMail}");
      \Phalcon\DI::getDefault()->get('logger')->log("Se Actualizo la campaña con status{$mail->status} Mail{$idMail}");
      unset($mail);
      unset($mxcCount);
      \Phalcon\DI::getDefault()->get('logger')->log("Final sendingStart Mail{$idMail}");
      return true;
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    } catch (\Exception $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Ocurrio un error: " . $ex->getMessage());
      \Phalcon\DI::getDefault()->get('logger')->log($ex->getTrace());
    }
  }
}
