<div class="row" data-ng-show="loader">
  <div class="col-md-12">
    <md-progress-circular class="md-warn md-hue-3 center-block" md-diameter="150"></md-progress-circular>
  </div>
</div>

<div class="row" data-ng-hide="loader">
  <div class="col-sm-12">
    <div class="fill-block fill-block-primary">
      <div class="header">
        <div class="row">
          <div class="col-xs-12 col-sm-6 col-md-6">
            <span style="font-size: 1.7em; font-weight: bold">{{"{{data.name}}"}}</span><br>
          </div>
          <div class="col-xs-12 col-sm-6 col-md-6 text-right">
            <span style="font-size: 1.7em;">{{"{{data.finalPrice | currency: '$'}}"}}</span><br>
          </div>
        </div>

        <span style="font-size: 0.9em; font-style: italic">{{"{{data.description}}"}}</span>
      </div>
      <div class="body">
        <table class="table table-striped table-bordered">
          <tr>
            <th style="width: 30%">Espacio en disco:</th>
            <td>{{"{{data.diskSpace | number}}"}} MB</td>
          </tr>
          <tr>
            <td colspan="2">
              <table class="table table-bordered">
                <tr data-ng-repeat="item in data.services">
                  <th style="width: 30%">{{"{{item.service.name}}"}}</th>
                  <td>
                    <table class="table table-bordered">
                      <tr>
                        <th>Tipo de plan</th>
                        <td>{{"{{item.plantype.name}}"}}</td>
                      </tr>
                      <tr>
                        <th>Cantidad</th>
                        <td>{{"{{item.amount | number}}"}}</td>
                      </tr>
                      <tr>
                        <th>Modo de cobro</th>
                        <td>Por {{"{{item.accountingMode != null ? item.accountingMode == 'send' ? 'envío' : 'contacto' : 'Mensaje'}}"}}</td>
                      </tr>
                      <tr data-ng-if="item.speed != null">
                        <th>Velocidad de envío</th>
                        <td>{{"{{item.speed}}"}} Sms/Min</td>
                      </tr>
                      <tr>
                        <th>Precio unitario</th>
                        <td>{{"{{item.pricelist.price | currency : '$'}}"}}</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <div class="row">
          <div class="col-sm-12 text-right">
            <button type="button" class="button btn info-inverted" data-ng-click="updatePlanAccount()">Seleccionar plan e ir a pagar <i class="fa fa-arrow-right"></i> <i class="fa fa-usd"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>