<?php

namespace Sigmamovil\General\View;

class GlobalCountersManager {

  private $user;
  private $logger;

  public function __construct() {
    $this->user = \Phalcon\DI::getDefault()->get('user');
    $this->logger = \Phalcon\DI::getDefault()->get('logger');
  }

  public function getGlobalAccountants() {
//    var_dump($this->user->userType->idMasteraccount);
//    exit;
    $accountants = "";
    $green = "#00BF6F";
    $yellow = "#d8a900";
    $red = "#d80014";
    //Masteraccount

    if ($this->user->idRole == 3) {
      $masterConfig = \MasterConfig::findfirst([
                  'idMasteraccount = ?0',
                  'bind' => [$this->user->userType->idMasteraccount]
      ]);
      $detconfigs = \DetailConfig::find(array("conditions" => "idMasterConfig = ?0", "bind" => array($masterConfig->idMasterConfig)));

      if ($detconfigs) {
        foreach ($detconfigs as $detconfig) {
          if ($detconfig->idServices == 1) {
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' SMS consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                            <span class="glyphicon glyphicon-phone"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </div>
                        </a>
                      </li>';
          }
          if ($detconfig->idServices == 2) {
            if ($detconfig->accountingMode == 'contact') {
              $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' contactos creados, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                            <span class="glyphicon glyphicon-user"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </div>
                        </a>
                      </li>';
            } else if ($detconfig->accountingMode == 'sending') {
              $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' correos consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                            <span class="glyphicon glyphicon-envelope"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </div>
                        </a>
                      </li>';
            }
          }
          if ($detconfig->idServices == 7) {
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' SMS doble-via consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                            <span class="glyphicon glyphicon-transfer"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </div>
                        </a>
                      </li>';
          }
          if ($detconfig->idServices == 8) {
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' visualizaciones consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                            <span class="glyphicon glyphicon-list-alt"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </div>
                        </a>
                      </li>';
          }
        }
      }
    }
    //Allied
    else if ($this->user->idRole == 4) {
      $alliedConfig = \Alliedconfig::findfirst([
                  'idAllied = ?0',
                  'bind' => [$this->user->userType->idAllied]
      ]);
      $detconfigs = \DetailConfig::find(array("conditions" => "idAlliedconfig = ?0", "bind" => array($alliedConfig->idAlliedconfig)));
      if ($detconfigs) {
        foreach ($detconfigs as $detconfig) {
          if ($detconfig->idServices == 1) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' SMS consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                            <span style="color: '.$color.';">
                              <span class="glyphicon glyphicon-phone"></span> '.number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".").'
                            </span>
                          </div>
                        </a>
                      </li>';
          }
          if ($detconfig->idServices == 2) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            if ($detconfig->accountingMode == 'contact') {
              $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' correos consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                            <span style="color: '.$color.';">
                              <span class="glyphicon glyphicon-user"></span> '.number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".").'
                            </span>
                          </div>
                        </a>
                      </li>';
            } else if ($detconfig->accountingMode == 'sending') {
              $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' correos consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                            <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-envelope"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") .'
                            </span>
                          </div>
                        </a>
                      </li>';
            }
          }
          if ($detconfig->idServices == 7) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' SMS doble-via consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-transfer"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
          }
          if ($detconfig->idServices == 8) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' visualizaciones consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-list-alt"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </span>
                          <div>
                        </a>
                      </li>';
          }
        }
      }
    }
    //Account
    else if ($this->user->idRole == 5) {
      $accountConfig = \AccountConfig::findfirst([
        'idAccount = ?0',
        'bind' => [$this->user->userType->idAccount]
      ]);

      $account = \Account::findfirst([
        'idAccount = ?0',
        'bind' => [$this->user->userType->idAccount]
      ]);

      $detconfigs = \DetailConfig::find(array("conditions" => "idAccountConfig = ?0 AND status = 1", "bind" => array($accountConfig->idAccountConfig)));
      if ($detconfigs) {
        foreach ($detconfigs as $detconfig) {
          if ($detconfig->idServices == 1) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' SMS consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-phone"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
          }
          if ($detconfig->idServices == 2) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            if ($detconfig->accountingMode == 'contact') {
              $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' contactos creados, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-user"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
            } else if ($detconfig->accountingMode == 'sending') {
              $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' correos consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-envelope"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
            }
          }
          if ($detconfig->idServices == 7) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' SMS doble-via consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-transfer"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
          }
          if ($detconfig->idServices == 8) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' visualizaciones consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-list-alt"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
          }
          /*if ($detconfig->idServices == 12) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' mensaje whatsapp consumidos, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="fa fa-whatsapp" style="font-size:18px;"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
          }
          if ($detconfig->idServices == 13) {
            $total = intval($detconfig->totalAmount);
            $consumed = intval($detconfig->totalAmount - $detconfig->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . ' contactos whatsapp creados, tiene disponible ' . number_format($detconfig->amount, 0, ",", ".") . '">
                      <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                        <div id="">
                        <span style="color: '.$color.';">
                          <span class="fa fa-address-card" style="font-size:18px;"></span> ' . number_format($detconfig->totalAmount - $detconfig->amount, 0, ",", ".") . '/' . number_format($detconfig->totalAmount, 0, ",", ".") . '
                        </span>
                        </div>
                      </a>
                    </li>';
          }*/
        }
      }
      if($account->registerType == 'online'){
        $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="Recargar cuenta.">
            <a href="/account/show/'.$account->idAccount.'" class="default-cursor unlink per-headerTextColor"><span class="glyphicon glyphicon-plus-sign"></span></a></li>';
      }
    }
    //Subaccount
    else if ($this->user->idRole == 6) {
      $saxss = \Saxs::find(array("conditions" => "idSubaccount = ?0 AND status = 1", "bind" => array($this->user->userType->idSubaccount)));
      $subaccount = \Subaccount::findFirst(array("conditions" => "idSubaccount = ?0 ", "bind" => array($this->user->userType->idSubaccount)));
      $account = \Account::findFirst(array("conditions" => "idAccount = ?0 ", "bind" => array($subaccount->idAccount)));
      if ($saxss) {
        foreach ($saxss as $saxs) {
          if ($saxs->idServices == 1) {
            $total = intval($saxs->totalAmount);
            $consumed = intval($saxs->totalAmount - $saxs->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . ' SMS consumidos, tiene disponible ' . number_format($saxs->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor" >
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-phone"></span> ' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . '/' . number_format($saxs->totalAmount, 0, ",", ".") . '
                            </span>
                          </div>
                        </a>
                      </li>';
          }
          if ($saxs->idServices == 2) {
            if ($saxs->accountingMode == 'contact') {
              $amount = 0;
              $totalAmount = 0;
              foreach ($saxs->Subaccount->Account->AccountConfig->DetailConfig as $value) {
                if ($value->idServices == 2) {
                  $amount = $value->amount;
                  $totalAmount = $value->totalAmount;
                }
              }
              $total = intval($totalAmount);
              $consumed = intval($totalAmount - $amount);
              $p1 = $consumed*100;
              if($total==0){$total=1;}
              $availablePercent = 100-($p1/$total);
              if($availablePercent < 25 ){
                $color = $red;
              }elseif($availablePercent >= 25 && $availablePercent <= 50){
                $color = $yellow;
              }elseif($availablePercent > 50){
                $color = $green;
              }
              $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($totalAmount - $amount, 0, ",", ".") . ' contactos creados, tiene disponible ' . number_format($amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-user"></span> ' . number_format($totalAmount - $amount, 0, ",", ".") . '/' . number_format($totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
            } else if ($saxs->accountingMode == 'sending') {
              $total = intval($saxs->totalAmount);
              $consumed = intval($saxs->totalAmount - $saxs->amount);
              $p1 = $consumed*100;
              if($total==0){$total=1;}
              $availablePercent = 100-($p1/$total);
              if($availablePercent < 25 ){
                $color = $red;
              }elseif($availablePercent >= 25 && $availablePercent <= 50){
                $color = $yellow;
              }elseif($availablePercent > 50){
                $color = $green;
              }
              $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . ' correos consumidos, tiene disponible ' . number_format($saxs->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-envelope"></span> ' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . '/' . number_format($saxs->totalAmount, 0, ",", ".") . '
                            </span>
                          </div>
                        </a>
                      </li>';
            }
          }
          if ($saxs->idServices == 7) {
            $total = intval($saxs->totalAmount);
            $consumed = intval($saxs->totalAmount - $saxs->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . ' SMS doble-via consumidos, tiene disponible ' . number_format($saxs->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor" >
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-transfer"></span> ' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . '/' . number_format($saxs->totalAmount, 0, ",", ".") . '
                          </span>
                          </div>
                        </a>
                      </li>';
          }
          if ($saxs->idServices == 8) {
            $total = intval($saxs->totalAmount);
            $consumed = intval($saxs->totalAmount - $saxs->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . ' visualizaciones consumidos, tiene disponible ' . number_format($saxs->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor" >
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="glyphicon glyphicon-list-alt"></span> ' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . '/' . number_format($saxs->totalAmount, 0, ",", ".") . '
                            </span>
                          </div>
                        </a>
                      </li>';
          }
          /*if ($saxs->idServices == 12) {
            $total = intval($saxs->totalAmount);
            $consumed = intval($saxs->totalAmount - $saxs->amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . ' mensajes whatsapp consumidos, tiene disponible ' . number_format($saxs->amount, 0, ",", ".") . '">
                        <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor" >
                          <div id="">
                          <span style="color: '.$color.';">
                            <span class="fa fa-whatsapp" style="font-size:18px;"></span> ' . number_format($saxs->totalAmount - $saxs->amount, 0, ",", ".") . '/' . number_format($saxs->totalAmount, 0, ",", ".") . '
                            </span>
                          </div>
                        </a>
                      </li>';
          }
          if ($saxs->idServices == 13) {
            $amount = 0;
            $totalAmount = 0;
            foreach ($saxs->Subaccount->Account->AccountConfig->DetailConfig as $value) {
              if ($value->idServices == 13) {
                $amount = $value->amount;
                $totalAmount = $value->totalAmount;
              }
            }
            $total = intval($totalAmount);
            $consumed = intval($totalAmount - $amount);
            $p1 = $consumed*100;
            if($total==0){$total=1;}
            $availablePercent = 100-($p1/$total);
            if($availablePercent < 25 ){
              $color = $red;
            }elseif($availablePercent >= 25 && $availablePercent <= 50){
              $color = $yellow;
            }elseif($availablePercent > 50){
              $color = $green;
            }
            $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="' . number_format($totalAmount - $amount, 0, ",", ".") . ' contactos whatsapp creados, tiene disponible ' . number_format($amount, 0, ",", ".") . '">
                      <a href="javascript: void(0);" class="default-cursor unlink per-headerTextColor">
                        <div id="">
                        <span style="color: '.$color.';">
                          <span class="fa fa-address-card" style="font-size:18px;"></span> ' . number_format($totalAmount - $amount, 0, ",", ".") . '/' . number_format($totalAmount, 0, ",", ".") . '
                        </span>
                        </div>
                      </a>
                    </li>';
          }*/
        }
      }
      if($account->registerType == 'online'){
        $accountants .= '<li data-toggle="tooltip_default" data-placement="bottom" title="Recargar subcuenta.">
            <a href="/account/show/'.$subaccount->idAccount.'" class="default-cursor unlink per-headerTextColor"><span class="glyphicon glyphicon-plus-sign"></span></a></li>';
      }
    }


    return $accountants;
  }

}
