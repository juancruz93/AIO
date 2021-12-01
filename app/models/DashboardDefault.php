<?php

class DashboardDefault extends Modelbase {

  public $idDashboarddefault,
          $content,
          $created,
          $updated,
          $updatedBy,
          $createdBys;

  public function gerSource(){
    return "dashboard_default";
  }

}