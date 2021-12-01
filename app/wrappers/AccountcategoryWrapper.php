<?php

namespace Sigmamovil\Wrapper;

class AccountcategoryWrapper extends \BaseWrapper {

    private $form;

    public function __construct() {
        $this->form = new \AccountcategoryForm();
        parent::__construct();
    }

    public function listAccountCategory($page, $filter) {
        if ($page != 0) {
            $page = $page + 1;
        }
        if ($page > 1) {
            $page = ($page - 1) * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT;
        }

        $sanitize = new \Phalcon\Filter;

        $condition = "";

        if (isset($this->user->Usertype->idMasteraccount)) {
            $condition .= " AND idMasteraccount = {$this->user->Usertype->idMasteraccount}";
        } else {
            $condition .= " AND idMasteraccount is null";
        }
        if (isset($this->user->Usertype->idAllied)) {
            $condition .= " AND idAllied = {$this->user->Usertype->idAllied}";
        } else {
            $condition .= " AND idAllied is null";
        }

        $name = (isset($filter->name) ? " AND name like '%{$sanitize->sanitize($filter->name, "string")}%'" : '');

        $conditions = array(
            "conditions" => "deleted = ?0 {$condition} {$name}",
            "bind" => array(0),
            "limit" => \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT,
            "offset" => $page,
            "order" => "created DESC"
        );

        $autocampcateg = \AccountCategory::find($conditions);
        unset($conditions["limit"], $conditions["offset"], $conditions["order"]);
        $total = \AccountCategory::count($conditions);

        $data = array();
        if (count($autocampcateg) > 0) {
            foreach ($autocampcateg as $key => $value) {
                $data[$key] = array(
                    "idAccountCategory" => $value->idAccountCategory,
                    "idMasteraccount" => $value->idMasteraccount,
                    "idAllied" => $value->idAllied,
                    "name" => $value->name,
                    "description" => $value->description,
                    "expirationDate" => $value->expirationDate,
                    "status" => $value->status,
                    "updated" => date("Y-m-d", $value->updated),
                    "created" => date("Y-m-d", $value->created),
                    "createdBy" => $value->createdBy,
                    "updatedBy" => $value->updatedBy
                );
            }
        }

        return array(
            "total" => $total,
            "total_pages" => ceil($total / (\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)),
            "items" => $data
        );
    }

