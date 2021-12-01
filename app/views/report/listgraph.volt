<script type="text/javascript">
  $(document).ready(function () {
    $('[data-toggle="popover"]').popover();
  });
</script>   
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Graficos
    </div>
    <hr class="basic-line" />
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 center-div">
    <span style=" margin-right: 0 !important;" class="none-margin cursor inline-block" id="basic-addon1" placement="top" class="" data-toggle="popover" title="Instrucciones"
          data-content="Seleccione un servicio del cual quiera ver las graficas">
      <i class="fa fa-question-circle" aria-hidden="true" ></i>
    </span>
    <ui-select ng-model="data.selected" ng-required="true"
               ui-select-required theme="select2" sortable="false"
               close-on-select="true" ng-change="searchGraph()"
               style="max-width: 80%" class="inline-block none-margin none-padding-left">
      <ui-select-match
        placeholder="Seleccione un servicio">{{ "{{$select.selected.name}}" }}</ui-select-match>
      <ui-select-choices
        repeat="key.idService as key in services | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 text-right none-padding center-div" >
    <span class="">
      Fecha de envio
    </span>
    <span id="dateInitial" class="inline-block none-margin none-padding input-append date add-on input-group" 
          style="width: 25%" >
      <input ng-disabled="!data.selected" readonly id="valuedateInitial" placeholder="Fecha inicial"  type="text" 
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
      <input ng-disabled="!data.selected" id="valuedateFinal" readonly data-format="yyyy-MM" placeholder="Fecha final" type="text" 
             class="inline-block" style="padding: 4.5px !important; width: 80%">
      <span class="add-on input-group-addon inline-block none-margin none-padding">
        <i class="glyphicon glyphicon-calendar" ></i>
      </span>
    </span>
    <div class="inline-block ">
      <button ng-disabled="!data.selected" class="button  btn btn-md warning-inverted" ng-click="cleanFilters()" 
              data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
        <i class="fa fa-eraser"></i>
      </button>
      <button ng-disabled="!data.selected" class="button  btn btn-md primary-inverted" ng-click="searchDateMail()"
              data-toggle="tooltip" data-placement="top" title="Buscar">
        <i class="fa fa-search"></i>
      </button>
    </div>
  </div>
</div>
<div ng-show="data.selected">
  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  text-center" >
      <span class="medium-text primary">Total de envios {{"{{ title() }}"}}:</span>
      <br>
      <span class="medium-text primary">{{"{{total()}}"}}</span>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="space"></div>
  <highchart id="chart1" config="chart"></highchart>
</div>