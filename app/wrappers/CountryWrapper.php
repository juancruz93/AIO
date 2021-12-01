<?php

namespace Sigmamovil\Wrapper;

class CountryWrapper extends \BaseWrapper {

  private $countries;
  private $country;

  /**
   * @param \Language $language
   */
  public function setCountry(\Country $country) {
    $this->country = $country;
  }

  public function findCountry($idCountry) {
    $this->data = \Country::findFirst(["conditions" => "idCountry = ?0", "bind" => [0 => $idCountry]]);
    $this->modelCountry();
  }

  public function findCountries($page) {

    (($page > 0) ? $page = ($page * \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT) : "");


    $this->data = $this->modelsManager->createBuilder()
            ->from('Country')
            ->limit(\Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT)
            ->offset($page)
            ->getQuery()
            ->execute();
    $this->totals = $this->modelsManager->createBuilder()
            ->from('Country')
            ->getQuery()
            ->execute();
    $this->modelData();
  }

  public function modelData() {
    $this->countries = array("total" => count($this->totals), "total_pages" => ceil(count($this->totals) / \Sigmamovil\General\Misc\PaginationDecorator::DEFAULT_LIMIT));
    $arr = array();
    foreach ($this->data as $data) {
      $country = new \stdClass();
      $country->idCountry = $data->idCountry;
      $country->idCurrency = $data->idCurrency;
      $country->name = $data->name;
      $country->minDigits = (int) $data->minDigits;
      $country->maxDigits = (int) $data->maxDigits;

      $arr[] = $country;
    }
    $this->countries['items'] = $arr;
  }

  public function modelCountry() {
    $data = $this->data;

    $country = array();
    $country['idCountry'] = $data->idCountry;
    $country['idCurrency'] = $data->idCurrency;
    $country['name'] = $data->name;
    $country['minDigits'] = $data->minDigits;
    $country['maxDigits'] = $data->maxDigits;
    $this->country = $country;
  }

  public function editCountry($data) {
    $country = \Country::findFirst(["conditions" => "idCountry = ?0", "bind" => [0 => $data->idCountry]]);

    if (!$country) {
      throw new \InvalidArgumentException("El país no existe");
    }
    if ($data->name == "") {
      throw new \InvalidArgumentException("El campo nombre es de caracter obligatorio");
    }
    if (!is_numeric($data->minDigits) and $data->minDigits != "") {
      throw new \InvalidArgumentException("El campo Dígitos mínimos debe ser un valor numérico");
    }
    if (!is_numeric($data->maxDigits) and $data->maxDigits != "") {
      throw new \InvalidArgumentException("El campo Dígitos máximos debe ser un valor numérico");
    }



    $country->name = $data->name;
    $country->minDigits = $data->minDigits;
    if ($data->minDigits == "") {
      $country->minDigits = null;
    }
    $country->maxDigits = $data->maxDigits;
    if ($data->maxDigits == "") {
      $country->maxDigits = null;
    }

    if (!$country->save()) {
      foreach ($country->getMessages() as $msg) {
        $this->logger->log("Message: {$msg}");
        throw new \InvalidArgumentException($msg);
      }
    }
  }

  function getCountries() {
    return $this->countries;
  }

  function getCountry() {

    return $this->country;
  }

  public function getAllIndicatives() {
    $indicatives = \Country::find(array(
                "conditions" => "phoneCode IS NOT NULL",
                "order" => "name ASC"
    ));

    $data = [];
    if (count($indicatives) > 0) {
      foreach ($indicatives as $key => $value) {
        $data[$key] = array(
            "idCountry" => $value->idCountry,
            "name" => $value->name,
            "phoneCode" => $value->phoneCode,
            "minDigits" => $value->minDigits,
            "maxDigits" => $value->maxDigits
        );
      }
    }
    
    return $data;
  }

}
