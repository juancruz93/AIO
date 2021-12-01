<?php

/**
 * Description of FieldsValidations
 *
 * @author juan.pinzon
 */
class FieldsValidations {

  private $customFields;
  private $filtersSegment;

  public function __construct($filtersSegment) {
    $this->filtersSegment = $filtersSegment;
  }

  public function getFiltersSegment() {
    return $this->filtersSegment;
  }

  public function getCustomFields() {
    return $this->customFields;
  }

  public function setCustomFields($customFields) {
    $this->customFields = $customFields;
  }

  public function conditions($contact, $field, $filterValue, $filter) {
    $valid = true;
    $field = strtolower($field);
    $filterValue = strtolower($filterValue);
    if (!isset($contact->$field)) {
      $cf = $this->getCustomFields();
      if (isset($cf)) {
        if (is_numeric($field)) {
          $customfield = Customfield::findFirst(array(
                      "columns" => "name",
                      "conditions" => "idCustomfield = ?0",
                      "bind" => array($field)
          ));
          $field = strtolower($customfield->name);
        }
        $contact = $this->assignCustomField($contact, $field);
        if (!$contact) {
          return false;
        }
      } else {
        return false;
      }
    }

    if ($filter == $this->getFiltersSegment()->equal_to) {
      if ($contact->$field !== $filterValue) {
        $valid = false;
      }
    } else if ($filter == $this->getFiltersSegment()->in_contains) {
      if (strstr($contact->$field, $filterValue) == FALSE) {
        $valid = false;
      }
    } else if ($filter == $this->getFiltersSegment()->it_does_not_have) {
      if (strstr($contact->$field, $filterValue) != FALSE) {
        $valid = false;
      }
    } else if ($filter == $this->getFiltersSegment()->starts_with) {
      if (preg_match("/^{$filterValue}/i", trim($contact->$field)) == 0) {
        $valid = false;
      }
    } else if ($filter == $this->getFiltersSegment()->ends_in) {
      if (preg_match("/{$filterValue}$/i", trim($contact->$field)) == 0) {
        $valid = false;
      }
    } else if ($filter == $this->getFiltersSegment()->grater_than) {
      if ($contact->$field < $filterValue && is_numeric($contact->$field)) {
        $valid = false;
      }
    } else if ($filter == $this->getFiltersSegment()->low_to) {
      if ($contact->$field > $filterValue && is_numeric($contact->$field)) {
        $valid = false;
      }
    }

    return $valid;
  }

  public function validateContact($filters, $segmentCondition, $contact) {
    $flag = false;
    $conditions = (($segmentCondition == "Todas las condiciones") ? "all" : "any");
    if ($conditions === "all") {
      foreach ($filters as $filter) {
        $flag = $this->conditions($contact, $filter["idCustomfield"], $filter["value"], $filter["conditions"]);
        if (!$flag) {
          break;
        }
      }
    } else {
      foreach ($filters as $filter) {
        $flag = $this->conditions($contact, $filter["idCustomfield"], $filter["value"], $filter["conditions"]);
        if ($flag) {
          break;
        }
      }
    }
    unset($filters, $segmentCondition, $contact);
    return $flag;
  }

  private function assignCustomField($contact, $field) {
    foreach ($this->getCustomFields() as $cf) {
      $cfName = strtolower($cf["name"]);
      if ($cfName === $field) {
        $contact->$cfName = strtolower($cf["value"]);
        return $contact;
      }
    }
    return false;
  }

}
