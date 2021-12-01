<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sigmamovil\Wrapper;

use Sigmamovil\General\Links\ParametersEncoder;

/**
 * Description of WhatsappWrapper
 *
 * @author santiago.cardona
 */
class WppcategoryWrapper extends \BaseWrapper {

    public function findWppCategory($page, $filter) {
    
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
            "order" => "idWppCategory DESC",
            "offset" => $page,  
        );

        $wppCategory = \WppCategory::find($conditions);

        $total = \WppCategory::count(array("conditions" => "deleted = 0 AND idAccount = ?0" . $where, "bind" => array(0 => $this->user->Usertype->Subaccount->idAccount)));

        $consult = array();
        if (count($wppCategory)) {
        foreach ($wppCategory as $key => $value) {
            $consult[$key] = array(
                "idWppCategory" => $value->idWppCategory,
                "name" => $value->name,
                "description" => $value->description,
                "status" => $value->status,
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

    public function deletewppcategory($idWppCategory) {
        $this->db->begin();
        $wppcategory = \WppCategory::findFirst(array(
            "conditions" => "idWppCategory = ?0", 
            "bind" => array((Int) $idWppCategory)
        ));

        if (!$wppcategory) {
            throw new \InvalidArgumentException("No se encontró la categoria de WhatsApp, por favor valida la información");
        }

        $userMail = \Phalcon\DI::getDefault()->get('user')->email;
        $wppcategory->status = 0;
        $wppcategory->deleted = time();
        $wppcategory->updatedBy = $userMail;
    
        if (!$wppcategory->save()) { 
          foreach ($wppcategory->getMessages() as $msg) {
            $this->logger->log("Message: {$msg}");
            throw new \InvalidArgumentException($msg);
          }
          $this->db->rollback();
        }
        $this->db->commit();
        return $arrFinish = array("message" => "Se eliminó la categoría ".$wppcategory->name." correctamente");
      }


}