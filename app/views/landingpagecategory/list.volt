<div class="row" >
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de categoria de landing page
    </div>
    <hr class="basic-line" />
  </div>
</div>
<div class="row" >
  <div class="col-xs-3 col-sm-3 col-lg-3 wrap">
    <div class="input-group">
      <input class="form-control"  id="name" ng-change="functions.Filter.name()" placeholder="Buscar por nombre" ng-model="data.filter.name" />
      <span class=" input-group-addon" id="basic-addon1" >
        <i class="fa fa-search"></i>
      </span>
    </div>
  </div>

  <div class="col-md-offset-3 col-xs-3 col-sm-3 col-lg-6 text-right wrap form-inline">
    <div class="input-group"
         moment-picker="data.filter.dateinitial"
         format="YYYY-MM-DD">

      <input class="form-control"
             placeholder="Seleccionar fecha inicial"
             ng-model="data.filter.dateinitial"
             ng-model-options="{ updateOn: 'blur' }">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-calendar"></i>
      </span>
    </div>
    <div class="input-group"
         moment-picker="data.filter.dateend"
         format="YYYY-MM-DD">

      <input class="form-control"
             placeholder="Seleccionar fecha final"
             ng-model="data.filter.dateend"
             ng-model-options="{ updateOn: 'blur' }">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-calendar"></i>
      </span>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
    <a ng-click="functions.redirect('landingpage#/')" class="button btn button default-inverted">Regresar</a>
    <a ui-sref="create" class="button btn button success-inverted">Crear categoría</a>
  </div>
</div>
<div class="row" >
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="misc.list.items.length > 0">
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.Pagination.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.Pagination.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>[[misc.list.total]]
            </b> registros </span><span>Página <b>[[ data.page ]]
            </b> de <b>
              [[ (misc.list.total_pages ) ]]
            </b></span>
        </li>
        <li   ng-class="data.page == (misc.list.total_pages) || misc.list.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.Pagination.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0 ? 'disabled'  : ''">
          <a  href="#/" ng-click="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.Pagination.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div> 
    <table class="table table-bordered sticky-enabled">
      <thead class="theader">
        <tr>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>

        <tr class="repeat-item" data-ng-repeat="i in misc.list.items track by $index" data-ng-class="i.status == 0 ? 'danger letter-no-hover':''">
          <td style="width: 40%">
            <b class="medium-text">[[i.name]]</b><br>
            <em class="extra-small-text">Creado por <b>[[i.createdBy]]</b> el día <b >[[(i.created * 1000)| date:'dd-MM-yyyy HH:mm']]</b> <br>
              Actualizado por <b>[[i.updatedBy]]</b> el día <b>[[(i.created * 1000)| date : 'dd-MM-yyyy HH:mm']]</b></em>
          </td>
          <td>
            <p>[[i.description]]</p>
          </td>
          <td class="text-right">
            <a href="" data-ng-click="functions.confirmDelete($index)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
              <span class="glyphicon glyphicon-trash"></span>
            </a>
            <a ui-sref="edit({idLandingPageCategory:[[i.idLandingPageCategory]]})" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar categoría">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.Pagination.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.Pagination.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>[[misc.list.total]]
            </b> registros </span><span>Página <b>[[ data.page ]]
            </b> de <b>
              [[ (misc.list.total_pages ) ]]
            </b></span>
        </li>
        <li   ng-class="data.page == (misc.list.total_pages) || misc.list.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? true  : false || data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.Pagination.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.Pagination.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div> 
  </div>
  <div ng-show="misc.list.items.length == 0">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay registros de categorías de encuestas que coincidan con los filtros, para crear una haga <a ui-sref="create"><u>Clic aquí</u></a>.
          </h2>    
          </h2>    
        </div>
      </div>
    </div>
  </div>
</div>


<div id="deleteCategory" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>¿Esta seguro?</h2>
      <div>
        Si elimina la categoría <strong>[[data.categorySelected.name]]</strong> no se podrán recuperar.
      </div>
      <br>
      <div>
        <a ng-click="functions.Modals.hide('deleteCategory');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a href="#" ng-click="restServices.deleteCategory(data.categorySelected.idLandingPageCategory)" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>

