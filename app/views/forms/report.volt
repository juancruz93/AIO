<div class="row">
            <div class="clearfix"></div>
            <div class="space"></div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Reporte de inscritos por formulario: <b></b>
    </div>
    <hr class="basic-line">
    <p>
      <h2>{{'{{misc.nameForm}}'}}</h2>
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <table class="table table-bordered sticky-enabled" id="resultTable">
        <tr style="border-left: solid 4px #474646;">
          <th>Categoría:</th>
          <td>{{'{{misc.categoriForm}}'}}</td>
          <th>Número de suscripción:</th>
          <td>{{'{{misc.numSuscription}}'}}</td>
        </tr>
        <tr style="border-left: solid 4px #474646;">
          <th>Creado por:</th>
          <td>{{'{{misc.createFor}}'}}</td>
          <th>Actualizado por:</th>
          <td>{{'{{misc.updateFor}}'}}</td>
        </tr>
        <tr style="border-left: solid 4px #474646;">
          <th>Fecha de Creación:</th>
          <td>{{'{{misc.dateCreated}}'}}</td>
          <th>Fecha de actualización:</th>
          <td>{{'{{misc.updateDate}}'}}</td>
        </tr>
    </table>
  </div>
</div>

<div class="row wrap">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
      <div class="row">
        <div class="col-xs-2 col-sm-2 col-lg-5  text-left">
          <div class="input-group">
            <span  class=" input-group-addon cursor" id="basic-addon1" placement="top" class="" data-toggle="popover" title="Instrucciones"
                   data-content="Para filtrar por varios campos que pueden ser el correo, el número de móvil o cualquiera de los campos personalizados se deben de separar por comas sin importar el orden">
              <i class="fa fa-question-circle" aria-hidden="true" ></i>
            </span>
            <input class="form-control"  id="name" placeholder="Buscar por correo, nombre o apellido" ng-keyup="functions.searchcontacts()" ng-model="search" ng-disabled="misc.total <= 0"/>
          </div>
        </div>
        <div class="col-xs-10 col-sm-10 col-lg-7 text-right pull-right " >
          <a ui-sref="list()">
            <button class="button  btn btn-md default-inverted">
              <i class="fa fa-arrow-left"></i>
              Regresar
            </button>
          </a>
          <a ng-show="misc.total >0" href="javascript:void(0)">
            <button class="button  btn btn-md info-inverted" ng-click="functions.dowloadReport();">
              Descargar reporte
            </button>
          </a>
        </div>
      </div>
    </div>
  </div>
  <div ng-class="{'hidden' : misc.progressbar}" >
    <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
  </div>
  {#  <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>#}
  <div ng-show="misc.total >= 1">
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="misc.page == 1 ? 'disabled'  : ''">
          <a  href="javascript:void(0)" ng-click="misc.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="misc.page == 1 ? 'disabled'  : ''">
          <a href="javascript:void(0)"  ng-click="misc.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{misc.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ misc.page }}"}}
            </b> de <b>
              {{ "{{ (contacts[2].total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="misc.page == (contacts[2].total_pages) || contacts[2].total_pages == 0 ? 'disabled'  : ''">
          <a href="javascript:void(0)" ng-click="misc.page == (contacts.total_pages)  || contactlists.total_pages == 0  ? true  : false || misc.page == (contacts[2].total_pages)  || contacts[2].total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li  ng-class="misc.page == (contacts[2].total_pages)  || contacts[2].total_pages == 0 ? 'disabled'  : ''">
          <a href="javascript:void(0)" ng-click="misc.page == (contacts[2].total_pages)  || contacts[2].total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
            
    <!-- Table data -->
    <table class="table table-bordered sticky-enabled" id="resultTable">
      <thead class="theader">
        <tr style="border-left: solid 4px #474646;">
          <th ng-repeat="field in misc.arrayTitlesFil track by $index">
{#          <th ng-repeat="field in fieldspersonal track by $index" ng-if="field.id!='encabezado'&&field.id!='button'">#}
            {{'{{field.label}}'}}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr ng-repeat="contact in misc.arrayDataContacts">
{#          <td ng-repeat="(key, property) in contact" ng-if="key!='_id'">#}
          <td ng-repeat="(key, property) in contact track by $index" ng-if="key!='_id'">
            {{'{{property}}'}}
          </td>
        </tr>
      </tbody>
    </table>

    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="misc.page == 1 ? 'disabled'  : ''">
          <a  href="javascript:void(0)" ng-click="misc.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="misc.page == 1 ? 'disabled'  : ''">
          <a href="javascript:void(0)"  ng-click="misc.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{misc.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ misc.page }}"}}
            </b> de <b>
              {{ "{{ (contacts[2].total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="misc.page == (contacts[2].total_pages) || contacts[2].total_pages == 0 ? 'disabled'  : ''">
          <a href="javascript:void(0)" ng-click="misc.page == (contacts[2].total_pages)  || contacts[2].total_pages == 0  ? true  : false || misc.page == (contacts[2].total_pages)  || contacts[2].total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="misc.page == (contacts[2].total_pages)  || contacts[2].total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="misc.page == (contacts[2].total_pages)  || contacts[2].total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
  </div>
  <br>
  <div ng-show="misc.total <= 0" class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            Aún no se han registrado contactos mediante el formulario
          </h2>
          <div ng-hide="total != 1" class="row">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>