    public function saveAccountCategory($data) {
        $accountCategory = new \AccountCategory();
        $accountCategory->idMasteraccount = ((isset($this->user->Usertype->idMasteraccount)) ? $this->user->Usertype->idMasteraccount : Null);
        $accountCategory->idAllied = ((isset($this->user->Usertype->idAllied)) ? $this->user->Usertype->idAllied : Null);
        $accountCategory->deleted = 0;
        $accountCategory->status = $data['status'];
        $accountCategory->expirationDate = $data['expirationDate'];
        $accountCategory->description = (isset($data['description']) && $data['description'] != "") ? $data['description'] : "Sin descripción";

        $this->form->bind($data, $accountCategory);

        $accountCategory->name = substr($accountCategory->name, 0, 70);
        $accountCategory->description = substr($accountCategory->description, 0, 200);
        
        $conditions = "";

        if (isset($this->user->Usertype->idMasteraccount)) {
            $conditions .= " AND idMasteraccount = {$this->user->Usertype->idMasteraccount}";
        } else {
            $conditions .= " AND idMasteraccount is null";
        }
        if (isset($this->user->Usertype->idAllied)) {
            $conditions .= " AND idAllied = {$this->user->Usertype->idAllied}";
        } else {
            $conditions .= " AND idAllied is null";
        }

        $acc = \AccountCategory::findFirst(array(
                    "conditions" => "deleted = 0 AND name = ?0" . $conditions,
                    "bind" => array(ucwords($accountCategory->name))
        ));

        if ($acc) {
            throw new \InvalidArgumentException("Ya existe un registro de categoría con el nombre ingresado, por favor valida la información");
        }

        if (!$this->form->isValid() || !$accountCategory->save()) {
            foreach ($this->form->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
            foreach ($accountCategory->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return true;
    }

    public function getAccountCategory($id) {
        if (!isset($id)) {
            throw new \InvalidArgumentException("El id de la categoría es inválido, por favor valida la información");
        }

        $accountCategory = \AccountCategory::findFirst(array(
                    "conditions" => "idAccountCategory = ?0",
                    "bind" => array($id)
        ));

        if (!$accountCategory) {
            throw new \InvalidArgumentException("No se ha encontrado el registro de la categoría, por favor valida la información");
        }

        $data = array(
            "idAccountCategory" => $accountCategory->idAccountCategory,
            "idMasteraccount" => $accountCategory->idMasteraccount,
            "idAllied" => $accountCategory->idAllied,
            "name" => $accountCategory->name,
            "description" => $accountCategory->description,
            "status" => $accountCategory->status,
            "expirationDate" => $accountCategory->expirationDate
        );

        return $data;
    }

    public function editAccountCategory($data) {
        if (!isset($data["idAccountCategory"])) {
            throw new \InvalidArgumentException("El id de la categoría es inválido, por favor valida la información");
        }

        $accountCategory = \AccountCategory::findFirst(array(
                    "conditions" => "idAccountCategory = ?0",
                    "bind" => array($data["idAccountCategory"])
        ));

        if (!$accountCategory) {
            throw new \InvalidArgumentException("No se ha encontrado el registro de la categoría, por favor valida la información");
        }
        $accountCategory->status = $data['status'];
        $accountCategory->expirationDate = $data['expirationDate'];
        $accountCategory->description = (isset($data['description']) && $data['description'] != "") ? $data['description'] : "Sin descripción";
        $this->form->bind($data, $accountCategory);

        $conditions = "";
        if (isset($this->user->Usertype->idMasteraccount)) {
            $conditions .= " AND idMasteraccount = {$this->user->Usertype->idMasteraccount}";
        } else {
            $conditions .= " AND idMasteraccount is null";
        }
        if (isset($this->user->Usertype->idAllied)) {
            $conditions .= " AND idAllied = {$this->user->Usertype->idAllied}";
        } else {
            $conditions .= " AND idAllied is null";
        }
        $acc = \AccountCategory::findFirst(array(
                    "conditions" => "deleted = 0 AND name = ?0 AND idAccountCategory != ?1" . $conditions,
                    "bind" => array(ucwords($accountCategory->name), $accountCategory->idAccountCategory)
        ));

        if ($acc) {
            throw new \InvalidArgumentException("Ya existe un registro de categoría con el nombre ingresado, por favor valida la información");
        }

        if (!$this->form->isValid() || !$accountCategory->update()) {
            foreach ($this->form->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
            foreach ($accountCategory->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return true;
    }

    public function deleteAccountCategory($data) {
        if (!isset($data)) {
            throw new \InvalidArgumentException("El id de la categoría es inválido, por favor valida la información");
        }

        $accountCategory = \AccountCategory::findFirst(array(
                    "conditions" => "idAccountCategory = ?0",
                    "bind" => array($data)
        ));

        if (!$accountCategory) {
            throw new \InvalidArgumentException("No se ha encontrado el registro de la categoría, por favor valida la información");
        }

        $accountCategory->deleted = 1;

        if (!$accountCategory->update()) {
            foreach ($accountCategory->getMessages() as $message) {
                throw new \InvalidArgumentException($message);
            }
        }

        return true;
    }

    public function getAccountCategories() {
        $conditions = "";

        if (isset($this->user->Usertype->idMasteraccount)) {
            $conditions .= " AND idMasteraccount = {$this->user->Usertype->idMasteraccount}";
        } else {
            $conditions .= " AND idMasteraccount is null";
        }
        if (isset($this->user->Usertype->idAllied)) {
            $conditions .= " AND idAllied = {$this->user->Usertype->idAllied}";
        } else {
            $conditions .= " AND idAllied is null";
        }

        $acc = \AccountCategory::find(array(
                    "conditions" => "deleted = 0" . $conditions
        ));

        $accountCategory = array();
        foreach ($acc as $value) {
            $val = array();
            $val['idAccountCategory'] = $value->idAccountCategory;
            $val['name'] = $value->name;
            $val['expirationDate'] = $value->expirationDate;
            $accountCategory[] = $val;
        }
        return $accountCategory;
    }

}
