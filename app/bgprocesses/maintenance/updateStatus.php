<?php
require_once(__DIR__ . "/../bootstrap/index.php");
$updateStatus = new updateStatus();

$updateStatus->changeStatus();

class updateStatus {

  public function changeStatus() {

    try {
      \Phalcon\DI::getDefault()->get('logger')->log("Inicio el proceso de actualizar stados de contact list");
      $idContactlistArray = [3634, 3636, 3637, 3638, 3639, 3640, 3641, 3642, 3643, 3644, 3645, 3648, 3649, 3650, 3651, 3653, 3654, 3655, 3656, 3657, 3659, 3660, 3662, 3663, 3364, 3665, 3666, 3667, 3668, 3669, 3670, 3671, 3672, 3674, 3675, 3676, 3678, 3679, 3680, 3684, 3687, 3688, 3689, 3690, 3691, 3692, 3693, 3694, 3695, 3696, 3697, 3698, 3700, 3701, 3702, 3703, 3704, 3705, 3706, 3707, 3708, 3723, 3724, 3725, 3726, 3727, 3728, 3729, 3730, 3731, 3732, 3733, 3734, 3735, 3736, 3737, 3740, 3742];

      foreach ($idContactlistArray as $idContactlist) {

        $cxcl = \Cxcl::find(array(
                    "conditions" => "idContactlist = ?0 ",
                    "fields" => "idContact",
                    "bind" => array(0 => $idContactlist)
        ));
        foreach ($cxcl as $value) {
          $contion = array(''
              . 'conditions' => array(
                  'idContact' => (int) $value->idContact
              ),
              'fields' => array(
                  'idContactlist.' . $value->idContactlist => 1
          ));
          $queryEmailContact = \Cxc::find($contion);

          foreach ($queryEmailContact[0]->idContactlist[$value->idContactlist] as $val) {
            $cxclUpdate = \Cxcl::findFirst(array(
                        "conditions" => "idContactlist = ?0 and idContact = ?1",
                        "bind" => array(0 => 200, 1 => $value->idContact)
            ));
            if (($val['name'] == 'Estado') && (strtolower($val['value']) == 'des-suscrito')) {

              $time = time();
              $cxclUpdate->idContactlist = $value->idContactlist;
              $cxclUpdate->status = 'unsubscribed';
              $cxclUpdate->unsubscribed = $time;
              $cxclUpdate->active = 0;
              $cxclUpdate->spam = 0;
              $cxclUpdate->bounced = 0;
              $cxclUpdate->blocked = 0;

              $cxclUpdate->update();
            }
            if (($val['name'] == 'Estado') && (strtolower($val['value']) == 'rebotado')) {
              $time = time();
              $cxclUpdate->idContactlist = $value->idContactlist;
              $cxclUpdate->status = 'bounced';
              $cxclUpdate->unsubscribed = 0;
              $cxclUpdate->active = 0;
              $cxclUpdate->spam = 0;
              $cxclUpdate->bounced = $time;
              $cxclUpdate->blocked = 0;

              $cxclUpdate->update();
            }
            if (($val['name'] == 'Estado') && (strtolower($val['value']) == 'spam')) {
              $time = time();
              $cxclUpdate->idContactlist = $value->idContactlist;
              $cxclUpdate->status = 'spam';
              $cxclUpdate->unsubscribed = 0;
              $cxclUpdate->active = 0;
              $cxclUpdate->spam = $time;
              $cxclUpdate->bounced = 0;
              $cxclUpdate->blocked = 0;

              $cxclUpdate->update();
            }
          }
        }

        $contactlist = \Contactlist::findFirst(array(
                    "conditions" => "idContactlist = ?0",
                    "bind" => array(0 => $idContactlist)
        ));

        $Rebotados = \Cxcl::find(array(
                    "conditions" => "idContactlist = ?0 and status = ?1",
                    "bind" => array(0 => $idContactlist, 1 => 'bounced')
        ));

        $Spam = \Cxcl::find(array(
                    "conditions" => "idContactlist = ?0 and status = ?1",
                    "bind" => array(0 => $idContactlist, 1 => 'spam')
        ));


        $Dessuscritos = \Cxcl::find(array(
                    "conditions" => "idContactlist = ?0 and status = ?1",
                    "fields" => "idContact",
                    "bind" => array(0 => $idContactlist, 1 => 'unsubscribed')
        ));
        $Activos = \Cxcl::find(array(
                    "conditions" => "idContactlist = ?0 and status = ?1",
                    "fields" => "idContact",
                    "bind" => array(0 => $idContactlist, 1 => 'active')
        ));

        $contactlist->cbounced = count($Rebotados);
        $contactlist->cspam = count($Spam);
        $contactlist->cunsubscribed = count($Dessuscritos);
        $contactlist->cactive = count($Activos);
        $contactlist->update();
        \Phalcon\DI::getDefault()->get('logger')->log("el proceso de actualizar stados de contact list va por :" .$idContactlist);
      }
    } catch (InvalidArgumentException $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Error al actualizar estados de contactlist:" . $ex);
    } catch (Exception $ex) {
      \Phalcon\DI::getDefault()->get('logger')->log("Error al actualizar estados de contactlist:" . $ex);
    }
  }

}
