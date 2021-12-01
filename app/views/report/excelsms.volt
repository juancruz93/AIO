<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Detalles de reporte de envíos de sms por mes
    </div>
    <hr class="basic-line" />
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center none-padding center-div" >
    <span class="">
      Fecha de envio
    </span>
    <span id="dateInitial" class="inline-block none-margin none-padding input-append date add-on input-group" 
          style="width: 25%" >
      <input  readonly id="valuedateInitial" placeholder="Fecha inicial"  type="text" 
              class="inline-block" style="padding: 4.5px !important; width: 80%">
      <span class="add-on input-group-addon inline-block none-margin none-padding">
        <i class="glyphicon glyphicon-calendar" ></i>
      </span>
    </span>
    <span class="inline-block" style="margin-left: 20px; margin-right: -20px">
      Hasta
    </span>
    <span id="dateFinal" class="inline-block none-margin none-padding input-append date add-on input-group"  
          style="width: 25%">
      <input  id="valuedateFinal" readonly data-format="yyyy-MM" placeholder="Fecha final" type="text" 
              class="inline-block" style="padding: 4.5px !important; width: 80%">
      <span class="add-on input-group-addon inline-block none-margin none-padding">
        <i class="glyphicon glyphicon-calendar" ></i>
      </span>
    </span>
    <div class="inline-block ">
      <button class="button  btn btn-md warning-inverted" ng-click="cleanFilters()" 
              data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
        <i class="fa fa-eraser"></i>
      </button>
      <button  class="button  btn btn-md primary-inverted" ng-click="searchDateMail()"
               data-toggle="tooltip" data-placement="top" title="Buscar">
        <i class="fa fa-search"></i>
      </button>
      <button  class="button  btn btn-md success-inverted" ng-click="downloadSms()"
               data-toggle="tooltip" data-placement="top" title="Buscar">
        <i class="fa fa-download"></i>
      </button>
    </div>
  </div>
</div>
<div id="pagination" class="text-center">
  <ul class="pagination">
    <li ng-class="page == 1 ? 'disabled'  : ''">
      <a  href="#/excelsms" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="page == 1 ? 'disabled'  : ''">
      <a href="#/excelsms"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{infosms.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ page }}"}}
        </b> de <b>
          {{ "{{ (infosms.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="page == (infosms.total_pages) || infosms.total_pages == 0 ? 'disabled'  : ''">
      <a href="#/excelsms" ng-click="page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="page == (infosms.total_pages)  || infosms.total_pages == 0 ? 'disabled'  : ''">
      <a href="#/excelsms" ng-click="page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
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
      <a  href="#/excelsms" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="page == 1 ? 'disabled'  : ''">
      <a href="#/excelsms"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{infosms.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ page }}"}}
        </b> de <b>
          {{ "{{ (infosms.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="page == (infosms.total_pages) || infosms.total_pages == 0 ? 'disabled'  : ''">
      <a href="#/excelsms" ng-click="page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="page == (infosms.total_pages)  || infosms.total_pages == 0 ? 'disabled'  : ''">
      <a href="#/excelsms" ng-click="page == (infosms.total_pages)  || infosms.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>

<style>
  .thead{
    background-color: #474646 !important;
    color: #f5f5f5;
  }
</style>
