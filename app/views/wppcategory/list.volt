<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 wrap">
    <div class="title">
      Listado de categorías de envío de WhatsApp
    </div>
    <hr class="basic-line" />
    <p>En esta listado podrá ver, crear y editar categorías de WhatsApp</p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="col-sm-4 col-lg-4 col-md-4 col-xs-4 searchBoxFilter">
      <div class="input-group">
        <input class="form-control" id="name" ng-change='search()' placeholder="Buscar por nombre"
          ng-model="data.filter.name" />
        <span class=" input-group-addon" id="basic-addon1">
          <i class="fa fa-search"></i>
        </span>
      </div>
    </div>
    <div class="col-md-offset-1 col-md-3 col-lg-3 col-sm-3 col-xs-3 dateInitialBox">
      <!-- campo fecha inicial -->
      <div class="dropdown form-group dropdown-start-parent">
        <a class="dropdown-toggle" id="dropdownStart" role="button" data-toggle="dropdown"
          data-target=".dropdown-start-parent" href="#">
          <div class="input-group date">
            <input type="text" class="form-control" placeholder="Fecha Inicial" readonly="true"
              ng-model="data.filter.dateinitial" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
          </div>
        </a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
          <datetimepicker data-ng-model="data.filter.dateinitial"
            data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'end-date-changed', startView: 'month', minView: 'day', modelType: 'YYYY-MM-DD' }">
          </datetimepicker>
        </ul>
      </div>
    </div>

    <div class="col-md-3 col-lg-3 col-sm-3 col-xs-3 dateFinalBox">
      <!-- campo fecha Final -->
      <div class="dropdown form-group dropdown-end-parent">
        <a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown"
          data-target=".dropdown-end-parent" href="#">
          <div class="input-group date">
            <input type="text" class="form-control" placeholder="Fecha Final" readonly="true"
              ng-model="data.filter.dateend" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
          </div>
        </a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
          <datetimepicker data-ng-model="data.filter.dateend"
            data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'end-date-changed', startView: 'month', minView: 'day', modelType: 'YYYY-MM-DD' }">
          </datetimepicker>
        </ul>

      </div>
    </div>

    <div class="col-md-1 col-lg-1 col-xs-1 col-sm-1 text-right refreshBoxFuera">
      <div class="dropdown form-group dropdown-end-parent refreshBoxDentro">
        <button type="button" title="Borrar Filtros" data-ng-click="functions.refresh()"
          class="btn btn-danger glyphicon glyphicon-erase warning-inverted"></button>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class=" col-sm-2 col-lg-12 wrap text-right">
    <a href="{{url('whatsapp#/')}}" class="button shining btn btn-sm default-inverted">Regresar al listado de
      WhatsApp</a>
    <a href="{{url('wppcategory/create')}}" class="button shining btn btn-md success-inverted">Crear una nueva categoría
      de WhatsApp</a>
  </div>
</div>

<div class="row">
  <div ng-show="data.wppcategory.items.length > 0" class="">
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i
              class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i
              class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b>
              <script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.wppcategory.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
            </b> de <b>
              {{ "{{ (data.wppcategory.total_pages ) }}"}}
            </b></span>
        </li>
        <li
          ng-class="data.page == (data.wppcategory.total_pages)  || data.wppcategory.total_pages == 0  ? 'disabled'  : ''">
          <a href="#/"
            ng-click="data.page == (data.wppcategory.total_pages)  || data.wppcategory.total_pages == 0  ? true  : false || functions.forward()"
            class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li
          ng-class="data.page == (data.wppcategory.total_pages)  || data.wppcategory.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="data.page == (data.wppcategory.total_pages)  || data.wppcategory.total_pages == 0  ? true  : false || functions.fastforward()"
            class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <table class="table table-bordered">
        <col width="50">
        <thead class="theader ">
          <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr ng-repeat="wppcategory in data.wppcategory.items">

            <td><b>{{ '{{wppcategory.idWppCategory}}' }}</b></td>

            <td>
              <div class="medium-text"  ng-if="wppcategory.status == 0" style="color:red;">{{ '{{wppcategory.name}}' }}</div>
              <div class="medium-text"  ng-if="wppcategory.status == 1">{{ '{{wppcategory.name}}' }}</div>
              <dd class="small-text"> </dd>
              <dd> <em class="extra-small-text">Creado por <strong>{{"{{(wppcategory.createdBy)}}"}}</strong> , el
                  <strong>{{"{{wppcategory.created}}"}}</strong></em></dd>
              <dd> <em class="extra-small-text">Actualizado por <strong> {{"{{(wppcategory.updatedBy)}}"}}</strong>, el
                  <strong>{{"{{wppcategory.updated}}"}}</strong></em></dd>
            </td>

            <td><div class="small-text"><em>{{ '{{wppcategory.description}}' }}</em></div></td>

            <td>
              <div class="pull-right">
                <a href="{{url('wppcategory/edit')}}/{{'{{wppcategory.idWppCategory}}'}}"
                  class="button shining btn btn-xs-round shining-round round-button primary-inverted"
                  data-toggle="tooltip" data-placement="left" title="Editar base de datos">
                  <span class="glyphicon glyphicon-pencil"></span>
                </a>
                <a style="cursor:pointer;" ng-click="openModal(wppcategory.idWppCategory)"
                  class="button shining btn btn-xs-round shining-round round-button danger-inverted"
                  data-toggle="tooltip" data-placement="left" title="Eliminar base de datos">
                  <span class="glyphicon glyphicon-trash"></span>
                </a>
              </div>
            </td>

          </tr>
        </tbody>
      </table>
    </div>
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i
              class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i
              class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b>
              <script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.wppcategory.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
            </b> de <b>
              {{ "{{ (data.wppcategory.total_pages ) }}"}}
            </b></span>
        </li>
        <li
          ng-class="data.page == (data.wppcategory.total_pages)  || data.wppcategory.total_pages == 0  ? 'disabled'  : ''">
          <a href="#/"
            ng-click="data.page == (data.wppcategory.total_pages)  || data.wppcategory.total_pages == 0  ? true  : false || functions.forward()"
            class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li
          ng-class="data.page == (data.wppcategory.total_pages)  || data.wppcategory.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="data.page == (data.wppcategory.total_pages)  || data.wppcategory.total_pages == 0  ? true  : false || functions.fastforward()"
            class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
  </div>
</div>
<br>

<div ng-show="data.wppcategory.items.length == 0">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2>
          No hay registros de categoria de WhatsApp, para crear una haga <a href="wppcategory/create">clic aquí</a>.
        </h2>
        </h2>
      </div>
    </div>
  </div>
</div>

<div id="deleteDialog" class="dialog ">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape ">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
        preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276" />
      </svg>
    </div>
    <div class="dialog-inner dialog-padding">
      <p ng-show="PlaneError.error == 1">{{'{{PlaneError.msg}}'}}</p>
      <div class="body row text-center">
        <h3>¿Desea eliminar la categoria?</h3>
      </div>
      <div>
        <h5>Si se elimina la categoría no la podrá volver a usar pero seguirá asociada a los envíos en los que la haya
          usado.</h5>
      </div>
      <div class="body row" style="padding-top: 1em;">
        <a ng-click="closeModal()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a ng-click="deleteCategory()" id="btn-ok" class="button shining btn btn-md success-inverted">Eliminar</a>
      </div>
    </div>
  </div>
</div>