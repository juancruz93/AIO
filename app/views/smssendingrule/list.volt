<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Listado de reglas de envío de SMS
    </div>            
    <hr class="basic-line">
    <p>
      Aquí encontrará el listado de reglas de envío de SMS, que se servirán para envíar los SMS por un canal determinado dependiendo de su prefijo e indicativo.
    </p>            
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap text-left">
    <div class="form-inline">
      <div class="form-group">
        <div class="input-group">
          <input type="text" class="undeline-input form-control" id="exampleInputAmount" placeholder="Buscar por nombre" autofocus="true" data-ng-model="filterName" data-ng-change="searchForName()">
          <div class="input-group-addon"><i class="fa fa-search"></i></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap text-right" data-ng-show="list.items.length > 0">
    <a ui-sref="create" class="shining btn success-inverted">Crear nueva regla</a>
  </div>
</div>

<div class="row" >
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="list.items.length > 0">
    <table class="table table-bordered sticky-enabled">
      <thead class="theader">
        <tr>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr data-ng-repeat="i in list.items track by $index" data-ng-class="i.status == 0 ? 'danger letter-no-hover':''">
          <td style="width: 35%">
            <b class="medium-text">{{"{{i.name}}"}}</b><br>
            <b class="small-tex">{{"{{i.country}}"}}</b><br>
            <em class="extra-small-text">Creado por <b>{{"{{i.createdBy}}"}}</b> el día <b>{{"{{i.created}}"}}</b> <br>
              Actualizado por <b>{{"{{i.updatedBy}}"}}</b> el día <b>{{"{{i.updated}}"}}</b></em>
          </td>
          <td style="width: 50%">
            <p>{{"{{i.description}}"}}</p>
          </td>
          <td class="text-right">
            <a href="" data-ng-click="confirmDelete(i.idSmsSendingRule)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
              <span class="glyphicon glyphicon-trash"></span>
              <md-tooltip md-direction="top">
                Eliminar
              </md-tooltip>
            </a>
            <a ui-sref="edit({id:{{"{{i.idSmsSendingRule}}"}}})" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar categoría">
              <span class="glyphicon glyphicon-pencil"></span>
              <md-tooltip md-direction="top">
                Editar
              </md-tooltip>
            </a>
            <a ui-sref="show({id:{{"{{i.idSmsSendingRule}}"}}})" class="button shining btn btn-xs-round shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
              <span class="glyphicon glyphicon-eye-open"></span>
              <md-tooltip md-direction="top">
                Ver detalle
              </md-tooltip>
            </a>
          </td>
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
              Si elimina esta regla de envío de SMS no se podrán recuperar los datos
            </div>
            <br>
            <div>
              <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
              <a href="#" ng-click="deleteSmstemplate()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
            </div>
          </div>
        </div>
      </div>
      </tr>
      </tbody>
    </table>
  </div>
  <div ng-show="list.items.length == 0">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay registros de reglas de envío que coincidan con los filtros, para crear una haga <a ui-sref="create"><u>Clic aquí</u></a>.
          </h2>    
          </h2>    
        </div>
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

