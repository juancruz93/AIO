<?php

class CustomfieldManager {

  public $mail;
  public $contentMail;
  public $urlManager;
  public $html;
  public $text;
  public $subject;
  public $customFields;
  public $fieldsInDbase = array();
  public $cleanFields = array();

  function __construct(MailContent $contentMail, $urlManager) {
    $this->log = Phalcon\DI::getDefault()->get('logger');
    $this->contentMail = $contentMail;
    $this->mail = $contentMail->mail;
    $this->forms = true;
    $this->urlManager = $urlManager;
  }

  /**
   * 
   * @param String $html
   * @return String
   */
  public function prepareUpdatingForms($html) {
    preg_match_all('/%%FORM_([a-zA-Z0-9_\-]*)%%/', $html, $arrayForms);


    if (count($arrayForms[0]) == 0) {
      $this->forms = false;
      return $html;
    }

    list($allforms, $allids) = $arrayForms;

    $this->ids = array_unique($allids);

    $search = $replace = array();

    foreach ($this->ids as $id) {
      $search[] = '%%FORM_' . $id . '%%';
      $replace[] = '$$$_updating_form_' . $id . '_$$$';
    }

    $html_forms = str_ireplace($search, $replace, $html);

    return $html_forms;
  }

  /**
   * 
   * @param type $html
   * @param type $contact
   * @return type
   */
  public function processUpdatingForms($html, $contact) {
    $linkdecoder = new \EmailMarketing\General\Links\ParametersEncoder();
    $linkdecoder->setBaseUri($this->urlManager->getBaseUri(true));

    foreach ($this->ids as $id) {
      $parameters = array(1, $id, $contact['idContact'], $this->mail->idMail);
      $url = $linkdecoder->encodeLink('form/update', $parameters);

      $search[] = '$$$_updating_form_' . $id . '_$$$';
      $replace[] = $url;
    }

    $html_form_link = str_ireplace($search, $replace, $html);

    return $html_form_link;
  }

  /**
   * Retorna los posibles campos personalizados (todo lo que esté entre %%, Ej: %%NOMBRE%%) 
   * encontrados en el html, texto plano y el asunto
   * @return string
   * @throws InvalidArgumentException
   */
  public function searchCustomFields($content) {
    /*
     * 1.Buscamos los posibles campos personalizados y los agregamos a un arreglo (Si están repetidos aparecerán 
     * dos veces)
     * Ej:
     * 
     * "Contenido enviado a %%NOMBRE%% %%APELLIDO%%, %%NOMBRE%%, 
     * %hshs%%ggs%hshs% JJJ% $ajaa%%$$%%%% %%%%%% %%EMAIL %%EMAIL%%"
     * 
     * Array
      (
      [0] => Array
      (
      [0] => %%NOMBRE%%
      [1] => %%APELLIDO%%
      [2] => %%NOMBRE%%
      [3] => %%%%
      [4] => %%%%
      [5] => %%EMAIL%%
      )

      [1] => Array
      (
      [0] => NOMBRE
      [1] => APELLIDO
      [2] => NOMBRE
      [3] =>
      [4] =>
      [5] => EMAIL
      )
      )
     */

    $allFields = $content . $this->contentMail->plaintext . $this->mail->subject;
    preg_match_all('/%%([a-zA-Z0-9_\-\.]*)%%/', $allFields, $arrayFields);

    // 2.Si hay error en el preg_match_all se lanzará una excepción
    if ($arrayFields === false) {
      throw new InvalidArgumentException('Error returned by Preg_match_all, invalid values');
    }

    //3.Si no se encuentran campos personalizados simplemente se retornará 
    //una cadena de texto igual a 'No Fields'
    $array = $arrayFields[0];
    if (count($array) == 0) {
      return false;
    }

//		$this->log->log("Fields: " . print_r($arrayFields, true));
    // 4.Dividimos el arreglo que contiene los posibles campos personalizados en 2
    list($cf, $fields) = $arrayFields;

    //5.Creamos la variable global $this->customFields
    list($customFields, $primaryfield) = $this->setFields($cf);

    if (count($customFields) > 0) {
      $customFields = $this->setIdFields($customFields);
    }

    //7. Si no hay coincidencias retornamos el mensaje 'No Custom'
    if (count($customFields) <= 0 && count($primaryfield) <= 0) {
      return false;
    }
    
    return $customFields;
  }

