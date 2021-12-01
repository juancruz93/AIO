<?php

class MarketingController extends ControllerBase
{
  
  public function initialize() {
    $this->tag->setTitle("Marketing");
    parent::initialize();
  }

  public function indexAction()
    {
        $platforms = Services::find();
        $this->view->setVar('platforms', $platforms);
    }
}
