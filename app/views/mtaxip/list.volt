<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de MTA
    </div>            
    <hr class="basic-line">
    <p>
      Este listado de direcciones IP se definen para el uso del envió de los correos. 
    </p>            
  </div>
</div>
<div class="row" >
  <div class="col-xs-3 col-sm-3 col-lg-3 wrap">

  </div>

  <div class="col-md-offset-3 col-xs-3 col-sm-3 col-lg-6 text-right wrap form-inline">
    <div class="input-group"
         moment-picker="data.filter.dateinitial"
         format="YYYY-MM-DD">

      <input class="form-control"
             placeholder="Seleccionar fecha inicial"
             ng-model="data.filter.dateinitial"
             ng-model-options="{ updateOn: 'blur' }">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-calendar"></i>
      </span>
    </div>
    <div class="input-group"
         moment-picker="data.filter.dateend"
         format="YYYY-MM-DD">

      <input class="form-control"
             placeholder="Seleccionar fecha final"
             ng-model="data.filter.dateend"
             ng-model-options="{ updateOn: 'blur' }">
      <span class="input-group-addon">
        <i class="glyphicon glyphicon-calendar"></i>
      </span>
    </div>
    <div class="input-group">      
      <button type="button" title="Borrar Filtros" data-ng-click="functions.refresh()" class="btn btn-danger glyphicon glyphicon-erase warning-inverted" ></button>
    </div>
  </div>

</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
    <a href="tools#/" class="button shining btn btn-sm default-inverted">Regresar</a>
    <a href="#/create" class="button shining btn btn-sm success-inverted">Crear nuevo MTA </a>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="misc.list.total > 0">
  <ul class="pagination">
    <li ng-class="data.page == 1 ? 'disabled'  : ''">
      <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="data.page == 1 ? 'disabled'  : ''">
      <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{misc.list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
        </b> de <b>
          {{ "{{ (misc.list.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/" ng-click="data.page == (list.total_pages)  || list.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>

<div class="row" >
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="misc.list.total > 0">
    <table class="table table-bordered sticky-enabled">
      <thead class="theader">
        <tr>
          <th>Nombre</th>
          <th>Detalle</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>

        <tr class="repeat-item" data-ng-repeat="i in misc.list.items">
          <td style="width: 40%">
            <b class="medium-text">{{"{{i.nameMta}}"}}</b><br>
            <em class="extra-small-text">Creado por <b>{{"{{i.createdBy}}"}}</b> el día <b >{{"{{i.created}}"}}</b> <br>
              Actualizado por <b>{{"{{i.updatedBy}}"}}</b> el día <b>{{"{{i.created}}"}}</b></em>
          </td>
          <td>
          Direcciones IP:
          <span data-ng-repeat="a in i.nameIp"> 
            <span ng-show="i.nameIp.length < 0">{{"{{contador=$index+1}}"}}</span>
            {{"{{a}}"}} <span ng-show="i.nameIp.length > contador">,</span>
          </span>
          </td>
          <td class="text-right">
            <a href="" data-ng-click="functions.confirmDelete(i.idMta)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
              <span class="glyphicon glyphicon-trash"></span>
            </a>
            <a href="{{url('mtaxip#/edit')}}/{{"{{i.idMta}}"}}" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar MTA">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div ng-show="misc.list.items.length == 0">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            No hay registros que coincidan con los filtros, para crear uno nuevo <a href="#/create"><u>Clic aquí</u></a>.
          </h2>    
          </h2>    
        </div>
      </div>
    </div>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="misc.list.total > 0">
  <ul class="pagination">
    <li ng-class="data.page == 1 ? 'disabled'  : ''">
      <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="data.page == 1 ? 'disabled'  : ''">
      <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{misc.list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
        </b> de <b>
          {{ "{{ (misc.list.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/" ng-click="data.page == (list.total_pages)  || list.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="data.page == (misc.list.total_pages)  || misc.list.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
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
        Debe tener en cuenta que si elimina el registro ya no lo podrá volver a utilizar ni ver
      </div>
      <br>
      <div>
        <a onclick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close="">Cancelar</a>
        <a href="#/" data-ng-click="restServices.deletemtaxip()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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