  private function setFields($fields) {
    //1.Tomamos la primera parte(Los que estan entre %%) del arreglo de posibles campos personalizados 
    // y quitamos los repetidos
    $cleanFields = array_unique($fields);
    //2.Recorremos el arreglo con valores únicos y tomamos los campos que no sean primarios (Nombre, apellido, email)
    // para insertarlos en la variable global fields
    $customFields = array();
    $primaryFiels = array();
    foreach ($cleanFields as $x) {
      if ($x == '%%EMAIL%%' || $x == '%%NOMBRE%%' || $x == '%%APELLIDO%%' || $x == '%%FECHA_DE_NACIMIENTO%%' || $x == '%%TELEFONO%%' || $x == '%%INDICATIVO%%') {
        $primaryFiels[] = $x;
      } else {
        $customFields[] = $x;
      }
    }
    unset($cleanFields);
    return array($customFields, $primaryFiels);
  }

  private function setIdFields($customFields) {
    List($targetType, $ids) = $this->setArrayContacListMysql($this->mail);
    $arrCustomField = array();
    if ($targetType == "segment") {
      $arrCustomField = $this->getCustomfielForSegment($ids);
    } else {
      $arrCustomField = $this->getCustomfielForContaclist($ids);
    }

    $search = array('Ñ', 'ñ', 'Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú');
    $replace = array('N', 'n', 'A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u');


    //$idFields = array();
    $arrCustomfield = array();
    $ff = array();
    if (count($arrCustomField) > 0) {
      foreach ($customFields as $c) {
        foreach ($arrCustomField as $r) {
          $fieldDB = str_replace($search, $replace, $r->name);
          $fieldDB = strtoupper($fieldDB);
          //BUSCAMOS EN EL CAMPO TRAIDO DE LA BD SI VIENE CON GUIN BAJO, DE SER ASI LO REEMPLAZAMOS POR UN ESPACIO
          $pos = strpos($fieldDB, '_');
          if( $pos !== false ){
            $fieldDB = str_replace('_',' ',$fieldDB);
          }
          
          $fieldHtml = str_replace(array('_', '%%'), array(' ', ''), $c);

          if (trim($fieldDB) == trim($fieldHtml)) {
            
            //$idFields[] = $r->idCustomfield;
            $ff[$r->idCustomfield] = $c;
          } else {
            $ff["delete"][] = $c;
          }
        }
      }
    }else{
      foreach ($customFields as $c) {
        $ff["delete"][] = $c;
      }
    }
    $arrCustomfield = $ff;
    
    return $arrCustomfield;
  }

  /**
   * 
   * @param Mail $mail
   * @return array $arrReturn
   */
  public function setArrayContacListMysql(Mail $mail) {
    $target = json_decode($mail->target);
    $arrReturn = array();
    if ($target->type == "segment") {
      $arrSegment = $target->segment;
      $countSegments = count($arrSegment);
      if ($countSegments > 0) {
        for ($i = 0; $i < $countSegments; $i++) {
          $arrIds[] = $arrSegment[$i]->idSegment;
        }
      }
      unset($arrSegment);
      unset($countSegments);
    } else {
      $arrContactList = $target->contactlists;
      $countContactList = count($arrContactList);
      if ($countContactList > 0) {
        for ($i = 0; $i < $countContactList; $i++) {
          $arrIds[] = $arrContactList[$i]->idContactlist;
        }
      }

      unset($arrContactList);
      unset($countContactList);
    }
    if ($arrIds > 0) {
      $arrReturn[] = $target->type;
      $arrReturn[] = implode(', ', $arrIds);
    } else {
      $arrReturn[] = false;
    }

    
    return $arrReturn;
  }

