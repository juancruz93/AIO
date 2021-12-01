<?php

namespace Sigmamovil\Wrapper;

class TaxWrapper extends \BaseWrapper {

    private $form;

    public function __construct() {
        $this->form = new \TaxForm();
        parent::__construct();
    }

    public function listTax($page, $name) {
        if ($page != 0) {
            $page = $page + 1;
        }
        if ($page > 1) {
            $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
        }

        $filter = new \Phalcon\Filter;

        $filterName = $filter->sanitize(((isset($name)) ? $name : ""), "string");
        $idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : '');
        $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : '');
        $cond1 = ((!empty($idMasteraccount)) ? "AND idMasteraccount = {$filter->sanitize($idMasteraccount, "int")}" : "AND idMasteraccount IS NULL");
        $cond2 = ((!empty($idAllied)) ? "AND idAllied = {$filter->sanitize($idAllied, "int")}" : "AND idAllied IS NULL");

        $conditions = array(
            "conditions" => "deleted = ?0 {$cond1} {$cond2} AND name LIKE '%{$filterName}%'",
            "bind" => array(0),
            "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
            "order" => "idTax DESC",
            "offset" => $page
        );

        $tax = \Tax::find($conditions);
        unset($conditions["limit"], $conditions["offset"],$conditions["order"]);
        $total = \Tax::count($conditions);

        $data = [];
        if (count($tax) > 0) {
            foreach ($tax as $key => $value) {
                $data[$key] = array(
                    "idTax" => $value->idTax,
                    "country" => $value->Country->name,
                    "created" => $value->created,
                    "updated" => $value->updated,
                    "deleted" => $value->deleted,
                    "status" => $value->status,
                    "name" => $value->name,
                    "type" => $value->type,
                    "amount" => $value->amount,
                    "description" => $value->description,
                    "createdBy" => $value->createdBy,
                    "updatedBy" => $value->updatedBy
                );
            }
        }

        $array = array(
            "total" => $total,
            "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
            "items" => $data
        );

        return $array;
    }

    public function listFullTax($idCountry) {
        $filter = new \Phalcon\Filter;

        $idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : '');
        $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : '');
        $cond1 = ((!empty($idMasteraccount)) ? "AND idMasteraccount = {$filter->sanitize($idMasteraccount, "int")}" : "AND idMasteraccount IS NULL");
        $cond2 = ((!empty($idAllied)) ? "AND idAllied = {$filter->sanitize($idAllied, "int")}" : "AND idAllied IS NULL");
        $tax = \Tax::find(array(
                    "columns" => "idTax, name",
                    "conditions" => "deleted = ?0 AND status = ?1 AND idCountry = ?2 {$cond1} {$cond2}",
                    "bind" => array(0, 1, $idCountry)
        ));

        $data = [];
        if (count($tax) > 0) {
            foreach ($tax as $key => $value) {
                $data[$key] = array(
                    "idTax" => $value->idTax,
                    "name" => $value->name,
                );
            }
        }

        return $data;
    }

    public function createTax($data) {
        $tax = new \Tax();
        $this->form->bind($data, $tax);
        
        $t = \Tax::findFirst($this->getConditionsForValidateNameInAllLevels($tax->name));

        if ($t) {
            throw new \InvalidArgumentException("Ya existe un impuesto registrado con el nombre ingresado, por favor valida la información");
        }
        
        $idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : NULL);
        $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);
        $tax->idMasteraccount = $idMasteraccount;
        $tax->idAllied = $idAllied;

        $tax->name = substr($tax->name, 0, 69);
        $tax->description = substr($tax->description, 0, 99);

        if (!$this->form->isValid() || !$tax->save()) {
            foreach ($this->form->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
            foreach ($tax->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return ["message" => "El impuesto se ha guardado exitosamente"];
    }

    public function getTax($id) {
        if (!isset($id)) {
            throw new \InvalidArgumentException("Dato de impuesto inválido");
        }

        $tax = \Tax::findFirst(array(
                    "conditions" => "idTax = ?0",
                    "bind" => array($id)
        ));

        if (!$tax) {
            throw new \InvalidArgumentException("El tipo de moneda que intenta editar no existe");
        }

        $data = array(
            "idTax" => (int) $tax->idTax,
            "idCountry" => $tax->idCountry,
            "created" => $tax->created,
            "updated" => $tax->updated,
            "deleted" => $tax->deleted,
            "status" => (int) $tax->status,
            "name" => $tax->name,
            "type" => $tax->type,
            "amount" => (int) $tax->amount,
            "description" => $tax->description,
            "createdBy" => $tax->createdBy,
            "updatedBy" => $tax->updatedBy
        );

        return $data;
    }

    public function editTax($data) {
        $tax = \Tax::findFirst(array(
                    "conditions" => "idTax = ?0",
                    "bind" => array($data["idTax"])
        ));

        if (!$tax) {
            throw new \InvalidArgumentException("El impuesto que intenta editar no existe");
        }

        $this->form->bind($data, $tax);
        
        $t = \Tax::findFirst($this->getConditionsForValidateNameInAllLevels($tax->name));
        
        if ($t && $t->idTax != $tax->idTax) {
            throw new \InvalidArgumentException("Ya existe una lista de precios con el nombre ingresado, por favor valida la información");
        }
        
        $tax->name = substr($tax->name, 0, 69);
        $tax->description = substr($tax->description, 0, 99);
        
        $idMasteraccount = ((isset($this->user->Usertype->Masteraccount->idMasteraccount)) ? $this->user->Usertype->Masteraccount->idMasteraccount : NULL);
        $idAllied = ((isset($this->user->Usertype->Allied->idAllied)) ? $this->user->Usertype->Allied->idAllied : NULL);
        $tax->idMasteraccount = $idMasteraccount;
        $tax->idAllied = $idAllied;

        if (!$this->form->isValid() || !$tax->update()) {
            foreach ($this->form->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
            foreach ($tax->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return ["message" => "El impuesto se ha actualizado exitosamente"];
    }

    public function deleteTax($id) {
        if (!$id) {
            throw new \InvalidArgumentException("Dato de impuesto inválido");
        }

        $tax = \Tax::findFirst(array(
                    "conditions" => "idTax = ?0",
                    "bind" => array($id)
        ));

        if (!$tax) {
            throw new \InvalidArgumentException("El impuesto que intenta eliminar no existe");
        }

        $tax->deleted = time();
        if (!$tax->update()) {
            foreach ($tax->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return ["message" => "El impuesto se ha eliminado exitosamente"];
    }

}
