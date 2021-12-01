<div ng-cloak>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Lista de configuración de Email para envíos de SMS.
      </div>
      <p>
        Aquí encontrará el listado de las tarifas con su respectivo plan y los rangos para cada tarifa.  
      </p>
    </div>
  </div>
{#  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">
      <div class="input-group">
        <input class="form-control" id="name" placeholder="Buscar por nombre" data-ng-model="data.filter.name" data-ng-change="restServices.getAll()" />
        <span class=" input-group-addon" id="basic-addon1" >
          <i class="fa fa-search"></i>
        </span>
      </div>
    </div>
  </div>#}

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-lg-12 text-right wrap">
      <a href="{{ url('tools') }}" class="button shining btn btn default-inverted">Regresar</a>
      <a href="#/create" class="btn btn-md success-inverted">Crear Configuración de Email para envíos de SMS nueva.</a>
    </div>
  </div>

  <div class="row">
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
            <th>Correo de Remitente</th>
            <th>Correo de Notidicacion</th>
            <th>Detalle</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody ng-repeat="smsxemail in misc.list.items track by $index">
          <tr>
            <td style="width: 40%">
              <b class="medium-text">{{'{{smsxemail.senderEmail}}'}}</b><br>
            </td>
            <td style="width: 40%">
              <b class="medium-text">{{'{{smsxemail.notificationEmail}}'}}</b><br>
            </td>
            <td>
              <p>Tipo de Envio: {{'{{smsxemail.description}}'}}</p>
            </td>
            <td class="text-right">
              <a href="" data-ng-click="functions.confirmDelete(smsxemail.idSmsxEmail)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
                <span class="glyphicon glyphicon-trash"></span>
              </a>
              <a ui-sref="edit({idSmsxEmail:{{'{{smsxemail.idSmsxEmail}}'}} })" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar categoría">
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
</div>

