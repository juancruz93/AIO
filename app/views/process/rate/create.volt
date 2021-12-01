
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        {{'{{title}}'}} de tarifas
      </div>
      <p>
        Aquí encontrará el listado de las tarifas con su respectivo plan y los rangos para cada tarifa.  
      </p>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
        <a href="{{ url('rate') }}" class="button shining btn btn-sm default-inverted">Regresar</a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 wrap">
      <form name="rateForm" class="form-horizontal" role="form" ng-submit="functions.validate()">
        <div class="block block-info">
          <div class="body">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-2 text-left">*Nombre del plan:</label>
                <span class="input hoshi input-default col-sm-5">
                  <input class="undeline-input" ng-model="data.name" id="name" >
                  </select>                 
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-2 text-left">*Descripcion:</label>
                <span class="input hoshi input-default  col-sm-5">
                  <input class="undeline-input" ng-model="data.description" id="description" required>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-2 text-left">*Caducidad:</label>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="col-sm-2"></div>
                <span class="input hoshi input-default col-sm-5">
                  <label>Fecha inicio:</label>
                  <span class="input-append date col-sm-12 col-md-12 input-group datetimepicker">
                    <input type="datetime" id="dtpicker" ng-model="data.dateInitial" id="dateInitial" class="undeline-input" />
                    <span class="add-on input-group-addon">
                      <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                    </span>
                  </span>
                </span>
                <div class="col-sm-2"></div>
                <span class="input hoshi input-default col-sm-5">
                  <label>Fecha Final:</label>
                  <span class="input-append date col-sm-12 col-md-12 input-group datetimepicker">
                    <input type="datetime" id="dtpicker" ng-model="data.dateEnd" id="dateEnd" class="undeline-input" />
                    <span class="add-on input-group-addon">
                      <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                    </span>
                  </span>
                </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-2 text-left">*Países:</label>
                <span class="input hoshi input-default  col-sm-5">
                  <select ng-model="data.idCountry" class="undeline-input col-sm-12 col-md-12" required="">
                    <option ng-repeat="countrys in country"
                            value="{{"{{countrys.idCountry}}"}}">
                      {{"{{countrys.name}}"}}
                    </option>
                  </select>                 
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-2 text-left">*Servicios:</label>
                <span class="input hoshi input-default  col-sm-5">
                  <select ng-model="data.idServices" ng-change="functions.changeService()" class="undeline-input col-sm-12 col-md-12" required="">
                    <option ng-repeat="service in services"
                            value="{{"{{service.idServices}}"}}">
                      {{"{{service.name}}"}}
                    </option>
                  </select>                 
                </span>
                <span class="input hoshi input-default col-sm-5" data-ng-show="viewMode">
{#                <span class="input hoshi input-default col-sm-5">#}
                  <select ng-model="data.accountingMode" class="undeline-input" required="">
                    <option ng-repeat="accountingMode in accountingModes"
                            value="{{"{{accountingMode.key}}"}}">
                      {{"{{accountingMode.name}}"}}
                    </option>
                  </select>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-2 text-left">*Online</label>
                <div class="col-sm-8">
                  <md-switch class="md-primary none-margin" ng-model="data.online" md-no-ink aria-label="Switch 1">
                  </md-switch>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-2 text-left">*Estado</label>
                <div class="col-sm-8">
                  <md-switch class="md-primary none-margin" ng-model="data.status" md-no-ink aria-label="Switch 2">
                  </md-switch>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7 wrap">
                <a ng-click="functions.removeRange()" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Eliminar">
                  <span class="glyphicon glyphicon-remove"></span>
                </a>
                <a ng-click="functions.addRange();" class="button shining btn btn-xs-round shining shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="top" title="Agregar">
                  <span class="glyphicon glyphicon-plus"></span>
                </a>
                <table class="table table-bordered">
                  <thead class="theader">
                    <tr>
                      <th>Desde</th>
                      <th>Hasta </th>
                      <th data-ng-show="viewSpace">Capacidad</th>
                      <th>Valor</th>
                    </tr>
                  </thead>
                  <tbody>

                    <tr ng-repeat="range in data.ranges">
                      <td>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <input maxlength="50" class="form-control" string-to-number ng-model="range.since" id="since" disabled="" placeholder="0" >
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <input maxlength="50" class="form-control"  ng-model="range.until" id="until">
                          </div>
                        </div>
                      </td>
                      <td data-ng-show="viewSpace">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <input maxlength="50" class="form-control" ng-model="range.space" id="space">
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <input maxlength="100" class="form-control"  ng-model="range.value{#range.value | currency#}" id="value">
                          </div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
          <div class="footer" align="right">
            <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="#/" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
          </div>
        </div>
      </form>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 wrap">
      <div class="fill-block fill-block-primary" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          <ul>
            {#                  <li>El nombre de la lista de contactos debe ser un nombre único, es decir, no pueden existir dos listas de contactos con el mismo nombre.</li>
                        <li>Recuerde que los campos con asterisco(*) son obligatorios</li>#}
          </ul>
          </p>
        </div>
      </div>
    </div>
  </div>
