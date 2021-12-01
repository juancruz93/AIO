<?php

/**
 * Description of UpdateAnswerOfSurvey
 *
 * @author jose.quinones
 */
require_once(__DIR__ . "/../bootstrap/index.php");

$id = 0;
if (isset($argv[1])) {
  $id = $argv[1];
}

$updateAnswerOfSurvey = new UpdateAnswerOfSurvey();
$updateAnswerOfSurvey->index($id);

class UpdateAnswerOfSurvey {
  
  public function index($idSurvey){
    $survey = \Survey::findFirst(array(
      "conditions" => array(
        "idSurvey" => $idSurvey
      ),
      "fields" => array(
        "_id" => false,  
        "idSurvey" => true,
      )  
    ));
    if (!$survey) {
      throw new \InvalidArgumentException('No se encontró la encuesta solicitada, por favor valide la información');
    }
    unset($survey);
    $questions = \Question::find([
      "conditions" => array(
        "idSurvey" => $idSurvey,
      ),
      "fields" => array(
        "_id" => false,    
        "idQuestion" => true,
        "component" => true,
      )
    ]);
    foreach ($questions as $question) {
      if ($question->component == 'select' || $question->component == 'radio' || $question->component == 'checkbox') {
        $idQuestion = (string) $question->idQuestion;
        $answers = \Answer::find([
          "conditions" => array(
            "idQuestion" => $idQuestion,
          )  
        ]);
        $contacts = [];
        foreach ($answers as $answer) {
          $name_answer = (string) trim($answer->answer);
          if (!$contacts[$name_answer]) {
            $name_answer = trim($answer->answer);
            $contacts[$name_answer] = [
              "idAnswer" => (int) $answer->idAnswer,  
            ];
          } else {
            if($contacts[$name_answer]) {
              $idAnswer = (int) $contacts[$name_answer]["idAnswer"];
              if ($idAnswer != $answer->idAnswer) {
                $answerFirst = \Answer::findFirst([
                  "conditions" => array(
                    "idAnswer" => $idAnswer,
                  )
                ]);
                $result = array_merge($answerFirst->contacts, $answer->contacts);
                //
                $answerFirst->contacts = $result;
                $answerFirst->count = count($result);
                $answerFirst->save();
                //
                $answer->delete();
              }
            }
          }
        }
      } 
    }
  }
}
