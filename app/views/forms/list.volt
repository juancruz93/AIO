<div class="clearfix"></div>
<div class="space"></div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de formularios
    </div>
    <hr class="basic-line">
    <p>
      En esta lista podra ver, crear, editar y eliminar los formularios.
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 none-padding-left">
      <div class="col-xs-12 col-sm-6 col-lg-6">
        <div class="input-group">
          <input type="text" class="form-control" id="exampleInputAmount" placeholder="Buscar por nombre" autofocus="true" data-ng-model="filter.name" data-ng-change="filtername()">
          <span class="input-group-addon"><i class="fa fa-search"></i></span>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-lg-6">
        <ui-select ng-change="filtername()" ng-model="filter.idFormCategory" theme="select2" sortable="false" close-on-select="true">
          <ui-select-match placeholder="Seleccione una categoría">{{ "{{$select.selected.name}}" }}</ui-select-match>
          <ui-select-choices repeat="key.idFormCategory as key in category | propsFilter: {name: $select.search}">
            <div ng-bind-html="key.name | highlight: $select.search"></div>
          </ui-select-choices>
        </ui-select>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right none-padding">
      <a href="{{ url('marketing') }}" class="button btn button default-inverted">Regresar</a>
      <a ui-sref="create.describe" class="button btn button success-inverted">Crear formulario</a>
    </div>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="list.items.length > 0">
  <ul class="pagination">
    <li ng-class="page == 1 ? 'disabled'  : ''">
      <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="page == 1 ? 'disabled'  : ''">
      <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ page }}"}}
        </b> de <b>
          {{ "{{ (list.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>

<div class="row" >
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="list.items.length > 0">
    <table class="table table-bordered sticky-enabled">
      <thead class="theader">
        <tr>
          <th>Nombre</th>
          <th>Detalle</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr data-ng-repeat="i in list.items">
          <td>
            <b>{{"{{i.name}}"}}</b><br>
          </td>
          <td style="width: 60%">
            <p>{{"{{i.description}}"}}</p>
            <p><b>Tipo: </b>{{ '{{ translateType(i.type) }}' }}</p><br>
            <em class="extra-small-text">Creado por <b>{{"{{i.createdBy}}"}}</b> el día {{"{{i.created}}"}} <br>
              Actualizado por <b>{{"{{i.updatedBy}}"}}</b> el día {{"{{i.updated}}"}}</em>
          </td>
          <td>

            <a href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" title="Eliminar formulario" data-ng-click="openModal(i.idForm)">
              <md-tooltip md-direction="bottom">
                Eliminar formulario
              </md-tooltip>
              <span class="glyphicon glyphicon-trash"></span>
            </a>
            {#            <a class="button shining btn btn-xs-round shining-round round-button info-inverted" title="Editar formulario" ng-click="reloadEditForm(i.idForm)">#}
            <a ui-sref="create.edit({id:i.idForm})" class="button shining btn btn-xs-round shining-round round-button info-inverted" title="Editar formulario">
              <md-tooltip md-direction="bottom">
                Editar formulario
              </md-tooltip>
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
            {#            <a href="{{ url('forms/contacts/') }}{{ '{{ i.idForm }}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted" title="Ver contactos">#}
            <a ui-sref="contacts({id:i.idForm})" class="button shining btn btn-xs-round shining-round round-button primary-inverted" title="Ver contactos">
              <md-tooltip md-direction="bottom">
                Ver contactos
              </md-tooltip>
              <span class="fa fa-users"></span>
            </a>
            <a href="" ng-if="i.content != false" class="button shining btn btn-xs-round shining-round round-button info-inverted" title="codigo html" data-ng-click="codeHtml($index)">
              <md-tooltip md-direction="bottom">
                Html
              </md-tooltip>
              <span class="fa fa-code"></span>
            </a>
            <a href="" ng-if="i.content != false" class="button shining btn btn-xs-round shining-round round-button success-inverted" title="Iframe" data-ng-click="codeIframe($index)">
              <md-tooltip md-direction="bottom">
                Iframe
              </md-tooltip>
              <span class="glyphicon glyphicon-list-alt"></span>
            </a>
            <button type="button" ng-if="i.content != false" class="button btn btn-xs-round success-inverted" data-ng-click="previsualizar($index)">
              <md-tooltip md-direction="bottom">
                Previsualizar
              </md-tooltip>
              <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
            {#                <a href="{{ url('forms/report/') }}{{ '{{ i.idForm }}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted" title="Ver reporte">#}
              <a ui-sref="reportforms({id:i.idForm})" class="button shining btn btn-xs-round shining-round round-button primary-inverted" title="Ver reporte">
                <md-tooltip md-direction="bottom">
                  Reporte de formularios
                </md-tooltip>
                <i class="fa fa-address-book"></i>
              </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div ng-show="list.items.length == 0">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay registros de formularios que coincidan con los filtros, para crear uno haga <a href="{{ url('forms/create#/basicinformation/') }}"><u>Clic aquí</u></a>.
          </h2>
          </h2>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="list.items.length > 0">
  <ul class="pagination">
    <li ng-class="page == 1 ? 'disabled'  : ''">
      <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="page == 1 ? 'disabled'  : ''">
      <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ page }}"}}
        </b> de <b>
          {{ "{{ (list.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>
<div id="codeHtml" class="dialog" >
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <div class="form-horizontal">
        <div class="form-group">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
            <textarea id="Description" type="text" ng-model="codeHtmlString" placeholder="html"  class="undeline-input" style="resize: none;height:500px"></textarea>
          </div>
        </div>
        <div class="form-group">
          <a ng-click="removeDialog('codeHtml');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
        </div>
      </div>
    </div>
  </div>    
</div>
<div id="iframe" class="dialog" >
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <div class="form-horizontal">
        <div class="form-group">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
            <textarea id="Description" type="text" ng-model="codeIframeString" placeholder="html"  class="undeline-input" style="resize: none"></textarea>
          </div>
        </div>
        <div class="form-group">
          <a ng-click="removeDialog('iframe');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
        </div>
      </div>
    </div>
  </div>    
</div>
        
<div id="preview" class="dialog" >
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <md-progress-linear md-mode="indeterminate" ng-hide="previewShow" class="md-warn"></md-progress-linear>
      <div ng-if="previewShow">
        <div class="form-horizontal">
          <div class="form-group" style="overflow: scroll;" >
            <form  ng-submit="validateForm()"ng-style="{'background-color':backgroundForm}">
              <div ng-model="input"  fb-form="sigmaForm" fb-default="defaultValue"></div>
            </form>
          </div>
        </div>
      </div>
      <div class="form-group">
        <a ng-click="removeDialog('preview');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
      </div>
    </div>
  </div>    
</div>

<div id="deleteDialog" class="dialog ">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape ">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner dialog-padding">
      <p ng-show="PlaneError.error == 1">{{'{{PlaneError.msg}}'}}</p>
      <div class="body row text-center">
        <h3>¿Desea eliminar el formulario?</h3>
      </div>
      <div>
        <h5>Si se elimina el formulario no lo podrá volver a usar.</h5>
      </div>
      <div class="body row" style="padding-top: 1em;">                    
        <a ng-click="closeModal()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        
        <a ng-click="deleteForm(i.idForm)"  id="btn-ok" class="button shining btn btn-md success-inverted">Eliminar</a>
      </div>
    </div>
  </div>
</div> 
      