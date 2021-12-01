{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}

  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  <link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">
{% endblock %}

{% block js %}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  
  {{ javascript_include('js/angular/mail/dist/mail.893d208fdfd38a12f66a.min.js') }} 
  {#{{ javascript_include('js/angular/mail/app.js') }}
  {{ javascript_include('js/angular/mail/controllers.js') }}
  {{ javascript_include('js/angular/mail/directives.js') }}
  {{ javascript_include('js/angular/mail/services.js') }}#}
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}
  {{ javascript_include('library/moment/src/prueba.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/moment/src/moment.js') }}
  {{ javascript_include('library/angular-moment/angular-moment.min.js') }}
  {{ javascript_include('library/angular-file-upload-master/dist/angular-file-upload.js') }}
{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div  data-ng-controller="mailStructureEditorController" ng-cloak>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Contenido del correo <em><b>{{ mail.name }}</b></em>
        </div>
        <hr class="basic-line"/>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="subtitle">
          <em>Estructuras prediseñadas</em>
        </div>
        <br>
        <p class="small-text text-justify">
          Elija un esqueleto o marco de trabajo para empezar a agregar elementos con el editor
          avanzado y crear el contenido a su gusto
        </p>
      </div>
    </div>

    <div class="row ">
      <div class="col-xs-6 col-sm-6 col-lg-6 col-lg-offset-6 text-right wrap">
        <a href="{{ url('mail/create#/content/')~mail.idMail }}" class="button shining btn btn-sm danger-inverted"><i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar</a>
      </div>
    </div>

    <div class="row">
      <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="addContactlist()">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
          <div class="block block-info fill-block-default">
            <div class="body row">
              <div class="row">

                <div id="pagination" class="text-center">
                  <ul class="pagination">
                    <li ng-class="page == 1 ? 'disabled'  : ''">
                      <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                    </li>
                    <li  ng-class="page == 1 ? 'disabled'  : ''">
                      <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                    </li>
                    <li>
                      <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{mailstructure.total }}"}}
                        </b> registros </span><span>Página <b>{{"{{ page }}"}}
                        </b> de <b>
                          {{ "{{ (mailstructure.total_pages ) }}"}}
                        </b></span>
                    </li>
                    <li   ng-class="page == (mailstructure.total_pages) || mailstructure.total_pages == 0 ? 'disabled'  : ''">
                      <a href="#/" ng-click="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                    </li>
                    <li   ng-class="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0 ? 'disabled'  : ''">
                      <a ng-click="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                    </li>
                  </ul>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap row">
                  <div class="fill-block fill-block-default text-center" >
                    <div class="body">
                      <div class="row" style="margin-left: 0.5%; margin-right: 5%">
                        <div class="col-xs-12 col-sm-12 col-lg-12  row ">
                          <div class="col-xs-4 col-sm-4 col-lg-4 ">
                            <div class="input-group">
                              <input class="form-control"  id="name" ng-keyup='search()' placeholder="Buscar por nombre" ng-model="filter.name" />
                              <span class=" input-group-addon" id="basic-addon1" >
                                <i class="fa fa-search"></i>
                              </span>
                            </div>
                          </div>
                        </div> 
                      </div> 
                      <br>
                      <div class="row" >

                        <div class="col-sm-6 col-md-3"  ng-repeat="key in mailstructure[0].items">
                          <div class="thumbnail">
                            <a href="{{url("mail/contenteditor/") ~ mail.idMail ~ "/" ~ "{{ key.idMailStructure}}" }}" >
                              <img src="{{url('')}}mail_structure/{{user.userType.subAccount.account.idAllied}}/{{"{{key.idMailStructure}}"}}_thumb.png" >
                              {#                              <img src="{{url('')}}images/1.png" style="width: 100%">#}
                            </a>
                            <div class="caption none-padding" style=" margin-bottom: -8px">
                              <br>
                              {{"{{ key.name }}"}}
                              <p class="none-padding ">
                                <a href="{{url("mail/contenteditor/") ~ mail.idMail ~ "/" ~ "{{ key.idMailStructure}}" }}" > Elegir </a>
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> 
              </div>

              <div id="pagination" class="text-center">
                <ul class="pagination">
                  <li ng-class="page == 1 ? 'disabled'  : ''">
                    <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                  </li>
                  <li  ng-class="page == 1 ? 'disabled'  : ''">
                    <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                  </li>
                  <li>
                    <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{mailstructure.total }}"}}
                      </b> registros </span><span>Página <b>{{"{{ page }}"}}
                      </b> de <b>
                        {{ "{{ (mailstructure.total_pages ) }}"}}
                      </b></span>
                  </li>
                  <li   ng-class="page == (mailstructure.total_pages) || mailstructure.total_pages == 0 ? 'disabled'  : ''">
                    <a href="#/" ng-click="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                  </li>
                  <li   ng-class="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="page == (mailstructure.total_pages)  || mailstructure.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                  </li>
                </ul>
              </div>                              

            </div>
          </div>
        </div>
    </div>
  </form>
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
        <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger">Cerrar</button>
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
var templateBase = "mail";
  </script>

{% endblock %}
