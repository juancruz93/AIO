<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{ title }}"}}      
    </div>
    <hr class="basic-line" />
  </div>
</div>
<div class="row">
  {% if(user.UserType.idAllied OR user.UserType.idAccount) %}
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 center-div">
    {% else %}
      <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4  center-div" >
      {% endif %}
      <ui-select  ng-model="search.emailUser"  theme="select2" sortable="false"
                  ng-change="searchReport()"
                  close-on-select="true"  style="max-width: 100%" class="inline-block none-margin none-padding-left"> 
        <ui-select-match
          placeholder="Seleccione un usuario">{{ "{{$select.selected.email}}" }}</ui-select-match>
        <ui-select-choices
          repeat="key.email as key in users | propsFilter: {name: $select.search}">
          <div ng-bind-html="key.email | highlight: $select.search"></div>
        </ui-select-choices>
      </ui-select>
    </div>  
    {% if(user.UserType.idAllied OR user.UserType.idAccount) %}
      <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 center-div" >
        <ui-select  ng-model="search.subaccount"  theme="select2" sortable="false"
                    ng-change="searchReport()"
                    close-on-select="true"  style="max-width: 100%" class="inline-block none-margin none-padding-left"> 
          <ui-select-match
            placeholder="Seleccione una subcuenta">{{ "{{$select.selected.name}}" }}</ui-select-match>
          <ui-select-choices
            repeat="key.idSubaccount as key in subaccount | propsFilter: {name: $select.search}">
            <div ng-bind-html="key.name | highlight: $select.search"></div>
          </ui-select-choices>
        </ui-select>
      </div>
    {% endif %}
    {% if(user.UserType.idAllied OR user.UserType.idAccount) %}
      <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 text-right none-padding center-div">
      {% else %}
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 text-right none-padding center-div">
        {% endif %}
        <div class="col-md-3 col-lg-5 col-sm-3 col-xs-4 dateFinalBox">
          {#<div class="input-group"
               format="YYYY-MM-DD">

            <input id="valuedateInitial" class="form-control"
                   moment-picker="search.valuedateInitial"
                   placeholder="Seleccionar fecha inicial"
                   ng-model="search.valuedateInitial"              
                   ng-model-options="{ updateOn: 'blur' }">
            <span class="input-group-addon">
              <i class="glyphicon glyphicon-calendar"></i>
            </span>
          </div>#}
          <div class="dropdown form-group dropdown-start-parent">
          <a class="dropdown-toggle" id="dropdownStart" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent"
             href="">
            <div class="input-group date">
              <input type="text" class="form-control" placeholder="Fecha Inicial" readonly="true" data-ng-model="search.valuedateInitial" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
          </a>
          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            <datetimepicker data-ng-model="search.valuedateInitial"
                            data-datetimepicker-config="{ dropdownSelector: '#dropdownStart', renderOn: 'end-date-changed', startView: 'month', minView: 'hour', modelType: 'YYYY-MM-DD HH:mm:ss' }"
                            data-on-set-time="functions.startDateOnSetTime()"
                            dataBeforeRender="functions.startDateBeforeRender($dates)"
                            ></datetimepicker>
          </ul>
        </div>
        </div>
        <div class="col-md-3 col-lg-5 col-sm-3 col-xs-3 dateFinalBox">
          {#<div class="input-group"         
               format="YYYY-MM-DD">

            <input id="valuedateFinal" class="form-control"
                   moment-picker="search.valuedateFinal"
                   placeholder="Seleccionar fecha final"
                   ng-model="search.valuedateFinal"             
                   ng-model-options="{ updateOn: 'blur' }">
            <span class="input-group-addon">
              <i class="glyphicon glyphicon-calendar"></i>
            </span>
          </div>#}
          <div class="dropdown form-group dropdown-end-parent">
          <a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent"
             href="">
            <div class="input-group date">
              <input type="text" class="form-control" placeholder="Fecha Inicial" readonly="true" data-ng-model="search.valuedateFinal" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
          </a>
          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            <datetimepicker data-ng-model="search.valuedateFinal"
                            data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'end-date-changed', startView: 'month', minView: 'hour', modelType: 'YYYY-MM-DD HH:mm:ss' }"
                            data-on-set-time="functions.startDateOnSetTime()"
                            dataBeforeRender="functions.startDateBeforeRender($dates)"
                            ></datetimepicker>
          </ul>
        </div>
        </div>
        <div class="">
          <button class="button  btn btn-md warning-inverted" ng-click="cleanFilters()" 
                  data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
            <i class="fa fa-eraser"></i>
          </button>
          <button class="button  btn btn-md primary-inverted" ng-click="searchDate()"
                  data-toggle="tooltip" data-placement="top" title="Buscar">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
      <div class="inline-block none-margin none-padding" >
        <a href="{{ url("/reports/index")}}" class="">
          <button class="button  btn btn-md default-inverted">
            <i class="fa fa-arrow-left"></i> Regresar al inicio
          </button>
        </a>
        <button class="button  btn btn-md info-inverted" ng-click="dowloadReport()">
          <i class="fa fa-download"></i> Descargar reporte
        </button>
      </div>
    </div>
  </div>
</div>
</div>
<div ng-show="report.items.length > 0">
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>
            {{ "{{report.total }}"}}
          </b> registros </span><span>Página <b>
          {{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (report.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (report.total_pages) || report.total_pages == 0 ? 'disabled'  : ''">
        <a  ng-click="page == (report.total_pages)  || report.total_pages == 0  ? true  : false || page == (report.total_pages)  || report.total_pages == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (report.total_pages)  || report.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (report.total_pages)  || report.total_pages == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>   
  <table class="table table-bordered table-responsive" id="resultTable">
    <thead class="theader">
      <tr style="border-left: solid 4px #474646;">
        <th>Nombre del envio</th>
        <th>Subcuenta</th>
        <th>Usuario</th>
        <th>Fecha de envío</th>
        <th>Enviados</th>
        <th>No enviados</th>
        <th>Total</th>
        <th>Cantidad para cobro</th>
        <th>Detalle</th>
      </tr>
    </thead>
    <tbody ng-repeat="key in report.items">
      <tr>
        <td>
          {{"{{ key.namesms }}"}}
        </td>
        <td>
          {{"{{ key.namesubaccount }}"}}
        </td>
        <td>
          {{"{{ key.createdBy }}"}}
        </td>
        <td>
          {{"{{ key.startdate }}"}}
        </td>
        <td>
          {{"{{ key.sent }}"}}
        </td>
        <td>
          {{"{{ key.undelivered }}"}}
        </td>
        <td>
          {{"{{ key.total }}"}}
        </td>
        <td>
          {{"{{ key.messageCount }}"}}
        </td>
        <td>
          <a href="{{url("statistic#/sms")}}/{{'{{key.idSms}}'}}" class="button btn btn-xs-round primary-inverted" data-toggle="tooltip" data-placement="top" title="Ver estadísticas">
            <span class="fa fa-bar-chart"></span>
          </a>
        </td>
      </tr>
    </tbody>
  </table>
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>
            {{ "{{report.total }}"}}
          </b> registros </span><span>Página <b>
          {{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (report.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (report.total_pages) || report.total_pages == 0 ? 'disabled'  : ''">
        <a  ng-click="page == (report.total_pages)  || report.total_pages == 0  ? true  : false || page == (report.total_pages)  || report.total_pages == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (report.total_pages)  || report.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (report.total_pages)  || report.total_pages == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
</div>
<br>
<div ng-hide="report.items.length > 0" class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2>
          No hay resultados con los criterios de búsqueda.
        </h2>    
      </div>
    </div>
  </div>
</div>
