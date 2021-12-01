<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class LandingpagecategoryController extends ControllerBase{
    
    public function initialize() {
        $this->tag->setTitle('Categorias de landing');
        parent::initialize();
    }
    
    public function indexAction(){}
    public function listAction(){}
    public function createAction(){}
    public function deleteAction(){}
}

