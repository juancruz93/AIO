<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{ titleInfosmsbydestinataries }}"}}  
    </div>
    <hr class="basic-line" />
    <p>
      En esta lista podrá visualizar los envíos de SMS detallado por cada destinatario.
    </p>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-align-right ">
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" >
      <div class="input-group" style="margin-left: 8px">
          <input class="form-control" ng-keydown="$event.keyCode === 13 && misc.searchData()" id="name" placeholder="Buscar Nombre Campaña SMS" ng-model="misc.filterNameCampaign" />
        <span class=" input-group-addon" id="basic-addon1" >
          <i class="fa fa-search"></i>
        </span>
      </div>
    </div>
    <div class="col-xs-3 col-sm-3 col-lg-2">
      <div class="input-group">
        <input class="form-control" ng-keydown="$event.keyCode === 13 && misc.searchData()"  id="name" placeholder="Buscar Celular " ng-model="misc.filterPhoneNumber" />
        <span class=" input-group-addon" id="basic-addon1" >
          <i class="fa fa-search"></i>
        </span>
      </div>
    </div>

    <div  class="col-xs-3 col-sm-2 col-lg-3 text-right">
      <div class="input-group"
           moment-picker="filter.dateInitial"
           format="YYYY-MM-DD">
        <input  class="form-control"
                placeholder="Seleccionar fecha inicial"
                ng-model="filter.dateInitial"
                ng-model-options="{ updateOn: 'blur' }">
        <span class="input-group-addon">
          <i class="glyphicon glyphicon-calendar"></i>
        </span>
      </div>
    </div>
    <div class="col-xs-2 col-sm-2 col-lg-3 text-right">
      <div class="input-group"
           moment-picker="filter.dateEnd"
           format="YYYY-MM-DD">

        <input class="form-control"
               placeholder="Seleccionar fecha final"
               ng-model="filter.dateEnd"
               ng-model-options="{ updateOn: 'blur' }">
        <span class="input-group-addon">
          <i class="glyphicon glyphicon-calendar"></i>
        </span>
      </div>
    </div>
    <div class="col-xs-3 col-sm-3 col-md-1 col-lg-1 text-align-center" style="padding: 0px;">
      <button class="button  btn btn-md primary-inverted" ng-click="misc.searchData()"
              data-toggle="tooltip" data-placement="top" title="Buscar"> 
        <i class="fa fa-search"></i>
      </button>
      <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md warning-inverted" ng-click="cleanFilters()"
              data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
        <i class="fa fa-eraser"></i>
      </button>
    </div>
  </div>
</div>
<br><br>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
  <div class="inline-block none-margin none-padding" >
    <a href="{{ url("/reports/index")}}" class="">
      <button class="button  btn btn-md default-inverted">
        <i class="fa fa-arrow-left"></i> Regresar
      </button>
    </a>
    <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md info-inverted" ng-click="downloadReportSmsByDestinataries()">
      <i class="fa fa-download"></i> Descargar reporte
    </button>
  </div>
</div>
<br><br><br>
<div ng-class="{'hidden' : misc.progressbar}" style="margin-left: 20px">
  <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
