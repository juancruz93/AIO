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
      Detalle de correos validados
    </div>
    <hr class="basic-line" />
  </div>
</div>


<uib-tabset active="activeJustified">
  <uib-tab label="Correos validos" index="0" heading="Correos validos" ng-click="functions.changeOptionEmailValidation()" id="open">
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <ui-select ng-disabled="misc.progressbar == true? false : true" multiple ng-model="misc.search.account" theme="bootstrap" sortable="false"
                     ng-change="functions.searchReport()" close-on-select="true" style="min-height:30px;">
            <ui-select-match placeholder="Buscar por cuenta">{{ "{{$item.name}}" }}</ui-select-match>
            <ui-select-choices repeat="key.idAccount as key in misc.accounts | propsFilter: {name: $select.search}">
              <div ng-bind-html="key.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <ui-select ng-disabled="misc.progressbar == true? false : true" multiple ng-model="misc.search.categorie" theme="bootstrap" sortable="false"
                     ng-change="functions.searchReport()" close-on-select="true" style="min-height:30px;">
            <ui-select-match placeholder="Escoger categoría">{{ "{{$item.name}}" }}</ui-select-match>
            <ui-select-choices repeat="key.name as key in misc.categories | propsFilter: {name: $select.search}">
              <div ng-bind-html="key.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <input class="form-control" id="name" placeholder="Buscar por email" data-ng-model="misc.search.email" aria-invalid="false">
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <div class="dropdown form-group dropdown-start-parent input-group">
            <a class="dropdown-toggle" id="dropdownStart" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent" href="javascript:void(0)">
              <div class="input-group date">
                <input type="text" class="form-control" placeholder="Fecha Inicial" readonly="true" data-ng-model="misc.search.dateInitial" data-date-time-input="YYYY-MMM-DD" style="background-color: white;">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
              </div>
            </a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
              <datetimepicker data-ng-model="misc.search.dateInitial"
                              data-datetimepicker-config="{ dropdownSelector: '#dropdownStart', renderOn: 'end-date-changed', startView: 'month', minView: 'day', modelType: 'YYYY-MM-DD' }"
                              data-on-set-time="functions.startDateOnSetTime()"
                              dataBeforeRender="functions.startDateBeforeRender($dates)"></datetimepicker>
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <div class="dropdown form-group dropdown-end-parent input-group">
            <a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent" href="javascript:void(0)">
              <div class="input-group date">
                <input type="text" class="form-control" placeholder="Fecha Final" readonly="true" data-ng-model="misc.search.dateFinal" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
              </div>
            </a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
              <datetimepicker data-ng-model="misc.search.dateFinal"
                              data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'start-date-changed', startView: 'month', minView: 'day', modelType: 'YYYY-MM-DD' }"
                              data-on-set-time="functions.endDateOnSetTime()"
                              data-before-render="functions.endDateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)"></datetimepicker>
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
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
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
          <div class="inline-block none-margin none-padding" >
            <a href="{{ url("/reports/index")}}" class="">
              <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md default-inverted">
                Regresar al inicio
              </button>
            </a>
            <button ng-disabled="misc.progressbar == true? false : true " class="button  btn btn-md info-inverted" ng-click="functions.dowloadReport()">
              Descargar reporte
            </button>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div ng-class="{'hidden' : misc.progressbar}" >
          <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
        </div>
        <div ng-hide="(misc.totalValidations == 0 || !misc.progressbar)">

          <div id="pagination" class="text-center">
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
          </div>

          <!-- Table data -->
          <table class="table table-bordered sticky-enabled" id="resultTable">
            <thead class="theader">
              <tr style="border-left: solid 4px #474646;">
                <th>
                  Fecha de validación
                </th>
                <th>
                  Cuenta
                </th>
                <th>
                  SubCuenta
                </th>
                <th>
                  Correo
                </th>
                <th>
                  Nombre de la campaña
                </th>
                <th>
                  Categoría
                </th>
                <th>
                  Validación
                </th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="validation in data">
                <td>
                  {{'{{validation.dateTime}}'}}
                </td>
                <td>
                  {{'{{validation.account}}'}}
                </td>
                <td>
                  {{'{{validation.subaccount}}'}}
                </td>
                <td>
                  {{'{{validation.email}}'}}
                </td>
                <td>
                  {{'{{validation.name}}'}}
                </td>
                <td>
                  {{'{{validation.score}}'}}
                </td>
                <td>
                  {{'{{validation.evaluation}}'}}
                </td>
              </tr>
            </tbody>
          </table>
          <div id="pagination" class="text-center">
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
          </div>
        </div>
        <br>
        <div ng-show="misc.totalValidations <= 0" class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="block block-success">
              <div class="body success-no-hover text-center">
                <h2>
                  Aún no se tienen registros de datavalidation
                </h2>
                <div ng-hide="misc.totalValidations != 1" class="row">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </uib-tab>

  <uib-tab label="Correos no validos" index="1" heading="Correos no validos" ng-click="functions.changeOptionEmailBounced()" id="openBad">
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
          <ui-select ng-disabled="misc.progressbar == true? false : true" multiple ng-model="misc.search.account" theme="bootstrap" sortable="false"
                     ng-change="functions.searchReportBounced()" close-on-select="true" style="min-height:30px;">
            <ui-select-match placeholder="Buscar por cuenta">{{ "{{$item.name}}" }}</ui-select-match>
            <ui-select-choices repeat="key.idAccount as key in misc.accounts | propsFilter: {name: $select.search}">
              <div ng-bind-html="key.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>
        {#Filtro Categoria#}
        {#<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <ui-select ng-disabled="misc.progressbar == true? false : true" multiple ng-model="misc.search.categorie" theme="bootstrap" sortable="false"
                     ng-change="functions.searchReportBounced()" close-on-select="true" style="min-height:30px;">
            <ui-select-match placeholder="Escoger categoría">{{ "{{$item.name}}" }}</ui-select-match>
            <ui-select-choices repeat="key.name as key in misc.categoriesBad | propsFilter: {name: $select.search}">
              <div ng-bind-html="key.name | highlight: $select.search"></div>
            </ui-select-choices>
          </ui-select>
        </div>#}
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <input class="form-control ng-pristine ng-valid ng-empty ng-touched" id="name" placeholder="Buscar por email" data-ng-model="misc.search.email" aria-invalid="false" style="">
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <div class="dropdown form-group dropdown-start-parent input-group">
            <a class="dropdown-toggle" id="dropdownStart" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent" href="javascript:void(0)">
              <div class="input-group date">
                <input type="text" class="form-control" id="ini" placeholder="Fecha Inicial" readonly="true" data-ng-model="misc.search.dateInitialTwo" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
              </div>
            </a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
              <datetimepicker data-ng-model="misc.search.dateInitialTwo"
                              data-datetimepicker-config="{ dropdownSelector: '#dropdownStart', renderOn: 'end-date-changed', startView: 'month', minView: 'day', modelType: 'YYYY-MM-DD' }"
                              data-on-set-time="functions.startDateOnSetTime()"
                              dataBeforeRender="functions.startDateBeforeRender($dates)">
              </datetimepicker>
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <div class="dropdown form-group dropdown-end-parent input-group">
            <a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent" href="javascript:void(0)">
              <div class="input-group date">
                <input type="text" class="form-control" placeholder="Fecha Final" readonly="true" data-ng-model="misc.search.dateFinalTwo" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
              </div>
            </a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
              <datetimepicker data-ng-model="misc.search.dateFinalTwo"
                              data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'start-date-changed', startView: 'month', minView: 'day', modelType: 'YYYY-MM-DD' }"
                              data-on-set-time="functions.endDateOnSetTime()"
                              data-before-render="functions.endDateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)">
              </datetimepicker>
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
          <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md warning-inverted" ng-click="functions.cleanFiltersBad()" 
                  data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
            <i class="fa fa-eraser"></i>
          </button>
          <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md primary-inverted" ng-click="functions.searchDateTwo()"
                  data-toggle="tooltip" data-placement="top" title="Buscar">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">

      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
          <div class="inline-block none-margin none-padding" >
            <a href="{{ url("/reports/index")}}" class="">
              <button ng-disabled="misc.progressbar == true? false : true" class="button  btn btn-md default-inverted">
                Regresar al inicio
              </button>
            </a>
            <button ng-disabled="misc.progressbar == true? false : true " class="button  btn btn-md info-inverted" ng-click="functions.dowloadReportBounced()">
              Descargar reporte
            </button>
          </div>
        </div>
      </div>

      <div class="row">
        <div ng-class="{'hidden' : misc.progressbar}" >
          <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
        </div>
        <div ng-hide="(misc.totalValidations == 0 || !misc.progressbar)">

          <div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="misc.page == 1 ? 'disabled'  : ''">
                <a  href="javascript:void(0)" ng-click="misc.page == 1 ? true  : false || functions.fastbackwardTwo()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="misc.page == 1 ? 'disabled'  : ''">
                <a href="javascript:void(0)"  ng-click="misc.page == 1 ? true  : false || functions.backwardTwo()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{misc.totalValidations }}"}}
                  </b> registros </span><span>Página <b>{{"{{ misc.page }}"}}
                  </b> de <b>
                    {{ "{{ (misc.total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="misc.page == (misc.total_pages) || misc.total_pages == 0 ? 'disabled'  : ''">
                <a href="javascript:void(0)" ng-click="misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || functions.forwardTwo()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li  ng-class="misc.page == (misc.total_pages)  || misc.total_pages == 0 ? 'disabled'  : ''">
                <a href="javascript:void(0)" ng-click="misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || functions.fastforwardTwo()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>

          <!-- Table data -->
          <table class="table table-bordered sticky-enabled" id="resultTable">
            <thead class="theader">
              <tr style="border-left: solid 4px #474646;">
                <th>
                  Fecha de validación
                </th>
                <th>
                  Cuenta
                </th>
                <th>
                  SubCuenta
                </th>
                <th>
                  Correo
                </th>
                <th>
                  Nombre de la campaña
                </th>
                <th>
                  Categoría
                </th>
                <th>
                  Validación
                </th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="validation in data">
                <td>
                  {{'{{validation.datetime}}'}}
                </td>
                <td>
                  {{'{{validation.account}}'}}
                </td>
                <td>
                  {{'{{validation.subaccount}}'}}
                </td>
                <td>
                  {{'{{validation.email}}'}}
                </td>
                <td>
                  {{'{{validation.name}}'}}
                </td>
                <td>
                  {{'{{validation.code}}'}}
                </td>
                <td>
                  {{'{{validation.evaluation}}'}}
                </td>
              </tr>
            </tbody>
          </table>
          <div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="misc.page == 1 ? 'disabled'  : ''">
                <a  href="javascript:void(0)" ng-click="misc.page == 1 ? true  : false || functions.fastbackwardTwo()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="misc.page == 1 ? 'disabled'  : ''">
                <a href="javascript:void(0)"  ng-click="misc.page == 1 ? true  : false || functions.backwardTwo()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{misc.totalValidations }}"}}
                  </b> registros </span><span>Página <b>{{"{{ misc.page }}"}}
                  </b> de <b>
                    {{ "{{ (misc.total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="misc.page == (misc.total_pages) || misc.total_pages == 0 ? 'disabled'  : ''">
                <a href="javascript:void(0)" ng-click="misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || functions.forwardTwo()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li  ng-class="misc.page == (misc.total_pages)  || misc.total_pages == 0 ? 'disabled'  : ''">
                <a href="javascript:void(0)" ng-click="misc.page == (misc.total_pages)  || misc.total_pages == 0  ? true  : false || functions.fastforwardTwo()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>
        </div>
        <br>
        <div ng-show="misc.totalValidations <= 0" class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="block block-success">
              <div class="body success-no-hover text-center">
                <h2>
                  Aún no se tienen registros de datavalidation
                </h2>
                <div ng-hide="misc.totalValidations != 1" class="row">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </uib-tab>

</uib-tabset>
