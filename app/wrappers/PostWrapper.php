<?php

namespace Sigmamovil\Wrapper;

class PostWrapper extends \BaseWrapper {
  /*
   * 
   */
  public function save($data) {    
   
    $post = new \Post();
    if(isset($data->idSurvey)){
      $post->idSurvey = $data->idSurvey;
    }
    if(isset($data->idLandingPage)){
      $post->idLandingPage = $data->idLandingPage;
    }
    
    $post->description = $data->description;
    $post->scheduleddate = ($data->scheduledDate == 'now')? time() : $data->scheduledDate;
    $post->idPage = $data->idPage;
    $post->link = $data->link;
    $post->idPublish = $data->idPublish;
    if (!$post->save()) {
      foreach ($post->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
      $this->trace("fail", "error guardando en post");
    }
    return array("message"=>"Se registro correctamente el post.");
  }

}
