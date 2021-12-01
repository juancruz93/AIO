<style>
  .width-30{
    width: 30%
  }
  .width-50{
    width: 50%
  }
  .width-20{
    width: 20%
  }
</style>
<div ng-cloak>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Lista de categorías de cuenta
      </div>
      <hr class="basic-line">
      <p>
        Las categorías de cuenta le ayudarán a organizar de manera práctica los registros de las cuentas
      </p>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 none-padding-left">
        <div class="form-inline">
          <div class="form-group">
            <div class="input-group">
              <input type="text" class="form-control" id="exampleInputAmount" placeholder="Buscar por nombre" autofocus="true" data-ng-model="filter.name" data-ng-change="filtername()">
              <div class="input-group-addon"><i class="fa fa-search"></i></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right none-padding">
        <a href="{{ url('tools') }}" class="button shining btn btn default-inverted">Regresar</a>
        <a ui-sref="create" class="button shining btn btn success-inverted">Crear una nueva categoría</a>
      </div>
    </div>
  </div>

  <div class="row" >
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="list.items.length > 0">
      <table class="table table-bordered sticky-enabled">
        <thead class="theader">
          <tr>
            <th class="width-30">Nombre</th>
            <th class="width-50">Detalle</th>
            <th class="width-20">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr data-ng-repeat="i in list.items" data-ng-class="i.status == 0 ? 'danger letter-no-hover':''">
            <td>
              <b class="medium-text">{{"{{i.name}}"}}</b>
              <br>
              <em class="bold" ng-show="i.expirationDate != 0">
                Solicitará fecha de expiración
              </em>
            </td>
            <td>
              <p>
                {{"{{i.description}}"}}
              <p>
                <br>
                <em class="extra-small-text">Creado por <b>{{"{{i.createdBy}}"}}</b> el día {{"{{i.created}}"}} <br>
                  Actualizado por <b>{{"{{i.updatedBy}}"}}</b> el día {{"{{i.updated}}"}}</em>
            </td>
            <td>
              <a href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar categoría" data-ng-click="confirmDelete(i.idAccountCategory)">
                <span class="glyphicon glyphicon-trash"></span>
              </a>
              <a ui-sref="edit({id:i.idAccountCategory})" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar categoría">
                <span class="glyphicon glyphicon-pencil"></span>
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
              No hay registros de categorías de cuentas que coincidan con los filtros, para crear una haga <a ui-sref="create"><u>Clic aquí</u></a>.
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

  <div id="somedialog" class="dialog ng-scope">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"></rect>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>¿Esta seguro?</h2>
        <div>
          Debe tener en cuenta que si elimina la categoría ya no la podrá volver a utilizar ni ver
        </div>
        <br>
        <div>
          <a onclick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close="">Cancelar</a>
          <a href="#/" data-ng-click="deleteAccountCategory()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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