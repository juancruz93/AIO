<style>
  .modal {
    text-align: center;
    padding: 0!important;
  }

  .dialog-inner {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
  }

  .modal:before {
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
    margin-right: -4px;
  }

  .modal-dialog {
    display: inline-block;
    text-align: left;
    vertical-align: middle;
  }
</style>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de Landing Page
    </div>
    <hr class="basic-line">
    <p>
      Aqui encontrará el listado de Landing Page que se han creado en la plataforma. Podrá encontrar información acerca de la configuración de cada Landing Page (Previsualizar, duplicar, editar, generar link). Además podrá ver las estadísticas asociadas a cada una.
    </p>
  </div>
</div>
<div class="row" >
  <div class="col-xs-3 col-sm-3 col-lg-3 wrap">
    <div class="input-group">
      <input class="form-control" data-ng-change="resServices.listLanding()" id="name" placeholder="Buscar por nombre" ng-model="data.filter.name" />
      <span class=" input-group-addon" id="basic-addon1" >
        <i class="fa fa-search"></i>
      </span>
    </div>
  </div>
  <div class="col-xs-3 col-sm-3 col-lg-3">
    <ui-select ng-change='resServices.searchcategory()' multiple ng-model="data.filter.category" ng-required="true"  ui-select-required 
               theme="bootstrap" title=""  sortable="false" close-on-select="true" >
      <ui-select-match placeholder="Categorias">{{"{{$item.name}}"}}</ui-select-match>
      <ui-select-choices repeat="key.idLandingPageCategory as key in data.landingCategory | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>
    
    <div class="col-xs-3 col-sm-3 col-lg-3 text-right wrap ">        
      <div class="input-group"
           moment-picker="data.filter.dateinitial"
           format="YYYY-MM-DD">

        <input id="dateinitial" class="form-control"                   
               placeholder="Seleccionar fecha inicial"
               ng-model="data.filter.dateinitial"              
               ng-model-options="{ updateOn: 'blur' }">
        <span class="input-group-addon">
          <i class="glyphicon glyphicon-calendar"></i>
        </span>
      </div> 
    </div> 
    <div class="col-xs-3 col-sm-3 col-lg-3 text-right wrap">
      <div class="input-group"  
           moment-picker="data.filter.dateend"
           format="YYYY-MM-DD">

        <input id="dateend" class="form-control"
               placeholder="Seleccionar fecha final"
               ng-model="data.filter.dateend"             
               ng-model-options="{ updateOn: 'blur' }">
        <span class="input-group-addon">
          <i class="glyphicon glyphicon-calendar"></i>
        </span>
      </div>      
    </div>
  


