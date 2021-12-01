{% block css %}
  <style>
    .thumbnail:hover, #most{
      border: 1px solid #ff6e00;
    }
    #most{
      padding: 5px;
      position: absolute;
      top:30%;
      color: white;
      background-color: rgba(255, 110, 0, .9);
      z-index: 2;
      width: 100%;

    }
    .select2{
      z-index: 10 !important;
    }
  </style>
{% endblock %}
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de plantillas predeterminadas
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
          <input type="text" class="form-control" id="exampleInputAmount" placeholder="Buscar por nombre" data-ng-model="data.namemailtemp" data-ng-keyup="filtername()">
          <div class="input-group-addon"><i class="fa fa-search"></i></div>
        </div>
      </div>
      <div class="form-group">
        <label for="mailtempcateg">Categorías</label>
        <select class="chosen form-control input-lg" style="width: 230px; z-index: 10 !important; " data-ng-model="data.mailtempcat" data-ng-change="filterCateg()">
          <option value=""></option>
          <option value="0">Todas las categorías</option>
          <option ng-repeat="x in liscateg" value="{{"{{x.id}}"}}">{{"{{x.text}}"}}</option>
        </select>
      </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6 col-md-6 text-right wrap">
    <a href="{{ url('autoresponder#/birthday/') }}{{"{{idAutoresponder}}"}}" class="button shining btn btn-sm danger-inverted"><i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar</a>
  </div>
</div>

<div id="pagination" class="text-center" ng-show="list.items.length > 0">
  <ul class="pagination">
    <li ng-class="page == 1 ? 'disabled'  : ''">
      <a  href="#/selectautoresponder/{{"{{idAutoresponder}}"}}" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="page == 1 ? 'disabled'  : ''">
      <a href="#/selectautoresponder/{{"{{idAutoresponder}}"}}"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ page }}"}}
        </b> de <b>
          {{ "{{ (list.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/selectautoresponder/{{"{{idAutoresponder}}"}}" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="row">
      <div class="block block-info">
        <div class="body row">
          <div class="wrap">
            <md-progress-linear md-mode="query" data-ng-show="loader" class="md-warn"></md-progress-linear>
          </div>
          <div ng-show="list.items.length > 0" data-ng-hide="loader">
            <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3" data-ng-repeat="i in list.items">
              <div class="thumbnail">
                <div style="position: relative" class="text-center">
                  {#<a href="{{url('mail/contenteditor')}}/{{'{{idMail}}'}}/0/{{'{{i.idMailTemplate}}'}}"><div id="most">Seleccionar plantilla</div></a>#}
                  <a href="{{url('autoresponder/contenteditor')}}/{{'{{idAutoresponder}}'}}/{{'{{i.idMailTemplate}}'}}"><img src="{{url('')}}{{"{{i.dirImage}}"}}" /></a>
                  <div class="caption text-center">
                    <div><h4><b>{{"{{i.name}}"}}</b></h4>
                      <div class="btn-group" role="group">
                        <button type="button" class="btn default-inverted toltip" data-toggle="tooltip" data-placement="bottom" title="Previsualizar" data-ng-click="previewmailtempcont(i.idMailTemplateContent);" data-toggle="modal" data-target="#myModal">
                          <i class="fa fa-eye"></i>
                          <md-tooltip md-direction="bottom">
                            Previsualizar
                          </md-tooltip>
                        </button>
                      </div>
                      <div class="btn-group" role="group">
                          <a class="button btn alert-aoi-warning toltip" href="{{url('autoresponder/contenteditor')}}/{{'{{idAutoresponder}}'}}/{{'{{i.idMailTemplate}}'}}" data-toggle="tooltip" data-placement="bottom" title="Seleccionar plantilla" >
                          <i class="fa fa-hand-o-up"></i>
                          <md-tooltip md-direction="bottom">
                            Seleccionar plantilla
                          </md-tooltip>
                        </a>
                      </div>
                    </div>
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
</div>
<div id="pagination" class="text-center" ng-show="list.items.length > 0">
  <ul class="pagination">
    <li ng-class="page == 1 ? 'disabled'  : ''">
      <a  href="#/selectautoresponder/{{"{{idAutoresponder}}"}}" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
    </li>
    <li  ng-class="page == 1 ? 'disabled'  : ''">
      <a href="#/selectautoresponder/{{"{{idAutoresponder}}"}}"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
    </li>
    <li>
      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
        </b> registros </span><span>Página <b>{{"{{ page }}"}}
        </b> de <b>
          {{ "{{ (list.total_pages ) }}"}}
        </b></span>
    </li>
    <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
      <a href="#/selectautoresponder/{{"{{idAutoresponder}}"}}" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
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