<?php
require_once(__DIR__ . "/../bootstrap/index.php");

/**
 * Description of CounterMailSent
 *
 * @author jordan.zapata
 */


	$countMail =  new CounterMailSent();
	$countMail->countMail();

class CounterMailSent {
  

	public function countMail(){


		$Mail = \Mail::find(array(
           					  //"columns"=>"idMail",
                              "conditions" => "scheduleDate BETWEEN ?0 AND ?1 AND status ='sent' and messagesSent > 0",
                              "bind" => array(0 => '2017-10-01 00:00', 1 => '2017-12-24 00:00')
                  ));


		foreach ($Mail as $value) {
	      	$contion = array(
             'conditions' => array(
                'idMail' => (String)$value->idMail
		            )
		        );
   
      		$mxc = \Mxc::count($contion);

      		if (!(int)$mxc > 0 && ((int)$value->idMail > 6333)) {
      			var_dump($value->idMail);
      			$value->status = 'paused';
      			$value->sentprocessstatus = 'loading-target';
      			if ($value->save()) {
      				exec("php /websites/aio/app/bgprocesses/mail/MailSenderexample.php ".$value->idMail);

      				$value->status = 'sent';
	      			$value->sentprocessstatus = 'finished';
	      			$value->save();

      			}
      		}

		}
	}

}
