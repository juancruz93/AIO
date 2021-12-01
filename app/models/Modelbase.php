<?php

use \Phalcon\Mvc\Model as Model;

class Modelbase extends Model {

    public function beforeValidationOnCreate() {
        $this->created = time();
        $this->updated = time();

        if (isset($this->email)) {
            $this->email = strtolower($this->email);
        }
        if (isset($this->name)) {
            $this->name = mb_strtolower($this->name, 'UTF-8');
            $this->name = ucwords($this->name);
            $this->name = trim($this->name);
        }

        if (isset($this->lastname)) {
            $this->lastname = mb_strtolower($this->lastname, 'UTF-8');
            $this->lastname = ucwords($this->lastname);
            $this->lastname = trim($this->lastname);
        }

        if (isset($this->description)) {
            $this->description = (empty($this->description) ? "Sin descripción" : $this->description);
        }

        if (isset($this->observations)) {
            $this->observations = (empty($this->observations) ? "Sin observaciones" : $this->observations);
        }
        $email = "Indefinido";
        if (isset($this->createdBy)) {
            $email = $this->createdBy;
        }
        if (\Phalcon\DI::getDefault()->has('user') && isset(\Phalcon\DI::getDefault()->get('user')->email)) {
            $email = \Phalcon\DI::getDefault()->get('user')->email;
        }
        $this->createdBy = $email;
        $this->updatedBy = $email;
        if (isset($this->email)) {
            $this->email = strtolower($this->email);
        }
    }

    public function beforeValidationOnUpdate() {

        if (isset($this->email)) {
            $this->email = strtolower($this->email);
        }
        $this->updated = time();
        $email = ((\Phalcon\DI::getDefault()->has('user') && isset(\Phalcon\DI::getDefault()->get('user')->email)) ? \Phalcon\DI::getDefault()->get('user')->email : "Indefinido");
        if (isset($this->email)) {
            $this->email = strtolower($this->email);
        }

        if (isset($this->description)) {
            $this->description = (empty($this->description) ? "Sin descripción" : $this->description);
        }

        if (isset($this->observations)) {
            $this->observations = (empty($this->observations) ? "Sin observaciones" : $this->observations);
        }

        if (isset($this->name)) {
            $this->name = mb_strtolower($this->name, 'UTF-8');
            $this->name = ucwords($this->name);
        }

        if (isset($this->lastname)) {
            $this->lastname = mb_strtolower($this->lastname, 'UTF-8');
            $this->lastname = ucwords($this->lastname);
        }
        
        if (!isset($this->updatedBy) || $this->updatedBy == "Indefinido") {
          $this->updatedBy = $email;
        }
        
    }

}
