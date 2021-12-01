<?php

class SxcController extends ControllerBase
{

  public function contactsegmentAction($idSegment) {
    $segment = Segment::findFirst([["idSegment" => (int) $idSegment]]);
    $this->view->setVar("segment", $segment);
    $this->view->setVar("app_name", "sxc");
  }

  public function findcontactsegmentAction() {
    
  }

}