</div>
<br>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-lg-12 text-right wrap">
    <a href="{{ url('marketing') }}" class="button shining btn btn default-inverted">Regresar</a>
    <a href="{{ url('landingpagetemplate#/') }}" class="btn btn-md primary-inverted">Plantillas de landingpage</a>
    <a href="{{ url('landingpagecategory#/') }}" class="btn btn-md warning-inverted">Categorías de landing page</a>
    {#      <a href="{{ url('landingpage/create#/basicinformation/') }}" class="button shining btn btn success-inverted">Crear una nueva landing page</a>#}
    <a ui-sref="create.describe()" class="button shining btn btn success-inverted">Crear una nueva landing page</a>
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
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
        </b> de <b>
          {{ "{{ (data.list.total_pages ) }}"}}
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
<div class="row" >
  <div class="wrap">
    <md-progress-linear md-mode="query" data-ng-show="misc.loader" class="md-warn"></md-progress-linear>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap" ng-show="data.list.items.length > 0">
    <table class="table table-bordered sticky-enabled">
      <thead class="theader">
        <tr>
          <th>Nombre</th>
          <th>Detalle</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr data-ng-repeat="i in data.list.items | filter:search:strict" data-ng-class="i.status == 'published' ? 'success letter-no-hover': i.status == 'expireded' ? 'danger letter-no-hover':''">
          <td>
            <b class="medium-text">{{"{{i.name}}"}}</b><br>
            <em>{{"{{i.status == 'published' ? 'Publicada' : i.status == 'draft' ? 'Borrador' : 'Expirada'}}"}}</em>
          </td>
          <td>
            <p>
              {{"{{i.description}}"}}
            <p>
            <p>
              <b>Categoría:</b> {{ "{{ i.landingCategory }}" }}
            </p>
            <br>
            <em class="extra-small-text">Creado por <b>{{"{{i.createdBy}}"}}</b> el día {{"{{i.created}}"}} <br>
              Actualizado por <b>{{"{{i.updatedBy}}"}}</b> el día {{"{{i.updated}}"}}</em>
          </td>
          <td>
            <a href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" title="Eliminar Landing" data-ng-click="functions.confirmDelete(i.idLandingPage)">
              <md-tooltip md-direction="bottom">
                Eliminar Landing
              </md-tooltip>
              <span class="glyphicon glyphicon-trash"></span>
            </a>
            <a ui-sref="create.describe({idLandingPage:i.idLandingPage})" class="button shining btn btn-xs-round shining-round round-button info-inverted" title="Editar Landing">
              <md-tooltip md-direction="bottom">
                Editar Landing
              </md-tooltip>
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
            <button type="button" ng-if="i.content != false" class="button btn btn-xs-round default-inverted" data-ng-click="functions.linkgenerator(i.idLandingPage)">
              <md-tooltip md-direction="bottom">
                Generar link
              </md-tooltip>
              <i class="fa fa-link"></i>
            </button>

            <a ng-click="resServices.duplicate(i.idLandingPage)" ng-show="i.status == 'published'" class="button btn btn-xs-round primary-inverted" aria-label="Duplicar Landing">
              <i class="fa fa-files-o" aria-hidden="true"></i>
              <md-tooltip md-direction="bottom">
                Duplicar Landing Page
              </md-tooltip>
            </a>
            <a href="{{url('landingpage/preview/')}}{{"{{i.idLandingPage}}"}}" target="_blank" class="button btn btn-xs-round success-inverted">
              <md-tooltip md-direction="bottom">
                Previsualizar
              </md-tooltip>
              <i class="fa fa-eye" aria-hidden="true"></i>
            </a>
            {#<a href="{{url("statistic#/survey")}}/{{"{{i.idSurvey}}"}}"  ng-show="i.status == 'published'"  class="button shining btn btn-xs-round shining shining-round round-button success-inverted" >
              <md-tooltip md-direction="bottom">{
                Ver estadísticas
              </md-tooltip>
              <span class="fa fa-bar-chart"></span>
            </a>#}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div ng-show="data.list.items.length == 0">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay registros de Landing Page que coincidan con los filtros, para crear una haga <a ui-sref="create.describe()"><u>Clic aquí</u></a>.
          </h2>
          </h2>
        </div>
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
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
        </b> de <b>
          {{ "{{ (data.list.total_pages ) }}"}}
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

{# Inicio del modal para mostrar el link #}
<div class="modal fade linkgen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-success">
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <p class="small-text">
              Este es el link que podrá compartir dependiendo del tipo de encuesta que haya elegido
            <div class="form-group">
              <div class="col-sm-10">
                <input type="text" id="link" class="form-control" readonly="true" data-ng-model="linklp" />
              </div>
              <div class="col-sm-2">
                <button type="button" class="btn btn-info" id="btnCopy">
                  <i class="fa fa-copy"></i> Copiar
                </button>
              </div>
            </div>
            </p>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-sm-12 text-center">
            <button type="button" class="btn danger-inverted" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{# Final del modal link generator  #}      

<div id="somedialog" class="dialog ng-scope">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"></rect>
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>¿Esta seguro?</h2>
      <div>
        Debe tener en cuenta que si elimina la Landing Page ya no la podrá volver a utilizar ni ver.
      </div>
      <br>
      <div>
        <a onclick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close="">Cancelar</a>
        <a href="#/" data-ng-click="resServices.deleteLandingPage()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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

