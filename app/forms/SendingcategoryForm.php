<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;

class SendingcategoryForm extends Form
{
    public function initialize()
    {
        $this->add(new Text("name", array(                        
            'class' => 'input-field input-hoshi',
            'required' => 'required',
            'autofocus' => 'autofocus',
        )));
        
        $this->add(new Text("description", array(                     
            'class' => 'input-field input-hoshi',
            'required' => 'required'
        )));
    }   
}
