<?php

$apikey = "49-120-595bc68f5f5d35.77504100";
$secret = "b759a50b0e6af67e09823559d3ec65ead51af0bb"; 
$uri = "https://aio.sigmamovil.com/api/sms/createsmssend";
$method = "POST";

$data = json_encode(array(

	"name" => "prueba de envio desde api",
	"notification" => true,
	"email" => "garfel94@hotmail.com",
	"receiver" => array(
		"type" => "contactlist",
		"contactlists" => array(

			0 => array(
				"idContactList" => "1390",
				"name" => "Equipo De Desarrollo")
			)),
	"message" => 'PRUEBA DE ENVIO POR API %%NOMBRE%% %%APELLIDO%%',
	"idSmsCategory" => 42,
	"datesend" => '',
	"datenow" => true,
	"timezone" => '-0500',
	));

$api_request = new api_request();

$result = $api_request->send_http_request($apikey, $secret, $uri, $method, $data);

var_dump($result);

class api_request {
	
	public function send_http_request($apikey, $secret, $uri, $method, $data){
		$pwd = hash_hmac('sha1', $method . "|" . $uri . "|" . $data, $secret);
		
		$options = array(
			'http' => array(
				'header' => "Authorization: Hmac " . base64_encode($apikey . ":". $pwd),
				'method' => $method,
				'content' => $data
			)
		);
		
		$context = stream_context_create($options);
		var_dump($options);
	
		return file_get_contents($uri, false, $context);
	}
}
