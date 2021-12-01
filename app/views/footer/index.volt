{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
{% endblock %}

{% block js %}
  {{ javascript_include('js/angular/footer/app.js') }}
  {{ javascript_include('js/angular/footer/controllers.js') }}
  {{ javascript_include('js/angular/footer/directives.js') }}
  {{ javascript_include('js/angular/footer/services.js') }}

  <script>

    function verPreview(id) {
      $.post("{{url('footer/previewindex')}}/" + id, function (preview) {
        var e = preview.preview;
        $("#modal-body-preview").empty();
        //console.log(e);
        $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#modal-body-preview').contents().find('body').append(e);
      });
    }

  </script>
{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div ng-app="footer">

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Footers
        </div>
        <hr class="basic-line"/>
        <p class="small-text">
          Aquí puede ver, crear o editar los footers que podra asociar a una cuenta.
        </p>
      </div>
    </div>

    <div class="row" ng-controller="indexController">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="text-right">
          <a class="btn btn-small success-inverted" href="{{ url('footer/create') }}" >Crear nuevo footer</a>
        </div>
        <div ng-show="footer.items.length > 0" ng-cloak>
          <div id="pagination" class="text-center">
            <ul class="pagination">
              <li ng-class="page == 1 ? 'disabled'  : ''">
                <a  href="" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="page == 1 ? 'disabled'  : ''">
                <a href=""  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{footer.total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                  </b> de <b>
                    {{ "{{ (footer.total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="page == (footer.total_pages)  || footer.total_pages == 0  ? 'disabled'  : ''">
                <a href="" ng-click="page == (footer.total_pages)  || footer.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li   ng-class="page == (footer.total_pages)  || footer.total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (footer.total_pages)  || footer.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding ng-cloak">
            <table class="table table-bordered table-responsive">
              <thead class="theader ">
                <tr>
                  <th>Nombre</th>
                  <th>Descripcion</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr ng-repeat="footer in footer.items" >
                  <td>
                    <div class="medium-text">
                      {{ '{{footer.name}}' }}
                    </div>
                  </td>
                  <td>
                    <div class="small-text">
                      <em>
                        {{ '{{footer.description}}' }}
                      </em>
                    </div>
                  </td>
                  <td>
                    <div class="pull-right">
                      <a href="" id="btncontent" class="button shining btn btn-xs-round shining shining-round round-button default-inverted"
                         data-toggle="modal" data-target="#preview-modal" data-ng-click="viewContent(footer.idFooter)" data-placement="top" title="Previsualizar">
                        <span class="glyphicon glyphicon-eye-open"></span>
                      </a>
                      <a href="{{ url('footer/update') }}/{{ '{{footer.idFooter}}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted" data-placement="left" title="Editar footer">
                        <span class="glyphicon glyphicon-pencil"></span>
                      </a>
                      <a href="" id="delete" data-ng-click="openModal(footer.idFooter)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-placement="left" title="Eliminar footer" >
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
              <li ng-class="page == 1 ? 'disabled'  : ''">
                <a  href="" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
              </li>
              <li  ng-class="page == 1 ? 'disabled'  : ''">
                <a href=""  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
              </li>
              <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{footer.total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                  </b> de <b>
                    {{ "{{ (footer.total_pages ) }}"}}
                  </b></span>
              </li>
              <li   ng-class="page == (footer.total_pages)  || footer.total_pages == 0  ? 'disabled'  : ''">
                <a href="" ng-click="page == (footer.total_pages)  || footer.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
              </li>
              <li   ng-class="page == (footer.total_pages)  || footer.total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (footer.total_pages)  || footer.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div ng-show="footer.items.length == 0">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="block block-success">
            <div class="body success-no-hover text-center">
              <h2>
                No hay registros de Footers, para crear uno haga <a href="{{ url('footer/create') }}">clic aquí</a>.
              </h2>
              </h2>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade " id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-prevew-width">
          <div class="modal-content modal-prevew-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h1 class="modal-title" id="myModalLabel">Previsualización</h1>
            </div>
            <div class="modal-body modal-prevew-body" id="modal-body-preview" style="height: 550px;"></div>
            <div class="modal-footer">
              <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger-inverted">Cerrar</button>
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
            <div style="z-index: 999999;">
              <a data-ng-click="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
              <a data-ng-click="deleteFooter()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

{% endblock %}

{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
      var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
      var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
      var templateBase = "footer";
  </script>

{% endblock %}
