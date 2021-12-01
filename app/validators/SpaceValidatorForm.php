<?php

use \Phalcon\Validation\Message;
use \Phalcon\Validation\Validator;
use \Phalcon\Validation\ValidatorInterface;

class SpaceValidatorForm extends Validator implements ValidatorInterface {
    public function validate(\Phalcon\Validation $validator, $attribute) {
        $value  = $validator->getValue($attribute);
        $value2 = \trim($value);
        
        if (empty($value2) || !$value2) {
            $message = $this->getOption('message');
            
            if (!$message) {
                $message = "El campo {$attribute} no puede estar vacío";
            }

            $validator->appendMessage(new Message($message, $attribute, 'SpaceValidator'));
            
            return false;
        }
        return true;
    }

}
