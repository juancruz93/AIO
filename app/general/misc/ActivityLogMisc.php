<?php

namespace Sigmamovil\General\Misc;

class ActivityLogMisc {

  public function __construct() {
    
  }

  public function saveActivityLog($user, $service, $amount, $desc) {
    $activitylog = new \ActivityLog();
    $activitylog->idUser = $user->idUser;
    $activitylog->idServices = $service;
    $activitylog->amount = $amount;
    $activitylog->dateTime = \date("Y-m-d H:i:s", time());
    $activitylog->description = $desc;
    $activitylog->createdBy = $user->email;
    $activitylog->updatedBy = $user->email;
    if (!$activitylog->save()) {
      foreach ($activitylog->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
  }

}
