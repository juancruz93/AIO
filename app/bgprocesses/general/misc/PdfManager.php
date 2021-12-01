<?php

require_once(__DIR__."/SQLExecuter.php");

ini_set('memory_limit', '512M');
//require_once(__DIR__ . "\\..\\..\\bootstrap\\index.php");

class PdfManager 
{
  private $logger;
  private $source;
  private $destination;
  private $mail;
  private $total = 0;
  private $arrayIdContacts = array();

  public function __construct() {
    $di = \Phalcon\DI\FactoryDefault::getDefault();
    $this->logger = $di['logger'];
  }

  public function setMail(\Mail $mail) {
      $this->mail = $mail;
  }

  public function setSource($source) {
      $this->source = $source;
  }

  public function setDestination($destination) {
      $this->destination = $destination;
  }

  public function extract() {
    //Creamos un objeto de la clase ZipArchive()
    $enzip = new \ZipArchive();
    //Abrimos el archivo a descomprimir
    $enzip->open($this->source);
    //Extraemos el contenido del archivo dentro de la carpeta especificada
    $extracted = $enzip->extractTo($this->destination);
    /* Si el archivo se extrajo correctamente listamos los nombres de los
     * archivos que contenia de lo contrario mostramos un mensaje de error
     */
    if (!$extracted) {
        throw new Exception("Error while unziping file!");
    }
    $this->total = $enzip->numFiles;
  }

  public function searchIdContacts2(){
    $contaclists = json_decode($this->mail->target)->contactlists;
    $this->idCtlistNumber = $contaclists[0]->idContactlist;
    $idMail = $this->mail->idMail;

    $cxclRows = \Cxcl::find([
      "conditions" => "idContactlist = ?0 AND deleted = 0",
      "columns" =>"idContact",
      "bind" => [$this->idCtlistNumber]
    ])->toArray();

    //aqui lo unico que se desea es pasaar los ids de contactos a un arreglo utilizado por el objeto
    foreach ($cxclRows as $value) { $this->arrIdsContacts[] = (int) $value['idContact']; } 
  }

  public function searchIdContacts(){
    $contaclists = json_decode($this->mail->target)->contactlists;
    $this->idCtlistNumber = $contaclists[0]->idContactlist;
    $idMail = $this->mail->idMail;

    $cxclRows = \Cxcl::find([
      "conditions" => "idContactlist = ?0 AND deleted = 0",
      "columns" =>"idContact",
      "bind" => [$this->idCtlistNumber]
    ])->toArray();

    //aqui lo unico que se desea es pasaar los ids de contactos a un arreglo utilizado por el objeto
    foreach ($cxclRows as $value) { $this->arrIdsContacts[] = (int) $value['idContact']; } 
    //var_dump( $this->arrIdsContacts); die;   
  }

  public function validateAllCustomField(){
    $arrayFiles = glob($this->destination . "{*.pdf}", GLOB_BRACE);
    // hago una iteracion sobre el arreglo de ubicaciones de archivos 
    foreach ($arrayFiles as $file) {
      $path_parts = pathinfo($file);
      $basename = $path_parts['basename'];  //solamente saco el basename 
      $arrayFilesName[] =explode( '.', $basename )[0];  // y luego el nombre sin el ".pdf"
    } 

    if( count( explode( '_', $arrayFilesName[0] ) ) != 1){  //si el numero de items que arroja el explode es diferente de 1 entonces no hay guion bajo... 
    //es decir no tendra campo personalizado al principio del nombre ya que es necesario que tenga guion... para que el explode saque el campo Personlizado 
      $campoPrzdBase = explode( '_', $arrayFilesName[0] )[0];  //para compararlo luego.
      $numField = 0;
      foreach ( $arrayFilesName  as $value ) {
        $campoTemporal = explode('_', $value)[0];  //--> tomo el campo Personalizado
        if ($campoPrzdBase === $campoTemporal)  { 
          $numField = $numField + 1;
        }
      } 
      if($numField == 0){
          return FALSE; 
      } 
    }      
    return TRUE;      
  }

  public function save() {    
    $files = glob($this->destination . "{*.pdf}", GLOB_BRACE);
    $v = array();
    $arrayFilesName = array();
    foreach ($files as $file) {
      $path_parts = pathinfo($file);
      $size = filesize($file);
      $size = $size / 1024;
      $basename = explode("_", $path_parts['basename']);
      if(!in_array($basename[0], $arrayFilesName)){
          $arrayFilesName[] = strtoupper($basename[0]);
      }
      $ctls = Cxc::find([
          "conditions" => array(  'idContact' => array(  '$in' => $this->arrIdsContacts  ))   
      ]);
      $totalContacts = count($this->arrIdsContacts);
    }  
    foreach ($ctls as  $value){ 
      $cnt = 0;  //control de indices...
      foreach (array_filter($value->idContactlist[$this->idCtlistNumber]) as $item) {  
        if(in_array(strtoupper($item['name']), $arrayFilesName)){
          if( strlen($item['value']) == 0 ){ continue; } //si en caso tal el numero tiene una longitud de cero salte el ciclo
              //echo '<pre>' . var_export($basename[$cnt]." ".$cnt, true) . '</pre>';
          $namefilepdf = strtoupper($item['name']).'_'.$item['value'].'.'.$path_parts['extension'];
          $findpdfmail = Pdfmail::findFirst(array(
            'conditions' => 'idMail = ?0 AND name = ?1 AND status = 1',
            'bind' => array(0 => $this->mail->idMail, 1 => $namefilepdf)
          ));
          if($findpdfmail == false){
            $v[] = "(null, {$this->mail->idMail}, $value->idContact, '{$namefilepdf}', {$size}, '{$path_parts['extension']}', " . time() . ")";           
          }
          $cnt++;
        }
      }  
    } 
    if (count($v) > 0) {
      $values = implode(',', $v); 
      $sql = "INSERT IGNORE INTO pdfmail (idPdfmail, idMail, idContact, name, size, type, createdon) VALUES {$values}";
      try {
        $executer = new SQLExecuter();
        $executer->instanceDbAbstractLayer();
        $executer->setSQL($sql);
        $executer->executeAbstractLayer();
      } catch (\Exception $ex) {
        $this->logger->log("Exception: {$ex}");
        throw new \Exception("Exception: {$ex}");
      }
    }
  }

  public function getTotal() {
    return $this->total;
  }

} 