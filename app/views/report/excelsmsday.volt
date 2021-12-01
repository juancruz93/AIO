<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{ title }}"}}
    </div>
    <hr class="basic-line" />
  </div>
</div>
<div class="row">
{#  <div class="col-md-offset-3 col-xs-3 col-sm-12 col-lg-9 text-right wrap form-inline">#}
  <div class="col-xs-3 col-sm-12 col-lg-12 text-center wrap form-inline">
    <div class="input-group" format="YYYY-MM-DD">
      <input id="dateInitial" class="form-control"
             moment-picker="search.valuedateInitial"
             placeholder="Seleccionar fecha inicial"
              ng-model="search.valuedateInitial"              
             ng-model-options="{ updateOn: 'blur' }">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-calendar"></i>
      </span>
    </div>
    <div class="input-group" format="YYYY-MM-DD">
      <input id="dateFinal" class="form-control"
             moment-picker="search.valuedateFinal"
             placeholder="Seleccionar fecha final"
             ng-model="search.valuedateFinal"             
             ng-model-options="{ updateOn: 'blur' }">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-calendar"></i>
      </span>
    </div>
    <button class="button  btn btn-md warning-inverted" ng-click="cleanFilters()" 
            data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
      <i class="fa fa-eraser"></i>
    </button>
    <button  class="button  btn btn-md primary-inverted" ng-click="searchDateMail()"
             data-toggle="tooltip" data-placement="top" title="Buscar">
      <i class="fa fa-search"></i>
    </button>
    <button  class="button  btn btn-md success-inverted" ng-click="downloadSms()"
             data-toggle="tooltip" data-placement="top" title="Descargar">
      <i class="fa fa-download"></i>
    </button>
  </div>

</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-lg-12 text-center wrap">
      <a href="{{url('reports/index')}}" class="">
        <button class="button  btn btn-md default-inverted">
          <i class="fa fa-arrow-left"></i> Regresar al inicio
        </button>
      </a>
    </div>
</div>


<div ng-show="infosms.items.length > 0" class="row" >
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a   ng-click="page == 1 ? true  : false || backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{infosms.total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (infosms.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (infosms.total_pages) || infosms.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (infosms.total_pages)  || infosms.total_pages == 0 ? 'disabled'  : ''">
        <a  ng-click="page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered" id="resultTable">
      <tbody ng-repeat=" value  in infosms.items"  >
        <tr ng-class="{thead: $index == 0}">
          <td  ng-repeat="(v, key)  in value  track by $index">
            {{"{{ key }}"}}
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a   ng-click="page == 1 ? true  : false || backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{infosms.total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (infosms.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (infosms.total_pages) || infosms.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (infosms.total_pages)  || infosms.total_pages == 0 ? 'disabled'  : ''">
        <a  ng-click="page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
</div>
  <div ng-hide="infosms.items.length > 0" class="row">
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

<style>
  .thead{
    background-color: #474646 !important;
    color: #f5f5f5;
  }
</style>
