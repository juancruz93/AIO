<?php

namespace Sigmamovil\Wrapper;

class RateWrapper extends \BaseWrapper {
  
  private $rate;
  private $range;
  
  public function __construct() {
    $this->rate = new \RateForm();
    $this->range = new \RangeForm();
    parent::__construct();
  }
  
  public function getAll($page, $filterAll){
    (($filterAll['initial'] > 0) ? $page = ($filterAll['initial'] * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : $filterAll['initial'] );
    ($filterAll['filter'] > 0 ? $filterData = json_decode(json_encode($filterAll['filter'])) : $filterData = new \stdClass());
    $filter = new \Phalcon\Filter;
    $filName = ((isset($filterData->name)) ? "AND name LIKE '%{$filter->sanitize($filterData->name, "string")}%'" : "");
    if(isset($filterData->name)){
      $consulta = ((isset($filterData->name)) ? "AND name LIKE '%{$filter->sanitize($filterData->name, "string")}%'" : "");
    } else if(isset($filterData->idRate)){
      $consulta = ((isset($filterData->idRate)) ? "AND idRate LIKE '%{$filter->sanitize($filterData->idRate, "string")}%'" : "");
    } else {
      $consulta = NULL;
    }
    $filDate = "";
    if (isset($filterData->dateStart) && isset($filterData->dateEnd)) {
      if ($filterData->dateStart != "" && $filterData->dateEnd != "") {
        $initial = strtotime($filterData->dateStart);
        $end = strtotime($filterData->dateEnd);
        if ($initial > $end) {
          throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la final. ');
        }
        $filDate .= " AND created BETWEEN '{$initial}' AND '{$end}'";
      }
    }
    
    $conditions = array(
      "conditions" => "idAllied = ?0 AND deleted = 0 {$consulta} {$filDate} ",
      "bind" => array($this->user->Usertype->Allied->idAllied),
      "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
      "offset" => $page,
      "order" => "created DESC"
    );
    $rate = \Rate::find($conditions);
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \Rate::count($conditions);
    $data = array();
    if (count($rate) > 0) {
      foreach ($rate as $key => $value) {
        $data[$key] = array(
          "idRate" => $value->idRate,
          "services" => $value->Services->name,
          "name" => $value->name,
          "description" => $value->description,
          "accountingMode" => $value->accountingMode,
          "planType" => $value->planType,
          "status" => $value->status,
          "online" => $value->online,
          "dateinitial" => $value->dateinitial,
          "dateend" => $value->dateend,
          "updated" => date("Y-m-d", $value->updated),
          "created" => date("Y-m-d", $value->created),
          "createdBy" => $value->createdBy,
          "updatedBy" => $value->updatedBy,
        );
      }
    }
    return array(
        "total" => $total,
        "total_pages" => (ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT))),
        "items" => $data
    );
  }

  public function createAction($data){
    //
    $findRate = \Rate::findFirst(array("conditions" => "deleted = ?0  AND name = ?1 ","bind" => array(0,$data['name'])));
    if ($findRate) {
      throw new \InvalidArgumentException('EL nombre de la tarifa ya existe. ');
    }
    $form = new \RateForm();
    $modelRate = new \Rate();
    $modelRate->name = $data['name'];
    $modelRate->description = $data['description'];
    $modelRate->accountingMode = $data['accountingMode'];
    $modelRate->dateInitial = $data['dateInitial'];
    $modelRate->dateEnd = $data['dateEnd'];
    $modelRate->countries = json_encode($data["country"]);
    $modelRate->planType = $data['planType'];
    $modelRate->online = (($data["online"] == true) ? 1 : 0);
    $modelRate->status = (($data["status"] == true) ? 1 : 0);
    $modelRate->deleted = 0;
    $modelRate->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);
    $modelRate->idServices = $data['idServices'];
    $modelRate->createdBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    $modelRate->updatedBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    //
    $form->bind($data, $modelRate);
    if (!$form->isValid()) {
      foreach ($form->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    if (!$modelRate->save()) {
      foreach ($modelRate->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    for($i=0; $i<count($data['ranges']); $i++){
      //
      $modelRatexrange = $this->createRange($data['ranges'][$i],$modelRate);     
    }
    //
    return $modelRatexrange;
  }
  
  public function createRange($ranges,$modelRate){
    //
    $modelRange = new \Range();
    $modelRange->since   = $ranges['since'];
    $modelRange->until   = $ranges['until'];
    $modelRange->space   = $ranges['space'];
    $modelRange->value   = $ranges['value'];
    $modelRange->visible = (($ranges["visible"] == true) ? 1 : 0);
    $modelRange->deleted = 0;
    $modelRange->createdBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    $modelRange->updatedBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    //
    if (!$modelRange->save()) {
      foreach ($modelRange->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    $modelRatexrange = $this->createRatexrange($modelRange,$modelRate);
    //
    return $modelRatexrange;
  }
  
  public function createRatexrange($modelRange,$modelRate){
    //
    $modelRatexrange = new \Ratexrange();
    $modelRatexrange->idRate = $modelRate->idRate;
    $modelRatexrange->idRange = $modelRange->idRange;
    $modelRatexrange->deleted = 0;
    $modelRatexrange->createdBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    $modelRatexrange->updatedBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    //
    if (!$modelRatexrange->save()) {
      foreach ($modelRatexrange->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    return $modelRatexrange;
  }
  
  public function getOne($id){
    //
    $getRate = \Rate::findFirst(array("conditions" => "deleted = ?0  AND idAllied = ?1 and idRate =?2 ","bind" => array(0, $this->user->Usertype->Allied->idAllied,$id)));
    if(!$getRate){
      throw new \InvalidArgumentException("Por favor validar la tarifa que desea editar.");
    }
    //
    $getRatexrange = \Ratexrange::find(array("columns" => "idRange", "conditions" => "deleted = ?0  AND idRate =?1 ","bind" => array(0,$getRate->idRate)));
    if(!$getRatexrange){
      throw new \InvalidArgumentException("Por favor validar los rangos de la tarifa que desea editar.");
    }
    //
    $array = [];
    foreach ($getRatexrange as $ratexrange){
      $where = array("conditions" => "deleted = ?0  AND idRange =?1 ","bind" => array(0,(int)$ratexrange->idRange));
      $getRange = \Range::find($where);
      foreach ($getRange as $arrayRange){
        $array[] = $arrayRange;
      }
    }
    //
    return array(
      "idServices"=>$getRate->idServices,
      "name"=>$getRate->name,
      "description"=>$getRate->description,
      "accountingMode"=>$getRate->accountingMode,
      "planType"=>$getRate->planType,
      "dateInitial" =>$getRate->dateInitial,
      "dateEnd" =>$getRate->dateEnd,
      "country" =>json_decode($getRate->countries),
      "status"=>($getRate->status == 1) ? true : false,
      "online"=>($getRate->online == 1) ? true : false,
      "ranges"=>$array,
    );
  }
  
  public function editAction($idRate,$data){
    //
    $modelRate = \Rate::findFirst(array("conditions" => "deleted = ?0  AND idAllied = ?1 and idRate =?2 ","bind" => array(0, $this->user->Usertype->Allied->idAllied,$idRate)));
    if(!$modelRate){
      throw new InvalidArgumentException("No ha enviado ningún dato, por favor valide la información ");
    }
    $findRate = \Rate::findFirst(array("conditions" => "deleted = ?0  AND name = ?1 ","bind" => array(0,$data['name'])));
    if ($findRate->name != $modelRate->name) {
      if ($findRate) {
        throw new \InvalidArgumentException('EL nombre de la tarifa ya existe. ');
      }
    }
    //
    $modelRate->name = $data['name'];
    $modelRate->description = $data['description'];
    $modelRate->accountingMode = $data['accountingMode'];
    $modelRate->dateInitial = $data['dateInitial'];
    $modelRate->dateEnd = $data['dateEnd'];
    $modelRate->countries = json_encode($data["country"]);
    $modelRate->planType = $data['planType'];
    $modelRate->online = (($data["online"] == true) ? 1 : 0);
    $modelRate->status = (($data["status"] == true) ? 1 : 0);
    $modelRate->deleted = 0;
    $modelRate->idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);
    $modelRate->idServices = $data['idServices'];
    $modelRate->createdBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    $modelRate->updatedBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    //
    $this->rate->bind($data, $modelRate);
    if (!$this->rate->isValid()) {
      foreach ($this->rate->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    if (!$modelRate->save()) {
      foreach ($modelRate->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    return $this->findRange($data['ranges'],$idRate,$modelRate);
  }

  public function findRange($ranges,$idRate,$modelRate){
    $total = \Ratexrange::count(array("conditions" => "deleted = ?0 AND idRate =?1 ","bind" => array(0,$idRate)));
    $array = [];
    for($i=0; $i<count($ranges); $i++){
      //
      if($ranges[$i]['idRange'] != NULL){
        $modelRatexrange = $this->editRange($ranges[$i]);     
      } else {
        $modelRatexrange = $this->createRange($ranges[$i],$modelRate);     
      }
      $array[] = (int)$ranges[$i]['idRange'];
    }
    if($total > count($ranges)){
      $modelRatexrange = $this->deletedRange($array,$idRate);
    }
    //
    return $modelRatexrange;    
  }
  
  public function editRange($ranges){
    //
    $modelRange = \Range::findFirst(array("conditions" => "deleted = ?0  AND idRange =?1 ","bind" => array(0,$ranges['idRange'])));
    $modelRange->since = $ranges['since'];
    $modelRange->until = $ranges['until'];
    $modelRange->space = $ranges['space'];
    $modelRange->value = $ranges['value'];
    $modelRange->visible = (($ranges["visible"] == true) ? 1 : 0);
    $modelRange->deleted = 0;
    $modelRange->createdBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    $modelRange->updatedBy = ((isset($this->user->Usertype->Allied->email)) ? $this->user->Usertype->Allied->email : NULL);
    //
    if (!$modelRange->save()) {
      foreach ($modelRange->getMessages() as $msg) {
        throw new \InvalidArgumentException($msg);
      }
    }
    //
    return $modelRange;
  }
          
  public function deletedRange($ranges,$idRate){
    //
    $modelRatexrange = \Ratexrange::find(array("conditions" => "deleted = ?0  AND idRate = ?1","bind" => array(0,$idRate)));
    //
    foreach ($modelRatexrange as $key => $ratexrange){
      //
      if($ratexrange->idRange == isset($ranges[$key]) ? $ranges[$key] : NULL){ } else {
        //
        $modelratexrange = \Ratexrange::findFirst(array("conditions" => "deleted = ?0  AND idRate =?1 AND idRange =?2","bind" => array(0,$idRate,$ratexrange->idRange)));
        $modelratexrange->deleted = time();
        //
        if (!$modelratexrange->save()) {
          foreach ($modelratexrange->getMessages() as $msg) {
            throw new \InvalidArgumentException($msg);
          }
        }
      }
    }
  }
  
  public function validateAction() {
    //Hacer validaciones para quitar el plan de pagos en caso de cambiar de accountingMode
  }
  
  public function deleteAction($idRate){
    //
    $idAllied = $this->user->Usertype->Allied->idAllied;
    //
    $modelrate = \Rate::findFirst(array("conditions" => "deleted = ?0  AND idAllied =?1 AND idRate =?2","bind" => array(0,$idAllied,$idRate)));
    $modelrate->deleted = time();
    //
    if (!$modelrate->update()) {
      foreach ($modelrate->getMessages() as $message) {
        throw new \InvalidArgumentException($message);
      }
    }
    //
    return $modelrate;
  }
}