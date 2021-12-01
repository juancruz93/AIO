<?php

namespace Sigmamovil\General\View;

class FlashMessages {

  protected $messagesInfo = array();
  protected $messagesAdmin = array();
  protected $messagesFooter = array();
  protected $session;

  public function __construct() {
    $this->session = \Phalcon\DI::getDefault()->get('session');
  }

  private function searchMessagesInfo() {
    
//    var_dump(\Phalcon\DI::getDefault()->get('user')->UserType->Allied->idMasteraccount);
//    exit;

//    $this->session->set("flash-{$flash->idFlash}", true);
//    $this->session->set("flash-1", true);
//
//    var_dump($this->session->get("flash-1"));
//    exit;

    $messagesinfo = \Flashmessage::find(array(
                'conditions' => "start <= ?1 AND 'end' >= ?2 AND category = 'info'",
                'bind' => array(1 => \time(),
                    2 => \time())
    ));
    foreach ($messagesinfo as $msginf) {
      $target = json_decode($msginf->target);
      if (\Phalcon\DI::getDefault()->get('user')->UserType->idMasteraccount) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idMasteraccount, $target) and ! isset($msginf->idMasteraccount) and ! isset($msginf->idAllied) and ! isset($msginf->idAccount)) {
          $this->messagesInfo[] = $msginf;
//          $this->session->set("flash-{$msginf->idFlashmessage}", true);
        }
      } else if (\Phalcon\DI::getDefault()->get('user')->UserType->idAllied) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idAllied, $target) and $msginf->idMasteraccount == \Phalcon\DI::getDefault()->get('user')->UserType->Allied->idMasteraccount) {
          $this->messagesInfo[] = $msginf;
//          $this->session->set("flash-{$msginf->idFlashmessage}", true);
        }
      } else if (\Phalcon\DI::getDefault()->get('user')->UserType->idAccount) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idAccount, $target) and $msginf->idAllied == \Phalcon\DI::getDefault()->get('user')->UserType->Account->Allied->idAllied) {
          $this->messagesInfo[] = $msginf;
//          $this->session->set("flash-{$msginf->idFlashmessage}", true);
        }
      }
    }

// 
//    if (\count($messagesinfo) > 0) {
//      foreach ($messagesinfo as $msg) {
//        if ($msg->accounts == 'all' || $msg->accounts == null) {
//          $this->messagesInfo[] = $msg;
//        } else {
//          $idUser = \Phalcon\DI::getDefault()->get('session')->get('idUser');
//          $user = \User::findFirst(array(
//                      'conditions' => 'idUser = ?1',
//                      'bind' => array(1 => $idUser)
//          ));
//          $accounts = \json_decode($msg->accounts);
//          foreach ($accounts as $account) {
//            if ($account == $user->idAccount) {
//              $this->messagesInfo[] = $msg;
//            }
//          }
//        }
//      }
//    }
  }

  private function searchMessagesAdmin() {

    $messagesadmin = \Flashmessage::find(array(
                'conditions' => "start <= ?1 AND 'end' >= ?2 AND category = 'admin'",
                'bind' => array(1 => \time(),
                    2 => \time())
    ));
    foreach ($messagesadmin as $msgadmin) {
      $target = json_decode($msgadmin->target);
      if (\Phalcon\DI::getDefault()->get('user')->UserType->idMasteraccount) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idMasteraccount, $target) and ! isset($msgadmin->idMasteraccount) and ! isset($msgadmin->idAllied) and ! isset($msgadmin->idAccount)) {
          $this->messagesAdmin[] = $msgadmin;
        }
      } else if (\Phalcon\DI::getDefault()->get('user')->UserType->idAllied) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idAllied, $target) and $msgadmin->idMasteraccount == \Phalcon\DI::getDefault()->get('user')->UserType->Allied->idMasteraccount) {
          $this->messagesAdmin[] = $msgadmin;
        }
      } else if (\Phalcon\DI::getDefault()->get('user')->UserType->idAccount) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idAccount, $target) and $msgadmin->idAllied == \Phalcon\DI::getDefault()->get('user')->UserType->Account->Allied->idAllied) {
          $this->messagesAdmin[] = $msgadmin;
        }
      }
    }

