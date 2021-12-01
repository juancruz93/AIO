<?php
/**
 * Description of ExportSurveys
 *
 * @author jose.quinones
 */
require_once(__DIR__ . "/../bootstrap/index.php");

$idSurvey = 0;

if (isset($argv[1])) {
  $idSurvey = $argv[1];
}

$export = new ExportSurveys();
$export->start($idSurvey);

class ExportSurveys {
  
  public function __construct() {
    $di = \Phalcon\DI::getDefault();
    $this->logger = $di->get("logger");
    $this->db = $di->get('db');
  }
  
  public $idSurvey;
  public $data;

  public function start($idSurvey) { 
    $this->setIdSurvey($idSurvey);
    $this->data = $this->getAllInfoSurveyReport();
    $this->export($idSurvey);
    
    //$this->reportStaticsSurvey($title);
  }
  
  public function setIdSurvey($idSurvey) {
    $this->idSurvey = $idSurvey;
  }
  
  public function getAllInfoSurveyReport() {
    $survey = \Survey::findFirst(array(
                'conditions' => 'idSurvey = ?0',
                'bind' => [$this->idSurvey]
    ));

    if (!$survey) {
      throw new \InvalidArgumentException('No se encontró la encuesta solicitada, por favor valide la información');
    }
    $survey->SurveyContent;
    $survey = json_decode(json_encode($survey), true);


    $array[] = "encabezado";
    $array[] = "button";

    $manager = \Phalcon\DI::getDefault()->get('mongomanager');

    $optionsQuestion = array(
        'projection' => array('_id' => 0, 'idSurvey' => 1, 'idQuestion' => 1, 'component' => 1, 'question' => 1, 'count' => 1),
    );

    $query = [
        "idSurvey" => $this->idSurvey,
        "component" => ['$nin' => $array],
        "deleted" => 0
    ];

    $queryQuestion = new \MongoDB\Driver\Query($query, $optionsQuestion);
    $question = $manager->executeQuery("aio.question", $queryQuestion)->toArray();
    $one = 0;

    $aswerAr = array();

    $i = 0;
    if ($one == 0) {
      foreach ($question as $value) {
        $optionsAnswer = array('projection' => array('_id' => 0, 'idAnswer' => 1, 'idQuestion' => 1, 'answer' => 1, 'contacts' => 1, 'count' => 1),);
        $query = ["idQuestion" => $value->idQuestion, "deleted" => 0];
        $queryAnswer = new \MongoDB\Driver\Query($query, $optionsAnswer);
        $answer = $manager->executeQuery("aio.answer", $queryAnswer)->toArray();

        foreach ($answer as $contacts) {

          foreach ($contacts->contacts as $keyuser =>$contactdate) {
            $con = $keyuser;
            $conta = \Contact::findFirst([["idContact" => (int) $con]]);

            $i++;
            $contactarray = new \stdClass();
            if ($conta == false) {
              $contactarray->idContact = $con;
              $contactarray->name = $con;
              $contactarray->lastname = 'sin datos';
              $contactarray->email = 'sin datos';
              $contactarray->dateandhour = $contactdate;
            } else {
              $contactarray->idContact = $con;
              $contactarray->name = $conta->name;
              $contactarray->lastname = $conta->lastname;
              $contactarray->email = $conta->email;
              $contactarray->dateandhour = $contactdate;
            }
            $contactarray->questions = array();
            foreach ($question as $value) {
              $idQuestion = $value->idQuestion;
              $obj = new \stdClass();
              $obj->question = $value->question;
              array_push($contactarray->questions, $obj);
              $obj->answer = array();

              $optionsAnswer = array('projection' => array('_id' => 0, 'idAnswer' => 1, 'idQuestion' => 1, 'answer' => 1, 'contacts' => 1, 'count' => 1),);
              $query = ["idQuestion" => $value->idQuestion, "deleted" => 0];
              $queryAnswer = new \MongoDB\Driver\Query($query, $optionsAnswer);
              $answer = $manager->executeQuery("aio.answer", $queryAnswer)->toArray();

              foreach ($answer as $a) {
                $answeridQ = $a->idQuestion;
                if ($idQuestion == $answeridQ) {
                  foreach ($a->contacts as $keycont => $valuecont) {
                    if ($con == $keycont) {
                      array_push($obj->answer, $a->answer);
                    }
                  }
                }
              }
            }
            if ($i == 1) {
              array_push($aswerAr, $contactarray);
            }

            $stop = false;
            $stan = false;
            foreach ($aswerAr as $asy) {
              if ($asy->idContact == $contactarray->idContact) {
                $stan = true;
              } else {
                $stop = true;
              }
            }

            if ($stop == true & $stan == false) {
              array_push($aswerAr, $contactarray);
            }
          }
        }
      }

      $one = $one + 1;
    }

    $data = array();
    $data['survey'] = $survey;
    $data['questions'] = $question;
    $this->surveyInfo = $data;
    //return $aswerAr;
    
    $arrayConverted = array();
    //hay que convertir los objetos dentro del arreglo a arreglos tambien.
    foreach ($aswerAr as $value) { $arrayConverted[] = (array) $value; }
    
    $arrayConverted2 = $this->ordenarArray($arrayConverted, 'dateandhour', SORT_ASC); 

    $arrayConvertedtoObjects = array(); 
    foreach ($arrayConverted2 as $value) { $arrayConvertedtoObjects[] = (object) $value; } 

    //hay que ordenar el array por fechas para que salga ordenado ascendentemente el reporte... 
    return $arrayConvertedtoObjects;
  }
  