  /**
   * Recibe los Ids de los segmentos a cual consultar a mongo
   * 
   * retorna un objeto phalcon el cual tiene los customfield
   * @param type $arrIds
   * @return array 
   */
  public function getCustomfielForSegment($arrIds) {

    $arrid = explode(",", $arrIds);
    $whereids = array();
    foreach ($arrid as $key) {
      $whereids[] = $key;
    }
    $where = array("idSegment" => array('$in' => $whereids));
    $segment = \Segment::find($where);
    $arrIdContactList = array();

    foreach ($segment as $key => $val) {
      foreach ($val->contactlist as $key) {
        $arrIdContactList[] = (Int) $key['idContactlist'];
      }
    }

    $arrIdContactList = array_unique($arrIdContactList);
    $arrIdContactList = implode(', ', $arrIdContactList);


    return $this->getCustomfielForContaclist($arrIdContactList);
  }

  /**
   * Recibe un array de CONTACTLIST
   * @param type $arr
   * @return objPhalcon
   */
  public function getCustomfielForContaclist($arrIds) {

    $phql = "SELECT * FROM Customfield WHERE idContactlist IN ({$arrIds})";
    $modelsManager = Phalcon\DI::getDefault()->get('modelsManager');
    return $modelsManager->executeQuery($phql);
  }

  /**
   * 
   * @param type $contact,$customFields,$content
   * @return array ("html"=>newHtml,"text"=>newsTextPlain,"subject"=>newSubject)
   * @throws InvalidArgumentException
   */
  public function processCustomFields($contact, $customFields, $content) {
    //1. Validamos que la variable contacto no esté vacía y sea un arreglo
    if ($contact == null) {
      throw new InvalidArgumentException('Error processCustomFields received a not valid array');
    }



    //2.Emparejamos los campos primarios 
    $searchPrimaryFields = array('%%EMAIL%%', '%%NOMBRE%%', '%%APELLIDO%%', '%%FECHA_DE_NACIMIENTO%%', '%%TELEFONO%%', '%%INDICATIVO%%');
    $replacePrimaryFields = array((empty($contact->email) ? " " : $contact->email), (empty($contact->name) ? " " : $contact->name), (empty($contact->lastname) ? " " : $contact->lastname), (empty($contact->birthdate) ? " " : $contact->birthdate), (empty($contact->phone) ? " " : $contact->phone), (empty($contact->indicative) ? " " : $contact->indicative));

    $searchCustomFields = array();
    $replaceCustomFields = array();



    //3.Emparejamos los campos personalizados
    if ($customFields != false) {
      if (isset($contact->customfield)) {
        foreach ($customFields as $idh => $cf) {
          for ($i = 0; $i < count($contact->customfield); $i++) {
            foreach ($contact->customfield[$i] as $idc => $value) {
              if ($idh == $idc) {
                $searchCustomFields[] = $cf;
                $replaceCustomFields[] = (empty($value->value) ? " " : $value->value);
              }
            }
          }
        }
      } else {
        foreach ($customFields as $idh => $cf) {
          $searchCustomFields[] = $cf;
          $replaceCustomFields[] = " ";
        }
      }

      if (count($customFields["deleted"]) > 0) {
        $customFields["deleted"] = array_unique($customFields["deleted"]);
        foreach ($customFields["deleted"] as $key => $value) {
          $searchCustomFields[] = $value;
          $replaceCustomFields[] = " ";
        }
      }
    }



    //4.Fusionamos los arreglos con campos personalizados y primarios
    $search = array_merge($searchPrimaryFields, $searchCustomFields);
    $replace = array_merge($replacePrimaryFields, $replaceCustomFields);


    //5.Utilizamos str_replace para reemplazar los valores del contacto por la marca de campo personalizado
    $newHtml = str_replace($search, $replace, $content);
    $newText = str_replace($search, $replace, $this->contentMail->plaintext);
    $newSubject = str_replace($search, $replace, $this->mail->subject);

//    var_dump($newHtml);
//    exit();
    //6.Creamos el arreglo a retornar
    $content = array(
        'html' => $newHtml,
        'text' => $newText,
        'subject' => $newSubject
    );

    //7.Hacemos unset de los arreglos para liberar la posible memoria utilizada
    unset($newHtml);
    unset($newText);
    unset($newSubject);
    return $content;
  }

}
