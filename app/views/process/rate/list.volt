
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Lista de tarifas
      </div>
      <p>
        Aquí encontrará el listado de las tarifas con su respectivo plan y los rangos para cada tarifa.  
      </p>
      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 none-padding-left">
        <div class="input-group">
          <input class="form-control"  id="name" placeholder="Buscar por Nombre de la tarifa"  ng-model="data.filter.name" />
          <div class="input-group-btn">
            <button type="button" class="btn btn-default" ng-click="functions.Filter.name()">
              <i class="fa fa-search"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 none-padding-left">
        <div class="input-group">
          <input class="form-control"  id="idRate" placeholder="Buscar por codigo de la tarifa"  ng-model="data.filter.idRate" />
          <div class="input-group-btn">
            <button type="button" class="btn btn-default" ng-click="functions.Filter.idRate()">
              <i class="fa fa-search"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right pull-right">
        <a href="{{ url('tools') }}">
          <button class="button  btn btn-sm default-inverted">
            Regresar
          </button>
        </a>
        <a href="#/create">
          <button class="button  btn btn-sm success-inverted" style="margin-right: 0px;">
            Crear tarifa nueva
          </button>
        </a>
      </div>
    </div>
        
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
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{'{{misc.list.total}}'}}
              </b> registros </span><span>Página <b>{{'{{rate.page}}'}}
              </b> de <b>
                {{'{{(misc.list.total_pages )}}'}}
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
        <tbody ng-repeat="rate in misc.list.items track by $index">
          <tr>
            <td style="width: 40%">
              <b class="medium-text">{{'{{rate.name}}'}}</b><br>
              <em class="extra-small-text">Creado por <b>{{'{{rate.createdBy}}'}}</b> el día <b >{{'{{ (rate.created * 1000) | date:"dd-MM-yyyy HH:mm" }}'}}</b> <br>
                Actualizado por <b>{{'{{rate.updatedBy}}'}}</b> el día <b>{{'{{ (rate.updated * 1000) | date:"dd-MM-yyyy HH:mm" }}'}}</b></em>
            </td>
            <td>
              <p>{{'{{rate.description}}'}}</p>
            </td>
            <td class="text-right">
              <a href="" data-ng-click="functions.deleted(rate.idRate)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
                <span class="glyphicon glyphicon-trash"></span>
              </a>
              <a ui-sref="edit({idRate:{{'{{rate.idRate}}'}} })" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar categoría">
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
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{'{{misc.list.total}}'}}
              </b> registros </span><span>Página <b>{{'{{ data.page }}'}}
              </b> de <b>
                {{'{{ (misc.list.total_pages ) }}'}}
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
              La lista de tarifas se encuentra vacía, para crear una nueva tarifa haga <a href="#/create">clic aquí</a>.
            </h2>    
            </h2>    
          </div>
        </div>
      </div>
    </div>
                
  </div>

