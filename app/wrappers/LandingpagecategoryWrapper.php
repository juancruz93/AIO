<?php

namespace Sigmamovil\Wrapper;

class LandingPageCategoryWrapper extends \BaseWrapper {

  /**
   * @description consulta todos los datos para hacer el paginador primer parametro es la pagina de offset y el segundo son los filtros 
   * @param Integer $page
   * @param Array $data
   * @return Array 
   * @throws \InvalidArgumentException
   */
  public function getAll($page, $data) {
    (($data['initial'] > 0) ? $page = ($data['initial'] * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : $data['initial'] );
    ($data['filter'] > 0 ? $filterData = json_decode(json_encode($data['filter'])) : $filterData = new \stdClass());
    $filter = new \Phalcon\Filter;
    $filName = ((isset($filterData->name)) ? "AND name LIKE '%{$filter->sanitize($filterData->name, "string")}%'" : "");
    $filDate = "";
    if (isset($filterData->dateinitial) && isset($filterData->dateend)) {
      if ($filterData->dateinitial != "" && $filterData->dateend != "") {
        $initial = strtotime($filterData->dateinitial);
        $end = strtotime($filterData->dateend);
        if ($initial > $end) {
          throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la final. ');
        }
        $filDate .= " AND created BETWEEN '{$initial}' AND '{$end}'";
      }
    }
    $idAccount = $this->user->Usertype->Subaccount->Account->idAccount;

    $conditions = array(
        "conditions" => "deleted = ?0 AND idAccount = ?1 {$filName} {$filDate}",
        "bind" => array(0, $idAccount),
        "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
        "offset" => $page,
        "order" => "created DESC"
    );
    $landingPageCategory = \LandingPageCategory::find($conditions);
    $conditions["columns"] = "idSurveyCategory";
    unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
    $total = \LandingPageCategory::count($conditions);


    return array(
        "total" => $total,
        "total_pages" => (ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT))),
        "items" => $landingPageCategory->toArray()
    );
  }

  /**
   * @descripcion consulta solo una categoria de landing page siempre y cuando no este eliminada
   * @param integer $id
   * @return Array 
   * @throws \InvalidArgumentException
   */
  public function getOne($id) {
    $landingcagtegory = \LandingPageCategory::findFirst(array("conditions" => "deleted = ?0  AND idAccount = ?1 and idLandingPageCategory =?2 ", "bind" => array(0, $this->user->Usertype->Subaccount->Account->idAccount, $id)));
    if (!$landingcagtegory) {
      throw new \InvalidArgumentException("Por favor validar la categoria que desea editar.");
    }
    return array(
        "name" => $landingcagtegory->name,
        "description" => $landingcagtegory->description,
        "status" => ($landingcagtegory->status == 1) ? true : false,
    );
  }

}
