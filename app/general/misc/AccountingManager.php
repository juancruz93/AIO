<?php

namespace Sigmamovil\General\Misc;

class AccountingManager
{

  public function alliedConfigEdit($data, $config) {

    $configMasteraccount = \Phalcon\DI::getDefault()->get('Config')->findFirst(array(
      'conditions' => 'idMasteraccount = ?1',
      'bind' => array(1 => $config->Allied->idMasteraccount)
    ));

    foreach ($data->allied->alxs as $key) {
      if ($key->idServices == $this->services->sms) {
        if (!is_numeric($data->smsLimit)) {
          throw new \InvalidArgumentException("El campo limite de sms no puede estar vacio");
        }
        $sl = $data->smsLimit - $config->smsLimit;
        if ($sl < 0) {
          $configMasteraccount->smsLimit = $configMasteraccount->smsLimit + ($sl * -1);
        } else {
          $configMasteraccount->smsLimit = $configMasteraccount->smsLimit - $sl;
        }
      }
      if ($key->idServices == $this->services->sms_two_way) {
        if (!is_numeric($data->smstwowayLimit)) {
          throw new \InvalidArgumentException("El campo limite de sms no puede estar vacio");
        }
        $sltw = $data->smstwowayLimit - $config->smstwowayLimit;
        if ($sltw < 0) {
          $configMasteraccount->smstwowayLimit = $configMasteraccount->smstwowayLimit + ($sltw * -1);
        } else {
          $configMasteraccount->smstwowayLimit = $configMasteraccount->smstwowayLimit - $sltw;
        }
      }
      if ($key->idServices == $this->services->landing_page) {
        if (!is_numeric($data->landingpageLimit)) {
          throw new \InvalidArgumentException("El campo limite de landing no puede estar vacio");
        }
        $lan = $data->landingpageLimit - $config->landingpageLimit;
        if ($lan < 0) {
          $configMasteraccount->landingpageLimit = $configMasteraccount->landingpageLimit + ($lan * -1);
        } else {
          $configMasteraccount->landingpageLimit = $configMasteraccount->landingpageLimit - $lan;
        }
      }
      if ($key->idServices == $this->services->email_marketing) {
        if (!is_numeric($data->fileSpace)) {
          throw new \InvalidArgumentException("El campo almacenamiento no puede estar vacio");
        }
        if (!is_numeric($data->mailLimit)) {
          throw new \InvalidArgumentException("El campo limite de correos no puede estar vacio");
        }
        if (!is_numeric($data->contactLimit)) {
          throw new \InvalidArgumentException("El campo limite de contactos no puede estar vacio");
        }
        $fs = $data->fileSpace - $config->fileSpace;
        if ($fs < 0) {
          $configMasteraccount->fileSpace = $configMasteraccount->fileSpace + ($fs * -1);
        } else {
          $configMasteraccount->fileSpace = $configMasteraccount->fileSpace - $fs;
        }
        $ml = $data->mailLimit - $config->mailLimit;
        if ($ml < 0) {
          $configMasteraccount->mailLimit = $configMasteraccount->mailLimit + ($ml * -1);
        } else {
          $configMasteraccount->mailLimit = $configMasteraccount->mailLimit - $ml;
        }
        $cl = $data->contactLimit - $config->contactLimit;
        if ($cl < 0) {
          $configMasteraccount->contactLimit = $configMasteraccount->contactLimit + ($cl * -1);
        } else {
          $configMasteraccount->contactLimit = $configMasteraccount->contactLimit - $cl;
        }
      }
    }
    if (!$data->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($data->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    if (!$configMasteraccount->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      $this->trace("fail", "No se pudo editar la configuracion");
      foreach ($configMasteraccount->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

  public function accountConfigEdit($account, $alliedConfig) {
    $configAccount = \Phalcon\DI::getDefault()->get('Accountclassification')->findFirst(array(
      'conditions' => 'idAccountclassification = ?1',
      'bind' => array(1 => $account->idAccountclassification)
    ));
    foreach ($account->account[0]->axc as $key) {
      if ($key->idServices == \Phalcon\DI::getDefault()->get('services')->sms) {
        $smsLimit = $alliedConfig->smsLimit + $configAccount->smsLimit;
        $totalSmsLimit = $smsLimit - $account->smsLimit;
        if ($totalSmsLimit < 0 || $totalSmsLimit > $smsLimit) {
          throw new \InvalidArgumentException("El Limite de SMS ingresado supera al disponible");
        }
        $alliedConfig->smsLimit = $totalSmsLimit;
      }
      if ($key->idServices == \Phalcon\DI::getDefault()->get('services')->sms_two_way) {
        $smstwowayLimit = $alliedConfig->smstwowayLimit + $configAccount->smstwowayLimit;
        $totalSmstwowayLimit = $smstwowayLimit - $account->$smstwowayLimit;
        if ($totalSmstwowayLimit < 0 || $totalSmstwowayLimit > $smstwowayLimit) {
          throw new \InvalidArgumentException("El Limite de SMS ingresado supera al disponible");
        }
        $alliedConfig->smstwowayLimit = $totalSmstwowayLimit;
      }
      if ($key->idServices == \Phalcon\DI::getDefault()->get('services')->landing_page) {
        $smsLimit = $alliedConfig->landingpageLimit + $configAccount->landingpageLimit;
        $totalLandingpageLimit = landingpageLimit - $account->landingpageLimit;
        if ($totalLandingpageLimit < 0 || $totalLandingpageLimit > landingpageLimit) {
          throw new \InvalidArgumentException("El Limite de Landing ingresado supera al disponible");
        }
        $alliedConfig->landingpageLimit = $totalLandingpageLimit;
      }
      if ($key->idServices == \Phalcon\DI::getDefault()->get('services')->sms->email_marketing) {
        $fileSpace = $alliedConfig->fileSpace + $configAccount->fileSpace;
        $mailLimit = $alliedConfig->mailLimit + $configAccount->mailLimit;
        $contactLimit = $alliedConfig->contactLimit + $configAccount->contactLimit;
        $totalFileSpace = $fileSpace - $account->fileSpace;
        $totalMailLimit = $mailLimit - $account->mailLimit;
        $totalContactLimit = $contactLimit - $account->contactLimit;
        if ($totalFileSpace < 0 || $totalFileSpace > $fileSpace) {
          throw new \InvalidArgumentException("El Espacio disponible en disco (MB) ingresado supera al disponible");
        }
        if ($totalMailLimit < 0 || $totalMailLimit > $mailLimit) {
          throw new \InvalidArgumentException("El Limite de correos ingresado supera al disponible");
        }
        if ($totalContactLimit < 0 || $totalContactLimit > $contactLimit) {
          throw new \InvalidArgumentException("El Limite de contactos ingresado supera al disponible");
        }
        $alliedConfig->fileSpace = $totalFileSpace;
        $alliedConfig->mailLimit = $totalMailLimit;
        $alliedConfig->contactLimit = $totalContactLimit;
      }
    }

    $senderAllowed = $account->senderAllowed;
    $footerEditable = $account->footerEditable;
    $account->senderAllowed = (empty($senderAllowed) ? 0 : 1);
    $account->footerEditable = (empty($footerEditable) ? 0 : 1);
    if (!$account->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($account->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      $this->trace("fail", "No se actualizo la clasificación de cuenta: {$account->idAccountclassification}/{$account->name}");
    }
    if (!$alliedConfig->save()) {
      \Phalcon\DI::getDefault()->get('db')->rollback();
      foreach ($alliedConfig->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      $this->trace("fail", "No se actualizo la clasificación de cuenta: {$account->idAccountclassification}/{$account->name}");
    }
  }

}
