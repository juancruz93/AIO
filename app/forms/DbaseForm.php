<?php

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text;

class DbaseForm extends Form
{
    public function initialize()
    {        
        $this->add(new Text("name", array(
            'class' => 'input-field input-hoshi',
            'autofocus' => 'autofocus',
            'required' => 'required',
        )));
        
        $this->add(new Text("description", array(
            'class' => 'input-field input-hoshi'
        )));
        
        $this->add(new Text("color", array(
            'id' => 'color',
            'value' => '#fff2f2',
        )));
    }
}
