<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="wizard">
      <div class="wizard-inner">
        <div class="connecting-line"></div>
        <ul class="nav nav-tabs" role="tablist" style="text-align: center;">
          <li ui-sref-active="active" class="" style="float:none;display:inline-block;zoom:1;">
            <a ui-sref="payment.paymentplan({id: idAcc})" title="Planes de pago">
              <span class="round-tab">
                <i class="fa fa-shopping-cart"></i>
              </span>
              <md-tooltip md-direction="top">
                Planes de pago
              </md-tooltip>
            </a>
          </li>

          <li ui-sref-active="active" class="" data-ng-class="{'disabled no-click' : current}" style="float:none;display:inline-block;zoom:1;">
            <a ui-sref="payment.pay" title="Pago">
              <span class="round-tab">
                <i class="fa fa-money"></i>
              </span>
              <md-tooltip md-direction="top">
                Pago
              </md-tooltip>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div ui-view></div>