</div>
<br>
<div style="margin-left: 20px">
  <div class="tab" >
    <button id="lc" ng-disabled="misc.progressbar == true? false : true" class="tablinks" ng-click="dataTab($event, 'loteCsv')"><b style="color:black">Envío SMS Rápido / Archivo CSV</b></button>
    <button id="cc" ng-disabled="misc.progressbar == true? false : true" class="tablinks" ng-click="dataTab($event, 'contact')"><b style="color:#0B2161">Envío SMS por Lista de Contactos</b></button>
  </div>
  <div ng-show="data.data.length > 0" id="loteCsv" class="tabcontent" ng-cloak>
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a ng-disabled="misc.progressbar == true? false : true" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a  ng-disabled="misc.progressbar == true? false : true" ng-click="page == 1 ? true  : false || backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>
              {{ "{{data.totals }}"}}
            </b> registros </span><span>Página <b>
            {{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (data.page ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (data.page) || data.page == 0 ? 'disabled'  : ''">
          <a ng-disabled="misc.progressbar == true? false : true" ng-click="page == (data.page)  || data.page == 0  ? true  : false || page == (data.page)  || data.page == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (data.page)  || data.page == 0 ? 'disabled'  : ''">
          <a  ng-disabled="misc.progressbar == true? false : true" ng-click="page == (data.page)  || data.page == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
    <table class="table table-bordered table-responsive" id="resultTable">
      <thead class="theader">
        <tr>
          <th>Fecha</th>
          <th>Número de Celular</th>
          <th>Nombre de Campaña</th>
          <th>Mensaje</th>
          <th>Estado</th>
          <th>Cantidad para cobro</th>
        </tr>
      </thead>
      <tbody ng-repeat="key in data.data">
        <tr>
          <td>
            {{"{{ key.date }}"}}
          </td>
          <td>
            {{"{{ key.phone }}"}}
          </td>
          <td>
            {{"{{ key.name }}"}} 
          </td>
          <td>
            <i>{{"{{ key.message }}"}}</i>
          </td>
          <td ng-if="key.status == 'sent'">
            <b style="color: green">{{"{{translateStatus(key.status)}}"}} </b>
          </td>
          <td ng-if="key.status == 'undelivered'">
            <b style="color: red">{{"{{translateStatus(key.status)}}"}}</b>
          </td>
          <td class="text-center">
            <i>{{"{{ key.messageCount }}"}}</i>
          </td>
        </tr>
      </tbody>  
    </table> 
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a ng-disabled="misc.progressbar == true? false : true" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a ng-disabled="misc.progressbar == true? false : true" ng-click="page == 1 ? true  : false || backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>
              {{ "{{data.totals }}"}}
            </b> registros </span><span>Página <b>
            {{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (data.page ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (data.page) || data.page == 0 ? 'disabled'  : ''">
          <a  ng-disabled="misc.progressbar == true? false : true" ng-click="page == (data.page)  || data.page == 0  ? true  : false || page == (data.page)  || data.page == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li  ng-class="page == (data.page)  || data.page == 0 ? 'disabled'  : ''">
          <a ng-disabled="misc.progressbar == true? false : true" ng-click="page == (data.page)  || data.page == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>

  </div>
  <div ng-show="!data.data" class="row">
    <div ng-if="misc.progressbar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div ng-disabled class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay resultados con los criterios de búsqueda
          </h2>    
        </div>
      </div>
    </div>
  </div>

  <div ng-show="data.data.length > 0" id="contact" class="tabcontent">
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a  ng-disabled="misc.progressbar == true? false : true" ng-click="page == 1 ? true  : false || backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>
              {{ "{{data.totals }}"}}
            </b> registros </span><span>Página <b>
            {{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (data.page ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (data.page) || data.page == 0 ? 'disabled'  : ''">
          <a ng-disabled="misc.progressbar == true? false : true" ng-click="page == (data.page)  || data.page == 0  ? true  : false || page == (data.page)  || data.page == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (data.page)  || data.page == 0 ? 'disabled'  : ''">
          <a ng-click="page == (data.page)  || data.page == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
    <table class="table table-bordered table-responsive" id="resultTable">
      <thead class="theader">
        <tr>
          <th>Fecha</th>
          <th>Número de Celular</th>
          <th>Nombre de Campaña</th>
          <th>Mensaje</th>
          <th>Estado</th>
          <th>Cantidad para cobro</th>
        </tr>
      </thead>
      <tbody ng-repeat="key in data.data" >
        <tr>
          <td ng-if="key.date">
            {{"{{ key.date }}"}}
          </td>
          <td ng-if="!key.date">
            <i>no asignado</i>
          </td>
          <td>
            {{"{{ key.phone}}"}}
          </td>
          <td>
            {{"{{ key.name }}"}} 
          </td>
          <td>
            <i>{{"{{ key.message }}"}}</i>
          </td>
          <td ng-if="key.status == 'sent'">
            <b style="color: green">{{"{{translateStatus(key.status)}}"}} </b>
          </td>
          <td ng-if="key.status == 'undelivered'">
            <b style="color: red">{{"{{translateStatus(key.status)}}"}}</b>
          </td>
          <td class="text-center">
            <i>{{"{{ key.messageCount }}"}}</i>
          </td>
        </tr>
      </tbody> 
    </table>  
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a ng-disabled="misc.progressbar == true? false : true" ng-disabled="misc.progressbar == true? false : true" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a ng-disabled="misc.progressbar == true? false : true" ng-click="page == 1 ? true  : false || backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>
              {{ "{{data.totals }}"}}
            </b> registros </span><span>Página <b>
            {{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (data.page ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (data.page) || data.page == 0 ? 'disabled'  : ''">
          <a  ng-disabled="misc.progressbar == true? false : true" ng-click="page == (data.page)  || data.page == 0  ? true  : false || page == (data.page)  || data.page == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li  ng-class="page == (data.page)  || data.page == 0 ? 'disabled'  : ''">
          <a ng-click="page == (data.page)  || data.page == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>

  </div>
  <div ng-show="data.data.length <= 0" class="row">
    <div ng-if="misc.progressbar" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div ng-disabled class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay resultados con los criterios de búsqueda
          </h2>    
        </div>
      </div>
    </div>
  </div>   
</div>