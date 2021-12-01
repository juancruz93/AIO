<?php

class Action extends \Phalcon\Mvc\Model
{
    public $idResource;
    public $idAction;
    
    public function initialize()
    {
        $this->belongsTo('idResource', 'Resource', 'idResource');
        $this->hasMany('idAction', 'Allowed', 'idAction');
    }
}
