<?php
namespace Sigmamovil\General\Misc;

class UpdateNotification {
    protected $_db;
    protected $_HOST = "localhost";
    protected $_USER = "yanaconas_user";
    protected $_PASS = "y4n4c0n45M0tor$$*6102";
    protected $_DB_NAME = "yanaconas";
    protected $_DB_CHARSET = "utf8";
    protected $_result;
    protected $_success = 0;
    protected $_failed = 0;
    protected $_ID_NOTIFICATION = 0;

    public function setSent($sent, $nosent){
        $this->_success = $sent;
        $this->_failed = $nosent;
    }
    
    public function setIdNotification($id){
        $this->_ID_NOTIFICATION = $id;
    }
	
    public function __construct()
    {
        $this->_db = new \mysqli($this->_HOST, $this->_USER, $this->_PASS, $this->_DB_NAME);

        if ( $this->_db->connect_errno ){
            \Phalcon\DI::getDefault()->get('logger')->log("Fallo al conectar a BD Yanaconas: ".$this->_db->connect_error);
            exit();   
        }

        $this->_db->set_charset($this->_DB_CHARSET);
    }
	
    public function executeQuery(){
        \Phalcon\DI::getDefault()->get('logger')->log("Entro a executeQuery*******");
            $query = "UPDATE notification set failed = failed + ".$this->_failed .", success = success + ".$this->_success ." WHERE idnotification = " .$this->_ID_NOTIFICATION;
            if ($result = $this->_db->query($query)) {
                $this->_result = $result;
                \Phalcon\DI::getDefault()->get('logger')->log("Se actualizo la notificacion {$this->_ID_NOTIFICATION} de Yanaconas");
                unset($result);
                $this->_db->close();
            }else{
                throw new \InvalidArgumentException("Fallo en la actualizacion idnotificacion: {$this->_ID_NOTIFICATION} de Yanaconas - {$query}");
                \Phalcon\DI::getDefault()->get('logger')->log("Fallo en la actualizacion idnotificacion: {$this->_ID_NOTIFICATION} de Yanaconas");
                $this->_db->close();
                exit();
            }
    }
}

?>