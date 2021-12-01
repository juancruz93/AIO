<style>
  .select2{
    z-index: 10 !important;
  }
</style>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de plantillas predefinidas
    </div>            
    <hr class="basic-line">
    <p>
      Las plantillas prediseñadas. le serán útiles en el momento de crear contenido de correo, ya que tendrás que
      con que partir y solo necesitaras hacer algunos retoques. La plataforma tiene una gran variedad de diseños
      profesionales, pero también puedes crear tus propias plantillas con tu propio estilo.
    </p>            
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-6 col-md-6 wrap">
    <div class="form-inline">
      <div class="form-group">
        <div class="input-group">
          <input type="text" class="undeline-input form-control" id="exampleInputAmount" placeholder="Buscar por nombre" data-ng-model="data.namemailtemp" data-ng-keyup="filtername()">
          <div class="input-group-addon"><i class="fa fa-search"></i></div>
        </div>
      </div>
      <div class="form-group">
        <label for="mailtempcateg">Categorías</label>
        <select class="chosen form-control input-lg" style="width: 230px; z-index: 10 !important; " data-ng-model="data.mailtempcat" data-ng-change="filterCateg()">
          <option value=""></option>
          <option value="0">Todas las categorías</option>
          <optgroup label="{{"{{key == 'globalCategory' ? 'Categoría del sistema' : key == 'accountCategory' ? 'Categorías de cuenta' : 'Categorías de aliado'}}"}}" data-ng-repeat="(key,value) in liscateg">
            <option data-ng-repeat="x in value" value="{{"{{x.id}}"}}">{{"{{x.text}}"}}</option>
          </optgroup>
        </select>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-md-6 text-right wrap">
    <a href="{{url("mail")}}" class="button shining btn btn-sm default-inverted">Regresar al listado de envios</a>
    <a href="{{ url('mailtemplatecategory#/') }}" class="button shining btn btn-sm warning-inverted">Categorías de plantillas</a>
    <a href="#/create" class="button shining btn btn-sm success-inverted">Crear una nueva plantilla</a>
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

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-info">
      <div class="body row">
        <div class="wrap">
          <md-progress-linear md-mode="query" data-ng-show="loader" class="md-warn"></md-progress-linear>
        </div>
        <div ng-show="list.items.length > 0" data-ng-hide="loader">
          <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3" data-ng-repeat="i in list.items">
            <div class="thumbnail" style="height: 360px" style="position: relative">
              <img src="{{url('')}}{{"{{i.dirImage}}"}}?{{"{{imagenTime}}"}}" />
              <div class="caption text-center">
                <di>
                  <dd><strong class="small-text">{{"{{i.name}}"}}</strong></dd>
                  <dd><span class="smaill-text">Categoria: {{"{{i.nameMailTemplateContent}}"}}</span></dd>
                </di>
                <div role="group" style="top: 295px; position: absolute; width: 80%;
                     height: 190px;
                     margin: 10px;
                     padding:5px;" >
                  {#<button type="button" class="btn warning-inverted">
                    <i class="fa fa-star"></i>
                    <md-tooltip md-direction="bottom">
                      Star
                    </md-tooltip>
                  </button>#}
                  <button type="button" class="btn default-inverted toltip" data-ng-click="previewmailtempcont(i.idMailTemplateContent);" data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-eye"></i>
                    <md-tooltip md-direction="bottom">
                      Previsualizar
                    </md-tooltip>
                  </button>
                  <a href="{{url('mailtemplate/')}}edit/{{"{{i.idMailTemplate}}"}}" class="btn info-inverted" ng-show="i.global == 0 && i.idAccount != null || {% if user.Usertype.Allied.idAllied is defined%}{{user.Usertype.Allied.idAllied}}{%else%} false {%endif%} == i.idAllied ">
                    <i class="fa fa-pencil"></i>
                    <md-tooltip md-direction="bottom">
                      Editar
                    </md-tooltip>
                  </a>
                  <button type="button" class="btn danger-inverted" data-ng-click="confirmDelete(i.idMailTemplate)" ng-show="{% if user.Usertype.Subaccount.idSubaccount is defined %}false{%else%} true {%endif%} && i.global == 0 && i.idAccount != null || {% if user.Usertype.Allied.idAllied is defined%}{{user.Usertype.Allied.idAllied}}{%else%} false {%endif%} == i.idAllied">
                    <i class="fa fa-trash"></i>
                    <md-tooltip md-direction="bottom">
                      Eliminar
                    </md-tooltip>
                  </button>
                </div>
              </div>   
            </div>
          </div>
        </div>
        <div ng-show="list.items.length == 0">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="block block-success">
              <div class="body success-no-hover text-center">
                <h2>
                  No hay registros de plantillas que coincidan con los filtros, para crear una haga <a href="#/create">clic aquí</a>.
                </h2>    
                </h2>    
              </div>
            </div>
          </div>
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-prevew-width">
    <div class="modal-content modal-prevew-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Previsualización de plantilla</h4>
      </div>
      <div class="modal-body modal-prevew-body" id="preview-modal" style="height: 550px;"></div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="button btn btn-sm danger-inverted">Cerrar</button>
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
        Debe tener en cuenta que si elimina la plantilla ya no la podrá volver a utilizar ni ver
      </div>
      <br>
      <div>
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a href="#" ng-click="delete()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>
<script>
  $(function () {
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