  public function ordenarArray($array, $on, $order=SORT_ASC) {
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
      foreach ($array as $k => $v) {
        if (is_array($v)) {
          foreach ($v as $k2 => $v2) {
            if ($k2 == $on) {
              $sortable_array[$k] = $v2;
            }
          }
        } else {
          $sortable_array[$k] = $v;
        }
      }

      switch ($order) {
        case SORT_ASC:
          asort($sortable_array);
        break;
        case SORT_DESC:
          arsort($sortable_array);
        break;
      }

      foreach ($sortable_array as $k => $v) {
        $new_array[$k] = $array[$k];
      }
    }
    return $new_array;
  }
  
  public function export($idSurvey){
    //
    //$route2 = "../../../tmp/exportSurvey/Export-{$idSurvey}.csv";
    $route2 =  \Phalcon\DI::getDefault()->get('path')->path . "tmp/exportSurvey/Export-{$idSurvey}.csv";
    $file = fopen($route2, "w");
    unset($route2);
    //
    $titulos = 1;
    $separador = ";";
    foreach ($this->data as $value) {
      if ($titulos == 1) {
        fputs($file, utf8_decode("Fecha y Hora:") . $separador);
        fputs($file, utf8_decode("Nombre") . $separador);
        fputs($file, utf8_decode("Apellido") . $separador);
        fputs($file, utf8_decode("Correo") . $separador);
        foreach ($value->questions as $ques) {
          fputs($file, strtoupper(utf8_decode($ques->question)) . $separador);
        }
        fputs($file, "\r\n");
        $titulos = 0;
        continue;
      }
      fputs($file,  $value->dateandhour==null||""? $separador: date('Y-m-d G:i:s', $value->dateandhour). $separador);
      fputs($file, (string) $value->name==null||""? $separador: $value->name. $separador);
      fputs($file, (string) $value->lastname==null||""? $separador: $value->lastname. $separador);
      fputs($file, (string) $value->email==null||""? $separador:trim($value->email) . $separador);
      foreach($value->questions as $value2){
        foreach ($value2->answer as $answer) {
          $answerfinal = $answerfinal . $answer . "," ;  
        }  
        $answerfinal = substr($answerfinal, 0, -1); 
        fputs($file, utf8_decode($answerfinal). $separador);
        unset($answerfinal);
      }
      fputs($file, "\r\n");
      unset($value);
    }
    fclose($file);
    unset($file);
    exit;
  }
}
