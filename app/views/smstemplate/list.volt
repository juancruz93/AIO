<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="title">
      Lista de plantillas predefinidas de SMS
    </div>            
    <hr class="basic-line">
    <p>
      Las plantillas predefinidas le serán útiles en el momento de crear contenido de un SMS, ya que tendrás
      con que partir y solo necesitaras hacer algunos retoques si lo consideras necesario.
    </p>            
  </div>
</div>
<div class="clearfix"></div>
<div class="space"></div>   
<div class="row">
  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">            
    <div class="form-inline">
      <div class="form-group" style="margin-right: 5%">
        <div class="input-group">
          <input type="text" class="form-control" id="exampleInputAmount" placeholder="Buscar por nombre" autofocus="true" data-ng-model="data.namesmstemp" data-ng-keyup="filtername()">
          <div class="input-group-addon"><i class="fa fa-search"></i></div>
        </div>
      </div>
      <div class="form-group">
        <label for="smstempcat">Categorías</label>
        <select class="chosen form-control input-lg" style="width: 270px" data-ng-model="data.smstempcat" data-ng-change="filterCateg()">
          <option value=""></option>
          <option value="0">Todas las categorías</option>
          <option ng-repeat="x in listcateg" value="{{"{{x.idSmsTemplateCategory}}"}}">{{"{{x.name}}"}}</option>
        </select>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  text-right">   
    <a href="{{url('smstwoway#/')}}" class="button shining btn btn-sm default-inverted">Regresar al listado de SMS doble-vía</a>
    <a href="{{url('sms')}}" class="button shining btn btn-sm default-inverted">Regresar al listado de SMS</a>
    <a href="#/create" class="button shining btn btn-sm success-inverted">Crear plantilla prediseñada</a>
  </div>
</div>
<div class="clearfix"></div>
<div class="space"></div>   
<div class="row" >
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-show="list.items.length > 0">
    <table class="table table-bordered sticky-enabled">
      <thead class="theader">
        <tr>
          <th>Detalle</th>
          <th>Contenido</th>
          <th style="width: 10%">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr data-ng-repeat="i in list.items">
          <td style="white-space: nowrap">
            <strong class=" ng-binding medium-text">
              {{"{{i.name}}"}}   
            </strong>
            <dl>
              <dd class="small-text">Categoria: {{"{{i.nameSmsTemplateCategory}}"}}</dd>
              <dd> <em class="extra-small-text">Creado por <strong>{{"{{i.createdBy}}"}}</strong> , el <strong>{{"{{i.createdDate}}"}}</strong> a las <strong>{{"{{i.createdHour}}"}}</strong></em></dd>
              <dd> <em class="extra-small-text">Actualizado por <strong> {{"{{i.updatedBy}}"}}</strong>, el <strong>{{"{{i.updatedDate}}"}}</strong> a las <strong>{{"{{i.updatedHour}}"}}</strong></em></dd>
            </dl>
          </td>
          <td>
            <p>{{"{{i.content}}"}}</p>
          </td>
          <td class="text-right">
            <a href="" ng-click="confirmDelete(i.idSmsTemplate)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
              <span class="glyphicon glyphicon-trash"></span>
            </a>
            <a href="{{url('smstemplate#/edit')}}/{{"{{i.idSmsTemplate}}"}}" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar categoría">
              <span class="glyphicon glyphicon-pencil"></span>
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
              Si elimina esta plantilla no se podrán recuperar los datos
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
            No hay registros de plantillas que coincidan con los filtros, para crear una haga <a href="#/create"><u>Clic aquí</u></a>.
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

<script>
  $(function () {
    setTimeout(function () {
      $('[data-toggle="tooltip"]').tooltip();
    }, 1000);

    $(".chosen").select2({
      placeholder: 'Seleccione una categoría'
    });
  });

  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }
</script>
