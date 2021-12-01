<?php

namespace Sigmamovil\General\FormElements;

use Phalcon\Forms\Element;

class Url extends Element {

    public function render($attributes = null) {
        
        $html = '<input type="url" value="' . $this->_attributes['value'] . '" maxlength="' . $this->_attributes['maxlength'] . '" ' . $this->_attributes['autofocus'] . ' ' . $this->_attributes['required'] . ' id="' . (isset($this->_attributes['id']) ? $this->_attributes['id'] : $this->_attributes['name']) . '" name="' . $this->_attributes['name'] . '" class="' . $this->_attributes['class'] . '"/>';

        return $html;
    }

}