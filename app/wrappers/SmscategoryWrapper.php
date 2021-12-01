<?php

namespace Sigmamovil\Wrapper;

class SmscategoryWrapper extends \BaseWrapper {

  public function findallcategory() {
    $idAccount = ((isset($this->user->Usertype->Account->idAccount)) ? $this->user->Usertype->Account->idAccount : ((isset($this->user->Usertype->Subaccount->idAccount)) ? $this->user->Usertype->Subaccount->idAccount : ''));

    $conditions = array(
        "conditions" => "deleted = ?0 AND idAccount = ?1",
        "bind" => array(0, $idAccount)
    );

    $smscategory = \SmsCategory::find($conditions);
    $data = array();
    if (count($smscategory) > 0) {
      foreach ($smscategory as $key => $value) {
        $data[$key] = array(
            "idSmsCategory" => $value->idSmsCategory,
            "idAccount" => $value->idAccount,
            "name" => $value->name,
            "description" => $value->description,
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy
        );
      }
    }
    return $data;
  }

  public function findSmsCategory($page, $filter) {

    if ($page != 0) {
      $page = $page + 1;
    }
    if ($page > 1) {
      $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
    }

    $where = "";
    if (isset($filter->name) && $filter->name != "") {
      $where = "AND name LIKE '%" . $filter->name . "%'";
    }

    if ((isset($filter->dateinitial) && !empty($filter->dateinitial)) && (isset($filter->dateend) && !empty($filter->dateend))) {
      if ($filter->dateinitial > $filter->dateend) {
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a final');
      }
      if ($filter->dateinitial > date('Y-m-d')) {
        throw new \InvalidArgumentException('La fecha inicial no puede ser mayor a la actual.');
      }

      $startDate = strtotime($filter->dateinitial);

      $finalDate = strtotime($filter->dateend);

      $where .= " AND created  BETWEEN '{$startDate}' AND '{$finalDate}'";
    }
    
    $conditions = array(
      "conditions" => "deleted = 0 AND idAccount = ?0 " . $where, 
      "bind" => array(0 => $this->user->Usertype->Subaccount->idAccount),
      "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
      "order" => "idSmsCategory DESC",
      "offset" => $page,  
    );

    $smsCategory = \SmsCategory::find($conditions);

    $total = \SmsCategory::count(array("conditions" => "deleted = 0 AND idAccount = ?0" . $where, "bind" => array(0 => $this->user->Usertype->Subaccount->idAccount)));

    $consult = array();
    if (count($smsCategory)) {
      foreach ($smsCategory as $key => $value) {
        $consult[$key] = array(
            "name" => $value->name,
            "idSmsCategory" => $value->idSmsCategory,
            "description" => $value->description,
            "deleted" => $value->deleted,
            "createdBy" => $value->createdBy,
            "updatedBy" => $value->updatedBy,
            "updated" => date('d/m/Y', $value->updated),
            "created" => date('d/m/Y', $value->created),
        );
      }
    }

    $arrFinish = array("total" => $total, "total_pages" => ceil($total / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT), "items" => $consult);
    return $arrFinish;
  }

  public function editSmsCategory($arrSmsCategory) {
    $smscategory = \SmsCategory::findFirst(array("conditions" => "idSmsCategory = ?0", "bind" => array((Int) $arrSmsCategory['idSmsCategory'])));
    $form = new \SmsCategoryForm();
    $form->bind($arrSmsCategory, $smscategory);
    if (!$form->isValid() || !$smscategory->update()) {
      foreach ($smscategory->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }
  
  public function deleteSmsCategory($objSmsCategory) {
    $smscategory = \SmsCategory::findFirst(array("conditions" => "idSmsCategory = ?0", "bind" => array((Int) $objSmsCategory->idsmscategory)));
    $smscategory->deleted = time();
    //$mxmc->deleted = time();

    if (!$smscategory->update()) {

//      foreach ($mxmc->getMessages() as $msg) {
//        $this->logger->log("Message: {$msg}");
//        throw new \InvalidArgumentException($msg);
//      }

      foreach ($smscategory->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
    return true;
  }

}
