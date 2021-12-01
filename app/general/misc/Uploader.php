<?php

namespace Sigmamovil\General\Misc;

class Uploader
{

  protected $logger;
  protected $extNotAllowed = array();
  protected $extAllowed = array();
  protected $file;
  protected $size;
  protected $dir;

  function __construct() {
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
  }

  /**
   * Receives an array with extensions that are invalid, if this array is set, the object will use it 
   * @param Array $exts
   */
  public function setExtensionsNotAllowed($exts) {
    $this->extNotAllowed = implode('|', $exts);
  }

  /**
   * Receives an array with extensions that only are invalid, if this array is set and setExtensionsNotAllowed is empty, the object will use it 
   * @param Array $exts
   */
  public function setExtensionsAllowed($exts) {
    $this->extAllowed = implode('|', $exts);
  }

  /**
   * Receives the archive from the post, with a little modification, has set a key named newName that contains the new name of file to upload
   * @param Array $file
   */
  public function setFile($file) {
    $this->file = $file;
  }

  /**
   * Receives the maximun size supported in files load, and with this info the object valids the file 
   * @param string $size
   */
  public function setMaxSizeSupported($size) {
    $this->size = $size;
  }

  /**
   * Receives the path where the file will be upload
   * @param string $dir
   */
  public function setDir($dir) {
    $this->dir = $dir;
  }

  protected function validateExtension() {
    if (\count($this->extNotAllowed) != 0) {
      $exts = "%\.({$this->extNotAllowed})$%i";
      $isValid = \preg_match($exts, $this->file['name']);

      if ($isValid) {
        throw new \InvalidArgumentException("Extension de archivo inválida");
      }
    } else if (\count($this->extAllowed) != 0) {
      $exts = "%\.({$this->extAllowed})$%i";
      $isValid = \preg_match($exts, $this->file['name']);

      if (!$isValid) {
        throw new \InvalidArgumentException("Tipo de imagen inválida");
      }
    }
  }

  protected function validateSize() {
    $kb = $this->size / 1024;
    $mb = $kb / 1024;
    $mb = \explode('.', $mb);

    if ($this->file['size'] > $this->size) {
      throw new \InvalidArgumentException("El archivo excede el peso limite de {$mb[0]} MB, por favor valide la información");
    }
  }

  /**
   * Validate the file, and launches a exception if the file fails with the requeriments
   */
  public function validate() {
    $this->validateExtension();
    $this->validateSize();
  }

  /**
   * Upload the file
   */
  public function upload() {

    if (!\file_exists($this->dir)) {
      \mkdir($this->dir, 0777, true);
    }
 

    if (!is_writable($this->dir)) {
      $this->logger->log("the file could not be loaded in server, not authorized");
      throw new \Exception('No se pudo cargar el archivo, por favor contacte al administrador');
    }

    $this->dir = $this->dir . $this->file['newName'];
//echo 123;
//    exit;
    if (!\move_uploaded_file($this->file['tmp_name'], $this->dir)) {
      throw new \Exception('No se pudo cargar el archivo, por favor contacte al administrador');
    }
  }

  /**
   * Return the path where the file was upload
   * @return string $dir
   */
  public function getFileDirectory() {
    return $this->dir;
  }

  public function deleteFileFromServer($dir) {
    if (!unlink($dir)) {
      $this->logger->log("The file could not be delete, dir: {$dir}");
    }
  }

  public function fileExists($file) {
    return file_exists($file);
  }

    public function setAccount($account) { $this->account = $account; }
    public function setMail($mail) { $this->mail = $mail; }
    public function setData($data) {
        if (!is_object($data) || empty($data)) {
            throw new \InvalidArgumentException("Error invalid data object...");
        }
        $this->data = $data ;
    }
    public function validateExt($ext){
            $e = implode('|', $ext);
            $expr = "%\.({$e})$%i";
            $isValid = preg_match($expr, $this->data->originalName);
            if (!$isValid) {  throw new \InvalidArgumentException('Extensión de archivo invalida, por favor valide la información'); }
    }

    /*
    * Esta función valida que el archivo que se intenta subir no sobrepase el tamaño máximo configurado en kilobytes
    */
    public function validateSizeObj($size){
            $bytes = 1024*$size;
            $kb = $this->data->size/1024;
            if ($kb > $bytes) {
                    throw new \InvalidArgumentException("El peso del archivo ({$kb} KB), excede el limite determinado ({$size} KB), por favor valide la información");
            }
    }

    public function uploadFile($dir){
        if (!file_exists($dir)) { mkdir($dir, 0777, true); }
        $this->folder = $dir;
        $dir = $dir . $this->data->name;
        if (!move_uploaded_file($this->data->tmp_dir, $dir)){ throw new \Exception('File could not be uploaded on the server'); }
        $this->source = $dir;
    }

    public function decompressFile($source, $destination){
          //Creamos un objeto de la clase ZipArchive()
          $enzip = new \ZipArchive();

        //Abrimos el archivo a descomprimir
        $enzip->open($source);

        //Extraemos el contenido del archivo dentro de la carpeta especificada
        $extracted = $enzip->extractTo($destination);

        /* Si el archivo se extrajo correctamente listamos los nombres de los
         * archivos que contenia de lo contrario mostramos un mensaje de error
        */
        if(!$extracted) {
                throw new Exception("Error while unziping file!");
        }
    }

    public function changeNameOfFile($source, $destination){
            rename($source, $destination);
    }

    public function getSource(){
            return $this->source;
    }

    public function getFolder(){
            return $this->folder;
    }

}
