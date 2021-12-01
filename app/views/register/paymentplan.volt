<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-4"></div>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 text-center">
    <ui-select data-ng-model="data.paymentplan" theme="selectize" data-ng-required="true" data-ng-change="viewPaymentPlan(data.paymentplan)" title="Seleccione un plan de pago">
      <ui-select-match placeholder="Debe seleccionar un plan de pago">{{"{{$select.selected.name}}"}}</ui-select-match>
      <ui-select-choices repeat="item.idPaymentPlan as item in listpay | filter: $select.search">
        <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
      </ui-select-choices>
    </ui-select>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-3 col-lg-"></div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3"></div>
  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-6">
    <div ui-view></div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3"></div>
</div>