<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de plantillas de Landing Page
    </div>
    <hr class="basic-line">
    <p>
      Aqui encontrará el listado de Landing Page Template que se han creado en la plataforma. Podrá encontrar información acerca de la configuración de cada Landing Page Template (Previsualizar, duplicar, editar).
    </p>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">
    <div class="input-group">
      <input class="form-control" data-ng-change="functions.filterName()" id="name" placeholder="Buscar por nombre" ng-model="data.filter.name" />
      <span class=" input-group-addon" id="basic-addon1" >
        <i class="fa fa-search"></i>
      </span>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
    <ui-select ng-change='functions.filterCategory().searchcategory()' multiple ng-model="data.filter.category" ng-required="true"  ui-select-required 
               theme="bootstrap" title=""  sortable="false" close-on-select="true" >
      <ui-select-match placeholder="Categorias">[[$item.name]]</ui-select-match>
      <ui-select-choices repeat="key.idLandingPageTemplateCategory as key in data.categories | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="dropdown form-group dropdown-start-parent">
          <a class="dropdown-toggle" id="dropdownStart" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent"
             href="#">
            <div class="input-group date">
              <input type="text" class="form-control" placeholder="Fecha Inicial" readonly="true" data-ng-model="data.filter.dateStart" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
          </a>
          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            <datetimepicker data-ng-model="data.filter.dateStart"
                            data-datetimepicker-config="{ dropdownSelector: '#dropdownStart', renderOn: 'end-date-changed', startView: 'month', minView: 'hour', modelType: 'YYYY-MM-DD HH:mm:ss' }"
                            data-on-set-time="functions.startDateOnSetTime()"
                            dataBeforeRender="functions.startDateBeforeRender($dates)"
                            ng-change="functions.filterDate()"
                            ></datetimepicker>
          </ul>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="dropdown form-group dropdown-end-parent">
          <a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent"
             href="#">
            <div class="input-group date">
              <input type="text" class="form-control" placeholder="Fecha Final" readonly="true" data-ng-model="data.filter.dateEnd" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
              <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
          </a>
          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            <datetimepicker data-ng-model="data.filter.dateEnd"
                            data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'start-date-changed', startView: 'month', minView: 'hour', modelType: 'YYYY-MM-DD HH:mm:ss' }"
                            data-on-set-time="functions.endDateOnSetTime()"
                            data-before-render="functions.endDateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)"
                            ng-change="functions.filterDate()"
        </ul>
      </div>
    </div>
  </div>
</div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-lg-12 text-right wrap">
    <a href="{{ url('landingpage#/') }}" class="button shining btn btn default-inverted">Regresar</a>
    <a href="{{ url('landingpagecategory#/') }}" class="btn btn-md warning-inverted">Categorías de landing page</a>
    <a href="{{url('landingpagetemplate/create/')}}" class="button shining btn btn success-inverted">Crear una nueva landing page</a>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="data.list.items.length > 0">
  <ul class="pagination">
    <li ng-class="data.page == 1 ? 'disabled'  : ''">
      <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="data.page == 1 ? 'disabled'  : ''">
      <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>[[data.list.total]]
        </b> registros </span><span>Página <b>[[data.page]]
        </b> de <b>
          [[ (data.list.total_pages ) ]]
        </b></span>
    </li>
    <li   ng-class="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/" ng-click="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="wrap">
    <md-progress-linear md-mode="query" data-ng-show="misc.loader" class="md-warn"></md-progress-linear>
  </div>

  <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3" data-ng-repeat="i in data.list.items track by $index">
    <div class="thumbnail" style="height: 280px">
      <img src="{{url('')}}[[i.dirThumbnail !== '' ? i.dirThumbnail : 'images/circle/plantillas.jpg']]" />
      <div class="caption text-center">
        <di>
          <dd><strong class="small-text">[[i.name]]</strong></dd>
          <dd><span class="smaill-text">Categoria: [[i.namCategory]]</span></dd>
        </di>
        <div class="btn-group btn-group-sm" role="group">
          <a ng-href="{{url('landingpagetemplate/preview/')}}[[i.idLandingPageTemplate]]" class="btn default-inverted toltip" target="_blank">
            <i class="fa fa-eye"></i>
            <md-tooltip md-direction="bottom">
              Previsualizar
            </md-tooltip>
          </a>
          <a href="{{url('landingpagetemplate/create/')}}[[i.idLandingPageTemplate]]" class="btn info-inverted">
            <i class="fa fa-pencil"></i>
            <md-tooltip md-direction="bottom">
              Editar
            </md-tooltip>
          </a>
          {#<button type="button" class="btn danger-inverted" data-ng-click="confirmDelete(i.idMailTemplate)">
            <i class="fa fa-trash"></i>
            <md-tooltip md-direction="bottom">
              Eliminar
            </md-tooltip>
          </button>#}
        </div>
      </div>   
    </div>
  </div>
</div>

<div ng-show="data.list.items.length == 0">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2>
          No hay registros de plantillas que coincidan con los filtros, para crear una haga <a href="{{url('landingpagetemplate/create/')}}">clic aquí</a>.
        </h2>    
        </h2>    
      </div>
    </div>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="data.list.items.length > 0">
  <ul class="pagination">
    <li ng-class="data.page == 1 ? 'disabled'  : ''">
      <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="data.page == 1 ? 'disabled'  : ''">
      <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>[[data.list.total]]
        </b> registros </span><span>Página <b>[[data.page]]
        </b> de <b>
          [[ (data.list.total_pages ) ]]
        </b></span>
    </li>
    <li   ng-class="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/" ng-click="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>
