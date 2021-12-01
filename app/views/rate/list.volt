<div ng-cloak>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Lista de tarifas
      </div>
      <p>
        Aquí encontrará el listado de las tarifas con su respectivo plan y los rangos para cada tarifa.  
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">
      <div class="input-group">
        <input class="form-control" id="name" placeholder="Buscar por nombre" data-ng-model="data.filter.name" data-ng-change="restServices.getAll()" />
        <span class=" input-group-addon" id="basic-addon1" >
          <i class="fa fa-search"></i>
        </span>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 wrap">
      <div class="input-group">
        <input class="form-control" id="name" placeholder="Buscar por codigo" data-ng-model="data.filter.idRate" data-ng-change="restServices.getAll()" />
        <span class=" input-group-addon" id="basic-addon1" >
          <i class="fa fa-search"></i>
        </span>
      </div>
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
                              ng-change="restServices.getAll()"
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
                              ng-change="restServices.getAll()"
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-lg-12 text-right wrap">
      <a href="{{ url('tools') }}" class="button shining btn btn default-inverted">Regresar</a>
      <a href="#/create" class="btn btn-md success-inverted">Crear tarifa nueva</a>
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
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody ng-repeat="rate in misc.list.items track by $index">
          <tr>
            <td style="width: 40%">
              <b class="medium-text">{{'{{rate.name}}'}}</b><br>
              <div class="small-text">
                <em>
                  <b>Código de la tarifa:</b> {{ '{{rate.idRate}}' }}
                </em>
              </div>
              <div class="small-text">
                <em>
                  <b>Servicio:</b> {{ '{{rate.services}}' }}
                </em>
              </div>
              <div class="small-text">
                <em>
                  {{ '{{rate.accountingMode}}' }}
                </em>
              </div>
              <div class="small-text">
                <em>
                  <b>Plan de Pagos:</b> {{ '{{rate.planType}}' }}
                </em>
              </div>
            </td>
            <td>
              <p>{{'{{rate.description}}'}}</p>
              <em class="extra-small-text">Creado por <b>{{'{{rate.createdBy}}'}}</b> el día <b >{{'{{ rate.created }}'}}</b> <br>
                Actualizado por <b>{{'{{rate.updatedBy}}'}}</b> el día <b>{{'{{ rate.updated }}'}}</b></em>
            </td>
            <td class="text-right">
              <a href="" data-ng-click="functions.confirmDelete(rate.idRate)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
                <span class="glyphicon glyphicon-trash"></span>
              </a>
              <a ui-sref="edit({idRate:{{'{{rate.idRate}}'}} })" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar categoría">
                <span class="glyphicon glyphicon-pencil"></span>
              </a>
              <a href="" ng-click="restServices.reportMail()" class="button btn btn-xs-round primary-inverted" data-toggle="tooltip" data-placement="top" title="Ver estadísticas">
                <span class="fa fa-bar-chart"></span>
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
  <div id="somedialog" class="dialog">
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
          ¿Esta seguro de que desea eliminar la tarifa ?
        </div>
        <br>
        <div>
          <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="restServices.deletedRate()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>       
</div>
<script>
  $(function () {
    setTimeout(function () {
      $('[data-toggle="tooltip"]').tooltip();
    }, 1000);
  });
  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }
</script>
