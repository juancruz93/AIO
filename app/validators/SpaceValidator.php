<?php

use \Phalcon\Mvc\Model\Validator;
use \Phalcon\Mvc\Model\ValidatorInterface;

class SpaceValidator extends Validator implements ValidatorInterface
{
    public function validate(\Phalcon\Mvc\ModelInterface $model)
    {
        $message = $this->getOption('message');
        $field = $this->getOption('field');

        $value = $model->$field;

        $value2 = \trim($value);

        if (empty($value2)) {
            $this->appendMessage(
                $message,
                $field,
                "SpaceValidator"
            );
            return false;
        }
        return true;
    }

}

