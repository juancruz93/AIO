<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Contabilidad de las cuentas de aliado
    </div>            
    <hr class="basic-line">
    <p>
      Aquí encontrará el listado de la contabilidad
    </p>            
  </div>
</div>

<div class="row" ng-show="loader">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <md-progress-linear md-mode="query" class="md-warn"></md-progress-linear>
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

<div class="row" ng-show="list.items.length > 0">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap">
    <div class="table-responsive">
      <table class="table table-bordered sticky-enabled">
        <thead class="theader">
          <tr>
            <th></th>
            <th colspan="2">SMS</th>
            <th colspan="2">Envíos Email</th>
            <th colspan="2">Contactos</th>
          </tr>
          <tr>
            <th>Cuenta</th>
            <th>
              Mes anterior
            </th>
            <th>
              Mes actual
            </th>
            <th>
              Mes anterior
            </th>
            <th>
              Mes actual
            </th>
            <th>
              Mes anterior
            </th>
            <th>
              Mes actual
            </th>
          </tr>
        </thead>
        <tbody>
          <tr data-ng-repeat="i in list.items" data-ng-class="i.status == 0 ? 'danger':''">
            <td>{{"{{i.name}}"}}</td>
            <td>{{"{{i.lastTotalSmsSent}}"}}</td>
            <td>{{"{{i.currentTotalSmsSent}}"}}</td>
            <td>{{"{{i.lastTotalMailSent}}"}}</td>
            <td>{{"{{i.currentTotalMailSent}}"}}</td>
            <td>{{"{{i.lastTotalContacts}}"}}</td>
            <td>{{"{{i.currentTotalContacts}}"}}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div ng-show="list.items.length == 0">
  <div class="row">
    <div class="Fcol-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay registros de logs de actividades de la cuenta que coincidan con los filtros.
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