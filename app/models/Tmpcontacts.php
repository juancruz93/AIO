<?php

use Phalcon\Mvc\Collection;

class Tmpcontacts extends Collection
{

    public function getSource()
    {
        return "tmpcontacts";
    }

    public function writeAttribute($attribute, $value) {
        return $this->{$attribute} = $value;
    }

    public function beforeValidationOnUpdate()
    {
        $this->updated = time();
    }

    public function beforeValidationOnCreate()
    {
        $this->created = time();
        $this->updated = time();
        /* $email = (isset(\Phalcon\DI::getDefault()->get('user')->email) ? \Phalcon\DI::getDefault()->get('user')->email : "Indefinido");
         $this->createdBy = $email;
         $this->updatedBy = $email;
         if (isset($this->email)) {
             $this->email = strtolower($this->email);
         }*/
    }
}
