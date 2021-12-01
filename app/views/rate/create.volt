<div ng-cloak>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de la tarifa.
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
      <form class="form-horizontal" ng-submit="functions.validate()">
        <div class="block block-info">
          <div class="body">
            <div class="form-group">
              <label for="name" class="col-sm-2 control-label">*Nombre</label>
              <div class="col-sm-5">
                <input class="undeline-input" ng-model="data.name" id="name" ng-disabled="isDisabled" placeholder="*Nombre" required>
              </div>
            </div>
            <div class="form-group">
              <label for="description" class="col-sm-2 control-label">*Descripcion</label>
              <div class="col-sm-5">
                <input class="undeline-input" ng-model="data.description" id="description" ng-disabled="isDisabled" placeholder="*Descripcion" required>
              </div>
            </div>
            <div class="form-group">
              <label for="dateInitial" class="col-sm-2 control-label">*Caducidad:</label>
              <div class="col-sm-10">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 wrap">
                  <div class="form-horizontal">
                    <div class="form-group">                
                      <label for="" class="col-sm-4 control-label">Fecha inicio</label>
                      <div class="col-sm-8">
                        <div id='datetimepicker1' class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-10px none-padding input-append date">
                          <span class="input-append date add-on input-group none-padding datetimepicker1">
                            <input id="dateInitial" ng-model="data.dateInitial" type="text" data-format="yyyy-MM-dd hh:mm" class="form-control"  ng-disabled="isDisabled" required>
                            <span class="add-on input-group-addon">
                              <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                            </span>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 wrap">
                  <div class="form-horizontal">
                    <div class="form-group">
                      <label for="dateEnd" class="col-sm-4 control-label">Fecha final</label>
                      <div class="col-sm-8" >
                        <div id='datetimepicker2' class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-10px none-padding input-append date">
                          <span class="input-append date add-on input-group none-padding">
                            <input id="dateEnd" ng-model="data.dateEnd" type="text" data-format="yyyy-MM-dd hh:mm" class="form-control" ng-disabled="isDisabled" required>
                            <span class="add-on input-group-addon">
                              <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                            </span>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> 
              </div>
            </div>
            
            <div class="form-group">
              <label for="country" class="col-sm-2 control-label">*Países</label>
              <div class="col-sm-5">
                <ui-select multiple ng-model="data.country" ng-required="true"  ui-select-required 
                    theme="bootstrap" title=""  sortable="false" close-on-select="true" ng-disabled="isDisabled" required="">
                  <ui-select-match placeholder="Paises">{{'{{$item.name}}'}}</ui-select-match>
                  <ui-select-choices repeat="key as key in misc.country | propsFilter: {name: $select.search} | orderBy:'name'">
                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                  </ui-select-choices>
                </ui-select>
              </div>
            </div>
            <div class="form-group">
              <label for="idServices" class="col-sm-2 control-label">*Servicios</label>
              <div class="col-sm-5">
                <select ng-model="data.idServices" ng-change="functions.changeService()" class="undeline-input col-sm-12 col-md-12" ng-disabled="isDisabled" required>
                  <option ng-repeat="service in misc.services"
                          value="{{"{{service.idServices}}"}}">
                    {{"{{service.name}}"}}
                  </option>
                </select>   
              </div>
              <div class="col-sm-5" data-ng-show="misc.viewMode">
                <select ng-model="data.accountingMode" ng-change="functions.changeMode()" class="undeline-input" ng-disabled="isDisabled" required>
                  <option ng-repeat="accountingMode in misc.accountingModes"
                          value="{{"{{accountingMode.key}}"}}">
                    {{"{{accountingMode.name}}"}}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="planType" class="col-sm-2 control-label">*Plan de Pago</label>
              <div class="col-sm-5">
                <select ng-model="data.planType" ng-change="functions.changePlan()" class="undeline-input" ng-disabled="isDisabled" required>
                  <option ng-repeat="planType in misc.planTypes"
                          value="{{"{{planType.key}}"}}">
                    {{"{{planType.name}}"}}
                  </option>
                </select> 
              </div>
            </div>
            <div class="form-group" data-ng-show="misc.viewOnline">
              <label for="online" class="col-sm-2 control-label">Online</label>
              <div class="col-sm-5">
                <md-switch class="md-primary none-margin" ng-model="data.online" md-no-ink aria-label="Switch 1" ng-disabled="isDisabled">
                </md-switch>
              </div>
            </div>
            <div class="form-group">
              <label for="status" class="col-sm-2 control-label">*Estado</label>
              <div class="col-sm-5">
                <md-switch class="md-primary none-margin" ng-model="data.status" md-no-ink aria-label="Switch 2" ng-disabled="isDisabled">
                </md-switch>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7 wrap">
                <a ng-click="functions.removeRange()" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Eliminar Rango" ng-disabled="isDisabled">
                  <span class="glyphicon glyphicon-remove"></span>
                </a>
                <a ng-click="functions.addRange();" class="button shining btn btn-xs-round shining shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="top" title="Agregar Rango" ng-disabled="isDisabled">
                  <span class="glyphicon glyphicon-plus"></span>
                </a>
                <table class="table table-bordered">
                  <thead class="theader">
                    <tr>
                      <th data-ng-show="misc.viewRange">Desde</th>
                      <th data-ng-show="misc.viewRange">Hasta </th>
                      <th data-ng-show="misc.viewSpace">Capacidad(MB)</th>
                      <th>Valor en Pesos</th>
                      <th>Visible</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr ng-repeat="range in data.ranges track by $index">
                      <td data-ng-show="misc.viewRange">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <input maxlength="50" class="form-control" string-to-number ng-model="range.since" id="since" disabled="" placeholder="0">
                          </div>
                        </div>
                      </td>
                      <td data-ng-show="misc.viewRange">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <input maxlength="50" class="form-control"  ng-model="range.until" id="until" ng-disabled="isDisabled" >
                          </div>
                        </div>
                      </td>
                      <td data-ng-show="misc.viewSpace">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <input maxlength="50" class="form-control" ng-model="range.space" id="space" ng-disabled="isDisabled">
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <input maxlength="100" class="form-control"  ng-model="range.value{#range.value | currency#}" id="value" ng-disabled="isDisabled">
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                          <div class="form-group">
                            <md-switch class="md-primary none-margin" ng-model="range.visible" id="visible" md-no-ink aria-label="Switch 3" ng-disabled="isDisabled">
                            </md-switch>
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
            <div data-ng-show="misc.viewCreate">
              <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar Tarifa">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
            </div>
            <div data-ng-show="misc.viewEdit">
              <a ng-click="functions.openModal1()" class="button shining btn btn-xs-round shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Editar Tarifa" ng-disabled="isDisabled">
                <span class="glyphicon glyphicon-ok"></span>
              </a>
              <div data-ng-show="misc.viewUpdate" >
                <a href="" ng-click="functions.openModal2()" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Habilitar Tarifa">
                  <span class="glyphicon glyphicon-pencil"></span>
                </a>
              </div>
            </div>
            <a href="#/" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar Tarifa">
              <span class="glyphicon glyphicon-remove"></span>
            </a> 
          </div>
        </div>
        <div id="somedialog" class="dialog">
          <div class="dialog__overlay"></div>
          <div class="dialog__content">
            <div class="morph-shape">
              <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
              <rect x="3" y="3" fill="none" width="556" height="276"/>
              </svg>
            </div>
            <div class="dialog-inner">
              <h2>¿Esta seguro?</h2>
              <div data-ng-show="misc.viewMessage2">
                ¿Antes de editar recuerde verificar cuantas cuentas usan esta tarifa con el nombre del plan y su respectivo Id.?
                <br>
                <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
                <a data-ng-click="functions.isDisabled()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
              </div>
              <div data-ng-show="misc.viewMessage1">
                ¿está seguro de editar esta tarifa.?
                <br>
                <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>No</a>
                <button type="submit" class="button shining btn btn-md success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar Tarifas">
                  Si
                </button>
{#                  <a href="#/" data-ng-click="functionsApi.editRate()" id="btn-ok" class="button shining btn btn-md success-inverted">Si</a>
#}            </div>
            </div>
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
            <li>
              El nombre debe tener al menos 2 y máximo 40 caracteres.
            </li>
            <li>
              La descripción debe tener al menos 2 y máximo 100 caracteres
            </li>
            <li>
              Debe seleccionar los países donde se aplicará la tarifa.
            </li>
            <li>
              Debe seleccionar una fecha inicial y un fecha final para aplicar la tarifa.
            </li>
            <li>
              Debe seleccionar un plan de pago para la tarifa.
            </li>
            <li>
              El espacio en disco del rango debe ser un número en entero el cual representa la cantidad en MegaBytes (MB).
            </li>
            <li>
              Debe seleccionar al menos un servicio el cual desplegará un formulario para hacer su respectiva configuración
            </li>
            <li>
              Debe seleccionar un estado el cual podrá se activo o inactivo, por defecto el sistema lo pondrá activo.
            </li>
            <li>
              Debe agregar al menos un rango por tarifa.
            </li>
          </ul>
          </p>
        </div>
      </div>
    </div>
  </div>
      
</div>
<script>
  $(function () {
    setTimeout(function () {
      $('[data-toggle="tooltip"]').tooltip();
    }, 1000);
  });
  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }
</script>