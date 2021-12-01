{# empty Twig template #}
<script type="text/javascript">
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
      Informe de consumo de SMS por canal al mes
    </div>
    <hr class="basic-line" />
  </div>
</div>

    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-align-right">
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-lg-offset-8 col-md-offset-8">
          <div class="dropdown form-group dropdown-end-parent input-group">
            <a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent" href="javascript:void(0)">
              <div class="input-group date">
                <input type="text" class="form-control" placeholder="Fecha (yyyy-mm)" readonly="true" data-ng-model="misc.search.dateFinal" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
              </div>
            </a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
              <datetimepicker data-ng-model="misc.search.dateFinal"
                              data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'start-date-changed', startView: 'year', minView: 'month', modelType: 'YYYY-MM' }"
                              data-on-set-time="functions.endDateOnSetTime()"
                              data-before-render="functions.endDateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)"></datetimepicker>
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 text-align-right" style="padding: 0px;">
          <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md warning-inverted" ng-click="functions.cleanFilters()"
                  data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
            <i class="fa fa-eraser"></i>
          </button>
          <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md primary-inverted" ng-click="functions.searchDate()"
                  data-toggle="tooltip" data-placement="top" title="Buscar">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
      
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right" style="padding: 0px;">
          <div class="inline-block none-margin none-padding" >
            <a href="{{ url("/reports/index")}}" class="">
              <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md default-inverted">
                <i class="fa fa-arrow-left"></i>
                Regresar al inicio
              </button>
            </a>
            {#<button ng-disabled="misc.progressbar == true? false : true " class="button  btn btn-md info-inverted" ng-click="functions.dowloadReport()">
              Descargar reporte
            </button>#}
          </div>
        </div>
      </div>
      <div class="space"></div>
      <div class="row">
        <div ng-class="{'hidden' : misc.progressbar}" >
          <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
        </div>
        <div ng-hide="(misc.totalValidations == 0 || !misc.progressbar)">

          {#<div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="misc.page == 1 ? 'disabled'  : ''">
                <a  href="javascript:void(0)" ng-click="misc.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="misc.page == 1 ? 'disabled'  : ''">
                <a href="javascript:void(0)"  ng-click="misc.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{misc.totalValidations }}"}}
                  </b> registros </span><span>Página <b>{{"{{ misc.page }}"}}
                  </b> de <b>
                    {{ "{{ (misc.total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="misc.page == (misc.total_pages) || misc.total_pages == 0 ? 'disabled'  : ''">
                <a href="javascript:void(0)" ng-click="misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li  ng-class="misc.page == (misc.total_pages)  || misc.total_pages == 0 ? 'disabled'  : ''">
                <a href="javascript:void(0)" ng-click="misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>#}

          <!-- Table data -->
          <table class="table table-bordered sticky-enabled" id="resultTable">
            <thead class="theader">
              <tr style="border-left: solid 4px #474646;">
                <th ng-repeat="title in columnsChannel track by $index">
                  {{'{{title}}'}}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{'{{misc.dateFormat}}'}}</td>
                <td ng-repeat="valueData in data track by $index">
                    {{'{{valueData.countIdAdapter}}'}}
                </td>
              </tr>
            </tbody>
          </table>
          {#<div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="misc.page == 1 ? 'disabled'  : ''">
                <a  href="javascript:void(0)" ng-click="misc.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="misc.page == 1 ? 'disabled'  : ''">
                <a href="javascript:void(0)"  ng-click="misc.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{misc.totalValidations }}"}}
                  </b> registros </span><span>Página <b>{{"{{ misc.page }}"}}
                  </b> de <b>
                    {{ "{{ (misc.total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="misc.page == (misc.total_pages) || misc.total_pages == 0 ? 'disabled'  : ''">
                <a href="javascript:void(0)" ng-click="misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li  ng-class="misc.page == (misc.total_pages)  || misc.total_pages == 0 ? 'disabled'  : ''">
                <a href="javascript:void(0)" ng-click="misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>#}
        </div>
        <br>
        <div ng-show="misc.totalValidations <= 0" class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="block block-success">
              <div class="body success-no-hover text-center">
                <h2>
                  Aún no se tienen registros de sms
                </h2>
                <div ng-hide="misc.totalValidations != 1" class="row">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
