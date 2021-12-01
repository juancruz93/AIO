  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Detalle de envíos Realizados
      </div>
      <hr class="basic-line" />
    </div>
  </div>


  <div class="row">
    <div class="col-xs-12 col-sm-12 col-lg-12 text-right wrap">
      <a href="{{ url('reports/index') }}" class="button shining btn btn default-inverted">Regresar</a>
    </div>
  </div>
  
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right none-padding center-div">
    <div class="col-md-3 col-lg-5 col-sm-3 col-xs-4 dateFinalBox">
      <div class="input-group"
           format="YYYY-MM-DD">

        <input id="valuedateInitial" class="form-control"
               moment-picker="data.filter.valuedateInitial"
               placeholder="Seleccionar fecha inicial"
               ng-model="data.filter.valuedateInitial"              
               ng-model-options="{ updateOn: 'blur' }">
        <span class="input-group-addon">
          <i class="glyphicon glyphicon-calendar"></i>
        </span>
      </div>
    </div>
    <div class="col-md-3 col-lg-5 col-sm-3 col-xs-3 dateFinalBox">
      <div class="input-group"         
           format="YYYY-MM-DD">

        <input id="valuedateFinal" class="form-control"
               moment-picker="data.filter.valuedateFinal"
               placeholder="Seleccionar fecha final"
               ng-model="data.filter.valuedateFinal"             
               ng-model-options="{ updateOn: 'blur' }">
        <span class="input-group-addon">
          <i class="glyphicon glyphicon-calendar"></i>
        </span>
      </div>
    </div>
    <div class="">
      <button class="button  btn btn-md warning-inverted" ng-click="functions.cleanFilters()" 
              data-toggle="tooltip" data-placement="top" title="Eliminar los filtros">
        <i class="fa fa-eraser"></i>
      </button>
      <button class="button  btn btn-md primary-inverted" ng-click="functions.searchDate()"
              data-toggle="tooltip" data-placement="top" title="Buscar">
        <i class="fa fa-search"></i>
      </button>
    </div>
  </div>
    
  <div ng-show="misc.list.items.length > 0">
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a ng-click="data.page == 1 ? true  : false || functions.Pagination.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="data.page == 1 ? 'disabled'  : ''">
          <a ng-click="data.page == 1 ? true  : false || functions.Pagination.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{'{{misc.list.total}}'}}
            </b> registros </span><span>Página <b>{{'{{data.page}}'}}
            </b> de <b>
              {{'{{(misc.list.total_pages )}}'}}
            </b></span>
        </li>
        <li   ng-class="data.page == (misc.list.total_pages) || misc.list.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.Pagination.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.Pagination.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>   
    <table class="table table-bordered table-responsive" id="resultTable">
      <thead class="theader">
        <tr style="border-left: solid 4px #474646;">
          <th>Correo Remitente</th>
          <th>Fecha/Hora de Llegada del Email</th>
          <th>Fecha/Hora de envios de SMS</th>
          <th>Tipo envio</th>
          <th>Cantidad SMS</th>
          <th>Indicativo</th>
          <th>Movil</th>
          <th>Asunto</th>
          <th>Mensaje</th>
          <th>Descargar detalle de envio</th>
        </tr>
      </thead>
      <tbody ng-repeat="key in misc.list.items track by $index">
        <tr>
          <td>
            {{"{{ key.senderEmail }}"}}
          </td>
          <td>
            {{"{{ key.startdate }}"}}
          </td>
          <td>
            {{"{{ key.startdate }}"}}
          </td>
          <td>
            {{"{{ key.typeShipping }}"}}
          </td>
          <td>
            {{"{{ key.quantitySms }}"}}
          </td>
          <td>
            {{"{{ key.indicative }}"}}
          </td>
          <td>
            {{"{{ key.phone }}"}}
          </td>
          <td>
            {{"{{ key.name }}"}}
          </td>
          <td>
            {{"{{ key.message }}"}}
          </td>
          <td>
            <a href="{{url('report/downloadsmxemail/')}}{{'{{key.idSms}}'}}" class="button btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Descargar Reporte">
              <span class="fa fa-download"></span>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a ng-click="data.page == 1 ? true  : false || functions.Pagination.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a ng-click="data.page == 1 ? true  : false || functions.Pagination.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{'{{misc.list.total}}'}}
            </b> registros </span><span>Página <b>{{'{{ data.page }}'}}
            </b> de <b>
              {{'{{ (misc.list.total_pages ) }}'}}
            </b></span>
        </li>
        <li ng-class="data.page == (misc.list.total_pages) || misc.list.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="data.page == (data.list.total_pages)  || data.list.total_pages == 0  ? true  : false || data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.Pagination.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li ng-class="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.Pagination.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>           
  </div>
  <br>
  <div ng-show="misc.list.items.length == 0" class="row">
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
