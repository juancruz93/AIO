<?php

namespace Sigmamovil\General\View;

class NotificationManager
{
    public $notification = array();
    public $session;
    public $logger;
    
    public function __construct() 
    {
        $this->session = \Phalcon\DI::getDefault()->get('session');
        $this->logger = \Phalcon\DI::getDefault()->get('logger');
    }


    public function error($message) 
    {
        $this->session->set('danger', $message);
    }
    
    public function success($message) 
    {
        $this->session->set('success', $message);
    }
    
    public function warning($message) 
    {
        $this->session->set('warning', $message);
    }
    
    public function info($message) 
    {
        $this->session->set('info', $message);
    }
  
    public function notification() 
    {
        if ($this->session->has("danger") || $this->session->has("success") || $this->session->has("warning") || $this->session->has("info")) {
            return true;
        }
        
        return false;
    }        
        
    public function getNotification()
    {
        if ($this->session->has("danger")) {
            $message = new \stdClass();
            $message->type = 'danger';
            $message->message = $this->session->get("danger");
            $this->notification[] = $message;
            $this->session->remove("danger");
        }
        
        if ($this->session->has("success")) {
            $message = new \stdClass();
            $message->type = 'success';
            $message->message = $this->session->get("success");
            $this->notification[] = $message;
            $this->session->remove("success");
        }
        
        if ($this->session->has("warning")) {
            $message = new \stdClass();
            $message->type = 'warning';
            $message->message = $this->session->get("warning");
            $this->notification[] = $message;
            $this->session->remove("warning");
        }
        
        if ($this->session->has("info")) {
            $message = new \stdClass();
            $message->type = 'info';
            $message->message = $this->session->get("info");
            $this->notification[] = $message;
            $this->session->remove("info");
        }
        
        return $this->notification;
    }
}
