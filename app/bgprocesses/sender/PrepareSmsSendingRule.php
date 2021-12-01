<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PrepareSmsSendingRule
 *
 * @author juan.pinzon
 */
require_once(__DIR__ . "/../bootstrap/index.php");
require_once(__DIR__ . "/../sender/PrepareSms.php");
require_once(__DIR__ . "/../sender/ApisSms.php");

class PrepareSmsSendingRule {

  private $sms;
  private $rulesSendingMaster;
  private $idMasterAccount;
  private $oneSms;
  private $ruleSelected = null;
  private $configRule;
  private $configRuleSelected = null;
  private $flag;

  /**
   * 
   * @param type $sms
   * Métoddo constructor que recibe como parametro el sms, hacel seteo a la variable de esta clase
   * setea el id master account y ejecuta la función setRulesSending()
   */
  public function __construct($sms) {
    $this->sms = $sms;
    $this->setIdMasterAccount($this->sms->Subaccount->Account->Allied->Masteraccount->idMasteraccount);
    $this->setRulesSending();
  }

  /**
   * Método que setea las reglas de envío que tiene una cuenta maestra
   */
  private function setRulesSending() {
    $this->rulesSendingMaster = Mxssr::find(array(
                "conditions" => "idMasteraccount = ?0",
                "bind" => array($this->getIdMasterAccount())
    ));
  }

  /**
   * Método que selecciona la regla de envío según el indicativo del SMS
   */
  public function ruleSelect() {
    foreach ($this->rulesSendingMaster as $rule) {
      if ($rule->SmsSendingRule->Country->phoneCode == $this->oneSms->indicative) {
        $this->setRuleSelected($rule->SmsSendingRule);
        break;
      }
    }
    $this->setConfigRule();
  }
  
  /**
   * Método que setea las configuraciones de la regla seleccionada
   */
  public function setConfigRule() {
    $this->configRule = Ssrxadapter::find(array(
                "conditions" => "idSmsSendingRule = ?0",
                "bind" => array($this->ruleSelected->idSmsSendingRule)
    ));
  }
  
  /**
   * 
   * @param type $configRule
   * @return \stdClass
   * Método que crea un arreglo de objetos de las configuraciones de la regla de envío seleccionada
   */
  private function prepareConfigRule($configRule) {
    $array = array();
	//\Phalcon\DI::getDefault()->get('logger')->log("eppah ".$this->ruleSelected->idSmsSendingRule);
    foreach ($configRule as $cfr) {
		
      $obj = new stdClass();
      $obj->idSsrxAdapter = $cfr->idSsrxAdapter;
      $obj->SmsSendingRule = $cfr->SmsSendingrule; //Esto para producción debe de borrar
      $obj->Adapter = $cfr->Adapter;
      $obj->byDefault = $cfr->byDefault;
      $obj->prefix = (empty($cfr->prefix) ? null : explode(",", $cfr->prefix));
      $obj->totPrefix = (isset($obj->prefix) ? strlen($obj->prefix[0]) : 0);
      $array[] = $obj;
    }
    return $array;
  }
  
  /**
   * Método que selecciona la configuración pertinente para el envío de cada SMS,
   * según el prefijo, se setea bandera para saber si es necesario usar la configuración
   * por defecto
   */
  public function configRuleSelect() {
    $configRule = $this->prepareConfigRule($this->getConfigRule());
    $oneSms = $this->getOneSms();
    foreach ($configRule as $cfr) {
      $phonePrefix = substr($oneSms->phone, 0, $cfr->totPrefix);
      if (is_array($cfr->prefix)) {
        if (in_array($phonePrefix, $cfr->prefix)) {
          $this->setFlag(false);
          $this->setConfigRuleSelected($cfr);
          break;
        }
      }
    }
  }
  
  /**
   * 
   * @param type $flag
   * Método que recibe como argumento una bandera la cual sirve
   * para decidir si se usa la configuración por defecto o no
   */
  public function configRuleDefault($flag) {
    if ($flag) {
      $configRule = $this->prepareConfigRule($this->getConfigRule());
      foreach ($configRule as $cfr) {
        if ((int) $cfr->byDefault === 1) {
          $this->setConfigRuleSelected($cfr);
          break;
        }
      }
    }
  }

  function getIdMasterAccount() {
    return $this->idMasterAccount;
  }

  function setIdMasterAccount($idMasterAccount) {
    $this->idMasterAccount = $idMasterAccount;
  }

  function setOneSms($oneSms) {
    $this->oneSms = $oneSms;
  }

  function getOneSms() {
    return $this->oneSms;
  }

  function setRuleSelected($ruleSelected) {
    $this->ruleSelected = $ruleSelected;
  }

  function setRulesSendingMaster($rulesSendingMaster) {
    $this->rulesSendingMaster = $rulesSendingMaster;
  }

  function getConfigRule() {
    return $this->configRule;
  }

  function getConfigRuleSelected() {
    return $this->configRuleSelected;
  }

  function setConfigRuleSelected($configRuleSelected) {
    $this->configRuleSelected = $configRuleSelected;
  }

  function getFlag() {
    return $this->flag;
  }

  function setFlag($flag) {
    $this->flag = $flag;
  }

}
