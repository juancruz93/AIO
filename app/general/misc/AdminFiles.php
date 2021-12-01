<?php

namespace Sigmamovil\General\Misc;

class AdminFiles {

  private $dirPublic;

  public function __construct() {
    $this->dirPublic = getcwd() . "\\"; //Ruta del directorio público
  }

  /**
   * _Function for move files
   * @param type $oldDir => Direcotrio de origen
   * @param type $newDir => Nuevo directorio donde será movido
   * @throws \InvalidArgumentException
   */
  public function moveFile($oldDir, $newDir) {
    if (!file_exists($this->dirPublic . $newDir)) {
      mkdir($this->dirPublic . $newDir);
    }
    if (rename($this->dirPublic . $oldDir, $this->dirPublic . $newDir)) {
      throw new \InvalidArgumentException("No se puedo mover el archivo correctamente");
    }
  }

  /**
   * _Function for move files
   * @param type $oldDir => Directorio de origen
   * @param type $newDir => Directorio donde serán movidos
   * @throws \InvalidArgumentException
   */
  public function moveFilesDir($oldDir, $newDir) {
    if (!file_exists($this->dirPublic . $newDir)) {
      mkdir($this->dirPublic . $newDir);
    }
    foreach (glob($this->dirPublic . $oldDir . '*') as $value) {
      $filecopy = str_replace($this->dirPublic . $oldDir, $this->dirPublic . $newDir, $value);
      if (rename($value, $filecopy)) {
        throw new \InvalidArgumentException("No se pudo mover los archivos correctamente");
      }
    }
  }

  /**
   * _Function for know the size file
   * @param type $folderFile => Dirección del archivo
   * @return type
   */
  public function getSizeFile($folderFile) {
    return filesize($this->dirPublic . $folderFile);
  }

  /**
   * _Function for know the type file
   * @param type $folderFile => Directorio del archivo
   * @return type
   */
  public function getTypeFile($folderFile) {
    return filetype($this->dirPublic . $folderFile);
  }

}