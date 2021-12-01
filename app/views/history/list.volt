
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Historial de actividades del sistema 
    </div>            
    <hr class="basic-line" />
    <p>
      Cada vez que se realiza una actividad en el sistema esta se registra en un historial. A continuación se muestran las actividades realizadas.
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-lg-5 wrap">
    <div class="input-group">
      <input class="form-control"  id="email" ng-change='search()' placeholder="Buscar operación, descripción o usuario" ng-model="filters.string" autofocus="true" />
      <span class=" input-group-addon" id="basic-addon1" ng-click="getAll()">
        <i class="fa fa-search"></i>
      </span>
    </div>
  </div>

  <div class="col-xs-12 col-sm-12 col-lg-7 text-right row">
    <div style="display: inline-block; padding-top: 10px;" class="col-xs-12 col-sm-12 col-md-12 col-lg-2 text-right">
      Enviados entre
    </div>
    <div style="display: inline-block" class="col-xs-6 col-sm-6 col-md-6 col-lg-5 text-right">
      <span class="input-append date input-group" id='datetimepicker' >
        <input ng-model="filters.inidate" class="undeline-input" id="inidate">
        <span class="add-on input-group-addon">
          <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
        </span>
      </span>
    </div>
    <div style="display: inline-block" class="col-xs-6 col-sm-6 col-md-6 col-lg-5 text-right">
      <span class="input-append date input-group" id='datetimepicker2' >
        <input ng-model="filters.findate" class="undeline-input" id="findate">
        <span class="add-on input-group-addon">
          <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
        </span>
      </span>
    </div>
  </div>


</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 wrap" ng-show="userRole == root">
    Masteraccount: 
    <span class="input-default">
      <ui-select ng-model="filters.masteraccount" 
                 ui-select-required theme="select2" sortable="false"
                 close-on-select="true" ng-change="getAllieds()">
        <ui-select-match
          placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
        <ui-select-choices repeat="item in (masteraccounts | filter: $select.search) track by item.idMasteraccount">
        <span ng-bind="item.name"></span>
        </ui-select-choices>
      </ui-select>
    </span>

  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 wrap" ng-show="userRole == root || userRole == master">
    Allied
    <span class="input-default">
      <ui-select ng-model="filters.allied" ng-disabled="disabledAllied"
                 ui-select-required theme="select2" sortable="false"
                 close-on-select="true" ng-change="getAccounts()">
        <ui-select-match
          placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
        <ui-select-choices repeat="item in (allieds | filter: $select.search) track by item.idAllied">
        <span ng-bind="item.name"></span>
        </ui-select-choices>
      </ui-select>
    </span>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 wrap" ng-show="userRole == root || userRole == master || userRole == allied">
    Account
    
    <span class="input-default">
      <ui-select ng-model="filters.account" ng-disabled="disabledAccount"
                 ui-select-required theme="select2" sortable="false"
                 close-on-select="true" ng-change="getSubaccounts()">
        <ui-select-match
          placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
        <ui-select-choices repeat="item in (accounts | filter: $select.search) track by item.idAccount">
        <span ng-bind="item.name"></span>
        </ui-select-choices>
      </ui-select>
    </span>
    
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 wrap" ng-show="userRole == root || userRole == master || userRole == allied || userRole == account">
    Subaccount
    <span class="input-default">
      <ui-select ng-model="filters.subaccount" ng-disabled="disabledSubaccount"
                 ui-select-required theme="select2" sortable="false"
                 close-on-select="true" >
        <ui-select-match
          placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
        <ui-select-choices repeat="item in (subaccounts | filter: $select.search) track by item.idSubaccount">
        <span ng-bind="item.name"></span>
        </ui-select-choices>
      </ui-select>
    </span>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 wrap float-right" style="padding-top: 25px;">
    <button type="button" class="shining btn info-inverted float-right" data-toggle="tooltip" data-placement="top" title="Buscar" data-ng-click="getAll()" style="margin-left: 5px;"><i class="fa fa-search"></i></button>
    <button type="button" class="shining btn warning-inverted float-right" data-toggle="tooltip" data-placement="top" title="Limpiar los filtros" data-ng-click="cleanFilters()"><i class="fa fa-eraser"></i></button>
  </div>

</div>

<div  ng-if="history.items.length>0">

  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{history.total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (history.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (history.total_pages) || history.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (history.total_pages)  || history.total_pages == 0  ? true  : false || page == (history.total_pages)  || history.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (history.total_pages)  || history.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (history.total_pages)  || history.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
  <div class="row" >
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <table class="table table-bordered sticky-enabled" >
        <thead class="theader">
          <tr>
            <th>Operación</th>
            <th>Descripción</th>
            <th width="45%">Usuario</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="item in history.items">
            <td>
              {{'{{item.operation}}'}}
            </td>
            <td>
              {{'{{item.description}}'}}
            </td>
            <td>
              {{'{{item.userDescription}}'}}
            </td>
            <td>
              {{'{{item.created}}'}}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{history.total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (history.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (history.total_pages) || history.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (history.total_pages)  || history.total_pages == 0  ? true  : false || page == (history.total_pages)  || history.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (history.total_pages)  || history.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (history.total_pages)  || history.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
</div>
<div ng-if="history.items.length<=0">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay actividades en el sistema que coincidan con los filtros
          </h2>
        </div>
      </div>
    </div>
  </div>
</div>




<script>
  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }


</script>

</div>
</div>

