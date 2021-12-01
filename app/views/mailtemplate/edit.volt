{% extends "templates/default.volt" %}
{% block header %}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  {{ stylesheet_link('library/select2/css/select2.min.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('js/angular/mailtemplate/app.js') }}
  {{ javascript_include('js/angular/mailtemplate/controller.js') }}
  {{ javascript_include('js/angular/mailtemplate/services.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  <script>
    {% if mailtemplatecontent is defined %}
      objMail ={{ mailtemplatecontent.content }} ;
    {% endif %}
              function iframeResize() {
                var iFrame = document.getElementById('iframeEditor');
                iFrame.height = iFrame.contentWindow.document.body.scrollHeight + "px";
                //iFrame.height = "650px";
              }

      function htmlPreview() {
        var editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
        $.ajax({
          url: "{{url('mailpreview/preview')}}",
          type: "POST",
          data: {
            editor: editor
          },
          error: function (msg) {
            slideOnTop(msg, 3500, 'glyphicon glyphicon-remove', 'danger');
          },
          success: function () {
            $("#modal-body-preview").empty();
            $('#modal-body-preview').append($('<iframe frameborder="0" width="100%" height="100%" src="{{url('mailpreview/previewdata')}}"/>'));
          }
        });

        document.getElementById('iframeEditor').contentWindow.RecreateEditor();
      }

  </script>
  {{ javascript_include('js/angular/mailtemplate/controller.js') }}
  {{ javascript_include('js/angular/mailtemplate/services.js') }}
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
{% endblock %}
{% block content %}
  <div ng-app="mailtemplate.controller">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Nueva plantilla prediseñada
        </div>            
        <hr class="basic-line">
        <p class="text-justify">
          No es necesario que sepa desarrollar HTML, con el editor avanzado solo seleccione, arrastre, suelte
          elementos y organicelos de la manera en que los necesite en cuestion de segundos. El editor se encarga
          de transformar sus piezas en html que podrá ser visualizado en la mayoría de los gestores de correo
          como Gmail o Hotmail. Recuerde que al usar el editor avanzado tendrá contenido responsive que podrá ser
          visualizado correctamente en dispositivos móviles de pantallas pequeñas (Este servicio podría tener un
          costo adicional).
        </p>
      </div>
    </div>


    <div class="row"  ng-controller="editController">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-info">
          <div class="body row">
            <div class="col-md-12">
              <div class="form-horizontal">
                <div class="form-group">
                  <label for="namemailtempcat" class="col-sm-2 control-label">Nombre de la plantilla</label>
                  <div class="col-sm-9">
                    <input type="text" class="undeline-input form-control" id="name" name="namemailtempcat" maxlength="80" data-ng-model="data.name">
                    <div class="text-right" data-ng-class="data.name.length > 80 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/80':''}}"}}</div>
                  </div>
                </div>
                <div class="form-group" data-ng-show="!newcategorytemplatemail">
                  <label for="mailtempcat" class="col-sm-2 control-label">Categoría</label>
                  <div class="col-sm-9">
                    <select class="chosen form-control" data-ng-model="data.idMailTemplateCategory" style="width: 100%">
                      <option ng-repeat="x in liscateg" value="{{"{{x.idMailTemplateCategory}}"}}" ng-selected="x.idMailTemplateCategory == data.idMailTemplateCategory">{{"{{x.name}}"}}</option>
                    </select>
                  </div>
                  <div class="col-sm-1 text-right">
                    <a class="positive tooltip-de" data-placement="top" title="Nueva categoría" href="" data-ng-click="newCateg()"><i class="fa fa-plus fa-2x" style="margin-right: 80%"></i></a>
                  </div>
                </div>
                <div class="form-group" data-ng-show="newcategorytemplatemail">
                  <label for="newmailtempcat" class="col-sm-2 control-label">Nueva categoría</label>
                  <div class="col-sm-9">
                    <input type="text" class="undeline-input form-control" maxlength="80" data-ng-model="newmailtempcat">
                    <div class="text-right" data-ng-class="newmailtempcat.length > 80 ? 'negative':''">{{"{{newmailtempcat.length > 0 ?  newmailtempcat.length+'/80':''}}"}}</div>
                  </div>
                  <div class="col-sm-1 text-right">
                    <a class="negative tooltip-de" data-toggle="tooltip" data-placement="top" title="Cancelar" href="" data-ng-click="cancelCateg()"><i class="fa fa-times fa-2x"></i></a>
                    <a class="positive tooltip-de" data-toggle="tooltip" data-placement="top" title="Guardar" href="" data-ng-click="saveCateg()"><i class="fa fa-check fa-2x"></i></a>
                  </div>
                </div>
                {% if user.Usertype.Allied.idAllied is defined%}
                  <div class="form-group">
                    <label for="owner" class="col-sm-2 control-label">Propietario</label>
                    <div class="col-sm-9">
                      <select class="chosen form-control" data-ng-model="data.owner" style="width: 100%">
                        <option ng-repeat="x in accounts" value="{{"{{x.idAccount}}"}}" ng-selected="x.idAccount == data.owner">{{"{{x.name}}"}}</option>
                      </select>
                    </div>
                  </div>
                {% endif %}
              </div>
            </div>
          </div>
          <div class="body row">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="advanced">
                <iframe id="iframeEditor" src="{{url('mail/editor_frame')}}" frameborder="0" width="100%" height="713px" onload="iframeResize();" seamless></iframe>
              </div>
            </div>
          </div>
          <div class="footer row none-margin">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <a href="{{ url('mailtemplate#/') }}"
                 class="button btn btn-small danger-inverted">
                <i class="fa fa-times"></i> Salir sin guardar cambios
              </a>
            </div>
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 text-right">
              <a href="" onClick="htmlPreview();" class="button btn btn-small info-inverted"
                 data-toggle="modal" data-target="#preview-modal">
                <i class="fa fa-eye"></i> Previsualizar
              </a>
              <a class="button btn btn-small warning-inverted"  data-toggle="modal" data-target="#save-as">
                <i class="fa fa-save" aria-hidden="true"></i> Guardar como
              </a>
              <a class="button btn btn-small primary-inverted" data-ng-click="saveMailTemplate(1)">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Guardar y seguir editando
              </a>
              <button type="submit" class="button btn btn-small success-inverted" data-ng-click="saveMailTemplate()">
                <i class="fa fa-save"></i> Guardar y salir
              </button>
            </div>
          </div>
        </div>
      </div>



      <div class="modal fade" id="save-as">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Seleccione modo de guardado de plantilla</h5>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="form-group">
                  <div class="col-md-6">
                    <div class="col-md-1">
                      <div class="checkboxFive">
                        <input  type="checkbox" id="saveNew" ng-model="saveNew" ng-disabled="saveExist"/>
                        <label for="saveNew"></label>
                      </div>
                    </div>
                    <div class="col-md-10">
                      <label class="radio-inline">Nueva plantilla</label>
                    </div>
                  </div>
                  <div class="col-md-6">
        
                    <div class="col-md-1">
                      <div class="checkboxFive">
                        <input  type="checkbox"  id="saveExist" ng-model="saveExist" ng-disabled="saveNew" ng-change="getTemplateMail(100)"/>
                        <label for="saveExist"></label>
                      </div>
                    </div>
                    <div class="col-md-10">
                      <label class="radio-inline">Plantilla existente</label>
                    </div>
                    
                  </div>  
                </div>  
                &nbsp;
                <div class="form-group" ng-show="saveExist">
                  <div class="col-md-3">
                    <label for="mailtempcat" class="radio-inline">Categoría</label>
                  </div>
                  <div class="col-md-8">
                    <select class="chosen form-control" ng-model="idMailTemplateSaveas" style="width: 100%">
                      <option ng-repeat="x in MailTemplateSaveAs track by $index" value="{{"{{x.idMailTemplate}}"}}" ng-selected="x.idMailTemplate == x.idMailTemplate">{{"{{x.name}}"}}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 col-xs-offset-8">
                <a class="button btn btn-block danger-inverted" data-dismiss="modal" ng-click="clearModal()">
                  <i class="fa fa-times "></i>
                </a>
              </div>
              <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <a class="button btn btn-block success-inverted" ng-disabled="!saveNew && !saveExist" data-dismiss="modal" ng-click="saveAsMailTemplete()">
                  <i class="fa fa-check"></i>
                </a>
              </div>
            </div>
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
            <button type="button" data-dismiss="modal" class="button btn btn-sm danger-inverted">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
{% endblock %}
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  <script>
    //Este idMailTemplate se pone como variable para poderla capturar con angular
    var idMailTemplate = '{{mailtemplatecontent.idMailTemplate}}';
    $(function () {
      $('.tooltip-de').tooltip();
      $(".chosen").select2({
        placeholder: 'Seleccione una categoría'
      });
      //$(function () {
    {#$('#globalTemp').bootstrapToggle({
      on: 'On',
      off: 'Off',
      onstyle: 'success',
      offstyle: 'danger',
      size: 'small'
    });#}
        //});
      });
      var relativeUrlBase = "{{urlManager.get_base_uri()}}";
      var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
      var templateBase = "mailtemplate";
  </script>
{% endblock %}
