<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Listado de Logs de Actividades
    </div>            
    <hr class="basic-line">
    <p>
      Aquí encontrará el listado de logs de actividades que se han realizado en esta cuenta
    </p>            
  </div>
</div>

<div class="row">
  <div class="form-inline">
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">
      <div class="form-group">
        <div class="input-group">
          <input type="text" class="undeline-input form-control" id="email" placeholder="Buscar por email" autofocus="true" data-ng-model="data.email" data-ng-change="searchForName()">
          <div class="input-group-addon"><i class="fa fa-search"></i></div>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <div class="form-group">
        <select class="chosen form-control" style="width: 170px" data-placeholder="Filtro por servicio" data-ng-model="data.idServices" data-ng-change="searchForService()">
          <option value=""></option>
          <option value="0">Todas los servicios</option>
          <option ng-repeat="x in listservices" value="{{"{{x.idServices}}"}}">{{"{{x.name}}"}}</option>
        </select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
      <div class="form-group">
        <input type="text" class="form-control datepicker" placeholder="Fecha inicial" data-ng-model="data.startDate" data-ng-change="enableDate2()">
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
      <div class="form-group">
        <input type="text" class="form-control datepicker" placeholder="Fecha fin" data-ng-model="data.endDate" data-ng-disabled="endDate" data-ng-change="searchForDate()">
      </div>
    </div>
    <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
      <div class="form-group">
        <button type="button" class="shining btn info-inverted" data-toggle="tooltip" data-placement="top" title="Refrescar vista" data-ng-click="refresh()"><i class="fa fa-refresh"></i></button>
      </div>
    </div>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="list.items.length > 0">
  <ul class="pagination">
    <li ng-class="page == 1 ? 'disabled'  : ''">
      <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="page == 1 ? 'disabled'  : ''">
      <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ page }}"}}
        </b> de <b>
          {{ "{{ (list.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>

<div class="row" ng-show="list.items.length > 0">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap">
    <div class="table-responsive">
      <table class="table table-bordered sticky-enabled">
        <thead class="theader">
          <tr>
            <th>Usuario</th>
            <th>Servicio</th>
            <th>Cantidad</th>
            <th>Fecha</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody>
          <tr data-ng-repeat="i in list.items" data-ng-class="i.status == 0 ? 'danger':''">
            <td>{{"{{i.user}}"}}</td>
            <td>{{"{{i.service}}"}}</td>
            <td>{{"{{i.amount}}"}}</td>
            <td>{{"{{i.dateTime}}"}}</td>
            <td style="width: 40%">{{"{{i.description}}"}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div ng-show="list.items.length == 0">
  <div class="row">
    <div class="Fcol-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay registros de logs de actividades de la cuenta que coincidan con los filtros.
          </h2>    
          </h2>    
        </div>
      </div>
    </div>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="list.items.length > 0">
  <ul class="pagination">
    <li ng-class="page == 1 ? 'disabled'  : ''">
      <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="page == 1 ? 'disabled'  : ''">
      <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ page }}"}}
        </b> de <b>
          {{ "{{ (list.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>
<script>
  $(".chosen").select2();
  $('.datepicker').datepicker({
    format: "yyyy-mm-dd",
    language: "es"
  });
  $('[data-toggle="tooltip"]').tooltip();
  (function () {
    
  });
</script>