//    $messagesadmin = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
//            ->from("Flashmessage")
//            ->where("'start' <= " . time() . " AND 'end' <= " . time() . " AND category = 'admin'")
//            ->getQuery()
//            ->execute();
//    var_dump($messagesadmin);
//    var_dump(time());
//    exit();
//    $user = \Phalcon\DI::getDefault()->get('user');
//    if ($user->Usertype->idSubaccount) {
//      $user = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
//              ->from('User')
//              ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
//              ->leftJoin("City", "City.idCity = User.idCity")
//              ->where("Usertype.idSubaccount = {$user->Usertype->idSubaccount}")
//              ->orderBy('User.created')
//              ->getQuery()
//              ->getSingleResult();
//    }
//    if (\count($messagesadmin) > 0) {
//      foreach ($messagesadmin as $msg) {
//        if ($msg->allied == 'all' || $msg->allied == null) {
//          if ($user->Usertype->idAllied) {
//            $this->messagesAdmin[] = $msg;
//          }
//        } else {
//          $allied = \json_decode($msg->allied);
//          if (isset($allied) && $allied !== '') {
//            foreach ($allied as $al) {
//              if ($al == $user->Usertype->idAllied) {
//                $this->messagesAdmin[] = $msg;
//              }
//            }
//          }
//        }
//        if ($msg->accounts == 'all' || $msg->accounts == null) {
//          if ($user->Usertype->idAccount && !isset($user->Usertype->idSubaccount)) {
//            $this->messagesAdmin[] = $msg;
//          } else if (isset($user->Usertype->idSubaccount)) {
//            $this->messagesAdmin[] = $msg;
//          }
//        } else {
//          $accounts = \json_decode($msg->accounts);
//          if (isset($accounts) && $accounts !== '') {
//            foreach ($accounts as $account) {
//              if ($account == $user->Usertype->idAccount && !isset($user->Usertype->idSubaccount)) {
//                $this->messagesAdmin[] = $msg;
//              } else if (isset($user->Usertype->idSubaccount)) {
//                $this->messagesAdmin[] = $msg;
//              }
//            }
//          }
//        }
//      }
//    }
  }

  private function searchMessagesFooter() {

    $messagesfooter = \Flashmessage::find(array(
                'conditions' => "start <= ?1 AND 'end' >= ?2 AND category = 'footer'",
                'bind' => array(1 => \time(),
                    2 => \time())
    ));
    foreach ($messagesfooter as $msgfoot) {
      $target = json_decode($msgfoot->target);
      if (\Phalcon\DI::getDefault()->get('user')->UserType->idMasteraccount) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idMasteraccount, $target) and ! isset($msgfoot->idMasteraccount) and ! isset($msgfoot->idAllied) and ! isset($msgfoot->idAccount)) {
          $this->messagesFooter[] = $msgfoot;
        }
      } else if (\Phalcon\DI::getDefault()->get('user')->UserType->idAllied) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idAllied, $target) and $msgfoot->idMasteraccount == \Phalcon\DI::getDefault()->get('user')->UserType->Allied->idMasteraccount) {
          $this->messagesFooter[] = $msgfoot;
        }
      } else if (\Phalcon\DI::getDefault()->get('user')->UserType->idAccount) {
        if (in_array(\Phalcon\DI::getDefault()->get('user')->UserType->idAccount, $target) and $msgfoot->idAllied == \Phalcon\DI::getDefault()->get('user')->UserType->Account->Allied->idAllied) {
          $this->messagesFooter[] = $msgfoot;
        }
      }
    }

//    $messagesadmin = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
//            ->from("Flashmessage")
//            ->where("'start' <= " . time() . " AND 'end' <= " . time() . " AND category = 'admin'")
//            ->getQuery()
//            ->execute();
//    var_dump($messagesadmin);
//    var_dump(time());
//    exit();
//    $user = \Phalcon\DI::getDefault()->get('user');
//    if ($user->Usertype->idSubaccount) {
//      $user = \Phalcon\DI::getDefault()->get('modelsManager')->createBuilder()
//              ->from('User')
//              ->join("Usertype", "Usertype.idUsertype = User.idUsertype")
//              ->leftJoin("City", "City.idCity = User.idCity")
//              ->where("Usertype.idSubaccount = {$user->Usertype->idSubaccount}")
//              ->orderBy('User.created')
//              ->getQuery()
//              ->getSingleResult();
//    }
//    if (\count($messagesadmin) > 0) {
//      foreach ($messagesadmin as $msg) {
//        if ($msg->allied == 'all' || $msg->allied == null) {
//          if ($user->Usertype->idAllied) {
//            $this->messagesAdmin[] = $msg;
//          }
//        } else {
//          $allied = \json_decode($msg->allied);
//          if (isset($allied) && $allied !== '') {
//            foreach ($allied as $al) {
//              if ($al == $user->Usertype->idAllied) {
//                $this->messagesAdmin[] = $msg;
//              }
//            }
//          }
//        }
//        if ($msg->accounts == 'all' || $msg->accounts == null) {
//          if ($user->Usertype->idAccount && !isset($user->Usertype->idSubaccount)) {
//            $this->messagesAdmin[] = $msg;
//          } else if (isset($user->Usertype->idSubaccount)) {
//            $this->messagesAdmin[] = $msg;
//          }
//        } else {
//          $accounts = \json_decode($msg->accounts);
//          if (isset($accounts) && $accounts !== '') {
//            foreach ($accounts as $account) {
//              if ($account == $user->Usertype->idAccount && !isset($user->Usertype->idSubaccount)) {
//                $this->messagesAdmin[] = $msg;
//              } else if (isset($user->Usertype->idSubaccount)) {
//                $this->messagesAdmin[] = $msg;
//              }
//            }
//          }
//        }
//      }
//    }
  }

  public function getLengthInfo() {
    $this->searchMessagesInfo();
    return \count($this->messagesInfo);
  }

  public function getLengthAdmin() {
    $this->searchMessagesAdmin();
    return \count($this->messagesAdmin);
  }

  public function getLengthFooter() {
    $this->searchMessagesFooter();
    return \count($this->messagesFooter);
  }

  public function getMessagesInfo() {
    $msg = $this->messagesInfo;
    $this->messagesInfo = array();
    return $msg;
  }

  public function getMessagesAdmin() {
    $msg = $this->messagesAdmin;
    $this->messagesAdmin = array();
    return $msg;
  }

  public function getMessagesFooter() {
    $msg = $this->messagesFooter;
    $this->messagesFooter = array();
    return $msg;
  }

}
