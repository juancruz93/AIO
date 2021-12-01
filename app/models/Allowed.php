<?php

class Allowed extends \Phalcon\Mvc\Model
{
    public $idRole;
    public $idAction;
    
    public function initialize()
    {
        $this->belongsTo('idRole', 'Role', 'idRole');
        $this->belongsTo('idAction', 'Action', 'idAction');
    }
}
