<?php


class blockContact {
    $correos = array();

    $i = 0;
    foreach ($correos as $correo) {
    $data = json_encode(array(
        "name" => "no mando email ni motive",
        "idContactlistCategory" => 101, //categoria de las listas de contacto creadas
        "description" => "Lista creada desde api",
        "email"=> $correo,
        "motive"=>"solicitud"
    ));
        $api_request = new api_request();
    $result = $api_request->send_http_request($apikey, $secret, $uri, $method, $data);
    $i++;
    echo"<pre>Respuesta:<br>";
    echo $i." - ".$correo."<br>";
    print_r(json_decode($result,true));
    echo"</pre>";
    }
}