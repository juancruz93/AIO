<?php

class CustomfieldManagerSms {

  public $sms;
  public $contentSms;
  public $urlManager;
  public $html;
  public $text;
  public $subject;
  public $customFields;
  public $fieldsInDbase = array();
  public $cleanFields = array();
  public $contact;
  public $flagSms;

  function __construct($sms, $contact = null,$flagSms = true) {
//    $this->log = Phalcon\DI::getDefault()->get('logger');
//    $this->contentMail = $contentMail;
    $this->sms = $sms;
    $this->contact = $contact;
    $this->flagSms = $flagSms;
//    $this->forms = true;
//    $this->urlManager = $urlManager;
  }
  public function setFlagMore($morecaracter){
    
    if($morecaracter == 1){
        $this->flagSms = false;    
    }
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

    $allFields = $content;
    preg_match_all('/%%([a-zA-Z0-9_\-\.]*)%%/', $allFields, $arrayFields);

    // 2.Si hay error en el preg_match_all se lanzará una excepción
    if ($arrayFields === false) {
      throw new InvalidArgumentException('Error returned by Preg_match_all in CustomfieldManagerSms, invalid values');
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
      if ($x == '%%EMAIL%%' || $x == '%%NOMBRE%%' || $x == '%%APELLIDO%%' || $x == '%%FECHA_DE_NACIMIENTO%%' || $x == '%%TELENONO%%' || $x == '%%INDICATIVO%%') {
        $primaryFiels[] = $x;
      } else {
        $customFields[] = $x;
      }
    }
    unset($cleanFields);
    return array($customFields, $primaryFiels);
  }

  private function setIdFields($customFields) {
    $arrCustomField = array();

    if ($this->contact == null) {
      List($targetType, $ids) = $this->setArrayContacListMysql($this->sms);
      if ($targetType == "segment") {
        $arrCustomField = $this->getCustomfielForSegment($ids);
      } else {
        $arrCustomField = $this->getCustomfielForContaclist($ids);
      }
    } else {
      $ids = $this->getContactListForContact($this->contact);
      $arrCustomField = $this->getCustomfielForContaclist($ids);
    }

//    var_dump($arrCustomField);

    $search = array('Ñ', 'ñ', 'Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú');
    $replace = array('N', 'n', 'A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u');


    //$idFields = array();
    $arrCustomfield = array();
    $ff = array();
    if (count($arrCustomField) > 0) {
      foreach ($customFields as $c) {
        foreach ($arrCustomField as $r) {
          $fieldDB = str_replace($search, $replace, $r->alternativename);
          $fieldDB = strtoupper($fieldDB);
//          $fieldHtml = str_replace(array('_', '%%'), array(' ', ''), $c);
          $fieldHtml = str_replace(array('%%'), array(''), $c);
          if (trim($fieldDB) == trim($fieldHtml)) {
            //$idFields[] = $r->idCustomfield;
            $ff[$r->idCustomfield] = $c;
          } else {
            $ff["delete"][] = $c;
          }
        }
      }
    } else {
      foreach ($customFields as $c) {
        $ff["delete"][] = $c;
      }
    }

    $arrCustomfield = $ff;

    return $arrCustomfield;
  }

