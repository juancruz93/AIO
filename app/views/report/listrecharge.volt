{# empty Twig template #}
<script type="text/javascript">
  $.fn.datetimepicker.defaults = {
    maskInput: false,
    pickDate: true,
    pickTime: false
  };
  $(document).ready(function () {
    $('[data-toggle="popover"]').popover();
  });

</script>
<style>
  @media (max-width: 995px) {
    .center-div{
      text-align: center;
    }
  }
</style>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{ title }}"}}
    </div>
    <hr class="basic-line" />
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 center-div">
        <span style=" margin-right: 0 !important;" class="none-margin cursor inline-block" id="basic-addon1" placement="top" class="" data-toggle="popover" title="Instrucciones"
              data-content="Seleccione una o varias cuentas">
          <i class="fa fa-question-circle" aria-hidden="true" ></i>
        </span>
        <ui-select multiple ng-model="search.account"  theme="select2" sortable="false"
                   ng-change="searchReportRecharge()"
                   close-on-select="true"  style="max-width: 80%" class="inline-block none-margin none-padding-left"> 
          <ui-select-match
            placeholder="Seleccione una o varias cuentas">{{ "{{$item.name}}" }}</ui-select-match>
          <ui-select-choices
            repeat="key.idAccount as key in accounts | propsFilter: {name: $select.search}">
            <div ng-bind-html="key.name | highlight: $select.search"></div>
          </ui-select-choices>
        </ui-select>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 text-right none-padding   ">
        <span class="">
          Filtrar desde
        </span>
        <span id="dateInitial" class="inline-block none-margin none-padding input-append date add-on input-group" 
              style="width: 25%">
          <input readonly id="valuedateInitial" placeholder="Fecha inicial" data-format="yyyy-MM-dd" type="text" 
                 class="inline-block" style="padding: 4.5px !important; width: 80%">
          <span class="add-on input-group-addon inline-block none-margin none-padding">
            <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
          </span>
        </span>
        <span class="inline-block" style="margin-left: 20px; margin-right: -20px">
          Hasta
        </span>
        <span id="dateFinal" class="inline-block none-margin none-padding input-append date add-on input-group"  
              style="width: 25%">
          <input id="valuedateFinal" readonly data-format="yyyy-MM-dd" placeholder="Fecha final" type="text" 
                 class="inline-block" style="padding: 4.5px !important; width: 80%">
          <span class="add-on input-group-addon inline-block none-margin none-padding">
            <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i> 
          </span>
        </span>
        <div class="inline-block ">
          <button class="button  btn btn-md warning-inverted" ng-click="cleanFilters()" 
                  data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
            <i class="fa fa-eraser"></i>
          </button>
          <button class="button  btn btn-md primary-inverted" ng-click="searchDateRecharge()"
                  data-toggle="tooltip" data-placement="top" title="Buscar">
            <i class="fa fa-search"></i>
          </button>
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
          <button ng-disabled="misc.progressbar == true ? false : true "class="button  btn btn-md info-inverted" ng-click="dowloadReport()" >
            <i class="fa fa-download"></i> Descargar reporte
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<br>
<div ng-class="{'hidden' : misc.progressbar}" style="margin-left: 20px">
  <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
</div>
<br>            
<div ng-show="report[0].items.length > 0">
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
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
        <a ng-click="page == (report.total_pages)  || report.total_pages == 0  ? true  : false || page == (report.total_pages)  || report.total_pages == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (report.total_pages)  || report.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (report.total_pages)  || report.total_pages == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>    
  <table class="table table-bordered table-responsive table-hover" id="resultTable">
    <thead class="theader">
      <tr style="border-left: solid 4px #474646;">
        <th>Ver mas</th>
        <th>Cuenta</th>
        <th>Servicio</th>
        <th>Fecha Recarga</th>
        <th>Cantidad Recargada</th>
        <th>Nuevo Saldo Disponible</th>
        <th>Nuevo Limite</th>
        <th>Realizado por</th>
      </tr>
    </thead>
    <tbody ng-repeat="key in report[0].items">
      <tr>
        <td class="cursor" data-toggle="collapse" data-target="#allinfo{{'{{ key.idRecharge }}'}}" aria-expanded="false" aria-controls="allinfo{{ '{{ key.idRecharge }}' }}">
          <span class="glyphicon glyphicon-plus"></span>
        </td>
        <td >
          {{"{{ key.nameaccount }}"}}
        </td>
        <td ng-if="key.idService == 2">
          Contactos
        </td>
        <td ng-if="key.idService == 1">
          SMS
        </td>
        <td>
          {{"{{ key.createds }}"}}
        </td>
        <td>
          {{"{{ key.rechargeAmount }}"}}
        </td>
        <td>
          {{"{{ key.DisponibleAfter }}"}}
        </td>
        <td>
          {{"{{ key.TotalAfter }}"}}
        </td>
        <td>
          {{"{{ key.createdBy }}"}}
        </td>
      </tr>
      
      <tr id="allinfo{{ '{{ key.idRecharge }}' }}" class="collapse" ng-model="key.idRecharge  ">
        <td colspan="8">
          <div class="row">
            <div class="col-lg-12">
              <div class="block block-info">
                <div class="body row">
                  <div class="col-lg-5 col-md-5 col-sm-5 text-center">
                  </div>
                  <div class="col-lg-7 col-md-7 col-sm-7 ">
                    <table class="table-condensed table" style="border: 2px">
                      <thead ng-if="key.history.length > 0 ">
                        <th>
                          Fecha de Recarga
                        </th>
                        <th>
                          Cantidad Recargada
                        </th>
                        <th>
                          Nuevo Saldo Disponible
                        </th>
                        <th>
                          Nuevo Limite
                        </th>
                        <th>
                          Realizado Por
                        </th>
                      </thead>
                      <tbody ng-if="key.history.length > 0 ">
                        <tr tr ng-repeat="rechargeHistory in key.history" >
                          <td>
                            <strong> {{ "{{ rechargeHistory.created * 1000 | date:'yyyy-MM-dd HH:mm:ss' }}" }} </strong>
                          </td>
                          <td>
                            <strong> {{ "{{ rechargeHistory.rechargeAmount}}" }} </strong>
                          </td>
                          <td>
                            <strong> {{ "{{ (rechargeHistory.rechargeAmount*1) + (rechargeHistory.initialAmount*1)}}" }} </strong>
                          </td>
                          <td>
                            <strong> {{ "{{ (rechargeHistory.rechargeAmount*1) + (rechargeHistory.initialTotal*1)}}" }} </strong>
                          </td>
                          <td>
                            <strong> {{ "{{ rechargeHistory.createdBy}}" }} </strong>
                          </td>
                        </tr>
                      </tbody>
                      <div ng-if="(key.history.length <= 0||key.history == null) ">
                          <h3> 
                            No hay historial de recargas.
                          </h3>
                      </div>
                    </table>  
                  </div>
                </div>
              </div>
            </div>
          </div>
        </td>
      </tr>  

      
    </tbody>
  </table>
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  ng-click="page == 1 ? true  : false || fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
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
        <a ng-click="page == (report.total_pages)  || report.total_pages == 0  ? true  : false || page == (report.total_pages)  || report.total_pages == 0  ? true  : false || forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (report.total_pages)  || report.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (report.total_pages)  || report.total_pages == 0  ? true  : false || fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>    
</div>
<br>
<div ng-hide="report[0].items.length > 0" class="row">
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
<script>
  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }
</script>
