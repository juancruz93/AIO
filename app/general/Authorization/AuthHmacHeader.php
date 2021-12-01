<?php

namespace Sigmamovil\General\Authorization;

class AuthHmacHeader implements AuthHeader
{
	protected $user;
	protected $pwd;
	protected $header;
	protected $method;
	protected $uri;
	protected $data;
	protected $permissions;
	protected $log;
	protected $authorization;
			
	function __construct($method, $uri, $data,$log)
	{
		$this->log = $log;
		$this->method = $method;
		$this->uri = $uri;
		$this->data = $data;
		$this->authorization = "hmac";
		$this->permissions = array(
		    'apicontact',
            'apiversionone',
            'apicontactlist',
            'apistatics',
            'apimail',
            'apisms',
            'apisubaccount',
            'apismscategory',
            'apismstwoway',
            'apireport',
            'apivoicemessages',
        );
	}

	
	public function verifyHeader()
 {
//  Metodo getallheaders() no funciona con la version del servidor, por lo que se aplica una funcion manual
  $header = $this->_getallheaders();
  if (isset($header['Authorization']) ||  isset($header['authorization'])) {
   $this->header = isset($header['Authorization'])?$header['Authorization']:$header['authorization'];
   return true;
  } else {
            return false;
        }
  
  //throw new \InvalidArgumentException("Autenticación Invalida");
 }
	
	public function processHeader()
	{
		$header_data = explode(" ", $this->header);
        
		if(strtolower($header_data[0]) === 'hmac') {
			$auth = explode(":", base64_decode($header_data[1]));
			if(isset($auth[0]) && isset($auth[1])) {
				$this->user = $auth[0];
				$this->pwd = $auth[1];
				return true;
			}
		}
		if(strtolower($header_data[0]) === 'basic'){
			$auth = explode(":", $header_data[1]);
			if(isset($auth[0]) && isset($auth[1])) {
				$this->user = $auth[0];
				$this->pwd = $auth[1];
				$this->authorization = "basic";
				return true;
			}
		}
		
		throw new \Exception("Autenticación Invalida");
	}
	
	public function checkPermissions($controller, $action)
	{   
            
		if(in_array($controller, $this->permissions)) {
			return true;
		}
		
		throw new \Exception("No tiene permisos para acceder a este recurso");
	}

	public function checkUserPWD(\Apikey $apikey)
	{
		if($this->authorization != "hmac"){
			if($apikey->secret == $this->pwd){
				return true;
			}
			
		}else{
			$msg = $this->method . '|' . $this->uri . '|' . trim($this->data);
			$this->log->log($msg."-".$apikey->secret);
			$hash = hash_hmac('sha1', $msg, $apikey->secret);
		
		
			if($hash == $this->pwd && $apikey->status == 1 ) {
				return true;
			} 
		}
		
		throw new \Exception("HMAC Invalido");
	}
	
	public function getAuthUser()
	{
		return $this->user;
	}
	
	//Funcion para recuperar los Headers del Request, teniendo en cuenta la funcion de PHP getallheaders()
	function _getallheaders()
	{
		if(function_exists('getallheaders')) {
			return getallheaders();
		}
		$headers = array();
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
		
}
