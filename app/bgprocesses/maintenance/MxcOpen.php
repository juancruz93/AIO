<?php

require_once(__DIR__ . "/../bootstrap/index.php");
$data = 0;
if (isset($argv[1])) {
  $data = $argv[1];
}

$mxcopen = new MxcOpen();
$mxcopen->update($data);
class MxcOpen {
  

	public function update($data){
		\Phalcon\DI::getDefault()->get('logger')->log("MXCOPEN-12-18 :: inicio el proceso de MxcOpen");
		$arrayDataMxc = array();
		$contador=0;

	  	$manager = \Phalcon\DI::getDefault()->get('mongomanager');
      	$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);


      $name = \Phalcon\DI::getDefault()->get('path')->path . "tmp/mxc.csv";
      var_dump($name);
      $handle = fopen($name, "r");
      while ((($record = fgets($handle)) !== false)) {
     // var_dump($record);
    
			$arrayInfo = explode(";", $record);
      var_dump($arrayInfo);

      	$contion = array(
             'conditions' => array(
                'idMail' => (String)$arrayInfo[0],
                'idContact'=>(int)$arrayInfo[1]
            ),
            'fields' =>array(
            	'open'=> 1,
            	'totalOpening'=> 1
            ) 
        );
   
      $mxc = \Mxc::find($contion);

      if ($mxc) {


        $Mail = \Mail::find(array(
                      //"columns"=>"idMail",
                              "conditions" => "idMail = ?0",
                              "bind" => array(0 => $arrayInfo[0])
                  ))->toArray();

      
        $nuevafecha = strtotime ( '+1 day' , strtotime ($Mail[0]["scheduleDate"] ) ) ;
        $nuevafecha = date ( 'Y-m-d H:i' , $nuevafecha );
        $nuevafecha = strtotime($nuevafecha);

      	$arrayDataMxc[$contador][]=$arrayInfo[0];
      	$arrayDataMxc[$contador][]=$arrayInfo[1];

      	 $bulkUpdate = new \MongoDB\Driver\BulkWrite;

            $bulkUpdate->update(
            	[
            		'idMail'=> (String)$arrayInfo[0],
            		'idContact' => (int)$arrayInfo[1]
            	], 
            	[
            		'$set' => [
            			'open' =>  (int)$nuevafecha,
            			'totalOpening' => (int) $mxc[0]['totalOpening']+1,
                  'status' => 'sent'
            		]
            	]
            );

            $manager->executeBulkWrite('aio.mxc', $bulkUpdate, $writeConcern);
            unset($bulkUpdate);
            $contador++;
      }
			
		}

    /////////////////////////////////////

		\Phalcon\DI::getDefault()->get('logger')->log("MXCOPEN-12-18 :: final del scritp MxcOpen total processados" . $contador . " y registros modificados :" . json_encode($arrayDataMxc));
	}

}
