<?php

namespace Sigmamovil\Wrapper;

use MongoDB\Driver\Query;
use Sigmamovil\General\Links\ParametersEncoder as pe;

class SurveyWrapper extends \BaseWrapper {

  private $form;
  public $idContactlist = array();
  public $idSegment = array();
  public $inIdcontact = array();

  public function __construct() {
    $this->form = new \SurveyForm();
    parent::__construct();
  }

  public function getPublicsurvey() {

    $survey = \Survey::find(["conditions" => "endDate > now() and status= 'published' and idSubaccount = ?0", "bind" => array($this->user->Usertype->idSubaccount)]);
//        var_dump(count($survey));
//        exit();
    $data = array();
    if (count($survey) > 0) {
      foreach ($survey as $key => $value) {
        $data[$key] = array(
            "idSurvey" => $value->idSurvey,
            "name" => $value->name
        );
      }
    }
    return $data;
  }

  public function listSurvey($page, $filter) {
    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $sanitize = new \Phalcon\Filter;

    $name = (isset($filter->name) ? " AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');

    $conditions = array(
        "conditions" => "idSubaccount = {$this->user->Usertype->idSubaccount} AND deleted = ?0 {$name}",
        "bind" => array(0),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );

    $survey = \Survey::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \Survey::count($conditions);

    $data = array();

    if (count($survey) > 0) {
      foreach ($survey as $key => $value) {
        $data[$key] = array(
            "idSurvey" => $value->idSurvey,
            "surveyCategory" => $value->SurveyCategory->name,
            "name" => $value->name,
            "description" => $value->description,
            "status" => $value->status,
            "startDate" => $value->startDate,
            "endDate" => $value->endDate,
            "updated" => date("Y-m-d", $value->updated),
            "created" => date("Y-m-d", $value->created),
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "content" => $value->SurveyContent,
            "count" => $value->totalCount,
        );
      }
    }
    return array(
        "total" => $total,
        "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
        "items" => $data
    );
  }

  public function getAllSurveyCategories() {
    $suerveycategory = \SurveyCategory::find(array(
                'conditions' => "deleted = 0 AND status = 1 AND idAccount = " . \Phalcon\DI::getDefault()->get('user')->Usertype->Subaccount->idAccount
    ));

    $array = array();
    foreach ($suerveycategory as $value) {
      $arr = array();
      $arr['idSurveyCategory'] = $value->idSurveyCategory;
      $arr['name'] = $value->name;
      $array[] = $arr;
    }

    return $array;
  }

  public function saveSurvey($data) {

    if (!isset($data['idCategorySurvey'])) {
      throw new \InvalidArgumentException("El campo categoría es obligatorio, por favor valide la información");
    }
    $survey = new \Survey();
    $survey->idSubaccount = ((isset($this->user->Usertype->idSubaccount)) ? $this->user->Usertype->idSubaccount : Null);
    $survey->idSurveyCategory = $data['idCategorySurvey'];
    $survey->deleted = 0;
    $survey->type = $data["type"];
    //$survey->status = $data['status'] ? 1 : 0;

    if (isset($data["url"]) && $data["url"] != "") {
      if (preg_match('/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $data["url"])) {
        
      } else {

        //Concateno SCHEME con los datos de la URL
        $parse = 'http://' . $data["url"];
        //Realiza una comparación con la variable $parse
        if (preg_match('/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $parse)) {
          //Enviamos los datos de la URL concatenada SCHEME
          $data["url"] = 'http://' . $data["url"];
        } else {
          throw new \InvalidArgumentException("La url no tiene un formato valido, por favor valide la información.");
        }
      }
    }

    $this->form->bind($data, $survey);

    $survey->name = substr($survey->name, 0, 70);
    $survey->description = substr($survey->description, 0, 200);

    if (!$this->form->isValid()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    if (!$survey->save()) {
      foreach ($survey->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $survey;
  }

  public function findSurvey($idSurvey) {
    $survey = \Survey::findFirst(array(
                'conditions' => "idSurvey = ?0",
                'bind' => array($idSurvey)
    ));

    $array = array();
    $array['idSurvey'] = $survey->idSurvey;
    $array['idSurveyCategory'] = $survey->idSurveyCategory;
    $array['name'] = $survey->name;
    $array['description'] = $survey->description;
    $array['status'] = $survey->status;
    $array['type'] = $survey->type;
    $array['startDate'] = $survey->startDate;
    $array['endDate'] = $survey->endDate;
    $array['messageFinal'] = $survey->messageFinal;
    $array['url'] = $survey->url;


    return $array;
  }

  public function editSurvey($data, $idSurvey) {
    $survey = \Survey::findFirst(array(
                "conditions" => "idSurvey = {$idSurvey}"
    ));

    if (!$survey) {
      throw new \InvalidArgumentException("No se encontró la encuesta, por favor valide la información");
    }

    if (!isset($data['idCategorySurvey'])) {
      throw new \InvalidArgumentException("El campo categoría es obligatorio, por favor valide la información");
    }

    if (preg_match('/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $data["url"])) {
      
    } else {
      //Concateno SCHEME con los datos de la URL
      $parse = 'http://' . $data["url"];
      //Realiza una comparación con la variable $parse
      if (preg_match('/^(http|https):\\/\\/[a-z0-9]+([\\-\\.]{1}[a-z0-9]+)*\\.[a-z]{2,5}' . '((:[0-9]{1,5})?\\/.*)?$/i', $parse)) {
        //Enviamos los datos de la URL concatenada SCHEME
        $data["url"] = 'http://' . $data["url"];
      } else {
        throw new \InvalidArgumentException("La url no tiene un formato valido, por favor valide la información.");
      }
    }

    $survey->idSubaccount = ((isset($this->user->Usertype->idSubaccount)) ? $this->user->Usertype->idSubaccount : Null);
    $survey->idSurveyCategory = $data['idCategorySurvey'];
    $survey->deleted = 0;
    $survey->type = $data["type"];
    //$survey->status = $data['status'] ? 1 : 0;

    $this->form->bind($data, $survey);

    if (!$this->form->isValid()) {
      foreach ($this->form->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    $survey->name = substr($data['name'], 0, 70);
    $survey->description = substr($data['description'], 0, 200);

    if (!$survey->save()) {
      foreach ($survey->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }

    return $survey;
  }

  public function createContentSurvey($idSurvey, $data) {
    $surveyContent = new \SurveyContent();
    $content = json_encode($data);

    $surveyContent->idSurvey = $idSurvey;
    $surveyContent->content = $content;

    if (!$surveyContent->save()) {
      foreach ($surveyContent->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    $this->saveQuestions($surveyContent);

    return $surveyContent;
  }

  public function editContentSurvey($data, $surveyContent) {
    $content = json_encode($data);

    $surveyContent->content = $content;

    if (!$surveyContent->save()) {
      foreach ($surveyContent->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    $this->editQuestion($surveyContent);

    return $surveyContent;
  }

  public function getSurveyContent($idSurvey) {
    if (!isset($idSurvey)) {
      throw new \InvalidArgumentException("Dato de encuesta inválido");
    }

    $surveycontent = \SurveyContent::findFirst(array(
                "conditions" => "idSurvey = ?0",
                "bind" => array($idSurvey)
    ));
    
    $survey = \Survey::findFirst(array(
                "conditions" => "idSurvey = ?0",
                "bind" => array($idSurvey)
    ));

    if (!$surveycontent) {
      return $data = ["response" => "empty", "content" => ""];
    }

    $data = ["response" => "success", "content" => $surveycontent->content, "status" => $surveycontent->Survey->status,"deleted"=>(int) $survey->deleted];

    return $data;
  }

  public function saveQuestions(\SurveyContent $surveyContent) {
    $questions = json_decode($surveyContent->content);
    foreach ($questions->content as $value) {
      $question = new \Question();
      $question->idQuestion = (string) $value->id;
      $question->idSurvey = $surveyContent->idSurvey;
      $question->component = $value->component;
      $question->question = $value->label;
      $question->count = (int) 0;

      if (!$value->objExt->notDb) {
        if (!$question->save()) {
          foreach ($question->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }

        $this->saveAnswer($value, $question->idQuestion);
      }
    }
  }

  public function saveAnswer($question, $idQuestion) {
    $answers = \Answer::find([array("idQuestion" => (String) $idQuestion)]);
    if(!$answers){
      if ($question->component != "textArea") {
        foreach ($question->options as $value) {
          $answer = new \Answer();
          $contactManger = new \Sigmamovil\General\Misc\ContactManager();
          $nextIdAnswer = $contactManger->autoIncrementCollection("id_answer");
          $answer->idAnswer = $nextIdAnswer;
          $answer->idQuestion = $idQuestion;
          $answer->answer = trim($value);
          $answer->contacts = [];
          $answer->count = (int) 0;

          if (!$answer->save()) {
            foreach ($answer->getMessages() as $msg) {
              throw new \InvalidArgumentException($msg);
            }
          }
        }
      }
    }
  }

  public function editQuestion(\SurveyContent $surveyContent) {
    $questions = json_decode($surveyContent->content);
    $question = \Question::find(array(array("idSurvey" => (String) $surveyContent->idSurvey)));
    foreach ($questions->content as $k => $val) {
      foreach ($question as $key => $q) {
        if ($val->id == $q->idQuestion) {
          if ($val->label != $q->question) {
            $q->question = $val->label;
            if (!$q->save()) {
              foreach ($q->getMessages() as $msg) {
                throw new \InvalidArgumentException($msg);
              }
            }
            unset($question[$key]);
            unset($questions->content[$k]);
          } else {
            unset($question[$key]);
            unset($questions->content[$k]);
          }
          if ($q->component != "textArea") {
            $this->deleteAnswer($q->idQuestion);
            $this->saveAnswer($val, $q->idQuestion);
          }
        }
      }
    }

    if (count($questions->content) > 0) {
      foreach ($questions->content as $value) {
        $qst = new \Question();
        $qst->idQuestion = (string) $value->id;
        $qst->idSurvey = $surveyContent->idSurvey;
        $qst->component = $value->component;
        $qst->question = $value->label;
        $qst->count = (int) 0;

        if ($qst->component != "encabezado" && $qst->component != "button") {
          if (!$value->objExt->notDb) {
            if (!$qst->save()) {
              foreach ($qst->getMessages() as $msg) {
                throw new \InvalidArgumentException($msg);
              }
            }
          }
        }

        $this->saveAnswer($value, (String) $value->id);
      }
    }

    if (count($question) > 0) {
      foreach ($question as $value) {
        $idQuestion = $value->idQuestion;
        if (!$value->delete()) {
          foreach ($value->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
        $this->deleteAnswer($idQuestion);
      }
    }
  }

  public function deleteAnswer($idQuestion) {
    $answers = \Answer::find([array("idQuestion" => (String) $idQuestion)]);

    if (count($answers) > 0) {
      foreach ($answers as $val) {
        if (!$val->delete()) {
          foreach ($val->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
      }
    }
  }

  public function saveConfirmation($data) {
    if (!isset($data->idSurvey)) {
      throw new \InvalidArgumentException("Dato de encuesta inválido");
    }

    $survey = \Survey::findFirst(array(
                "conditions" => "idSurvey = ?0",
                "bind" => array($data->idSurvey)
    ));

    if (!$survey) {
      throw new \InvalidArgumentException("La encuesta que desea confirmar no existe");
    }

    if ($data->status != 1) {
        
        //SI EL STATUS QUE LLEGA ES EL 3 (PASO CONFIRMAR ENCUESTAS) ACTUALIZAMOS EL STATUS DE LA ENCUESTA A published
        $survey->status = "published";
        
      if ($data->status != 3) {
        if (empty($data->startDate)) {
          throw new \InvalidArgumentException("La fecha incial no puede estar vacía");
        }
        if (time() > strtotime($data->startDate)) {
          throw new \InvalidArgumentException("La fecha y hora incial no puede ser anterior a la actual");
        }
      }
      if (empty($data->endDate)) {
        throw new \InvalidArgumentException("La fecha de expiración no puede estar vacía");
      }
      if (strtotime($data->endDate) < strtotime($data->startDate)) {
        throw new \InvalidArgumentException("La fecha y hora de expiración no puede ser anterior a la fecha inicial");
      }

      $survey->startDate = $data->startDate;
      $survey->endDate = $data->endDate;
    }

//    $survey->status = $data->status;

    if (!$survey->update()) {
      foreach ($survey->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    \Phalcon\DI::getDefault()->get('notification')->success("La encuesta ha sido guardada exitosamente");

    return ["message" => "La encuesta ha sido guardada exitosamente"];
  }

  public function linkGenerator($idSurvey) {
    if (!isset($idSurvey)) {
      throw new \InvalidArgumentException("Dato de encuesta inválido");
    }

    $survey = \Survey::findFirst(array(
                "columns" => "idSurvey, type",
                "conditions" => "idSurvey = ?0",
                "bind" => array($idSurvey)
    ));

    if (!$survey) {
      throw new \InvalidArgumentException("La encuesta a la que intenta generar link no existe");
    }

    $url = "{$this->urlManager->get_base_uri(true)}survey/showsurvey/{$survey->idSurvey}/";

    return ["link" => $url];
  }

  public function saveAnswerSurvey($idSurvey, $idContact, $data) {
    $survey = \Survey::findFirst(array(
                'conditions' => 'idSurvey = ?0',
                'bind' => [$idSurvey]
    ));
    if (!$survey) {
      throw new \InvalidArgumentException('No se encontró la encuesta solicitada, por favor valide la información');
    }

//    if ($survey->type == \Phalcon\DI::getDefault()->get('typeSurvey')->contact) {
    if (isset($idContact) && !empty($idContact) && $idContact != 0) {
      $contact = \Contact::findFirst([[
              "idContact" => (int) $idContact
      ]]);
      if (!$contact) {
        throw new \InvalidArgumentException('No se encontró el contacto, por favor valide la información');
      }

      $manager = \Phalcon\DI::getDefault()->get('mongomanager');

      $optionsQuestion = array(
          'projection' => array('_id' => 0, 'idQuestion' => 1),
      );

      $query = [
          "idSurvey" => $idSurvey,
          "deleted" => 0
      ];

      $queryQuestion = new \MongoDB\Driver\Query($query, $optionsQuestion);
      $question = $manager->executeQuery("aio.question", $queryQuestion)->toArray();

      $idsQuestion = $this->fixArrayQuestion($question);
      unset($question);

      $answer = \Answer::find([[
              "contacts." . $idContact => $idContact,
              "idQuestion" => ['$in' => $idsQuestion]
      ]]);
      if (empty($answer)) {
        foreach ($data as $key => $value) {
          $question = \Question::findFirst([[
                  "idQuestion" => $key
          ]]);
          if ($question->component == \Phalcon\DI::getDefault()->get('componentQuestion')->textArea) {
            $this->createAnswer($key, $value, $idContact);
          } else if ($question->component == \Phalcon\DI::getDefault()->get('componentQuestion')->select || $question->component == \Phalcon\DI::getDefault()->get('componentQuestion')->radio) {
            $this->updateAnswer($key, $value, $idContact);
          } else if ($question->component == \Phalcon\DI::getDefault()->get('componentQuestion')->checkbox) {
            foreach ($value as $item) {
              if (!empty($item)) {
                $this->updateAnswer($key, $item, $idContact);
              }
            }
          }
          if (!empty($value)) {
            if (is_array($value) && empty($value[0])) {
              
            } else {
                $i=0;$aswerAr = array();
                foreach ($idsQuestion as $value) {
                $optionsAnswer = array('projection' => array('_id' => 0, 'idAnswer' => 1, 'idQuestion' => 1, 'answer' => 1, 'contacts' => 1, 'count' => 1),);
                $query = ["idQuestion" => $value, "deleted" => 0];
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

              $question->count = count($aswerAr);
              if (!$question->save()) {
                foreach ($question->getMessages() as $message) {
                  throw new \InvalidArgumentException($message);
                }
              }
            }
          }
        }
        
        $survey->totalCount = count($aswerAr);
        if (!$survey->save()) {
          foreach ($survey->getMessages() as $message) {
            throw new \InvalidArgumentException($message);
          }
        }
      } else {
        throw new \InvalidArgumentException('La encuesta solo puede ser respondida una vez');
      }
    } else {
      foreach ($data as $key => $value) {
        $question = \Question::findFirst([[
                "idQuestion" => $key
        ]]);
        if ($question->component == \Phalcon\DI::getDefault()->get('componentQuestion')->textArea) {
          $this->createAnswer($key, $value);
        } else if ($question->component == \Phalcon\DI::getDefault()->get('componentQuestion')->select || $question->component == \Phalcon\DI::getDefault()->get('componentQuestion')->radio) {
          $this->updateAnswer($key, $value);
        } else if ($question->component == \Phalcon\DI::getDefault()->get('componentQuestion')->checkbox) {
          foreach ($value as $item) {
            if (!empty($item)) {
              $this->updateAnswer($key, $item);
            }
          }
        }
        if (!empty($value)) {
          $question->count = $question->count + 1;
          if (!$question->save()) {
            foreach ($question->getMessages() as $message) {
              throw new \InvalidArgumentException($message);
            }
          }
        }
      }
      $survey->totalCount = $survey->totalCount + 1;
      if (!$survey->save()) {
        foreach ($survey->getMessages() as $message) {
          throw new \InvalidArgumentException($message);
        }
      }
    }

    return true;
  }

  private function updateAnswer($key, $value, $idContact = null) {

    $iduser;
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $bulk = new \MongoDB\Driver\BulkWrite;
    $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);

    if (!empty($value)) {
      $string = trim($value);
      $answer = \Answer::findFirst([[
        "idQuestion" => $key, "answer" => ['$regex' => ".*$string.*"]
      ]]);

      if (!$answer) {
        $answer = new \Answer();
        $contactManger = new \Sigmamovil\General\Misc\ContactManager();
        $nextIdAnswer = $contactManger->autoIncrementCollection("id_answer");
        $answer->idAnswer = $nextIdAnswer;
        $answer->idQuestion = $key;
        $answer->answer = trim($value);

        if ($idContact != null) {
          $answer->contacts = [$idContact => time()];
        } else {
          $ans = \Question::findFirst([["idQuestion" => $key]]);
          $sur = \Survey::findFirst(array(
                      'conditions' => 'idSurvey = ?0',
                      'bind' => [$ans->idSurvey]
          ));

          $iduser = $sur->totalCount;
          $idcon = 'user' . ($iduser + 1);

          $answer->contacts = [$idcon => time()];
        }

        $answer->count = 1;

        if (!$answer->save()) {
          foreach ($answer->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
      } else {
        $contacemply = $answer->contacts;
        if ($idContact != null) {

//          if (empty($contacemply)) {
          // $answer->contacts = [$idContact => (string) $idContact];
//          }else{
          $answer->contacts[$idContact] = time();
//          }
        } else {
          $ans = \Question::findFirst([["idQuestion" => $key]]);
          $sur = \Survey::findFirst(array(
                      'conditions' => 'idSurvey = ?0',
                      'bind' => [$ans->idSurvey]
          ));

          $iduser = $sur->totalCount;
          $idcon = "user" . ($iduser + 1);


//          if (empty($contacemply)) {
          //  $answer->contacts = [$idcon => $idcon];
//          } else {
          //$answer->contacts[$idcon] = (string) $idcon;
          $answer->contacts[$idcon] = time();
//          }
        }

        //$answer->count = (int) $answer->count + 1;
        $answer->count = count($answer->contacts);

        if (!$answer->save()) {
          foreach ($answer->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }

        if (empty($contacemply)) {
          
        } else {
          if (isset($idContact)) {
            $value = trim($value);
            $bulk->update(['idQuestion' => $key, 'answer' => $value], ['$set' => ['contacts.' . $idContact => time()]], ['multi' => true]);
            $manager->executeBulkWrite('aio.answer', $bulk, $writeConcern);
          } else {
//            var_dump(['idQuestion' => $key, 'answer' => $value], ['$set' => ['contacts.' . $idcon => $idcon]], ['multi' => true]);
//            exit();
//            $value = trim($value);
//            $bulk->update(['idQuestion' => $key, 'answer' => $value], ['$set' => ['contacts.0.' . $idcon => true]], ['multi' => true]);
//            $manager->executeBulkWrite('aio.answer', $bulk, $writeConcern);
            $value = trim($value);
            //$bulk->update(['idQuestion' => $key, 'answer' => $value], ['$set' => ['contacts.' . $idcon => $idcon]], ['multi' => true]);
            $bulk->update(['idQuestion' => $key, 'answer' => $value], ['$set' => ['contacts.' . $idcon => time()]], ['multi' => true]);
            $manager->executeBulkWrite('aio.answer', $bulk, $writeConcern);
          }
        }
      }

//      return $answer;
    }
  }

  private function createAnswer($key, $value, $idContact = null) {

    $iduser;
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $bulk = new \MongoDB\Driver\BulkWrite;
    $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);

    if (!empty($value)) {
      $string = trim($value);
      $answer = \Answer::findFirst([[
        "idQuestion" => $key, "answer" => ['$regex' => ".*$string.*"]
      ]]);

      if (!$answer) {
        $answer = new \Answer();
        $contactManger = new \Sigmamovil\General\Misc\ContactManager();
        $nextIdAnswer = $contactManger->autoIncrementCollection("id_answer");
        $answer->idAnswer = $nextIdAnswer;
        $answer->idQuestion = $key;
        $answer->answer = trim($value);


        if (isset($idContact)) {
          //$answer->contacts = [$idContact => (string) $idContact];
          $answer->contacts = [$idContact => time()];
        } else {
          $ans = \Question::findFirst([["idQuestion" => $key]]);
          $sur = \Survey::findFirst(array(
                      'conditions' => 'idSurvey = ?0',
                      'bind' => [$ans->idSurvey]
          ));

          $iduser = $sur->totalCount;
          $idcon = 'user' . ($iduser + 1);

          $answer->contacts = [$idcon => time()];
        }

        $answer->count = 1;

        if (!$answer->save()) {
          foreach ($answer->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
      } else {

        $contacemply = $answer->contacts;
        if (isset($idContact)) {

//          if (empty($contacemply)) {
          // $answer->contacts = [$idContact => (string) $idContact];
//          }else{
          //$answer->contacts[$idContact] = (string) $idContact;
          $answer->contacts[$idContact] = time();
//          }
        } else {
          $ans = \Question::findFirst([["idQuestion" => $key]]);
          $sur = \Survey::findFirst(array(
                      'conditions' => 'idSurvey = ?0',
                      'bind' => [$ans->idSurvey]
          ));

          $iduser = $sur->totalCount;
          $idcon = "user" . ($iduser + 1);

//          if (empty($contacemply)) {
          //  $answer->contacts = [$idcon => $idcon];
//          } else {
          $answer->contacts[$idcon] = time();
//          }
        }

        $answer->count = (int) $answer->count + 1;

        if (!$answer->save()) {
          foreach ($answer->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }

        if (empty($contacemply)) {
          
        } else {

          if (isset($idContact)) {
            $value = trim($value);
            $bulk->update(['idQuestion' => $key, 'answer' => $value], ['$set' => ['contacts.' . $idContact => time()]], ['multi' => true]);
            $manager->executeBulkWrite('aio.answer', $bulk, $writeConcern);
          } else {
            $value = trim($value);
            $bulk->update(['idQuestion' => $key, 'answer' => $value], ['$set' => ['contacts.' . $idcon => time()]], ['multi' => true]);
            $manager->executeBulkWrite('aio.answer', $bulk, $writeConcern);
          }
        }
      }

//      return $answer;
    }

//    if (!empty($value)) {
//      $answer = new \Answer();
//      $contactManger = new \Sigmamovil\General\Misc\ContactManager();
//      $nextIdAnswer = $contactManger->autoIncrementCollection("id_answer");
//      $answer->idAnswer = $nextIdAnswer;
//      $answer->idQuestion = $key;
//      $answer->answer = trim($value);
//
//
//      if (isset($idContact)) {
//        $answer->contacts = [$idContact => (string) $idContact];
//      } else {
//
//        $iduser;
//
//        $ans = \Question::findFirst([["idQuestion" => $key]]);
//        $sur = \Survey::findFirst(array(
//                    'conditions' => 'idSurvey = ?0',
//                    'bind' => [$ans->idSurvey]
//        ));
//
//        if (isset($iduser)) {
//          $iduser = $sur->totalCount;
//          $idcon = "user" . ($iduser + 1);
//        }
//
//        $answer->contacts = [$idcon => $idcon];
//      }
//
//      $answer->count = 1;
//      
//      var_dump($answer);
//      exit;
//
//      if (!$answer->save()) {
//        foreach ($answer->getMessages() as $msg) {
//          throw new \InvalidArgumentException($msg);
//        }
//      }
//    }
  }

  public function fixArrayQuestion($question) {
    $array = array();
    foreach ($question as $value) {
      $array[] = $value->idQuestion;
    }
    return $array;
  }

  public function sendMail($data) {

    if (isset($data->mailtemplate->idMailTemplate)) {
      $mailTemplate = \MailTemplate::findFirst(array("conditions" => "idMailTemplate = ?0 and deleted = 0", "bind" => array($data->mailtemplate->idMailTemplate)));

      if (!$mailTemplate) {
        throw new \InvalidArgumentException("La plantilla seleccionada no se encuentra registrada o puede estar eliminada, por favor validar.");
      }
    } else {
      throw new \InvalidArgumentException("Diligenciar la plantilla de correo,por favor validar.");
    }

    if (isset($data->mailcategory->idMailCategory)) {
      $mailCategory = \MailCategory::findFirst(array("conditions" => "idMailCategory = ?0 and deleted = 0", "bind" => array($data->mailcategory->idMailCategory)));

      if (!$mailCategory) {
        throw new \InvalidArgumentException("La categoria seleccionada no se encuentra registrada o puede estar eliminada, por favor validar.");
      }
    } else {
      throw new \InvalidArgumentException("Diligenciar la categoria de correo,por favor validar.");
    }

    if (isset($data->senderName->idNameSender)) {
      $nameSender = \NameSender::findFirst(array("conditions" => "idNameSender = ?0 and status = 1", "bind" => array($data->senderName->idNameSender)));

      if (!$nameSender) {
        throw new \InvalidArgumentException("El nombre del remitente seleccionado no se encuentra registrado o puede estar en estado inactivo, por favor validar.");
      }
    } else {
      throw new \InvalidArgumentException("Diligenciar el nombre del remitente,por favor validar.");
    }

    if (isset($data->senderEmail->idEmailsender)) {
      $emailSender = \Emailsender::findFirst(array("conditions" => "idEmailsender = ?0 and status = 1", "bind" => array($data->senderEmail->idEmailsender)));

      if (!$emailSender) {
        throw new \InvalidArgumentException("El correo del remitente seleccionado no se encuentra registrado o puede estar en estado inactivo, por favor validar.");
      }
    } else {
      throw new \InvalidArgumentException("Diligenciar el correo del remitente,por favor validar.");
    }

    if (!isset($data->subject)) {
      throw new \InvalidArgumentException("Diligenciar el asunto del correo,por favor validar.");
    }
    $target = new \stdClass();
    if (!isset($data->listDestinatary)) {
      throw new \InvalidArgumentException("Diligenciar la lista de destinatario del correo,por favor validar.");
    } else {
      $target->type = ($data->listDestinatary->id == 1) ? "contactlist" : "segment";
    }

    if (!isset($data->destinatary)) {
      throw new \InvalidArgumentException("Diligenciar los destinatarios correo,por favor validar.");
    } else {
      if ($target->type == "contactlist") {
        $target->contactlists = $data->destinatary;
      } else {
        $target->segment = $data->destinatary;
      }
    }
    switch ($target->type) {
      case "contactlist":
        $this->getIdContaclist($target->contactlists);
        $this->getAllCxclMail();
        break;
      case "segment":
        $this->getIdSegment($target->segment);
        $this->getAllIdContactSegmentMail();
        break;
      default:
    }
    
    $flagSending = false;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == "sending") {
        $flagSending = true;
      }
    }
    if($flagSending){
      //Se realiza validaciones de los sms programados
      $balance = $this->validateBalance();
      $target = 0;
      if($balance['mailFindPending']){
        foreach ($balance['mailFindPending'] as $value){
          $target = $target + $value['target'];
        }
      }
      $amount = $balance['balanceConsumedFind']['amount'];

      unset($balance);
      $totalTarget =  $amount - $target;
      $target = $target + count($this->inIdcontact);

      if($target>$amount){
        $target = $target - $amount;
        if($totalTarget <= 0){
          $tAvailable = (object) ["totalAvailable" => 0];
        } else {
          $tAvailable = (object) ["totalAvailable" => $totalTarget];
        }
        $this->sendmailnotmailbalance($tAvailable);
        throw new \InvalidArgumentException("No tiene saldo disponible para realizar esta campaña!, su saldo disponlble es {$totalTarget} envios, ya que existen campañas programadas pendientes por enviar.");
      }
      unset($target);
      unset($amount);
      unset($totalTarget);
      unset($tAvailable);
    }

    $mailTemplateContent = \MailTemplateContent::findFirst(array("conditions" => "idMailTemplate = ?0", "bind" => array($mailTemplate->idMailTemplate)));

    if (!$mailTemplateContent) {
      throw new \InvalidArgumentException("No se encontro el contenido de la plantilla, contacte al administrador.");
    }

    $this->db->begin();

    $sendMail = new \Mail();

    $sendMail->idSubaccount = $this->user->Usertype->idSubaccount;
    $sendMail->idEmailsender = $emailSender->idEmailsender;
    $sendMail->idSurvey = $data->survey->idSurvey;
    $sendMail->name = $data->survey->name;
    $sendMail->replyto = (isset($data->replyTo)) ? $data->replyTo : null;
    $sendMail->subject = $data->subject;
    $sendMail->scheduleDate = (isset($data->scheduleDate)) ? $data->scheduleDate : date("Y-m-d H:i", time());
    $sendMail->confirmationDate = date("Y-m-d H:i", time());
    $sendMail->gmt = "-0500";
    $sendMail->target = json_encode($target);
    $sendMail->attachment = 0;
    $sendMail->idNameSender = $nameSender->idNameSender;
    $sendMail->type = "survey";
    $sendMail->status = "scheduled";
    $sendMail->quantitytarget = count($this->inIdcontact);

    if (!$sendMail->save()) {
      $this->db->rollback();
      foreach ($answer->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    $sendMailContent = new \MailContent();

    $sendMailContent->idMail = $sendMail->idMail;
    $sendMailContent->typecontent = "Editor";
    $sendMailContent->content = $mailTemplateContent->content;

    if (!$sendMailContent->save()) {
      $this->db->rollback();
      foreach ($answer->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }

    $changeStatus = new \stdClass();
    $changeStatus->status = "published";
    $changeStatus->type = "contact";

    if (!$this->changeStatus($changeStatus, $data->survey->idSurvey)) {
      $this->db->rollback();
      throw new \InvalidArgumentException($msg);
    }

    if (!$this->changeType($changeStatus, $data->survey->idSurvey)) {
      $this->db->rollback();
      throw new \InvalidArgumentException($msg);
    }

    $this->db->commit();
    return array("message" => "El envio de email se programo correctamente.");
  }

  public function changeStatus($data, $idSurvey) {
    $survey = \Survey::findFirst(array(
                "conditions" => "idSurvey = ?0",
                "bind" => array($idSurvey)
    ));
    if (!$survey) {
      throw new \InvalidArgumentException("No se encontro la encuesta seleccionada, contacte al administrador.");
    }

    $survey->status = $data->status;
    if (!$survey->save()) {
      return false;
    }
    return true;
  }

  public function changeType($data, $idSurvey) {
    $survey = \Survey::findFirst(array(
                "conditions" => "idSurvey = ?0",
                "bind" => array($idSurvey)
    ));
    if (!$survey) {
      throw new \InvalidArgumentException("No se encontro la encuesta seleccionada, contacte al administrador.");
    }

    $survey->type = $data->type;
    if (!$survey->save()) {
      return false;
    }
    return true;
  }

  public function getCategory($idCategorySurvey) {
    $dataReturn = array();
    $suerveycategory = \SurveyCategory::findFirst(array(
                'conditions' => "deleted = 0 AND status = 1 AND idSurveyCategory = " . $idCategorySurvey
    ));

    $arrayReturn = array("name" => $suerveycategory->name,
        "description" => $suerveycategory->description
    );

    return array("data" => $arrayReturn);
  }

  // funcion que recibe el idSurveyDuplicate para retornarlo al controller.js
  public function setSurveyDuplicate() {

    return $this->getIdSurvey;
  }

  //funcion que se encarga de duplicar la encuesta a partir de una encuesta en estado de publicada
  public function duplicateSurvey($idSurvey) {

    $sql = "SELECT
                      s.*, sc.*
                  FROM
                      survey s
                  INNER JOIN survey_content sc ON s.idSurvey = sc.idSurvey
                  WHERE
                      s.idSurvey ={$idSurvey}";
    $surveyData = $this->db->fetchAll($sql)[0];


    $survey = new \Survey();


    $survey->idSurveyCategory = $surveyData["idSurveyCategory"];
    $survey->idSubaccount = $this->user->Usertype->idSubaccount;
    $survey->totalCount = 0;
    $survey->status = "draft";
    $survey->name = $surveyData["name"] . " (Copia)";
    $survey->description = $surveyData["description"];
    $survey->messageFinal = $surveyData["messageFinal"];
    $survey->type = $surveyData["type"];

    if (!$survey->save()) {
      foreach ($survey->getMessages() as $msg) {
        throw new InvalidArgumentException($msg);
      }
    }

    $datasurvey = json_decode($surveyData["content"]);
    $survey_Content = new \SurveyContent();
    $contador= 0;
    foreach ($datasurvey as $contentarray) {
      $contentarray = (array) $contentarray;

      foreach ($contentarray as $key => $value) {
        if(is_numeric($value->id)) {          
          $time = round(microtime(1)*1000);
          usleep(100);
          $value->id = $time + $contador;
        }
        $contador++;
        
      } 
      
    }

    $datasuyenco = json_encode($datasurvey);

    $survey_Content->idSurvey = $survey->idSurvey;
    $survey_Content->content = $datasuyenco;

    if (!$survey_Content->save()) {
      foreach ($survey_Content->getMessages() as $msg) {
        throw new InvalidArgumentException($msg);
      }
    }
    $this->getIdSurvey = $survey->idSurvey;
  }
  
  public function getIdContaclist($arrContactList){
    foreach($arrContactList as $contactList) {
      $idContactlist = $contactList->idContactlist;
      $consultContactList = \Contactlist::findFirst(array("conditions" => "idSubaccount = ?0 and idContactlist = ?1 and deleted =?2", "bind" => array(0 => $this->user->Usertype->idSubaccount, 1 => $idContactlist, 2 => 0)));
      if (!$consultContactList) {
        throw new \InvalidArgumentException("La lista de contacto '{$contactList->name}' ha sido eliminado por favor verifique la información.");
      }
      $this->idContactlist[] = $idContactlist;
    }
    unset($arrContactList);
    unset($idContactlist);
    unset($consultContactList);
  }
  
  public function getIdSegment($arrSegment){
   /* foreach($arrSegment as $segment) {
    
      $idSegment = $segment->idContactlist;
      $consultSegment = \Segment::findFirst(array("conditions" => "idSubaccount = ?0 and idSegment = ?1 and deleted =?2", "bind" => array(0 => $this->user->Usertype->idSubaccount, 1 => $idSegment, 2 => 0)));
      if (!$consultSegment) {
        throw new \InvalidArgumentException("La lista de contacto '{$segment->name}' ha sido eliminado por favor verifique la información.");
      }
      $this->idSegment[] = $idSegment;
    } */
    foreach($arrSegment as $segment) {
      $idSegment = (int) $segment->idSegment;
      //$consultSegment = \Segment::findFirst(array("conditions" => "idSubaccount = ?0 and idSegment = ?1 and deleted =?2", "bind" => array(0 => $this->subAccount->idSubaccount, 1 => $idSegment, 2 => 0)));
      $consultSegment = \Segment::findFirst([["idSegment" => $idSegment, "deleted" => 0]]);
      if (!$consultSegment) {
        throw new \InvalidArgumentException("La lista de contacto '{$segment->name}' ha sido eliminado por favor verifique la información.".$segment->idSegment);
      }
      $this->idSegment[] = $idSegment;
    }

  }
  
  public function getAllCxclMail() {
    $idContactlist = implode(",", $this->idContactlist);
    unset($this->idContactlist);
    $sql = "SELECT DISTINCT idContact FROM cxcl"
      . " WHERE idContactlist IN ({$idContactlist})"
      . " AND unsubscribed = 0 "
      . " AND deleted = 0 "
      . " AND spam = 0 "
      . " AND bounced = 0 "
      . " AND blocked = 0 "
      . " AND singlePhone = 0";
    $cxcl = \Phalcon\DI::getDefault()->get('db')->fetchAll($sql);
    for ($i = 0; $i < count($cxcl); $i++) {
      $this->inIdcontact[$i] = (int) $cxcl[$i]['idContact'];
    }
    unset($sql);
    unset($cxcl);
  }
  
  public function getAllIdContactSegmentMail() {
    $manager = \Phalcon\DI::getDefault()->get('mongomanager');
    $command = new \MongoDB\Driver\Command([
      'aggregate' => 'sxc',
      'pipeline' => [
          ['$match' => ['idSegment' => ['$in' => $this->idSegment],'idContact' => ['$in' => $this->inIdcontact],'email' => ['$nin' => ["", null, "null"]] ]],
          ['$group' => ['_id' => '$idContact', 'data' => ['$first' => '$$ROOT']]]
      ],
      'allowDiskUse' => true,
    ]);
    $segment = $manager->executeCommand('aio', $command)->toArray();
    unset($this->inIdcontact);
    for ($i = 0; $i < count($segment[0]->result); $i++) {
      $this->inIdcontact[$i] = $segment[0]->result[$i]->_id;
    }
    unset($command);
    unset($segment);
  }
  
  public function validateBalance(){
    $date = date('Y-m-d h:i:s');
    $mailFindPending = \Mail::find(array(
      'conditions' => 'idSubaccount = ?0 and status = ?1 and scheduleDate >= ?2',
      'bind' => array(
        0 => $this->user->Usertype->subaccount->idSubaccount,
        1 => 'scheduled',
        2 => $date
      ),
      'columns' => 'idMail, quantitytarget AS target'  
    ));

    $balanceConsumedFind = \Saxs::findFirst(array(
      'conditions' => 'idSubaccount = ?0 and idServices = ?1 and accountingMode = ?2',
      'bind' => array(
        0 => $this->user->Usertype->subaccount->idSubaccount,
        1 => 2,
        2 => 'sending'
      ),
      'columns' => 'idSubaccount, totalAmount-amount as consumed, amount, totalAmount'
    ));

    $answer = ['mailFindPending'=>$mailFindPending->toArray(), 'balanceConsumedFind'=>$balanceConsumedFind->toArray()];

    return $answer;
  }

  public function sendmailnotmailbalance($data){
    $amount = 0;
    foreach ($this->user->Usertype->subaccount->saxs as $key) {
      if ($key->idServices == 2 && $key->accountingMode == "sending") {
        $amount = $data->totalAvailable;
        $totalAmount = $key->totalAmount;
        $subaccountName = $this->user->Usertype->Subaccount->name;
        $accountName = $this->user->Usertype->Subaccount->Account->name;
        $this->arraySaxs = array(
            "amount" => $amount,
            "totalAmount" => $totalAmount,
            "subaccountName" => $subaccountName,
            "accountName" => $accountName
        );
      }
    }
    $sendMailNot= new \Sigmamovil\General\Misc\SmsBalanceEmailNotification();
    //$arraySaxs es una variable tipo array que contine la informacion del saldo en saxs para el servicio de SMS
    $sendMailNot->sendMailNotification($this->arraySaxs);
    return true;
  }

}
