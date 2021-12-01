<?php 
require_once(__DIR__ . "/../bootstrap/index.php");

$import = new importContactList();
$import->start();
class importContactList{
	
	 protected $logger;
	 protected $path;
	 protected $route;
	 protected $fileManager;
	 protected $idUser;
	 protected $db;
	 protected $fields;
	 protected $arrIdImportContact;
	public function __construct(){
		$di = \Phalcon\DI::getDefault();
		$this->logger = $di->get("logger");
		$this->path = $di->get("tmpPath")->dir;
		$this->route = "/websites/aio/tmp/contactlistimport/";
		$this->fileManager = new \Sigmamovil\General\Misc\FileManager();
		$this->idUser = 1288;
		$this->db = $di->get('db');
    }
    
	public function start(){
		$ContactlistImport = array(
3878=>"parte3.csv",
);
		foreach($ContactlistImport as $key => $value){
		  $this->arrIdImportContact = array();
		  $this->fields = array();
	      $sql = "SET @fila=4;";
		  $this->db->execute($sql);
		  $internalNumber = uniqid();
          $date = date("ymdHi", time());
          $internalName = "{$key}_{$date}_{$internalNumber}.csv";
		  
		  $importfile = new \Importfile();
          $importfile->idContactlist = $key;
          $importfile->idUser = $this->idUser;
          $importfile->internalname = $internalName;
          $importfile->originalname = $value;
		  
		   if (!$importfile->save()) {
                foreach ($importfile->getMessages() as $msg) {
                     $this->logger->log(print_r($msg,true));
                }
            } else {
			    $destiny = $this->path. $internalName;

                $route = $this->route.$value;
                $temparray = $this->fileManager->viewcsv($route, $destiny);
				
				$sqlFields = "SELECT idCustomfield,@fila:=@fila+1 as 'numRow' from customfield where idContactlist = {$key};";
				$customfield = $this->db->fetchAll($sqlFields);
				foreach($customfield as $k => $v){
					$this->fields[$v['idCustomfield']] = $v['numRow'];
				}
				$this->fields['email'] = "4";
                $this->fields['name'] = "1";
                $this->fields['lastname'] = "2";
                $this->fields['birthdate'] = "3";
                $this->fields['indicative'] = null;
                $this->fields['phone'] = null;
                $this->fields['deleted'] = (int) 0;
				
				$importcontactfile = new \Importcontactfile();
                $importcontactfile->idSubaccount = 595;
                $importcontactfile->idImportfile = $importfile->idImportfile;
                $importcontactfile->rows = $temparray["rows"];
                $importcontactfile->status = "Pending";
                $importcontactfile->header = 0;
                $importcontactfile->delimiter = ";";
                $importcontactfile->dateformat = null;
                $importcontactfile->importmode = "active";
                $importcontactfile->update =  0;
                $importcontactfile->importrepeated =  0;
                $importcontactfile->fieldsmap = json_encode($this->fields);
				if (!$importcontactfile->save()){
					foreach ($importfile->getMessages() as $msg) {
                     $this->logger->log(print_r($msg,true));
                    }
				}
				array_push($this->arrIdImportContact,$importcontactfile->idImportcontactfile);
				$output = array();
			    $return = 0;
				exec("php /websites/aio/app/bgprocesses/contacts/Import.php ".$importcontactfile->idImportcontactfile,$output,$return);
				var_dump($importcontactfile->idImportcontactfile,$output,$return);
			}
		}
		var_dump($this->arrIdImportContact);exit(); 
	}	
}
?>