  /**
   * 
   * @param Sms $sms
   * @return array $arrReturn
   */
  public function setArrayContacListMysql(Sms $sms) {
    $receiver = json_decode($sms->receiver);
    $arrReturn = array();
    if ($receiver->type == "segment") {
      $arrSegment = $receiver->segment;
      $countSegments = count($arrSegment);
      if ($countSegments > 0) {
        for ($i = 0; $i < $countSegments; $i++) {
          $arrIds[] = $arrSegment[$i]->idSegment;
        }
      }
      unset($arrSegment);
      unset($countSegments);
    } else {
      $arrContactList = $receiver->contactlists;
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
      $arrReturn[] = $receiver->type;
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
  public function changeInvalidCharacters($content) {
    $search = array('Ñ', 'ñ', 'Á', 'á', 'É', 'é', 'Í', 'í', 'Ó', 'ó', 'Ú', 'ú', '¿');
    $replace = array('N', 'n', 'A', 'a', 'E', 'e', 'I', 'i', 'O', 'o', 'U', 'u', '');
    return str_replace($search, $replace, $content);
  }

  public function processCustomFields($contact, $customFields, $content, $customFieldForContact = null) {
    //1. Validamos que la variable contacto no esté vacía y sea un arreglo
    if ($contact == null) {
      throw new InvalidArgumentException('Error processCustomFields in CustomfieldManagerSms received a not valid array');
    }

    //2.Emparejamos los campos primarios 
    $searchPrimaryFields = array('%%EMAIL%%', '%%NOMBRE%%', '%%APELLIDO%%', '%%FECHA_DE_NACIMIENTO%%', '%%TELEFONO%%', '%%INDICATIVO%%');
    $replacePrimaryFields = array((empty($contact->email) ? " " : $contact->email), (empty($contact->name) ? " " : $contact->name), (empty($contact->lastname) ? " " : $contact->lastname), (empty($contact->birthdate) ? " " : $contact->birthdate), (empty($contact->phone) ? " " : $contact->phone), (empty($contact->indicative) ? " " : $contact->indicative));
    $searchCustomFields = array();
    $replaceCustomFields = array();
    $customField;
    if ($customFieldForContact == null) {
      $customField = (isset($contact->customfield)) ? $contact->customfield : null;
    } else {
      $customField = $customFieldForContact;
    }


    //3.Emparejamos los campos personalizados
    if ($customFields != false) {
      if (isset($customField) && !empty($customField) && $customField != null) {
        foreach ($customFields as $idh => $cf) {
          for ($i = 0; $i < count($customField); $i++) {
            foreach ($customField[$i] as $idc => $value) {
              if ($idh == $idc) {
                $searchCustomFields[] = $cf;
                $replaceCustomFields[] = (empty($value["value"]) ? " " : $this->changeInvalidCharacters($value["value"]));
              }
            }
          }
        }
      } else {
        foreach ($customFields as $idh => $cf) {
          if(!is_array($cf)) {
            $searchCustomFields[] = $cf;
            $replaceCustomFields[] = " ";
          }
        }
      }
      if (count($customFields["delete"]) > 0) {
        $customFields["delete"] = array_unique($customFields["delete"]);
        foreach ($customFields["delete"] as $key => $value) {
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
    if($this->flagSms){
      $newHtml = substr($newHtml, 0, 160);
    }else{
      $newHtml = substr($newHtml, 0, 300);  
    }
      
    
    //6.Creamos el arreglo a retornar
//    $content = array(
//        'html' => $newHtml
//    );
    //7.Hacemos unset de los arreglos para liberar la posible memoria utilizada
//    unset($newHtml);
    unset($newText);
    unset($newSubject);

    return $newHtml;
  }

  public function searchCustomfieldForContact($content) {

    preg_match_all('/%%([a-zA-Z0-9_\-]*)%%/', $content, $arrayFields);

    // 2.Si hay error en el preg_match_all se lanzará una excepción
    if ($arrayFields === false) {
      throw new InvalidArgumentException('Error returned by Preg_match_all in CustomfieldManagerSms, invalid values');
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

  private function getContactListForContact($contact) {
    $contaclists = \Cxcl::find(array("conditions" => "idContact = ?0", "bind" => array((Int) $contact)));
    $arrIds = array();

    foreach ($contaclists as $contaclist => $value) {
      $arrIds[] = $value->idContactlist;
    }
    if ($arrIds > 0) {
      $arrReturn = implode(', ', $arrIds);
    } else {
      $arrReturn = false;
    }
    return $arrReturn;
  }

  public function findCustomField($idContact) {
    $arr = array();
    $cxc = Cxc::findFirst([["idContact" => $idContact]]);
    if ($cxc) {
      foreach ($cxc->idContactlist as $value) {
        array_push($arr, $value);
      }
    }

    return $arr;
  }

}
