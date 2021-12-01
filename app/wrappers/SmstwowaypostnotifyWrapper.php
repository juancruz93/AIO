<?php

namespace Sigmamovil\Wrapper;

/**
 * Description of SmstwowaypostnotifyWrapper
 *
 * @author juan.pinzon
 */
class SmstwowaypostnotifyWrapper extends \BaseWrapper {

  public function createSmsTwowayPostNotify($data) {
    if ((!isset($data["smstwowaydata"]['url']) || $data["smstwowaydata"]['url'] == "") ||
            !isset($data["smstwowaydata"]['password']) || $data["smstwowaydata"]['password'] == "") {
      throw new \InvalidArgumentException("Por Favor Genera una Clave de autenticacion");
    }

    $smstwowaypn = new \Smstwowaypostnotify();
    $smstwowaypn->idSubaccount = $this->user->usertype->Subaccount->idSubaccount;
    $smstwowaypn->url = $data["smstwowaydata"]['url'];
    $smstwowaypn->password = $data["smstwowaydata"]['password'];
    $smstwowaypn->createdBy = $this->user->email;
    $smstwowaypn->updatedBy = $this->user->email;

    if (!$smstwowaypn->save()) {
      foreach ($smstwowaypn->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      $this->trace("fail", "Error al insertar ");
    }
    return array(
        "res" => 1
    );
  }

  public function findPostCredentials($idSubaccount) {
    $conditions = array(
        "conditions" => "idSubaccount= ?0",
        "bind" => [0 => $idSubaccount]
    );
    $resSql = \Smstwowaypostnotify::findFirst($conditions);

    $dataReturn = array();
    if ($resSql == false) {
      $dataReturn = array(
          "res" => 0
      );
    } else {
      $dataReturn = $resSql->toArray();
    }
    return $dataReturn;
  }
  
  public function editSmsTwowayPostNotify($data) {
    if ((!isset($data["smstwowaydata"]['url']) || $data["smstwowaydata"]['url'] == "") ||
            !isset($data["smstwowaydata"]['password']) || $data["smstwowaydata"]['password'] == "") {
      throw new \InvalidArgumentException("Por Favor Genera una Clave de autenticacion");
    }

    //$smstwowaypn = new \Smstwowaypostnotify();
    $arrayFind = array('conditions'=>'idSubaccount =?0','bind'=>[0 => $this->user->usertype->Subaccount->idSubaccount]);
    $smstwowaypn = \Smstwowaypostnotify::findFirst($arrayFind);
    $smstwowaypn->idSubaccount = $this->user->usertype->Subaccount->idSubaccount;
    $smstwowaypn->url = $data["smstwowaydata"]['url'];
    $smstwowaypn->password = $data["smstwowaydata"]['password'];
    $smstwowaypn->createdBy = $this->user->email;
    $smstwowaypn->updatedBy = $this->user->email;

    if (!$smstwowaypn->update()) {
      foreach ($smstwowaypn->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      $this->trace("fail", "Error al actualizar ");
    }
    return array(
        "res" => 1
    );
  }

}
